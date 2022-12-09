<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customerpayment */
$this->title = $model->customer->name;
$this->params['breadcrumbs'][] = ['label' => 'Payment Collection Report', 'url' => ['report/paymentreport']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="customerpayment-view">

   <!--  <h1><?= Html::encode($this->title) ?></h1> -->

        <p>
            
            <?php 
             if(yii::$app->controller->action->id=='paymentview'){
                 echo Html::a('Back', ['paymentreport'], ['class' => 'btn btn-default']);
            } 
            if(yii::$app->controller->action->id=='paymentprint'){
                echo '<h3>Payment Collection details</h3>';
            }
            ?>
        </p>
    <div class="box box-default">
        <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
               
                [
                    'label'=>'Customer Id',
                    'value'=>$model->customer->solnet_customer_id
                ],

                [
                    'label'=>'Customer Name',
                    'value'=>$model->customer->name
                ],
                [
                    'label'=>'Invoice Number',
                    'value'=>$model->invoice->invoice_number
                ],
               
                
                'payment_method',
                //'cheque_no',
                [
                'attribute'=> 'cheque_no',
                 'value'=>function($data){
                    if(empty($data->cheque_no)){
                        return '--';
                    }else{
                        return $data->cheque_no;
                    }

                 }
                ],
				[
					'label' => 'Amount Paid',
					'value' => function($data){
						return number_format($data->amount_paid,2);
					},
				],
               
                [
                 'label'=>'payment_date',
                 'value'=> date("d-m-Y ",  strtotime($model->payment_date)),
                ],
                'reciept_no',
                'comment:ntext',
                [
                    'label'=>'Remark',
                    'value'=>function($data){
                                $remark=array();
                                if(!empty($data->discount)){
                                    $remark[].='Discount';
                                }
                                if(!empty($data->deduct_tax)){
                                    $remark[].='Tax';
                                }
                                if(!empty($data->bank_admin)){
                                    $remark[].='Bank Admin';
                                }
                                
                               return implode(', ', $remark);
                        
                                }
                ],
               // 'created_at',
                //'updated_at',
            ],
        ]) ?>
    </div>
  </div>
</div>


