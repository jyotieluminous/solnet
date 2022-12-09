<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/* @var $this yii\web\View */
/* @var $model app\models\Bank */
if(!empty($model)){
$this->title = $model->bank_name;
$this->params['breadcrumbs'][] = ['label' => 'Banks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-view">
    
    <p>
  
        <?php echo Html::a('Update', ['update', 'id' => $model->bank_id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->bank_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this bank?',
                'method' => 'post',
            ],
        ]) ?>

        <?php echo Html::a('Back',['index'], ['class' => 'btn btn-default']) ?>
    </p>

  <div class="box box-default">
    <div class="box-body">
        <?php echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'bank_id',
                'bank_name',
                'account_no',
                'account_name',
                [                      // the currency name of the model
                'label' => 'Currency',
                'value' => $model->currency->currency,
                 ],
                'bank_branch',
                [                     
                'label' => 'Status',
                'value' => ucfirst($model->status),
                 ],
               // 'is_deleted',
               // 'created_at',
                //'updated_at',
            ],
        ]) ?>
    </div>
  </div>
  <?php }else{
      throw new NotFoundHttpException('The requested page does not exist.');} ?>
</div>
