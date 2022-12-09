<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Linkcustomepackage */
if(!empty($model)){
$this->title = $model->customer->name;
$this->params['breadcrumbs'][] = ['label' => 'Disconnection Report', 'url' => ['customerpackage/disconnectreport']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="linkcustomepackage-view">

  <!--   <h1><?= Html::encode($this->title) ?></h1> -->
<p>
        <?php 
            if(yii::$app->controller->action->id=='disconnectview'){

            echo Html::a('Back',['disconnectreport'], ['class' => 'btn btn-default']); 

        }
        
        if(Yii::$app->user->identity->fk_role_id=='8')
        {
          $attributes = [
                    //'cust_pck_id',
                 [                      // the currency name of the model
                    'label' => 'Customer ID',
                    'value' => $model->customer->solnet_customer_id,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Customer Name',
                    'value' => $model->customer->name,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Mobile Number',
                    'value' => $model->customer->mobile_no,
                    ],
                     [                      // the currency name of the model
                    'label' => 'Address',
                    'value' => $model->customer->billing_address,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Package title',
                    'value' => $model->package->package_title,
                    ],
                    
                    'package_speed',
                    [                      // the currency name of the model
                    'label' => 'Package title',
                    'value' => $model->speed->speed_type,
                    ],
                    [
                    'attribute'=>'disconnection_date',
                     'value'=>function($data){
                         return date("d-m-Y",strtotime($data->disconnection_date));
                    },
                    ],
                    [
                    'label' => 'Reason for disconnection',
                    'value' => $model->reason_for_disconnection
                    ],
                ];
        }
        else
        {
           $attributes = [
                    //'cust_pck_id',
                 [                      // the currency name of the model
                    'label' => 'Customer ID',
                    'value' => $model->customer->solnet_customer_id,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Customer Name',
                    'value' => $model->customer->name,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Mobile Number',
                    'value' => $model->customer->mobile_no,
                    ],
                     [                      // the currency name of the model
                    'label' => 'Address',
                    'value' => $model->customer->billing_address,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Package title',
                    'value' => $model->package->package_title,
                    ],
                    
                    'package_speed',
                    [                      // the currency name of the model
                    'label' => 'Package title',
                    'value' => $model->speed->speed_type,
                    ],
                    [
                        'label' => 'Price',
                        'value' =>  $model->currency->currency." ".number_format($model->package_price,2),
                    ],
                    [
                    'attribute'=>'disconnection_date',
                     'value'=>function($data){
                         return date("d-m-Y",strtotime($data->disconnection_date));
                    },
                    ],
                    [
                    'label' => 'Reason for disconnection',
                    'value' => $model->reason_for_disconnection
                    ],
                   
                ];
        }
         ?>
</p>
    <div class="box box-default">
        <div class="box-body">  
        <h4>Disconnection Report</h4>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => $attributes
            ]) ?>
        </div>
    </div>

</div>
<?php }else{
    if ( Yii::$app->session->hasFlash('notFoundMessage')):?>
            <div class="alert alert-danger"><?php echo Yii::$app->session->getFlash('notFoundMessage');?></div>
 <?php endif;
  echo Html::a('Back ', ['index'], ['class' => 'btn btn-default']) ;
}
