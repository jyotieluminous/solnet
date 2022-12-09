<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use app\models\Customer;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Outstanding Invoices';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['CustomerinvoiceSearch']['invoice_date']))
{
	$strDate = $_GET['CustomerinvoiceSearch']['invoice_date'];
}
else{
	$strDate = '';
}

$floatIDRTotBalance = 0;
$floatSGDTotBalance = 0;
$floatUSDTotBalance = 0;
$floatTotBalance    = 0;
if(!empty($dataProvider->getModels())) 
{
	
	$model = $dataProvider->getModels();
	
 foreach ($dataProvider->getModels() as $key => $val) {
	 if($model[$key]['linkcustomepackage']->fk_currency_id==1){    //For IDR Currency
		 $floatIDRTotBalance  += $val->pending_amount;
	 }elseif($model[$key]['linkcustomepackage']->fk_currency_id==2){  //For SGD Currency
		 $floatSGDTotBalance  += $val->pending_amount;
	 }elseif($model[$key]['linkcustomepackage']->fk_currency_id==3){   //For USD Currency
		 $floatUSDTotBalance  += $val->pending_amount;
	 }
     $floatTotBalance = 'IDR '.number_format($floatIDRTotBalance,2).'<br/>SGD '.number_format($floatSGDTotBalance,2).' <br/>USD '.number_format($floatUSDTotBalance,2).'<br/>';
    }
}

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
						['class' => 'yii\grid\CheckboxColumn'],
						[
							'attribute'=>'name',
							'value'=> 'customer.name'
						],
						[
							'attribute'=>'custid',
							'value'=>'customer.solnet_customer_id',
						],
						
						[
							'attribute'=>'user_name',
							'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
							'value'=> function($data)
							{
								return $data->customer->user->name;
								
							},
							//'group'=>true,
						],
						[
							'attribute'=>'state',
							'value'=>function($data)
							{
								return $data->customer->state->state;
							},
							
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
					    //'customer.billing_address',
						'customer.linkcustomerpackage.package.package_title',
						[
							'attribute'=>'speed',
							'value'=>function($data){
								return $data->customer->linkcustomerpackage->package_speed.' '.$data->customer->linkcustomerpackage->speed->speed_type;
							}
						],
						'invoice_number',
						[
							'attribute'=>'invoice_date',
							'options'=>['width'=>'15%'],
							'filter'=>false,
							'value'=>function($data){
									return date('d-m-Y',strtotime($data->invoice_date));
							},
							'footer' =>'Total'
						],
						[
							 'label' => 'Invoice Balance',
							 'attribute' => 'pending_amount',
							 'value' => function ($model, $key, $index, $widget) {
							 return number_format($model->pending_amount,2);
							 },
							 'footer' => $floatTotBalance,
	
						 ],
						[
							'attribute'=>'currency',
							'filter'=>Arrayhelper::map($currency, 'currency_id', 'currency') ,
							'value'=>'customer.linkcustomerpackage.currency.currency'
						],
						[
							'attribute'=>'payment_term',
							'value'=>function($data)
							{
								if($data->customer->linkcustomerpackage->payment_type=='advance')
								{
									return '30 Days';
								}elseif($data->customer->linkcustomerpackage->payment_type=='term')
								{
									return $data->customer->linkcustomerpackage->payment_term.' Days';
								}else{
									return '-';
								}
							}
						],
						
						[
							'attribute'=>'due_date',
							'value'=>function($data){
									return date('d-m-Y',strtotime($data->due_date));
							},
						],

						[
							'attribute'=>'no_of_days_past_due',
							'width'=>'50',
							'value'=>function($data){ return $data->getnumberofdays($data->due_date); }
							
						],
						[
							'header'=>'Action',
							'options'=>['width'=>'140%'],
							'class' => 'yii\grid\ActionColumn',
							'template'=>'{link} {update}',
							'buttons'=>[
							 	'link' => function ($url,$model,$key) {
										return Html::a('<i class="fa fa-file-pdf-o"></i>',['/invoice/pdf','id'=>$model->customer_invoice_id,'state'=>$model->customer->fk_state_id],['title'=>'Print Invoice']);
								},
								'update' => function ($url,$model,$key) {
									
										 $url = Url::to(['invoice/remark']);
		   								return Html::a('<i class="fa fa-pencil"></i>',['invoice/remark','id'=>$model->customer_invoice_id], ['class' => 'updatecomment','title'=>'Update']);
								},
								
							]
						],
];
?>

