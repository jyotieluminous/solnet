<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Package */
if(!empty($model)){
$this->title = 'Update Package: ' . $model->package_title;
$this->params['breadcrumbs'][] = ['label' => 'Packages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->package_title, 'url' => ['view', 'id' => $model->package_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="package-update">

   <!--  <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,'flagShowStatus'=>$flagShowStatus
    ]); 
}else{
   throw new NotFoundHttpException('The requested page does not exist.');
} ?>
</div>
