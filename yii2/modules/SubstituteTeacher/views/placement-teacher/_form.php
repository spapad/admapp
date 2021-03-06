<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\SubstituteTeacher\models\TeacherBoard;
use app\modules\SubstituteTeacher\models\Position;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use app\modules\SubstituteTeacher\models\Placement;
use kartik\datecontrol\DateControl;
use dosamigos\switchinput\SwitchBox;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\modules\SubstituteTeacher\models\Placement */
/* @var $form yii\widgets\ActiveForm */

$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-serial-number").each(function(index) {
        jQuery(this).html("" + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-serial-number").each(function(index) {
        jQuery(this).html("" + (index + 1))
    });
});
';

$this->registerJs($js);
$firstModelPlacementPosition = reset($modelsPlacementPositions);
?>

<div class="placement-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?php if ($model->isNewRecord): ?>
    <?php
    $url = Url::to(['teacher-board/choose']);
    $initial_text = empty($model->teacher_board_id) ? '' : $model->teacherBoard->teacher->name;
 
    echo $form->field($model, 'teacher_board_id')->widget(Select2::classname(), [
        'initValueText' => $initial_text,
        'options' => [
            'placeholder' => Yii::t('substituteteacher', 'Search for teacher...')
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Αναμονή για αποτελέσματα...'; }"),
                'searching' => new JsExpression("function () { return 'Αναζήτηση...'; }"),
                'noResults' => new JsExpression("function () { return 'Κανένα αποτέλεσμα.'; }"),
            ],
            'ajax' => [
                'url' => $url,
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {term:params.term}; }'),
                'delay' => 500
            ],
            // 'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function (res) { return res.text; }'),
            'templateSelection' => new JsExpression('function (res) { return res.text; }'),
        ],
    ])->hint(Yii::t('substituteteacher', 'Type three letter to search surname or name; if searching for both, use surname first.'));
    // $form->field($model, 'teacher_board_id')->widget(Select2::classname(), [
    //     'data' => TeacherBoard::selectablesWithTeacherInfo(),
    //     'options' => ['placeholder' => Yii::t('substituteteacher', 'Choose...')],
    //     'pluginOptions' => [
    //         'multiple' => false,
    //         'allowClear' => false
    //     ],
    // ]);
    ?>
    <?php else: ?>
    <h3>
        <?php echo $model->teacherBoard->teacher->name. ', ' . $model->teacherBoard->label; ?>
    </h3>
    <div class="row">
        <div class="form-group">
            <label class="col-sm-2 control-label">
                <?php echo Yii::t('substituteteacher', 'Altered'); ?>
            </label>
            <div class="col-sm-10">
                <p class="form-control-static">
                    <?php echo Yii::$app->formatter->asBoolean($model->altered); ?>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?=
    $form->field($model, 'placement_id')->widget(Select2::classname(), [
        'data' => Placement::defaultSelectables(),
        'options' => ['placeholder' => Yii::t('substituteteacher', 'Choose...')],
        'pluginOptions' => [
            'multiple' => false,
            'allowClear' => false
        ],
    ]);
    ?>

    <?php if (!$model->isNewRecord): ?>
    <div class="row">
        <div class="col-sm-6">
            <strong><?php echo Yii::t('substituteteacher', 'Created At'); ?></strong> <?php echo $model->created_at; ?>
        </div>
        <div class="col-sm-6">
            <strong><?php echo Yii::t('substituteteacher', 'Updated At'); ?></strong> <?php echo $model->updated_at; ?>
        </div>
    </div>
    <?php endif; ?>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'contract_start_date')->widget(DateControl::classname(), [
                    'type' => DateControl::FORMAT_DATE
                ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'contract_end_date')->widget(DateControl::classname(), [
                    'type' => DateControl::FORMAT_DATE
                ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'service_start_date')->widget(DateControl::classname(), [
                    'type' => DateControl::FORMAT_DATE
                ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'service_end_date')->widget(DateControl::classname(), [
                    'type' => DateControl::FORMAT_DATE
                ]);
            ?>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'altered')->widget(SwitchBox::className(), [
                'options' => [
                    'label' => '',
                ],
                'clientOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'onText' => Yii::t('substituteteacher', 'YES'),
                    'offText' => Yii::t('substituteteacher', 'No'),
                ]
            ]);
            ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'cancelled')->widget(SwitchBox::className(), [
                'options' => [
                    'label' => '',
                ],
                'clientOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'onText' => Yii::t('substituteteacher', 'YES'),
                    'offText' => Yii::t('substituteteacher', 'No'),
                ]
            ]);
            ?>
            <?= $form->field($model, 'cancelled_ada')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'dismissed')->widget(SwitchBox::className(), [
                'options' => [
                    'label' => '',
                ],
                'clientOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'onText' => Yii::t('substituteteacher', 'YES'),
                    'offText' => Yii::t('substituteteacher', 'No'),
                ]
            ]);
            ?>
            <?= $form->field($model, 'dismissed_ada')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php 
    DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper',
        'widgetBody' => '.container-items',
        'widgetItem' => '.item',
        'min' => 1,
        'insertButton' => '.add-item',
        'deleteButton' => '.remove-item',
        'model' => $firstModelPlacementPosition,
        'formId' => 'dynamic-form',
        'formFields' => [
            'position_id',
            'teachers_count',
            'hours_count'
        ],
    ]);
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo Yii::t('substituteteacher', 'Placement positions'); ?>
            <button type="button" class="add-item btn btn-success btn-xs">
                <span class="glyphicon glyphicon-plus"></span>
                <?php echo Yii::t('substituteteacher', 'Add new position'); ?>
            </button>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <!-- widgetContainer -->
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="col-xs-1">#</th>
                        <th>
                            <?php echo $firstModelPlacementPosition->getAttributeLabel('position_id'); ?>
                        </th>
                        <th>
                            <?php echo $firstModelPlacementPosition->getAttributeLabel('teachers_count'); ?>
                        </th>
                        <th>
                            <?php echo $firstModelPlacementPosition->getAttributeLabel('hours_count'); ?>
                        </th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="container-items">
                    <?php foreach ($modelsPlacementPositions as $index => $modelPlacementPosition): ?>
                    <tr class="item">
                        <td>
                            <span class="badge panel-serial-number">
                                <?php echo $index + 1; ?>
                            </span>
                        </td>
                        <td>
                            <?php
                                // necessary for update action.
                                if (!$modelPlacementPosition->isNewRecord) {
                                    echo Html::activeHiddenInput($modelPlacementPosition, "[{$index}]id");
                                }
                            ?>
                            <?= $form->field($modelPlacementPosition, "[{$index}]position_id")->dropDownList(Position::defaultSelectables('id', 'title', 'operation.label'), ['prompt' => Yii::t('substituteteacher', 'Choose...')])->label(false) ?>
                            <?php 
                                $teacher_errors = $modelPlacementPosition->getErrors('position_id');
                                if (!empty($teacher_errors)) :
                            ?>
                            <div class="text-danger">
                                <?= implode(', ', $teacher_errors) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $form->field($modelPlacementPosition, "[{$index}]teachers_count")->textInput(['type' => 'number', 'min' => 0])->label(false) ?>
                        </td>
                        <td class="col-sm-2">
                            <?= $form->field($modelPlacementPosition, "[{$index}]hours_count")->textInput(['type' => 'number', 'min' => 0])->label(false) ?>
                        </td>
                        <td class="col-sm-1 text-center">
                            <button type="button" class="remove-item btn btn-danger btn-sm">
                                <span class="glyphicon glyphicon-minus"></span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php DynamicFormWidget::end(); ?>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('substituteteacher', 'Place teacher') : Yii::t('substituteteacher', 'Update teacher placement'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>