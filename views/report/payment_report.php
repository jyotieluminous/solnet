<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\export\ExportMenu;
use app\models\Currency;
use app\models\Customerpayment;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Collection Report';
$this->params['breadcrumbs'][] = $this->title;

if(isset($_GET['start_date']) && isset($_GET['end_date']))
{
 $strStartDate = $_GET['start_date'];
 $strEndDate = $_GET['end_date'];
}
else{
    $strStartDate = date('Y-1-m');
    $strEndDate	  = date('Y-t-m');
}
//echo "<pre>";
//print_r($dataProvider->getModels());die;
$gridColumns=[
				['class' => 'yii\grid\SerialColumn'],
				
			
				[
					'label'=>'Date Received',
					'attribute'=>'payment_date',
                	'value'=> function($data){
                	 	return date("d-m-Y",  strtotime($data->payment_date));},
                
					'filter' => DatePicker::widget([
                    'name' => 'CustomerpaymentSearch[payment_date]',
                    'template' => '{addon}{input}',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy'
                        ]
                ])
				],

				[
					'attribute'=>'solnet_customer_id',
					'value'=>'customer.solnet_customer_id'
				],
				[
					/*'label'=>'Name',
					'value'=>'customer.name'*/
					'attribute'=>'name',
					'value'=>'customer.name'
				],
				[
					'attribute'=>'billing_address',
					'value'=>'customer.billing_address'
				],
				[
					'attribute'=>'state',
					'value'=>function($data)
					{
						return $data->customer->state->state;
					},
					
				],
				[
					'attribute'=>'sales_person',
					'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
					'value'=>function($data)
					{
						return $data->customer->user->name;
					}
					//'value'=>'user.user_name'
				],
				[
					'attribute'=>'agent_name',
					'value'=>function($data)
					{
						if($data->customer->agent_name!=null || $data->customer->agent_name!="")
							return $data->customer->agent_name;
						else
							return "-";
					}
				],
				[
					'attribute'=>'fiber_installed',
					'filter'=>array(''=>'All','power'=>'Power','dig'=>'Wireless','FTTH'=>'FTTH'),
					'value' => function($data){
							 if($data->customer->fiber_installed == null){
								 return '-';
							 }else{
								if($data->customer->fiber_installed == 'dig'){
							 		return 'Wireless';
							 	}else{
								 return $data->customer->fiber_installed;
							 	}
							 }
						 }
						
					
				],
				[
					'attribute'=>'invoice_number',
					'value'=>'invoice.invoice_number'
				],

				'reciept_no',
				
				[
					'attribute'=>'payment_method',
					'filter'=>[''=>'All','cash'=>'Cash','virtual_transfer'=>'Virtual Transfer','bank'=>'Bank','cheque'=>'Cheque'],
					'value'=>function($data){
					 return ucfirst($data->payment_method);
					}
					
				],
				
				
				[  
                'attribute'=>'cheque_no',          
                'value' =>function ($data) {
                    if(!empty($data->cheque_no))
                    {
                    
                        return $data->cheque_no;
                    }
                    else{
                    	
                        return '--';
                    }
                  }
                ],
				
				[
                 'attribute' => 'amount_paid',
				 'value' => function($data){
					return number_format($data->amount_paid,2);
				 },
                 'footer' =>strip_tags(Customerpayment::getPaymentTotal()),        
   				 ],

				[
					'label'=>'Currency',
					'value'=>'currency.currency',
					'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' => '']),
		
				],

				'comment',
				[
				'label'=>'Remarks',
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
		];
?>
<p>

<?php echo '&nbsp;'.Html::a('Reset Filters', ['/report/paymentreport'], ['class' => 'btn btn-success']);

 echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'filename'=>'payment_collection'.date('Ymdhis')
        ]); ?>
 
 </p>
<?php $form = ActiveForm::begin([
				'action' => ['report/paymentreport'],
				'method' =>'GET',
				'options' => [
                	'class' => 'form-horizontal form-bordered'
                 ],
                'fieldConfig' => [
                	'template' => '{label}<div class="col-sm-6">{input}</div>{error}',
                    'labelOptions' => ['class' => 'col-sm-2 control-label']
                 ]
             ]); 
		?>
<div class="row">
	<div class="col-md-12">
		<div class="col-md-4 text-right"><label>Select Date Received Date Range :-</label></div>
		<div class="col-md-6">
		
		<?php echo DateRangePicker::widget([
			'name' => 'start_date',
			'value' => $strStartDate,
			'nameTo' => 'end_date',
			'valueTo' => $strEndDate,
			'clientOptions' => [
									'autoclose' => true,
									'format' => 'dd-mm-yyyy'
								]
		]);?>

		</div>
		<div class="col-md-2"><?php echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);?></div>
	</div>
