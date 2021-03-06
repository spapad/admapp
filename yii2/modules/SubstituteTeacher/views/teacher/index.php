<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use app\modules\SubstituteTeacher\models\TeacherRegistry;
use app\modules\SubstituteTeacher\models\Teacher;
use kartik\select2\Select2;
use yii\bootstrap\ButtonDropdown;
use app\components\FilterActionColumn;
use app\modules\SubstituteTeacher\models\Specialisation;

$bundle = \app\modules\SubstituteTeacher\assets\ModuleAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\modules\SubstituteTeacher\models\TeacherSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('substituteteacher', 'Teachers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-index">
    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="btn-group-container">
        <?= Html::a(Yii::t('substituteteacher', 'Create Teacher'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= ButtonDropdown::widget([
            'label' => Yii::t('substituteteacher', 'Batch Insert Teachers'),
            'options' => ['class' => 'btn-primary'],
            'dropdown' => [
            'items' => [
                [
                    'label' => Yii::t('substituteteacher', 'Batch insert teachers in Registry'),
                    'url' => ['substitute-teacher-file/import', 'route' => 'import/file-information', 'type' => 'registry']
                ],
                '<li class="divider"></li>',
                [
                    'label' => Yii::t('substituteteacher', 'Batch Insert Placement Preferences'),
                    'url' => ['substitute-teacher-file/import', 'route' => 'import/file-information', 'type' => 'placement-preference']
                ],
                [
                    'label' => Yii::t('substituteteacher', 'Download import sample'),
                    'url' => "{$bundle->baseUrl}/ΥΠΟΔΕΙΓΜΑ ΜΑΖΙΚΗΣ ΕΙΣΑΓΩΓΗΣ ΠΡΟΤΙΜΗΣΕΩΝ ΤΟΠΟΘΕΤΗΣΗΣ ΕΤΟΥΣ.xls"
                ],
                '<li class="divider"></li>',
                [
                    'label' => Yii::t('substituteteacher', 'Batch Update Teacher Information'),
                    'url' => ['substitute-teacher-file/import', 'route' => 'import/file-information', 'type' => 'update-teacher']
                ],
                '<li class="divider"></li>',
                [
                    'label' => Yii::t('substituteteacher', 'Batch Insert Teacher In Year'),
                    'url' => ['substitute-teacher-file/import', 'route' => 'import/file-information', 'type' => 'teacher']
                ],
                [
                    'label' => Yii::t('substituteteacher', 'Download import sample'),
                    'url' => "{$bundle->baseUrl}/ΥΠΟΔΕΙΓΜΑ ΜΑΖΙΚΗΣ ΕΙΣΑΓΩΓΗΣ ΑΝΑΠΛΗΡΩΤΩΝ ΕΤΟΥΣ.xls"
                ],
                '<li class="divider"></li>',
                [
                    'label' => Yii::t('substituteteacher', 'Batch Import MK Experiences'),
                    'url' => ['substitute-teacher-file/import', 'route' => 'import/file-information', 'type' => 'stteacher-mkexperience']
                ],
                [
                    'label' => Yii::t('substituteteacher', 'Download import sample'),
                    'url' => "{$bundle->baseUrl}/ΥΠΟΔΕΙΓΜΑ ΜΑΖΙΚΗΣ ΕΙΣΑΓΩΓΗΣ ΠΡΟΫΠΗΡΕΣΙΩΝ ΑΝΑΠΛΗΡΩΤΩΝ ΕΤΟΥΣ.xls"
                ],                     
            ],
        ],
        ]);
        ?>
    </div>

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            if ($model->status == Teacher::TEACHER_STATUS_NEGATION) {
                return ['class' => 'danger'];
            } elseif ($model->status == Teacher::TEACHER_STATUS_APPOINTED) {
                return ['class' => 'success'];
            } elseif ($model->status == Teacher::TEACHER_STATUS_PENDING) {
                return ['class' => 'warning'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'attribute' => 'registry_id',
                'label' => Yii::t('substituteteacher', 'Teacher'),
                'value' => function ($model) {
                    return Html::a(Html::icon('user'), ['teacher-registry/view', 'id' => $model->registry_id], ['class' => 'btn btn-xs btn-default', 'title' => Yii::t('substituteteacher', 'View registry entry')]) . ' ' . $model->registry->name;
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'registry_id',
                    'data' => TeacherRegistry::selectables('id', 'name'),
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ]),
                'format' => 'html'
            ],
            [
                'attribute' => 'specialisation_id',
                'label' => Yii::t('substituteteacher', 'Specialisation'),
                'value' => function ($m) {
                    $all_labels = $m->registry->specialisation_labels;
                    return empty($all_labels) ? null : implode('<br/>', $all_labels);
                },
                'format' => 'html',
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'specialisation_id',
                    'data' => Specialisation::selectables(),
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => '...'],
                    'pluginOptions' => ['allowClear' => true],
                ]),
            ],
            'year',
            [
                'attribute' => 'status',
                'value' => 'status_label',
                'filter' => Teacher::getChoices('status')
            ],
            [
                'attribute' => '',
                'header' => Yii::t('substituteteacher', 'Teacher boards'),
                'value' => function ($m) {
                    return $m->boards ? implode(
                        '<br>',
                        array_map(function ($model) {
                            return $model->label;
                        }, $m->boards)
                    ) : null;
                },
                'format' => 'html'
            ],
            [
                'attribute' => '',
                'header' => Yii::t('substituteteacher', 'Placement preferences'),
                'value' => function ($m) {
                    return $m->placementPreferences ? implode(
                        '<br>',
                        array_map(function ($pref) {
                            return $pref->label_for_teacher;
                        }, $m->placementPreferences)
                    ) : null;
                },
                'filter' => false,
                'format' => 'html'
            ],

            [
                'class' => FilterActionColumn::className(),
                'filter' => FilterActionColumn::LINK_INDEX_CONFIRM,
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
</div>

