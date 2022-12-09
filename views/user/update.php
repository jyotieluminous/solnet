<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = 'Update Users: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="users-update">

           <?= $this->render('_form', [
        'model' => $model,
		'roleList'=>$roleList,
		'statesList'=>$statesList,
		'arrStateData'=>$arrStateData
    ]) ?>

</div>