<p style="display: inline-block;">
<?php echo Html::a('Reset Filters', ['/invoice/outstanding'], ['class' => 'btn btn-success']) ?>
<?php $form = ActiveForm::begin([
				'action' => ['invoice/multipledf'],
				'method' =>'POST',
				'options'=>['style'=>'display: inline;']
             ]); 
		?>
		<?php echo Html::hiddenInput('ids', '',['id'=>'ids']); ?>
		
<?php //echo '&nbsp;'.Html::a('Print Invoices','javascript:void(0)',['class' => 'btn btn-primary','onClick'=>'javascript:deleteAll()']) ?>
 <?php ActiveForm::end();?>
</p>
<?php
if(Yii::$app->user->identity->fk_role_id=='1' || Yii::$app->user->identity->fk_role_id=='2'){
	// Renders a export dropdown menu
echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
	'filename'=>'customer_outstanding_invoice'.date('Ymdhis')
]);
}
?>
<div id="loader" style='display:none;'></div>  
<?php $form = ActiveForm::begin([
				'action' => ['invoice/outstanding'],
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
		<div class="col-md-3 text-right"><label>Select Daterange :-</label></div>
		<div class="col-md-6">
		
		<?php echo DateRangePicker::widget([
			'name' => 'start_date',
			'value' => $strStartDate,
			'nameTo' => 'end_date',
			'valueTo' => $strEndDate,
			'clientOptions' => [
									'autoclose' => true,
									'format' => 'yyyy-mm-dd'
								]
		]);?>
		
		
		</div>
		<div class="col-md-3"><?php echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);?></div>
	</div>
</div>
 <?php ActiveForm::end();?>
 <div class="row">
 	<div class="col-md-11 text-right">
 		<label>Add Letter Head To Invoice PDF : </label>
 	</div>
 	<div class="col-md-1">
 		<input type="checkbox" value="" id="CustomerInvoices_print_header" onClick='headerChange()'>
 	</div>
 </div>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<div class="customerinvoice-index"> 
				<?php echo kartik\grid\GridView::widget([
					'dataProvider' => $dataProvider,
					'filterModel' => $searchModel,
					'showFooter' =>true,
					'id'=>'grid',
					'columns' => $gridColumns,
				]); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function headerChange() {
 var isHeader = document.getElementById('CustomerInvoices_print_header').checked;
  var basePath = "<?php echo Yii::$app->request->baseUrl;?>/";
  var action = "invoice/setsession";
  var getURl = basePath + action;
  var headerValue = isHeader;
  
  $.ajax({
   type: 'POST',
   url: getURl,
   data: { "header_flag" : headerValue }, 
 
  });
}
</script>
<script type="text/javascript">
		/*function deleteAll(){ 
			var ids = $('#grid').yiiGridView('getSelectedRows');
			if(ids.length >  0){
				$('#ids').val(ids);
				if(confirm("Are you sure you want to print the pdf's?")){
				$("#w0").submit()
				}
			}else{
				alert("Please select the records to print the pdf's");
			}
		}*/
	
	function sendmail(cust_inv_id)
	{
		if(confirm("Are you sure want to send this invoice?")){
			if(cust_inv_id!=""){
			$("#grid").css("opacity","0.39");
			$("#grid").css("background", "url('../web/images/ajax-loader.gif') 50% 50% no-repeat rgb(249,249,249)");	
			$('#loader').show();
			$.ajax({
					type     :'GET',
					cache    : false,
					url  : '<?php echo yii::$app->request->baseUrl; ?>/invoice/sendmail?id='+cust_inv_id,
					success  : function(response) {	

						
						}
					});
					return false;
			}
		}
	}
</script>
