<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use app\models\Customer;
use yii\helpers\Url;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer Support';
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
	
	foreach($dataProvider->getModels() as $key=>$value)
	{
		if($model[$key]['linkcustomepackage']->fk_currency_id==1){    //For IDR Currency
		 $floatIDRTotBalance  += $value->current_invoice_amount;
		 }elseif($model[$key]['linkcustomepackage']->fk_currency_id==2){  //For SGD Currency
			 $floatSGDTotBalance  += $value->current_invoice_amount;
		 }elseif($model[$key]['linkcustomepackage']->fk_currency_id==3){   //For USD Currency
			 $floatUSDTotBalance  += $value->current_invoice_amount;
		 }
	     $floatTotBalance = 'IDR '.number_format($floatIDRTotBalance,2).'<br/>SGD '.number_format($floatSGDTotBalance,2).' <br/>USD '.number_format($floatUSDTotBalance,2).'<br/>';
	    
	}
}
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
						['class' => 'yii\grid\CheckboxColumn'],
						[
							'attribute'=>'solnet_customer_id',
							'value'=>'customer.solnet_customer_id',
							'group'=>true,
						],
						[
							'attribute'=>'name',
							'value'=> 'customer.name',
							'group'=>true,
						],
						[
							'attribute'=>'mobile_no',
							'value'=> 'customer.mobile_no',
							'group'=>true,
						],
						[
							'attribute'=>'state',
							'value'=>function($data)
							{
								return $data->customer->state->state;
							},
							
						],
						[
							'attribute'=>'status',
							'filter'=>array('paid'=>'Paid','unpaid'=>'Unpaid'),
							'value'=>function($data)
							{
								return ucfirst($data->status);
							}
						],
						/*[
							'attribute'=>'user_name',
							'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
							'value'=> function($data)
							{
								return $data->customer->user->name;
								
							},
							//'group'=>true,
						],*/
						/*[
							'attribute'=>'agent_name',
							'value'=>function($data)
							{
								if($data->customer->agent_name!=null || $data->customer->agent_name!="")
									return $data->customer->agent_name;
								else
									return "-";
							}
						],*/
						[
							'attribute'=>'payment_type',
							'filter'=>array(''=>'All','term'=>'Term','advance'=>'Advance','bulk'=>'Bulk'),
							'value'=>function($data)
							{
								return ucfirst($data->payment_type);
								//return ucfirst($data->customer->linkcustomerpackage->payment_type);
							},
							'group'=>true,
						],
						//'customer.linkcustomerpackage.package.package_title',
						/*[
						'attribute'=>'package_title',
						'value'=>function($data)
						{
							return $data->customer->linkcustomerpackage->package->package_title;
						}
						],
						[
							'attribute'=>'speed',
							'value'=>function($data){
								if(!empty($data->customer->linkcustomerpackage->package_speed)){
									return $data->customer->linkcustomerpackage->package_speed.' '.$data->customer->linkcustomerpackage->speed->speed_type;
								}
							}
						],*/
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
						/*[
							'attribute'=> 	'last_due_amount',
							'value'=> function($data){
								return number_format($data->last_due_amount,2);
								
							},
						],*/
						[
							'attribute'=> 	'current_invoice_amount',
							'value'=> function($data){
								return number_format($data->current_invoice_amount,2);
								
							},
							'footer' => $floatTotBalance,
						],
						/*[
							'attribute'=> 	'paid_amount',
							'value'=> function($data){
								return number_format($data->paid_amount,2);
								
							},
						],
						[
							'attribute'=> 	'pending_amount',
							'value'=> function($data){
								return number_format($data->pending_amount,2);
								
							},
						],*/
						[
							'attribute'=>'total_invoice_amount',
							
							'value'=> function($data){
								return number_format($data->total_invoice_amount,2);
								
							},
							
						],
						
						
						/*[
							'attribute'=>'currency',
							'filter'=>Arrayhelper::map($currency, 'currency_id', 'currency') ,
							'value'=>'customer.linkcustomerpackage.currency.currency'
						],*/
						/*[
							'attribute'=>'status',
							'filter'=>array('paid'=>'Paid','cancelled'=>'Cancelled','partial'=>'Partial','unpaid'=>'Unpaid','cf'=>'CF'),
							'value'=>function($data){
								return ucfirst($data->status);
							}
						],*/
						[
							'attribute'=>'invoice_type',
							'filter'=>array('normal'=>'Normal','custom'=>'Custom'),
						],
						/*[
							'label'=>'Invoice Mode',
							'attribute'=>'invoice_send_via',
							'filter'=>array('email'=>'Email','Hardcopy'=>'hardcopy','both'=>'Both'),
							'value'=>function($data){
								if(!empty($data->customer->invoice_send_via)){
									return $data->customer->invoice_send_via;
								}
							}
						],
						[
							'attribute'=>'comments',
							'value'=>function($data)
							{
								if($data->comments)
								{
									return $data->comments;
								}
								else
								{
									return "-";
								}
							}
						],*/
						
						[
							'header'=>'Action',
							'options'=>['width'=>'140%'],
							'class' => 'yii\grid\ActionColumn',
							'template'=>'{link} {pay} ',
							'buttons'=>[
							 	'link' => function ($url,$model,$key) {
									return Html::a('<i class="fa fa-file-pdf-o"></i>',['/invoice/pdf','id'=>$model->customer_invoice_id,'state'=>$model->customer->fk_state_id],['title'=>'Print Invoice']);
								},
								
								'pay' =>function ($url,$model,$key) {
									return Html::a('<i class="fa fa-credit-card-alt"></i>',['invoice/paycustomersupport','id'=>$model->customer_invoice_id],['title'=>'Pay Invoice Amount']);
								},
							]
						],
];
?>

