<?php

use yii\bootstrap\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\SubstituteTeacher\models\Application */

$this->title = Yii::t('substituteteacher', 'Application') . " {$model->id}";
$this->params['breadcrumbs'][] = ['label' => Yii::t('substituteteacher', 'Applications'), 'url' => empty($url = Url::previous('applicationsindex')) ? ['index'] : $url];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="application-view">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <p>
        <?= Html::a(Yii::t('substituteteacher', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('substituteteacher', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'call_id',
                'value' => $model->call_id . ' (' . $model->call->title . ')'
            ],
            [
                'attribute' => 'teacher_board_id',
                'value' => $model->teacherBoard->teacher->name. ', ' . $model->teacherBoard->label
            ],
            [
                'label' => Yii::t('substituteteacher', 'Teacher status'),
                'attribute' => 'teacherBoard.teacher.status_label'
            ],
            'agreed_terms_ts:datetime',
            [
                'attribute' => 'state',
                'value' => $model->state_label,
                'format' => 'html'
            ],
            'reference:ntext',
            'deleted:boolean',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <?php 
    $provider = new ArrayDataProvider([
        'allModels' => $model->applicationPositions,
        // 'pagination' => [
        //     'pageSize' => 10,
        // ],
        // 'sort' => [
        //     'defaultOrder' => [
        //         'order' => SORT_DESC,
        //     ]
        // ],
    ]);
    ?>

    <?= GridView::widget([
        'dataProvider' => $provider,
        // 'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => [
                    'class' => 'text-center col-sm-1'
                ],
            ],
            [
                'attribute' => 'order',
                'contentOptions' => [
                    'class' => 'text-center col-sm-1'
                ],
            ],
            [
                'header' => Yii::t('substituteteacher', 'In group'),
                'value' => function ($m) {
                    return $m->callPosition ?
                        '<span class="label" style="color: #555; background-color: hsl('
                            . (($m->callPosition->group * 25 % 360) + 20) . ','
                            . (($m->callPosition->group == 0) ? '0%,90%' : '100%,50%') . ')">'
                            . (($m->callPosition->group == 0) ? Yii::t('substituteteacher', 'Sole position') : Yii::t('substituteteacher', 'Group {d}', ['d' => 1]))
                            . '</span>' :
                        null;
                },
                'contentOptions' => [
                    'class' => 'text-center col-sm-1'
                ],
                'format' => 'html'
            ],
            'callPosition.position.title',
            'deleted:boolean',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{place}',
                'buttons' => [
                    'place' => function ($url, $appposition_model, $key) use ($model) {
                        return Html::a('<span class="glyphicon glyphicon-check"></span>', '#', [
                            'title' => Yii::t('substituteteacher', 'Place teacher to this position or group.'),
                            'data' => [
                                'toggle' => 'modal',
                                'target' => '#placement-options-modal',
                                'call_position_id' => $appposition_model->call_position_id, // this ought to be the same as $model->call_id
                            ],
                            'class' => 'btn btn-sm btn-primary'
                        ]);
                    },
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ]
            ],
        ],
    ]); ?>

</div>


<?php
Modal::begin([
    'id' => 'placement-options-modal',
    'header' => '<h3>' . Yii::t('substituteteacher', 'Placement information') . '</h3>',
]);

$form = ActiveForm::begin([
        'id' => 'placement-options-form',
        'method' => 'post',
        'action' => [
            'placement/place',
            'application_id' => $model->id,
        ],
        'enableClientValidation' => false,
        'options' => ['class' => 'form-horizontal'],
    ]);

?>
<div class="container-fluid">
    <?= Html::hiddenInput('call_position_id', 'void', ['id' => 'modal_input_call_position_id']) ?>
    <div class="form-group">
        <label for="modal_input_comments" class="col-sm-4 control-label">
            <?php echo Yii::t('substituteteacher', 'Placements'); ?>
        </label>
        <div class="col-sm-8">
            <?= Html::dropDownList('placement_id', null, \app\modules\SubstituteTeacher\models\Placement::selectables('id', 'label', null, function ($aq) use ($model) { return $aq->andWhere(['call_id' => $model->call_id])->orderBy(['date' => SORT_DESC]); }), ['class' => 'form-control']) ?>
            <p class="hint">
                <?= Yii::t('substituteteacher', 'Only placements that match the application call are presented.') ?>
            </p>
        </div>
    </div>
    <div class="form-group text-right">
        <?=
            Html::button(Yii::t('substituteteacher', 'Cancel'), [
                'class' => 'btn btn-danger',
                'data' => [
                    'toggle' => 'modal',
                    'target' => '#placement-options-modal',
                ],
            ])

            ?>
        <?=
            Html::submitButton(Yii::t('substituteteacher', 'Do placement'), [
                'class' => 'btn btn-primary',
            ])

            ?>
    </div>
</div>
<?php
ActiveForm::end();
Modal::end();

$modal_js = "$('#placement-options-modal').on('show.bs.modal', function (e) {
    var button = $(e.relatedTarget);
    var modal = $(this);
    modal.find('#modal_input_call_position_id').val(button.data('call_position_id'))
})";
$this->registerJs($modal_js);
