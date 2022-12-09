<?php

use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/* @var $this yii\web\View */
/* @var $model app\models\Bank */
if(!empty($model)){
$this->title = 'Update Bank: ' . $model->bank_name;
$this->params['breadcrumbs'][] = ['label' => 'Banks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bank_name, 'url' => ['view', 'id' => $model->bank_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bank-update">

  <!--   <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,'flagShowStatus'=>$flagShowStatus
    ]);
}else{
    	
     throw new NotFoundHttpException('The requested page does not exist.');
   }
 ?>

</div>