<p style="display: inline-block;">
<?php echo Html::a('Reset Filters', ['/customer/customersupport'], ['class' => 'btn btn-success']) ?>
<?php $form = ActiveForm::begin([
				'action' => ['invoice/multipledf'],
				'method' =>'POST',
				'options'=>['style'=>'display: inline;']
             ]); 
		?>
		<?php echo Html::hiddenInput('ids', '',['id'=>'ids']); ?>
		
<?php echo '&nbsp;'.Html::a('Print Invoices','javascript:void(0)',['class' => 'btn btn-primary','onClick'=>'javascript:deleteAll()']) ?>
<?php ActiveForm::end();?>

</p>
<?php
	// Renders a export dropdown menu
 echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
	'filename'=>'customer_invoice'.date('Ymdhis')
]);
?>
 
<?php $form = ActiveForm::begin([
				'action' => ['invoice/index'],
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

 <?php ActiveForm::end();?>
 <hr>
 <div class="row">
	<div class="col-md-5 text-right">
		
		<label>Add Signature to invoice PDF : </label>
	</div>
 	<div class="col-md-1">
 	
 		<input type="checkbox" value="" id="CustomerInvoices_signature" onClick='signChange()'>
 	</div>
 	<div class="col-md-5 text-right">
 		<label>Add Letter Head to invoice PDF : </label>
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
					'id'=>'grid',
					'showFooter' =>true,
					'columns' => $gridColumns,
				]); ?>
				<div id="loader" style='display:none;'></div> 
			</div>
		</div>
	</div>
</div>

<?php

Modal::begin([
    'id'     => "modal",
    'header' => '<h3 class="text-center">Update comment</h3>',
]);

echo "<div id='modalContent'></div>";
Modal::end();


$this->registerJs(
    "$(document).on('ready pjax:success', function() {
            $('.updatecomment').click(function(e){
               e.preventDefault(); //for prevent default behavior of <a> tag.
               $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));
           });
        });
    ");

?>

<script type="text/javascript">
$(document).ready(function(){
	var isSignatureSet = "<?php echo Yii::$app->session['signature']?>";
	if(isSignatureSet==1)
	{
		$('#CustomerInvoices_signature').prop('checked', true);
	}
	else
	{
		$('#CustomerInvoices_signature').prop('checked', false);
	}
	var isHeaderSet = "<?php echo Yii::$app->session['print_header']?>";
	if(isHeaderSet==1)
	{
		$('#CustomerInvoices_print_header').prop('checked', true);
	}
	else
	{
		$('#CustomerInvoices_print_header').prop('checked', false);
	}
});
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

function signChange() {
	
 var isHeader = document.getElementById('CustomerInvoices_signature').checked;
  var basePath = "<?php echo Yii::$app->request->baseUrl;?>/";
  var action = "invoice/setsignature";
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
		function deleteAll(){ 
			var ids = $('#grid').yiiGridView('getSelectedRows');
			if(ids.length >  0){
				$('#ids').val(ids);
				if(confirm("Are you sure you want to print the pdf's?")){
				$("#w0").submit()
				}
			}else{
				alert("Please select the records to print the pdf's");
			}
		}
	
	function sendmail(cust_inv_id)
	{
		if(confirm("Are you sure want to send this invoice?")){
			if(cust_inv_id!=""){
			$("#grid").css("opacity","0.39");
			$("#loader").css("background", "url('../web/images/ajax-loader.gif') 50% 50% no-repeat rgba(249,249,249,0)");	
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
