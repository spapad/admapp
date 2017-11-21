<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenditures Management'), 'url' => ['/finance/default']];
$this->title = 'Διαχείριση Οικονομικού Έτους';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1>Διαχείριση Οικονομικού Έτους</h1>

<div class="body-content">

    <div class="row">
    	<div class="col-lg-4">
            <h3>Τρέχον έτος</h3>
            <p>Καθορισμός τρέχοντος έτους</p>
            <p><?= Html::a('Αλλαγή Έτους', Url::to(['/finance/finance-kae']), ['class' => 'btn btn-primary', 'data-method' => 'post']) ?></p>            
        </div>
		<div class="col-lg-4">
            <h3>Διαχείριση ΚΑΕ</h3>
            <p>Δημιουργία νέου ΚΑΕ, επεξεργασία, διαγραφή</p>
            <p><?= Html::a('Προβολή KAE', Url::to(['/finance/finance-kae']), ['class' => 'btn btn-primary', 'data-method' => 'post']) ?></p>            
        </div>
        <div class="col-lg-4">
            <h3>Διαχείριση Κρατήσεων</h3>
            <p>Δημιουργία νέας Κράτησης, επεξεργασία, διαγραφή</p>
        </div>
    </div>
</div>
