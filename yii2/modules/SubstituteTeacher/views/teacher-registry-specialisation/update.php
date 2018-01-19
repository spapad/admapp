<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\SubstituteTeacher\models\TeacherRegistrySpecialisation */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Teacher Registry Specialisation',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Registry Specialisations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="teacher-registry-specialisation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