</div>
<br>
 <?php ActiveForm::end();?>
 <div class="alert-success alert fade in" id="success" style="display:none"> </div>
 
 
<div class="box box-default">
		<div class="box-body">
		 <div class="horizontal-scroll">
			<div class="tbllanguage-form">
				<div class="customer-index">

					<?php echo GridView::widget([
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'id'=>'grid',
						'showFooter'=>true,
						'columns' => [
							['class' => 'yii\grid\SerialColumn'],
							
						
							[
								'label'=>'Date Received',
								'attribute'=>'payment_date',
								'value'=>function($data){
									return date('d-m-Y',strtotime($data->payment_date)); },
								'filter' => DatePicker::widget([
                                'name' => 'CustomerpaymentSearch[payment_date]',
                               // 'value'=>$strDate,
                                'template' => '{addon}{input}',
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-mm-yyyy'
                                    ]
                            ])
							],

							[
								'attribute'=>'solnet_customer_id',
								'value'=>'customer.solnet_customer_id'
							],
							[
								/*'label'=>'Name',
								'value'=>'customer.name'*/
								'attribute'=>'name',
								'value'=>'customer.name'
							],

							[
								'attribute'=>'billing_address',
								'value'=>'customer.billing_address'
							],
							[
								'attribute'=>'installation_address',
								'value'=>'customer.linkcustomerpackage.installation_address'
							],
							[
								'attribute'=>'state',
								'value'=>function($data)
								{
									return $data->customer->state->state;
								},
								
							],
							[
								'attribute'=>'sales_person',
								'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
								'value'=>function($data)
								{
									return $data->customer->user->name;
								}
								//'value'=>'user.user_name'
							],
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->customer->agent_name!=null || $data->customer->agent_name!="")
										return $data->customer->agent_name;
									else
										return "-";
								}
							],
							[
							'attribute'=>'fiber_installed',
							'filter'=>array(''=>'All','power'=>'Power','dig'=>'Wireless','FTTH'=>'FTTH'),
							'value' => function($data){
									 if($data->customer->fiber_installed == null){
										 return '-';
									 }else{
										if($data->customer->fiber_installed == 'dig'){
									 		return 'Wireless';
									 	}else{
										 return $data->customer->fiber_installed;
									 	}
									 }
								 }
								
							
						],
							[
								'attribute'=>'invoice_number',
								'value'=>'invoice.invoice_number'
							],

							'reciept_no',
							
							[
								'attribute'=>'payment_method',
								'filter'=>[''=>'All','cash'=>'Cash','virtual_transfer'=>'Virtual Transfer','bank'=>'Bank','cheque'=>'Cheque'],
								'value'=>function($data){
								 return ucfirst($data->payment_method);
								}
							
							],
							
							[
								'label'=>'Cheque Number',
								'attribute'=>'cheque_no',
								'footer'=>'<b>Total</b>',
							],
							
							[
								'label'=>'Currency',
								'value'=>'currency.currency',
								'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' => '']),
					
							],
							[
			                 'attribute' => 'amount_paid',
							 'value' => function($data){
								return number_format($data->amount_paid,2);
							 },
			                 'footer' =>Customerpayment::getPaymentTotal(),        
               				 ],


							'comment',
							[
							'label'=>'Remarks',
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
										
							[
				            'header'=>'Action',
				            'template'=>'  {paymentview} {print} {link}',
				            'buttons' => [

				            'paymentview'=> function ($url, $data) {
				                   
				                    return  Html::a('<i class="fa fa-eye"></i>', ['/report/paymentview','id'=>$data->payment_id], [ 
				                        'title'=>'View',
				                     ]);
				                },
				            'print' => function ($url, $data) {
				                   
				                    return  Html::a('<i class="fa fa-print"></i>', ['/report/paymentprint','id'=>$data->payment_id], [
				                          
				                        //'data-toggle'=>'tooltip', 
				                        'title'=>'Print',
				                        'target'=>'_blank',
				                        'data-pjax'=>'0'
				                     ]);
				            },
				            'link' => function ($url,$data) {
									return Html::a('<i class="fa fa-file-pdf-o"></i>',['/report/pdf','id'=>$data->invoice->customer_invoice_id,'state'=>$data->customer->fk_state_id],['title'=>'Print Invoice']);
								},
				            ],

				            'class' => 'yii\grid\ActionColumn'
                			],
						],
					]); ?>
				</div>
			</div>
		</div>
	</div>
</div>

	
	