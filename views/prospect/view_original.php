<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Prospect */

$this->title = $model->customer_name;
$this->params['breadcrumbs'][] = ['label' => 'Prospects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


   <!--  <h1><?= Html::encode($this->title) ?></h1> -->

    <p>
        <?php echo  Html::a('Update', ['update', 'id' => $model->prospect_id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->prospect_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this prospect?',
                'method' => 'post',
            ],
        ]) ?>
         <?php echo Html::a('Back', ['prospect/index'], ['class' => 'btn btn-default']) ?>
    </p>

     <p align="right">
     <?php 
                    if(yii::$app->controller->action->id=='view'){
                            echo Html::a('<i class="fa fa-print"></i> Print', ['prospect/pdf','id'=>$model->prospect_id], [
                            'class'=>'btn btn-danger',  
                            'data-toggle'=>'tooltip', 
                            'title'=>'Will open the generated PDF file in a new window',
                            'target'=>'_blank'
                        ]);
                    }
                ?>
    </p>
    <div class="prospect-view">
<div class="box box-default">
  <div class="box-body">
        <?php echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'prospect_id',
                'customer_name',
                'person_incharge',
                'address:ntext',
                'mobile_no',
                'email:email',
                'current_isp',
                //'current_contract_end_date',
                [                      
                'attribute' => 'current_contract_end_date',
                //'label'=>'Currency',
                //'value' => $model->currency->currency,
                'value' => function($data)
                         {   
                            if($data->current_contract_end_date == null)
                            {
                              return '-' ;

                            }
                            else
                            {   
                                return $data->current_contract_end_date;
                            }
                         },
                 ],
                'current_package',
                //'current_isp_bill',
                 [                      
                'attribute' => 'current_isp_bill',
                'value' => function($data)
                         {   
                            if($data->current_isp_bill == null)
                            {
                              return '-' ;

                            }
                            else
                            {   
                                return $data->current_isp_bill;
                            }
                         },
                 ],

               
                 [                      
                'attribute' => 'fk_currency_id',
                'label'=>'Currency',
                //'value' => $model->currency->currency,
                'value' => function($data)
                         {   
                            if($data->fk_currency_id == null)
                            {
                              return '-' ;

                            }
                            else
                            {   
                                return $data->currency->currency;
                            }
                         },
                 ],
                'package_speed',
                 [                      
                'attribute' => 'fk_speed_id',
                'label'=>'Speed Type',
                'value' => $model->speed->speed_type,
                 ],
               // 'price_quote',
                  [                      
                'attribute' => 'price_quote',
                'value' => function($data)
                         {   
                            if($data->price_quote == null)
                            {
                              return '-' ;

                            }
                            else
                            {   
                                return $data->price_quote;
                            }
                         },
                 ],

                //'estimate_sign_up_date',
                  [                      
                'attribute' => 'estimate_sign_up_date',
                'value' => function($data)
                         {   
                            if($data->estimate_sign_up_date == null)
                            {
                              return '-' ;

                            }
                            else
                            {   
                                return $data->estimate_sign_up_date;
                            }
                         },
                 ],
                [  
                'attribute'=>'success_rate',          
                'label' => 'Success Rate',
                'value' => $model->success_rate.' %',
                 ],
                
                [  
                'attribute'=>'is_deal_closed',          
                'value' => ucfirst($model->is_deal_closed),
                 ],
                
                 [
                 'attribute'=>'fk_user_id',                      // the 
                 'label' => 'User Name',
                 'value' => $model->user->name,
                 ]
                //'is_deleted',
                //'created_at',
                //'updated_at',
            ],
        ]) ?>
       </div>
     </div>
</div>
