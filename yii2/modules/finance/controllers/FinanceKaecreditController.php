<?php

namespace app\modules\finance\controllers;

use Yii;
use app\modules\finance\models\FinanceKaecredit;
use app\modules\finance\models\FinanceKaecreditSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\finance\models\FinanceKae;
use yii\base\Model;
use yii\base\Exception;

/**
 * FinanceKaecreditController implements the CRUD actions for FinanceKaecredit model.
 */
class FinanceKaecreditController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all FinanceKaecredit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FinanceKaecreditSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

 /*   public function actionSetKaeCredits()
    {
        
    }*/
    
    /**
     * Displays a single FinanceKaecredit model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new FinanceKaecredit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $allkaes = FinanceKae::find()->asArray()->all();
        
        $kaecredits = array();
        $kaetitles = array();
        $i = 0;
        foreach ($allkaes as $kae)
        {
            $kaetitles[$i] = $kae['kae_title'];
            $kaecredits[$i] = new FinanceKaecredit();
            $kaecredits[$i]->kae_id = $kae['kae_id'];
            $kaecredits[$i]->kaecredit_amount = 0;
            $kaecredits[$i]->kaecredit_date = date("Y-m-d H:i:s");
            $kaecredits[$i++]->year = Yii::$app->session["working_year"];
            if($i == 2) break;
        }

        
        if(($userdata = Model::loadMultiple($kaecredits, Yii::$app->request->post()))){
           //echo "<pre>"; print_r(Yii::$app->request->post()); echo "</pre>"; die();
            //Model::loadMultiple($kaecredits, $data);
            //echo "<pre>"; print_r($kaecredits); echo "</pre>"; die();
            
            if(Model::loadMultiple($kaecredits, $userdata) && Model::validateMultiple($kaecredits))
                foreach ($kaecredits as $kaecredit)
                {   
                    $kaecredits->kaecredit_amount = bcdiv($kaecredits->kaecredit_amount*100, 1, 2);
                    echo $kaecredits->kaecredit_amount; die();
                    $kaecredit->save();
                }
            else throw new Exception("Data validation failure.");
            
            return $this->redirect(['/finance/finance-kaecredit']);
        } else {
            return $this->render('create', [
                'model' => $kaecredits,
                'kaetitles' => $kaetitles
            ]);
        }
    }

    /**
     * Updates an existing FinanceKaecredit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {//echo "Hallo"; die();
        //$model = $this->findModel($id);
        $model = FinanceKae::find()->asArray()->all();
        //foreach ()
        if ($model->load(Yii::$app->request->post())) {
            foreach($model as $kaecredit)
                //FinanceKaecredit::loa
            return $this->redirect(['view', 'id' => $model->kaecredit_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing FinanceKaecredit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FinanceKaecredit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FinanceKaecredit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FinanceKaecredit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}