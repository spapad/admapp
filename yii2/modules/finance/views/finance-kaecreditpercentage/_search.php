<?php

use app\modules\finance\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\finance\models\FinanceKaecreditpercentageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="finance-kaecreditpercentage-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'kaeperc_id') ?>

    <?= $form->field($model, 'kaeperc_percentage') ?>

    <?= $form->field($model, 'kaeperc_date') ?>

    <?= $form->field($model, 'kaeperc_decision') ?>

    <?= $form->field($model, 'kaecredit_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('modules/finance/app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Module::t('modules/finance/app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
