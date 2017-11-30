<?php
namespace app\modules\finance\components;
use Yii;
use yii\base\ActionFilter;
//use app\modules\finance\models\FinanceYear;


class FinanceInitialChecks extends ActionFilter
{
    public function beforeAction($action)
    {
        $parentBeforeAction = parent::beforeAction($action);

        if (!$parentBeforeAction) {
            return false;
        }
        
        if(!($workingYear = Integrity::uniqueCurrentYear()))
        {   
            if(!(Yii::$app->controller->id == 'finance-year'))
                Yii::$app->response->redirect(['/finance/finance-year']);
                
            Yii::$app->session->setFlash('info', "Σφάλμα στον ορισμό του οικονομικού έτους στο οποίο εργάζεστε. Παρακαλώ επικοινωνήστε με το διαχειριστή.");
        }
        else
            Yii::$app->session["working_year"] = $workingYear;
        
        if(!Integrity::creditsIntegrity(Yii::$app->session["working_year"]))
        {
            Yii::$app->session->setFlash('info', "Το άθροισμα των πιστώσεων των ΚΑΕ δεν συμφωνεί με την πίστωση για το έτος " . Yii::$app->session["working_year"] . ". Παρακαλώ διορθώστε για να προχωρήσετε.");
            
            if(!(Yii::$app->controller->id == 'finance-kaecredit')){
                return Yii::$app->response->redirect(['/finance/finance-kaecredit']);
            }
        }
        
        return true;
    }
}