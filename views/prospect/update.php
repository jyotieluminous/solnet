<?php

use yii\helpers\Html;
use yii\web\NotFoundHttpException;


/* @var $this yii\web\View */
/* @var $model app\models\Prospect */  
if(!empty($model)){
$this->title = 'Update Prospect: ' . $model->customer_name;
$this->params['breadcrumbs'][] = ['label' => 'Prospects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->customer_name, 'url' => ['view', 'id' => $model->prospect_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prospect-update">

      <?php echo $this->render('_form', [
        'model' => $model,'statesList'=>$statesList
    ]);
  }else{
    	  throw new NotFoundHttpException('The requested page does not exist.');

    	} ?>

</div>
