<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\modules\Email\components\EmailButtonWidget;
use app\models\LeavePrint;

/* @var $this yii\web\View */
/* @var $model app\models\Leave */

$this->title = $model->information;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Leaves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->information, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Print');

$js = <<<EOJS
$(function () { 
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]',
        html: true
    });
});
EOJS;
$this->registerJs($js, $this::POS_READY);
?>

<?php
if ($model->deleted) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-danger',
        ],
        'body' => Yii::t('app', 'This leave is marked as deleted.'),
    ]);
}
?>
<div class="leave-view">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="row">
        <div class="col-md-8">
            <p>
                <?= Html::a(Yii::t('app', 'Return to view'), ['view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Download'), ['download', 'id' => $model->id], [
                    'class' => 'btn btn-success',
                    'data' => [
                        'method' => 'post',
                    ],
                ])
                ?>
                <?= Html::a(Yii::t('app', 'Print again'), ['reprint', 'id' => $model->id], [
                    'class' => 'btn btn-default',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to print this leave?'),
                        'method' => 'post',
                    ],
                ])
                ?>
                <?= empty($filename) ? '' : Html::a(Yii::t('app', 'Send e-mail'), ['email', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => Yii::t('app', 'The leave is sent automatically to all recipients concerned (employees - organic and serve services).') . ' ' . Yii::t('app', 'Are you sure you want to e-mail this leave?'),
                        'method' => 'post',
                    ],
                    'data-toggle' => 'tooltip',
                    'title' => 'Θα αποσταλεί το <strong>αρχείο που παράγει η εφαρμογή</strong>'
                ])
                ?>
            </p>
        </div>
        <div class="col-md-4 text-right">
            <?= empty($filename) ? '' : EmailButtonWidget::widget([
                'redirect_route' => [
                    '/leave/print', 'id' => $model->id
                ],
                'template' => 'leave.mail.main',
                'template_data' => [
                    '{DECISION_PROTOCOL}' => $model->decision_protocol,
                    '{DECISION_DATE}' => Yii::$app->formatter->asDate($model->decision_protocol_date),
                    '{LEAVE_PERSON}' => Yii::$app->params['leavePerson'],
                    '{LEAVE_PHONE}' => Yii::$app->params['leavePhone'],
                    '{LEAVE_FAX}' => Yii::$app->params['leaveFax'],
                    '{LEAVE_TYPE}' => mb_strtolower($model->typeObj->name),
                ],
                'files' => [
                    // LeavePrint::path($filename),
                ],
                'to' => $emails, // [ $model->employeeObj->email ],
                'cc' => [
                    'spapad@outlook.com'
                ],
                'label' => 'Αποστολή',
                'tooltip' => 'Δεν Θα αποσταλεί το αρχείο που παράγει η εφαρμογή. Θα σταλεί <strong>το αρχείο που θα επιλέξετε</strong>.',
                'enable_upload' => true
            ])
            ?>
        </div>
    </div>

    <?php if ($filename != null) : ?>
    <div class="alert alert-info" role="alert">Το αρχείο εκτύπωσης της άδειας είναι διαθέσιμο για μεταφόρτωση ή αποστολή.</div>
    <?php else : ?>
    <div class="alert alert-danger" role="alert">Το αρχείο εκτύπωσης της άδειας δεν είναι διαθέσιμο. Προσπαθήστε να το εκτυπώσετε ξανά.</div>
    <?php endif; ?>

    <?php
        $leavePrintDataProvider = new ArrayDataProvider([
                'allModels' => $model->leavePrints,
                'pagination' => [
                    'pagesize' => 10,
                ],
                'sort' => [
                    'attributes' => [
                    'filename',
                    'create_ts',
                    'send_ts',
                    'to_emails',
                    ],
                ]
            ]);
    ?>
    <?php Pjax::begin(); ?>
    <?=
        GridView::widget([
            'dataProvider' => $leavePrintDataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['label' => Yii::t('app', 'Filename'),
                    'attribute' => 'filename'],
                ['label' => Yii::t('app', 'Created At'),
                    'attribute' => 'create_ts'],
                ['label' => Yii::t('app', 'Sent at'),
                    'attribute' => 'send_ts'],
                ['label' => Yii::t('app', 'Email recipients'),
                    'attribute' => 'to_emails',
                    'value' => function ($m) {
                        if (!empty($m->to_emails)) {
                            return strtr($m->to_emails, ",", " ");
                        } else {
                            return null;
                        }
                    }
                ],
            ],
        ]);
    ?>
    <?php Pjax::end(); ?>
</div>