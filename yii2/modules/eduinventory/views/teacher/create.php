<?php

use app\modules\eduinventory\EducationInventoryModule;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Teacher */

$this->title = Yii::t('app', 'Create Teacher');
$this->params['breadcrumbs'][] = ['label' => EducationInventoryModule::t('modules/eduinventory/app', 'Educational Data'), 'url' => ['/eduinventory']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'schools' => $schools,
        'specialisations' => $specialisations
    ]) ?>

</div>
