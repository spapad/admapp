<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use admapp\Util\Html as admappHtml;

/* @var $this yii\web\View */
/* @var $model app\models\LeaveType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="leave-type-form">

    <?php
    $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-sm-6\">{input}</div>\n<div class=\"col-sm-4\">{error}</div>",
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                ],
    ]);
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php if (!$model->isNewRecord) : ?>
        <?= admappHtml::displayValueOfField($model, 'create_ts', 2, 6) ?>
        <?= admappHtml::displayValueOfField($model, 'update_ts', 2, 6) ?>
    <?php endif; ?>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <?= Html::submitButton($model->isNewRecord ? 'Δημιουργία' : 'Ενημέρωση στοιχείων', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Επιστροφή', ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
