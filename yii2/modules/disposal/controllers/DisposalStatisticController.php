<?php
namespace app\modules\disposal\controllers;

use DateTime;
use Exception;
use Yii;
use kartik\mpdf\Pdf;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use app\modules\disposal\DisposalModule;
use app\modules\disposal\models\DisposalStatistic;
use app\modules\eduinventory\components\EduinventoryHelper;
use app\modules\schooltransport\models\Directorate;
use app\modules\disposal\models\Disposal;

class DisposalStatisticController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [   'class' => AccessControl::className(),
                'rules' =>  [
                    ['actions' => ['index', 'exportstatistic', 'exportexcel'], 'allow' => true, 'roles' => ['schtransport_viewer']],
                ]
            ]
        ];
    }
    
    
    public function beforeAction($action)
    {
        if($action->id == 'exportstatistic')
            $this->enableCsrfValidation = false;
            return parent::beforeAction($action);
    }
    
    
    public function actionIndex()
    {
        $school_years = DisposalStatistic::getSchoolYearOptions();
        if(is_null($school_years)){
            Yii::$app->session->addFlash('danger', DisposalModule::t('modules/disposal/app', "The are no disposals to show statistics."));
            return $this->redirect(['disposal/index']);
        }
        
        $prefectures['ALL'] = DisposalModule::t('modules/disposal/app', 'Όλοι οι νομοί');
        $prefectures = array_merge($prefectures, EduinventoryHelper::getPrefectures());
        $education_levels['ALL'] = DisposalModule::t('modules/disposal/app', 'Όλες οι βαθμίδες');
        $education_levels = array_merge($education_levels, EduinventoryHelper::getEducationalLevels());
        $duties['ALL'] = DisposalModule::t('modules/disposal/app', 'Όλα τα καθήκοντα');
        $duties = array_merge($duties, DisposalStatistic::getDutyOptions());
        $reasons['ALL'] = DisposalModule::t('modules/disposal/app', 'Όλοι οι λόγοι');
        $reasons = array_merge($reasons, DisposalStatistic::getReasonOptions());
        $specializations['ALL'] = DisposalModule::t('modules/disposal/app', 'Όλες οι ειδικότητες');
        $specializations = array_merge($specializations, EduinventoryHelper::getSpecializations());
        $groupby_options = DisposalStatistic::getGroupByOptions();
        
        $chart_types = DisposalStatistic::getChartTypeOptions();
        
        $model = new DisposalStatistic();
        $model->statistic_schoolyear = [EduinventoryHelper::getSchoolYearOf(date('Y-m-d'))];
        $model->statistic_educationlevel = 'ALL';
        $model->statistic_prefecture = 'ALL';
        $model->statistic_specialization = 'ALL';
        $model->statistic_duty = 'ALL';
        $model->statistic_reason = 'ALL';
        $model->statistic_groupby = DisposalStatistic::GROUPBY_PERFECTURE;
        $model->statistic_charttype = DisposalStatistic::CHARTTYPE_BAR;
        //echo "<pre>"; print_r($model); echo "<pre>"; die();
        $result_data = $model->getStatistics();
        //echo "<pre>"; print_r($result_data); echo "<pre>"; die();
        
/*         if (count($result_data['LABELS']) < 2) {
            $model->statistic_charttype = DisposalStatistic::CHARTTYPE_DOUGHNUT;
        } */

        if ($model->load(Yii::$app->request->post())){        
            return $this->render('index', ['model' => $model, 'school_years' => $school_years, 
                'prefectures' => $prefectures, 'education_levels' => $education_levels, 'chart_types' => $chart_types,
                'duties' => $duties, 'reasons' => $reasons, 'specializations' => $specializations,
                'groupby_options' => DisposalStatistic::getGroupByOptions(), 
                'selected_chart_type' => $model->statistic_charttype,
                'chart_title' => $model->getStatisticLiteral(), 'result_data' => $model->getStatistics()
            ]);
        }
        else {
            return $this->render('index', ['model' => $model, 'school_years' => $school_years,
                'prefectures' => $prefectures, 'education_levels' => $education_levels, 'chart_types' => $chart_types,
                'duties' => $duties, 'reasons' => $reasons, 'specializations' => $specializations,
                'groupby_options' => DisposalStatistic::getGroupByOptions(),
                'selected_chart_type' => $model->statistic_charttype,
                'chart_title' => $model->getStatisticLiteral(), 'result_data' => $result_data
            ]);
        }
        
    }
    
    public function actionExportstatistic()
    {
        if (!isset(Yii::$app->request->post()['image_data']) || !isset(Yii::$app->request->post()['table_data'])) {
            Yii::$app->session->addFlash('danger', DisposalModule::t('modules/disposal/app', "Error in creating pdf file."));
            return $this->redirect(['index']);
        }
        
        $content = $this->renderPartial('export', ['image' => Yii::$app->request->post()['image_data'],
            'tabledata' => Yii::$app->request->post()['table_data']]);
        
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'filename' => 'statistic.pdf',
            'destination' => Pdf::DEST_DOWNLOAD,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Περιφερειακή Διεύθυνση Πρωτοβάθμιας και Δευτεροβάθμιας Εκπαίδευσης Κρήτης'],
        ]);
        return $pdf->render();
    }
    
    public function actionExportexcel($period)
    {
        try {
            if (!isset($period) || is_null($period) || !is_numeric($period)) {
                throw new Exception("An invalid period value was given to export school transports data.");
            }
            
            $transports = SchtransportTransport::getSchoolYearTransports($period);
            //echo "<pre>"; print_r($transports); echo "<pre>";die();
            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();
            
            $cellstyle =    ['borders' => ['outline' => [   'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FFFF0000'],]]];
            
            $row = 1;
            $column = 1;
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Α/Α', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Σχολικό Έτος', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Σχολείο', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Κατηγορία Προγράμματος', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Περιγραφή Κατηγορίας Προγράμματος', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Τίτλος Προγράμματος', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Κωδικός Προγράμματος', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Χώρα Προορισμού', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Πόλη Προορισμού', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Ημερομηνία Αναχώρησης', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Ημερομηνία Επιστροφής', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Βαθμίδα Εκπαίδευσης', DataType::TYPE_STRING);
            $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, 'Διεύθυνση Εκπαίδευσης', DataType::TYPE_STRING);
            foreach ($transports as $transport) {
                $row++;
                $column = 1;
                $startyear = Statistic::getSchoolYearOf(DateTime::createFromFormat('Y-m-d', $transport['transport_startdate']));
                $directorate = Directorate::findOne(['directorate_id' => $transport['directorate_id']]);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, $row-1, DataType::TYPE_NUMERIC);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, (string)$startyear . '-' . (string)($startyear+1), DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, $transport['school_name'], DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, $transport['programcategory_programtitle'], DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, $transport['programcategory_programdescription'], DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, $transport['program_title'], DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, $transport['program_code'], DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, $transport['meeting_country'], DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, $transport['meeting_city'], DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, Yii::$app->formatter->asDate($transport['transport_startdate'], 'dd-MM-Y'), DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, Yii::$app->formatter->asDate($transport['transport_enddate'], 'dd-MM-Y'), DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, DisposalModule::t('modules/disposal/app', $directorate['directorate_stage']), DataType::TYPE_STRING);
                $worksheet->setCellValueExplicitByColumnAndRow($column++, $row, $directorate['directorate_name'], DataType::TYPE_STRING);
            }
            $writer = new Xls($spreadsheet);
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="file.xls"');
            $writer->save('php://output');
        } catch (Exception $exc) {
            Yii::$app->session->addFlash('danger', DisposalModule::t('modules/disposal/app', $exc->getMessage()));
            return $this->redirect('index');
        }
    }        
}
