<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Employee */

$this->title = $model->surname . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Εργαζόμενοι', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Μεταβολή', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Διαγραφή', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            /*[
              'label' => 'A/A',
              'attribute' => 'id',
            ],*/
            [
              'label' => 'Κατάσταση',
              'attribute' => 'status0.name',
            ],
            'name',
            'surname',
            'fathersname',
            'mothersname',
            'tax_identification_number',
            'email:email',
            'telephone',
            'address',
            'identity_number',
            'social_security_number',
            [
              'label' => 'Ειδικότητα',
              'attribute' => 'specialisation0.code',
            ],
            'identification_number',
            'appointment_fek',
            [
              'label' => 'Ημ/νία διορισμού',
              'attribute' => function ($data) { return \Yii::$app->formatter->asDate($data['appointment_date']); }
            ],
            [
              'label' => 'Οργανική',
              'attribute' => 'serviceOrganic.name'
            ],
            [
              'label' => 'Υπηρέτηση',
              'attribute' => 'serviceServe.name'
            ],
            [
              'label' => 'Θέση',
              'attribute' => 'position0.name'
            ],
            [
              'label' => 'Βαθμός',
              'attribute' => 'rank0'
            ],
            //'rank0',
            [
              'label' => 'Ημ/νία απόκτησης Βαθμού',
              'attribute' => function ($data) { return \Yii::$app->formatter->asDate($data['rank_date']); }
            ],
            'pay_scale',
            [
              'label' => 'Ημ/νία απόκτησης κλιμακίου',
              'attribute' => function ($data) { return \Yii::$app->formatter->asDate($data['pay_scale_date']); }
            ],
            'service_adoption',
            [
              'label' => 'Ημ/νία ανάληψης υπηρεσίας',
              'attribute' => function ($data) { return \Yii::$app->formatter->asDate($data['service_adoption_date']); }
            ],
            'master_degree',
            'doctorate_degree',
            'work_experience',
            'comments:ntext',
            [
              'label' => 'Ημ/νία Δημιουργίας',
              'attribute' => function ($data) { return \Yii::$app->formatter->asDateTime($data['create_ts']); }
            ],
            [
              'label' => 'Ημ/νία Μεταβολής',
              'attribute' => function ($data) { return \Yii::$app->formatter->asDateTime($data['update_ts']); }
            ]
        ],
    ]) ?>

</div>
