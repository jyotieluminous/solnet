<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

?>
<div class="reminder-view">
  <div class="box box-default">
    <div class="box-body">
    <h3>Reminder Invoice</h3>
  <?php 
         echo DetailView::widget([
            'model' => $model,
            'attributes' => [

                  [
                    'label' => 'Customer ID',
                    'value' => $model->customer->solnet_customer_id,
                  ],
                
                  [                      
                    'label' => 'Customer Name',
                    'value' => $model->customer->name,
                  ],

                  [                      
                    'label' => 'Package Title',
                    'value' => $model->linkcustomepackage->package->package_title,
                  ],

                  [                      
                    'label' => 'Package Speed',
                    'value' =>$model->linkcustomepackage->package_speed.' '.$model->linkcustomepackage->speed->speed_type,
                  ],

                  [                      
                    'label' => 'Invoice Number',
                    'value' => $model->invoice_number,
                  ],

                  [                      
                    'label' => 'Invoice Date',
                    'value'=>function($data){
                      return date('m-d-Y',strtotime($data->invoice_date));
                      },
                  ],

                  [                      
                    'label' => 'Total Invoice Amount',
                    'value'=> $model->linkcustomepackage->currency->currency." ".number_format($model->total_invoice_amount,2)
                  ],

                   [                      
                    'label' => 'Pending Amount',
                    'value'=> $model->linkcustomepackage->currency->currency." ".number_format($model->pending_amount,2)
                  ],

                  [                      
                    'label' => 'Due Date',
                    'value'=>function($data){
                      return date('m-d-Y',strtotime($data->due_date));
                      },
                  ],

                  [
                    'label'=>'Payment Term',
                    'value'=>function($data){
                      if(!empty($data->linkcustomepackage->payment_term)){
                          return 'Net '.$data->linkcustomepackage->payment_term.' Days';
                         }
                    else{
                      return '--';
                    }
                    }
                  ],
                
                  [                     
                  'label' => 'Status',
                  'value' => ucfirst($model->status),
                   ],
                 
            ],
        ]);
         ?>
    </div>
  </div>
</div>
