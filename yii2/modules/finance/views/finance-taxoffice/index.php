<?php

use app\modules\finance\Module;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\finance\models\FinanceTaxofficeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->params['breadcrumbs'][] = ['label' => Module::t('modules/finance/app', 'Expenditures Management'), 'url' => ['/finance/default']];
$this->params['breadcrumbs'][] = ['label' => Module::t('modules/finance/app', 'Parameters'), 'url' => ['/finance/default/parameterize']];
$this->title = Module::t('modules/finance/app', 'Tax Offices');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="finance-taxoffice-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="text-align: right;">
        <?= Html::a(Module::t('modules/finance/app', 'Insert Tax Οffice'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'taxoffice_id', 'label' => Module::t('modules/finance/app', 'Code')],
            ['attribute' => 'taxoffice_name', 'label' => Module::t('modules/finance/app', 'Name')],
            ['class' => 'yii\grid\ActionColumn',
              'template' => '{update}&nbsp;{delete}'
            ],
        ],
    ]); ?>
</div>