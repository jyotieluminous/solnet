<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Bank;

/* @var $this yii\web\View */
/* @var $model app\models\Bankdeposit */

$this->title = 'Bank Deposit Details';
$this->params['breadcrumbs'][] = ['label' => 'Bankdeposits', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Bank Deposite Details';
?>
<div class="bankdeposit-view">
    <p>
     <?php 
            if(yii::$app->controller->action->id=='view'){

         echo Html::a('Update', ['update', 'id' => $model->bank_deposit_id], ['class' => 'btn btn-primary']); echo "&nbsp";
        /* echo Html::a('Delete', ['delete', 'id' => $model->bank_deposit_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this deposit?',
                'method' => 'post',
            ],
        ]);echo "&nbsp";*/
         echo Html::a('Back',['bankdeposite/index'], ['class' => 'btn btn-default']);
     }
       if(yii::$app->controller->action->id=='print'){
        echo ' <h3>Bank Deposit Details </h3>';
       }

      ?>
    </p>
<div class="box box-default">
  <div class="box-body">
        <?php echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'bank_deposit_id',
                ['attribute'=>'deposit_date',
                 'value' => date("d-m-Y ",  strtotime($model->deposit_date))
                ],
               // 'amount',
                [                      // the currency name of the model
                'label' => 'Customer Name',
                'value' => $model->customer->name,
                ],
                [                      // the currency name of the model
                'label' => 'Invoice Number',
                'value' => $model->customerinvoice->invoice_number,
                ],
				[
					'label' => 'Amount',
					'value' => function($data){
						return number_format($data->amount,2);
					},
				],
                [                      // the currency name of the model
                'label' => 'Currency',
                'value' => $model->currency->currency,
                ],
                
                [ 
                    'attribute'=>'deposit_type',
                    'value'=>function($data){
                        return ucfirst($data->deposit_type);
                    }
                ],

                [
                 'attribute' => 'bank', 
                 'value' => function($data)
                 {   
                    if($data->is_solnet_bank == '1')
                    {
                         $arrBankName= Bank::find()->where(['bank_id'=>$data->fk_bank_id])->select('bank_name')->one();
                        
                           return $arrBankName->bank_name ;

                    }
                    else
                    {   
                        return $data->bank;
                    }
                 },
                ],
                          
             
                [
                 'attribute' => 'account_no', 
                 'value' => function($data)
                 {   
                    if($data->is_solnet_bank == '1')
                    {
                         $arrBankName= Bank::find()->where(['bank_id'=>$data->fk_bank_id])->select('account_no')->one();
                        
                           return $arrBankName->account_no ;

                    }
                    else
                    {   
                        return $data->account_no;
                    }
                 },
                ],
                
                'description:ntext',
                /*[                      // the currency name of the model
                'label' => 'User',
                'value' =>Yii::$app->user->identity->name,
                ],*/
            
                [
                 'attribute' => 'is_solnet_bank', 
                 'value' => function($data)
                 {   
                    if($data->is_solnet_bank == '1')
                    {
                        return 'Yes' ;

                    }
                    else
                    {   
                        return 'No';
                    }
                 },
                ],
                /*[
                'attribute'=>'fk_user_id', 
                'label'=>'User Name',
                'value'=>$model->user->name,
                ],need to ask mam*/
                //'is_deleted',
                //'created_at',
                //'updated_at',
            ],
        ]) ?>
     
    </div>
  </div>
</div>
