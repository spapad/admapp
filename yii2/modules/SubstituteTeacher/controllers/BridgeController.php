<?php

namespace app\modules\SubstituteTeacher\controllers;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\httpclient\Client;
use app\modules\SubstituteTeacher\models\Prefecture;
use app\modules\SubstituteTeacher\models\CallPosition;
use app\modules\SubstituteTeacher\models\Teacher;
use app\modules\SubstituteTeacher\models\Call;
use app\modules\SubstituteTeacher\models\TeacherBoard;
use app\modules\SubstituteTeacher\models\TeacherRegistrySpecialisation;
use yii\db\Expression;
use app\modules\SubstituteTeacher\models\PlacementPreference;
use yii\web\NotFoundHttpException;
use yii\data\ArrayDataProvider;

class BridgeController extends \yii\web\Controller
{
    private $client = null; // http client for calls to the applications frontend

    public function init()
    {
        $this->client = new Client([
            'baseUrl' => $this->module->params['applications-baseurl'],
            // 'transport' => 'yii\httpclient\CurlTransport',
            // 'timeout' => 20,
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);
    }

    protected function getHeaders()
    {
        return [
            'Authorization' => "Bearer " . $this->module->params['applications-key']
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'remote-status' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['remote-status', 'receive', 'send', 'fetch'],
                        'allow' => true,
                        'roles' => ['admin', 'spedu_user'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionRemoteStatus()
    {
        $data = null;
        $status = null;
        $services_status = [
            'applications' => false,
            'load' => false,
            'clear' => false,
            'unload' => false,
        ];

        if (\Yii::$app->request->isPost) {
            $status_response = $this->client->post('index', null, $this->getHeaders())->send();
            $status = $status_response->isOk ? $status_response->isOk : $status_response->statusCode;
            $data = $status_response->getData();
            $services_status = array_merge($services_status, $data['services']);
        }
        return $this->render('remote-status', compact('status', 'services_status', 'data'));
    }

    public function actionReceive()
    {
        return $this->render('receive');
    }

    /**
     * @throws NotFoundHttpException if the fetch data cannot be found
     */
    public function actionFetch($what)
    {
        switch ($what) {
            case 'teacher':
                $ids = array_filter(explode(',', \Yii::$app->request->post('ids', '')));
                $dataProvider = new ArrayDataProvider([
                    'allModels' => Teacher::find()
                        ->where(['id' => $ids])
                        ->all(),
                    'pagination' => false,
                ]);
                return $this->renderAjax('_teacher_list', compact('dataProvider'));
                break;
            default:
                throw new NotFoundHttpException();
                break;
        }
    }

    /**
     * Choose a call and send relevant data to applications frontend.
     *
     * @param int|null call_id 
     */
    public function actionSend($call_id = 0)
    {
        $call_model = Call::findOne(['id' => $call_id]);
        // if call is selected, collect positions, prefectures, teachers and placement preferences
        if (!empty($call_model)) {
            // no filtering on prefectures; catalogue only
            $prefectures = Prefecture::find()->all();
            $prefecture_substitutions = [];
            $prefectures = array_map(function ($k) use ($prefectures, &$prefecture_substitutions) {
                $index = $k + 1;
                $prefecture_substitutions[$index] = $prefectures[$k]->id;
                return array_merge(['index' => $index], $prefectures[$k]->toApi());
            }, array_keys($prefectures));

            // collect the call positions of the specific call; 
            // also get prefectures that will be used to filter teachers
            $call_positions_prefectures = [];
            $call_positions = CallPosition::findOnePerGroup($call_id);
            $call_positions = array_map(function ($k) use (&$call_positions_prefectures, $call_positions, $prefecture_substitutions) {
                $index = $k + 1;
                $call_positions_prefectures[] = $call_positions[$k]->position->prefecture_id;
                return array_merge(['index' => $index], $call_positions[$k]->toApi($prefecture_substitutions));
            }, array_keys($call_positions));
            $call_positions_prefectures = array_unique($call_positions_prefectures);

            // get the teachers that meet the following criteria:
            // - they belong to the relevant boards (year / specialisation)
            // - they are eligible for appointment 
            // - they have priority for appointment (top X in board) 
            // To avoid huge joins, get the list of applicable specialisations prior to selecting teachers 
            $teachers = [];
            $teacherboard_table = TeacherBoard::tableName();
            $call_teacher_specialisations = $call_model->callTeacherSpecialisations;
            if (empty($call_teacher_specialisations)) {
                // no teachers wanted...
                $call_teacher_specialisations = [];
            }
            // keep track of specialisations and counts of teachers
            $teacher_counts = [];
            // Get the list per specialisation and combine all teachers 
            foreach ($call_teacher_specialisations as $call_teacher_specialisation) {
                $extra_wanted = intval($call_teacher_specialisation->teachers * (1 + $this->module->params['extra-call-teachers-percent']));
                $call_specialisation_teachers_pool = Teacher::find()
                    ->year($call_model->year)
                    ->status(Teacher::TEACHER_STATUS_ELIGIBLE)
                    ->joinWith(['boards', 'registry', 'registry.specialisations', 'placementPreferences'])
                    ->andWhere(["{$teacherboard_table}.[[specialisation_id]]" => $call_teacher_specialisation->specialisation_id])
                    ->andWhere(["{$teacherboard_table}.[[specialisation_id]]" => new Expression(TeacherRegistrySpecialisation::tableName() . '.[[specialisation_id]]')])
                    ->andWhere([PlacementPreference::tableName() . ".[[prefecture_id]]" => $call_positions_prefectures])
                    ->orderBy([
                        "{$teacherboard_table}.[[board_type]]" => SORT_ASC,
                        "{$teacherboard_table}.[[order]]" => SORT_ASC,
                        "{$teacherboard_table}.[[points]]" => SORT_ASC, // in case order is not used
                    ])
                    ->select([Teacher::tableName() . ".[[id]]"]);
                $call_specialisation_teachers = Teacher::find()
                    ->where(['id' => (new \yii\db\Query())
                        ->select(['id'])
                        ->distinct()
                        ->from(['au' => $call_specialisation_teachers_pool])
                    ])
                    ->limit($extra_wanted) // only get the number of teachers wanted
                    ->all();

                // keep track of specialisations and counts of teachers
                $teacher_counts[] = [
                    'specialisation' => $call_teacher_specialisation->specialisation->label,
                    'wanted' => $call_teacher_specialisation->teachers,
                    'extra_wanted' => $extra_wanted,
                    'available' => count($call_specialisation_teachers)
                ];
                // TODO make list unique in case of duplicates due to other specialisations ? 
                // TODO what happens when a teacher is selected from one board but not from anothr where he also is eligible?
                $teachers = array_merge($teachers, $call_specialisation_teachers);
            }
            $placement_preferences = [];
            $walk = array_walk($teachers, function ($m, $k) use (&$placement_preferences) {
                $placement_preferences = array_merge($placement_preferences, $m->placementPreferences);
            });
            $teacher_substitutions = [];
            $teacher_ids = array_map(function ($m) {
                return $m->id;
            }, $teachers);
            $teachers = array_map(function ($k) use ($teachers, &$teacher_substitutions) {
                $index = $k + 1;
                $teacher_substitutions[$index] = $teachers[$k]->id;
                return array_merge(['index' => $index], $teachers[$k]->toApi());
            }, array_keys($teachers));
            $placement_preferences = array_map(function ($k) use ($placement_preferences, $prefecture_substitutions, $teacher_substitutions) {
                $index = $k + 1;
                return array_merge(['index' => $index], $placement_preferences[$k]->toApi($prefecture_substitutions, $teacher_substitutions));
            }, array_keys($placement_preferences));

            // GET request displays "dry-run" results
            // POST does the actual sending of data
            $data = [
                'version' => '1.0',
                'prefectures' => $prefectures,
                'teachers' => $teachers,
                'positions' => $call_positions,
                'placement_preferences' => $placement_preferences
            ];
            $count_prefectures = count($prefectures);
            $count_teachers = count($teachers);
            $count_call_positions = count($call_positions);
            $count_placement_preferences = count($placement_preferences);

            $status_clear = null;
            $status_load = null;

            if (\Yii::$app->request->isPost) {
                // first issue a clear command
                $status_response = $this->client->delete('clear', null, $this->getHeaders())->send();
                $status_clear = $status_response->isOk ? $status_response->isOk : $status_response->statusCode;
                $response_data_clear = $status_response->getData();
                if ($status_clear === true) {
                    // then post data
                    $status_response = $this->client->post('load', $data, $this->getHeaders())->send();
                    $status_load = $status_response->isOk ? $status_response->isOk : $status_response->statusCode;
                    $response_data_load = $status_response->getData();
                }
            }
        }

        return $this->render('send', compact('call_model', 'teacher_ids', 'status_clear', 'response_data_clear', 'status_load', 'response_data_load', 'data', 'count_prefectures', 'count_teachers', 'teacher_counts', 'count_call_positions', 'count_placement_preferences'));
    }
}
