<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\NotFoundHttpException;

/* @var $this yii\web\View */
/* @var $model app\models\State */
if(!empty($model)){
$this->title = $model->state;
$this->params['breadcrumbs'][] = ['label' => 'States', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-view">

    <p>
    
        <?php echo Html::a('Update', ['update', 'id' => $model->state_id], ['class' => 'btn btn-primary']) ?>
        
        <?php echo Html::a('Delete', ['delete', 'id' => $model->state_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this state?',
                'method' => 'post',
            ],
        ]) ?>
        <?php echo Html::a('Back', ['index'], ['class' => 'btn btn-default']) ; ?>
    </p>
<div class="box box-default">
   <div class="box-body">
    <?php echo  DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'state_id',
            [                      // the country name of the model
            'label' => 'Country',
            'value' => $model->country->country,
             ],
            'state',
            'state_prefix',
             [  
             'attribute'=>'vat',          
            'label' => 'VAT',
            'value' => $model->vat.' %',
             ],
             [                      // the currency name of the model
            'label' => 'Status',
            'value' => ucfirst($model->status),
             ],
             [                      // the currency name of the model
            'label' => 'Signature Email ID',
            'value' => $model->signature_email_id,
             ],
             [                      // the currency name of the model
            'label' => 'Header Address',
            'value' => $model->header_address,
             ],
             [                      // the currency name of the model
            'label' => 'Header telephones',
            'value' => $model->header_telephones,
             ],
            //'created_at',
            //'updated_at',
        ],
    ]) ?>
    </div>
  </div>
</div>
<?php }else{
    throw new NotFoundHttpException('The requested page does not exist.');
    } ?>
