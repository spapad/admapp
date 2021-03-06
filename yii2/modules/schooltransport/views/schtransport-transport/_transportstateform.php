<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\schooltransport\Module;
use kartik\datecontrol\DateControl;
use kartik\typeahead\Typeahead;

/* @var $this yii\web\View */
/* @var $model app\modules\finance\models\FinanceExpenditure */
/* @var $form yii\widgets\ActiveForm */

$typeahead_notes = array('E-mail', 'Ε-mail', 'Πιστό Αντίγραφο', 'Ταχυδρομείο', 'Αλληλογραφία', 'Fax', 'Φαξ');

$disabled = ($transportstate_model['state_id'] == 1) ? false : true; 

$existingFileUrl = "";
if($updateFlag && !is_null($trnsprt_model->transport_signedapprovalfile)){
    $existingFile = Url::to(['/schooltransport/schtransport-transport/downloadsigned', 'id' =>$trnsprt_model['transport_id']]);
    $existingFileUrl = ' (<i>' . Html::a(Module::t('modules/schooltransport/app', 'Existing file') . '&nbsp;<span class="glyphicon glyphicon-download"></span>', $existingFile,
        ['title' => Module::t('modules/schooltransport/app', 'Download digitally signed file'),
            'data-method' => 'post',
            'target' => '_blank'
        ]). '</i>)';
}
//}
?>

<div class="finance-expenditure-form  col-lg-6">

    <?php $form = ActiveForm::begin(); ?>

	<?= $form->field($transportstate_model, 'transportstate_date')->widget(DateControl::classname(), 
                ['type' => DateControl::FORMAT_DATE])->label(Module::t('modules/schooltransport/app', 'Date'));
    ?>
    
	<?= $form->field($transportstate_model, 'transportstate_comment')->widget(Typeahead::classname(), 
	                                                       ['pluginOptions' => ['highlight'=>true],
	                                                         'dataset' => [['local' => $typeahead_notes, 'limit' => 10]]
	                                                       ])->
           label(Module::t('modules/schooltransport/app', 'Description')); ?>

	<?= $form->field($trnsprt_model, 'signedfile')->fileInput(['disabled' => $disabled])->label(Module::t('modules/schooltransport/app', 'Digitally Signed File') . $existingFileUrl) ?>   
    
    <div class="form-group  text-right">
    	<?= Html::a(Yii::t('app', 'Return'), ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton($transportstate_model->isNewRecord ? Module::t('modules/schooltransport/app', 'Forward State') : Module::t('modules/schooltransport/app', 'Update State'), ['class' => 'btn btn-primary']) ?>
    </div>
	
    <?php ActiveForm::end(); ?>

</div>