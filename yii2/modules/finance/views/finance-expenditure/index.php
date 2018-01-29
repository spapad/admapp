<?php

use app\modules\finance\Module;
use app\modules\finance\components\Money;
use app\modules\finance\models\FinanceSupplier;
use yii\grid\GridView;
use yii\helpers\Html;
use app\modules\finance\models\FinanceExpenditurestate;


/* @var $this yii\web\View */
/* @var $searchModel app\modules\finance\models\FinanceExpenditureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->params['breadcrumbs'][] = ['label' => Module::t('modules/finance/app', 'Expenditures Management'), 'url' => ['/finance/default']];
$this->title = Module::t('modules/finance/app', 'Expenditures');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="finance-expenditure-index">
	
	<?= $this->render('/default/infopanel');?>
	
    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render('/default/kaeslist', [
        'kaes' => $kaes,
        'btnLiteral' => Module::t('modules/finance/app', 'Create Expenditure'),
        'actionUrl' => '/index.php/finance/finance-expenditure/create',
	    'balances' => $balances,
    ]) ?> 
 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'suppl_id',
             'label' => Module::t('modules/finance/app', 'Supplier'),
             'format' => 'html',
             'value' => function ($model) {return FinanceSupplier::find()->where(['suppl_id' => $model['suppl_id']])->one()['suppl_name'];}
            ],
            ['attribute' => 'exp_amount', 
             'label' => Module::t('modules/finance/app', 'Amount'),
             'format' => 'currency',
             'value' => function ($model) {return Money::toCurrency($model['exp_amount']);}
            ],
            ['attribute' => 'fpa_value', 
             'label' => Module::t('modules/finance/app', 'VAT'),
             'format' => 'html',
             'value' => function ($model) {return Money::toPercentage($model['fpa_value']);}
            ],
            ['attribute' => 'exp_date', 
             'label' => Module::t('modules/finance/app', 'Created')],
            ['attribute' => 'Withdrawals', 'label' => Module::t('modules/finance/app', 'Assigned Withdrawals'),
             'format' => 'html',
                'value' => function($model) use ($expendwithdrawals) {
                $exp_withdrawals = $expendwithdrawals[$model['exp_id']]['WITHDRAWAL'];
                $count_withdrawals = count($exp_withdrawals);
                $retvalue = "<ul>";
                for($i = 0; $i < $count_withdrawals; $i++){
                    $retvalue .= "<li><strong><u>" . $exp_withdrawals[$i]['kaewithdr_decision'] . '</u></strong>' . 
                    '<br />' . Module::t('modules/finance/app', 'Assigned Amount') . ': ' .
                    Money::toCurrency($expendwithdrawals[$model['exp_id']]['EXPENDWITHDRAWAL'][$i], true);
                    $retvalue .= "</li>";
                }
                $retvalue .= "</ul>";
                return $retvalue;
             }
            ],
            ['attribute' => 'kae_id',
                'label' => Module::t('modules/finance/app', 'RCN'),
                'format' => 'html',
                'value' => function ($model) use ($expendwithdrawals) {
                                //return $expendwithdrawals[$model['exp_id']]['RELATEDKAE'];
                                return $model['kae_id'];
                            }
            ],
            ['attribute' => 'statescount', 
             'label' => Module::t('modules/finance/app', 'State'),
             'format' => 'html',
             'contentOptions' => ['class' => 'text-nowrap'],
             'value' => function($model) {
                            $state_commnents = array();
                            
                            $state_commnents[1] = Module::t('modules/finance/app', "Date"). ": " . ($tmp = FinanceExpenditurestate::findOne(['exp_id' => $model['exp_id'], 'state_id' => 1]))['expstate_date'] .  
                                                  " (" . $tmp['expstate_comment'] . ")";
                            $state_commnents[2] = Module::t('modules/finance/app', "Date"). ": " . ($tmp = FinanceExpenditurestate::findOne(['exp_id' => $model['exp_id'], 'state_id' => 2]))['expstate_date'] .
                                                  " (" . $tmp['expstate_comment'] . ")";
                            $state_commnents[3] = Module::t('modules/finance/app', "Date"). ": " . ($tmp = FinanceExpenditurestate::findOne(['exp_id' => $model['exp_id'], 'state_id' => 3]))['expstate_date'] .
                                                  " (" . $tmp['expstate_comment'] . ")";
                            $state_commnents[4] = Module::t('modules/finance/app', "Date"). ": " . ($tmp = FinanceExpenditurestate::findOne(['exp_id' => $model['exp_id'], 'state_id' => 4]))['expstate_date'] .
                                                  " (" . $tmp['expstate_comment'] . ")";
                            $retvalue = 'UNDEFINED STATE';
                            if($model['statescount'] == 1)
                                $retvalue = '<span class="glyphicon glyphicon-ok-sign" style="color:blue;" data-toggle="tooltip" data-html="true" title="' . $state_commnents[1] . '"></span>';
                            else if($model['statescount'] == 2)
                                $retvalue = '<span class="glyphicon glyphicon-ok-sign" style="color:blue;" data-toggle="tooltip" data-html="true" title="' . $state_commnents[1] . '"></span>
                                            &nbsp;<span class="glyphicon glyphicon-ok-sign" style="color:red; data-toggle="tooltip" data-html="true" title="' . $state_commnents[2] . '"></span>';
                            else if($model['statescount'] == 3)
                                $retvalue = '<span class="glyphicon glyphicon-ok-sign" style="color:blue;" data-toggle="tooltip" data-html="true" title="' . $state_commnents[1] . '"></span>
                                            &nbsp;<span class="glyphicon glyphicon-ok-sign" style="color:red; data-toggle="tooltip" data-html="true" title="' . $state_commnents[2] . '"></span>
                                            &nbsp;<span class="glyphicon glyphicon-ok-sign" style="color:orange;" data-toggle="tooltip" data-html="true" title="' . $state_commnents[3] . '"></span>';
                            else if($model['statescount'] == 4)
                                $retvalue = '<span class="glyphicon glyphicon-ok-sign" style="color:blue;" data-toggle="tooltip" data-html="true" title="' . $state_commnents[1] . '"></span>
                                            &nbsp;<span class="glyphicon glyphicon-ok-sign" style="color:red; data-toggle="tooltip" data-html="true" title="' . $state_commnents[2] . '"></span>
                                            &nbsp;<span class="glyphicon glyphicon-ok-sign" style="color:orange;" data-toggle="tooltip" data-html="true" title="' . $state_commnents[3] . '"></span>
                                            &nbsp;<span class="glyphicon glyphicon-ok-sign" style="color:green;" data-toggle="tooltip" data-html="true" title="' . $state_commnents[4] . '"></span>';                            
                            return $retvalue;                            
                        }
            ],
            [   'attribute' => 'invoice',
                'header' => '<span class="text-wrap">' . Module::t('modules/finance/app', 'Voucher<br />Actions') . '</span>',
                'format' => 'html',
                'value' => function ($model) use ($expendwithdrawals){
                $retvalue = "";
                if(is_null($expendwithdrawals[$model['exp_id']]['INVOICE']))
                    $retvalue = Html::a('<span class="glyphicon glyphicon-list-alt"></span>',
                        '/finance/finance-invoice/create?id=' . $model['exp_id'],
                        ['title' => Module::t('modules/finance/app',
                            'Create invoice for the expenditure.')]);
                        else {
                            $retvalue = Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                                '/finance/finance-invoice/view?expenditures_return=1&id=' . $expendwithdrawals[$model['exp_id']]['INVOICE'],
                                ['title' => Module::t('modules/finance/app',
                                    'View the invoice details for the expenditure.')]);
                                $retvalue .= "&nbsp;" . Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                    '/finance/finance-invoice/update?expenditures_return=1&id=' . $expendwithdrawals[$model['exp_id']]['INVOICE'],
                                    ['title' => Module::t('modules/finance/app',
                                        'Update the invoice details for the expenditure.')]);
                        }
                        $retvalue .= "";
                        return $retvalue;
                        
                },
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
            ['class' => 'yii\grid\ActionColumn',
             'header' => Module::t('modules/finance/app', 'Expenditure<br />Actions'),
             'contentOptions' => ['class' => 'text-nowrap'],
             'template' => '{backwardstate} {forwardstate} {delete}',
                'buttons' => [
                    'forwardstate' => function ($url, $model) {
                        if($model['statescount'] != 4){
                            return Html::a('<span class="glyphicon glyphicon-arrow-right"></span>', $url,
                                           ['title' => Module::t('modules/finance/app', 'Forward to next state')]);
                            }
                        },
                        'backwardstate' => function ($url, $model) {
                        if($model['statescount'] > 1){
                            return Html::a('<span class="glyphicon glyphicon-arrow-left"></span>', $url,
                                ['title' => Module::t('modules/finance/app', 'Backward to previous state'),
                                 'data'=>['confirm'=>Module::t('modules/finance/app', "Are you sure you want to change the state of the expenditure?"),
                                 'method' => "post"]
                                ]);
                            }
                        }
                    ],                    
                'urlCreator' => function ($action, $model) {
                    if ($action === 'delete') {
                        $url = '/finance/finance-expenditure/delete?id=' . $model['exp_id'];
                        return $url;
                    }
                    if ($action === 'backwardstate') {
                        $url ='/finance/finance-expenditure/backwardstate?id=' . $model['exp_id'];
                        return $url;
                    }
                    if ($action === 'forwardstate') {
                        $url ='/finance/finance-expenditure/forwardstate?id=' . $model['exp_id'];
                        return $url;
                    }

                }
            ],
       ],
    ]); ?>
</div>