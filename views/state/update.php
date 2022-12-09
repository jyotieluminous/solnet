<?php

use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/* @var $this yii\web\View */
/* @var $model app\models\State */
if(!empty($model)){
$this->title = 'Update State: ' . $model->state;
$this->params['breadcrumbs'][] = ['label' => 'States', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->state, 'url' => ['view', 'id' => $model->state_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="state-update">
<!-- 
    <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,'flagShowStatus'=>$flagShowStatus
    ]);
}else{
    	
   throw new NotFoundHttpException('The requested page does not exist.');

} ?>

</div>
