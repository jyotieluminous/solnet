<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use yii\bootstrap\Modal; 
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Customer;
use app\models\CustomerServiceDetail;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Billing Customers';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['page']) && $_GET!="")
{
	$page = $_GET['page'];
}
else
{
	$page = '';
} 
$limit = '20';
$queryParams = Yii::$app->request->getQueryParams();

if(!empty($queryParams) && isset($queryParams['limit']))
{
	if(isset($queryParams['limit']))
	{
		$limit =  $queryParams['limit'];
		$_SESSION['limit']=$limit;
	}
}
else
{
	$_SESSION['limit'] = $limit;
}

if(isset($_GET['CustomerSearch']['order_received_date']))
{
	$strDate = $_GET['CustomerSearch']['order_received_date'];
}
else{
	$strDate = '';
}

$floatTotalPackagePrice = 0;
$floatTotBalance = 0;
$floatServiceBalance = 0;
//Get total package price on the page
if(!empty($dataProvider->getModels())) 
{
	$model = $dataProvider->getModels();
	
	foreach($dataProvider->getModels() as $key=>$value)
	{
		$floatTotalPackagePrice += $value->linkcustomerpackage->package_price;
		
	    $floatTotBalance = number_format($floatTotalPackagePrice,2);
	    
	}
	foreach($dataProvider->getModels() as $key=>$value){
		if(isset($value->servicecharge->service)){
		$serviceData = $value->servicecharge->service;
		if(!empty($serviceData)){
			foreach($serviceData as $serKey => $serValue){
				$floatServiceBalance = $floatServiceBalance + ($serValue->service_price * $serValue->service_quantity);
			}
		}
		}
		
	}
}



	if(Yii::$app->user->identity->fk_role_id=='8' || Yii::$app->user->identity->fk_role_id=='23' || Yii::$app->user->identity->fk_role_id=='24' || Yii::$app->user->identity->fk_role_id=='25'){
	    $gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
							['class' => 'yii\grid\CheckboxColumn'],
							'name',
							'solnet_customer_id',
							'billing_address:ntext',
							[
								'attribute'=>'installation_address',
								'value'=>'linkcustomerpackage.installation_address'
							],
							'mobile_no',
							'state.state',
							
							'customer_type',
							[
								'attribute'=>'package_title',
								'value'=>'linkcustomerpackage.package.package_title'
							],
							[
								'attribute'=>'package_speed',
								'value'=>'linkcustomerpackage.package_speed'
							],
							[
								'attribute'=>'speed_type',
								'value'=>'linkcustomerpackage.speed.speed_type',
								'footer' =>'Total'
							],
					
							'it_pic',
							'optional_email',
							[
								'attribute'=>'bundling_package',
								'value'=>'linkcustomerpackage.bundling_package'
							],
						
							'linkcustomerpackage.is_disconnected',
							[
								'attribute'=>'user_name',
								'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
								'value'=>function($data)
								{
									if($data->user->name)
										return $data->user->name;
									else
										return '-';
								}
								//'value'=>'user.user_name'
							],
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->agent_name!="" || $data->agent_name!=null)
									{
										return $data->agent_name;
									}
									else
									{
										return "";
									}
								}
							],
							[
								'header'=>'Action',
								'options'=>['width'=>100],
								'template'=>'{view} ',
								'buttons' => [
									'view' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-eye"></span>', 
											['customer/billview','id'=>$model->customer_id]);
									},
									
								],
								'class' => 'yii\grid\ActionColumn'
							],
    ];
	    
	}else{
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
							['class' => 'yii\grid\CheckboxColumn'],
							'name',
							'solnet_customer_id',
							'billing_address:ntext',
							[
								'attribute'=>'installation_address',
								'value'=>'linkcustomerpackage.installation_address'
							],
							'mobile_no',
							'state.state',
							'customer_type',
							[
								'attribute'=>'package_title',
								'value'=>'linkcustomerpackage.package.package_title'
							],
							[
								'attribute'=>'package_speed',
								'value'=>'linkcustomerpackage.package_speed'
							],
							[
								'attribute'=>'speed_type',
								'value'=>'linkcustomerpackage.speed.speed_type',
								'footer'=>'Total'
							],
						
							[
								'attribute'=>'package_price',
								'value'=> function($data) {
									return number_format($data->linkcustomerpackage->package_price,2);
									
								},
								'footer' => $floatTotBalance,
							],
							[
								'attribute'=>'service_charge',
								'value'=> function($data) {
									//echo '<pre>';print_r($data);echo '</pre>';die;
									if(isset($data->servicecharge->service)){
										$charge = $data->getTotalServiceCharge($data->servicecharge->service);
															
										return number_format($charge,2);
									}else{
										return 0;
									}
									
									
								},
								'footer' => number_format($floatServiceBalance,2),
							],
							
							
							
							[
								 'attribute'=>'fiber_installed',
								 'label'=>'Fiber Installed',
								 'filter'=>[''=>'All','power'=>'Power','dig'=>'Wireless','FTTH' => 'FTTH'],
								  'format'=>'raw',
								 'value' => function($data){
									 if($data->fiber_installed == null){
										 return '-';
									 }else{
										if($data->fiber_installed == 'dig'){
									 		return 'Wireless';
									 	}else{
										 return $data->fiber_installed;
									 	}
									 }
								 }
								
							],
							
							
							
							'it_pic',
							'optional_email',
							[
								'attribute'=>'bundling_package',
								'value'=>'linkcustomerpackage.bundling_package'
							],
							
							[
								 'attribute'=>'status',
								 'label'=>'Status',
								 'filter'=>[''=>'All','active'=>'Active','inactive'=>'Inactive'],
								 'format'=>'raw',
								 'value' => function($data){
									if($data->status=='active')
									{
										$url = Url::to('@web/images/active.png');
										$strAltText =  'Active';
									}else{
										$url = Url::to('@web/images/inactive.png');	
										$strAltText =  'Inctive';
									}
									 $redirect_url = Yii::$app->request->baseUrl.'/customer/togglestatus';
									if($data->linkcustomerpackage->is_disconnected=='no')
									{
									 	return Html::a('<img src='.$url.' class="switch-button" />', 'javascript:void(0);', ['title' => $strAltText,'onclick'=>"javascript:changeStatus('".$data->status."','".$data->customer_id."','".$redirect_url."','customer-grid','Customer status changed successfully.')"]); 
									}else{
										 return '<img src='.$url.' class="switch-button" />'; 
									}
									
								 }
							],
							'linkcustomerpackage.is_disconnected',
							[
							'attribute'=>'sales_person',
							'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
							'value'=> function($data)
							{
								$strSalesPerson = Customer::find()->joinWith('user')->select('tblusers.name')->where(['customer_id'=>$data->customer_id])->one();
								if($strSalesPerson)
								{
									return $strSalesPerson->name;
								}
								else
								{
									return '-';
								}
							},
							//'group'=>true,
						],
							
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->agent_name!="" || $data->agent_name!=null)
									{
										return $data->agent_name;
									}
									else
									{
										return "";
									}
								}
							],
							[
								'header'=>'Action',
								'options'=>['width'=>100],
								'template'=>'{view} {update} {delete} {disconnect} {sales}',
								'buttons' => [
									'view' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-eye"></span>', 
											['customer/billview','id'=>$model->customer_id]);
									},
									'update' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-pencil"></span>', 
											['customer/plan','id'=>$model->customer_id]);
									},
									'delete' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-trash"></span>', 
											['customer/billingsingledelete','id'=>$model->customer_id],['data-confirm'=>"Are you sure you want to delete this record?"]);
									},
									'disconnect' => function ($url,$model) {
										if($model->linkcustomerpackage->is_disconnected=='no'){
											return Html::a('<span class="fa fa-chain-broken red"></span>','javascript:void(0)',['class' =>'list','value' => $url,'title'=>'Disconnect']);
										}else{
											$url = 'reactivate/'.$model->customer_id;
											return Html::a('<span class="fa fa-link check"></span>','javascript:void(0)',['class' =>'list','value' => $url,'title'=>'Reactivate']);
										}
									},
									'sales' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-user"></span>', 
											['customer/salesperson','customerId'=>$model->customer_id]);
									},
								],
								'class' => 'yii\grid\ActionColumn'
							],
];
}

if(Yii::$app->user->identity->fk_role_id=='22'){
	
	$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
							['class' => 'yii\grid\CheckboxColumn'],
							'name',
							'solnet_customer_id',
							//'billing_address:ntext',
							[
								'attribute'=>'installation_address',
								'value'=>'linkcustomerpackage.installation_address'
							],
							'mobile_no',
							'state.state',
							'customer_type',
							[
								'attribute'=>'package_title',
								'value'=>'linkcustomerpackage.package.package_title'
							],
							[
								'attribute'=>'package_speed',
								'value'=>'linkcustomerpackage.package_speed'
							],
							[
								'attribute'=>'speed_type',
								'value'=>'linkcustomerpackage.speed.speed_type',
								'footer'=>'Total'
							],
						
							
							'it_pic',
							'optional_email',
							[
								'attribute'=>'bundling_package',
								'value'=>'linkcustomerpackage.bundling_package'
							],
							
							[
								 'attribute'=>'status',
								 'label'=>'Status',
								
							],
							'linkcustomerpackage.is_disconnected',
							[
							'attribute'=>'sales_person',
							'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
							'value'=> function($data)
							{
								$strSalesPerson = Customer::find()->joinWith('user')->select('tblusers.name')->where(['customer_id'=>$data->customer_id])->one();
								if($strSalesPerson)
								{
									return $strSalesPerson->name;
								}
								else
								{
									return '-';
								}
							},
							//'group'=>true,
						],
							
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->agent_name!="" || $data->agent_name!=null)
									{
										return $data->agent_name;
									}
									else
									{
										return "";
									}
								}
							],
							
];
	
	
	
}

if(Yii::$app->user->identity->fk_role_id=='8'){
$gridColumnsExcel = [
    ['class' => 'yii\grid\SerialColumn'],
							['class' => 'yii\grid\CheckboxColumn'],
							'name',
							'solnet_customer_id',
							'billing_address:ntext',
							[
								'attribute'=>'installation_address',
								'value'=>'linkcustomerpackage.installation_address'
							],
							'mobile_no',
							'state.state',
							'customer_type',
							[
								'attribute'=>'package_title',
								'value'=>'linkcustomerpackage.package.package_title'
							],
							[
								'attribute'=>'package_speed',
								'value'=>'linkcustomerpackage.package_speed'
							],
							[
								'attribute'=>'speed_type',
								'value'=>'linkcustomerpackage.speed.speed_type'
							],
							'it_pic',
								
							[
								 'attribute'=>'fiber_installed',
								 'label'=>'Fiber Installed',
								 'filter'=>[''=>'All','power'=>'Power','dig'=>'Wireless','FTTH' => 'FTTH'],
								  'format'=>'raw',
								 'value' => function($data){
									 if($data->fiber_installed == null){
										 return '-';
									 }else{
									 	if($data->fiber_installed == 'dig'){
									 		return 'Wireless';
									 	}else{
										 	return $data->fiber_installed;
									 	}
									 }
								 }
								
							],
							'optional_email',
							[
								'attribute'=>'bundling_package',
								'value'=>'linkcustomerpackage.bundling_package'
							],
						
							'status',
							[
								'attribute'=>'Sales Person',
								'value'=>'user.name'
							],
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->agent_name!="" || $data->agent_name!=null)
									{
										return $data->agent_name;
									}
									else
									{
										return "";
									}
								}
							],
							'is_disconnected',
							[
								'attribute'=>'Is Disconnected',
								'value'=> 'linkcustomerpackage.is_disconnected'
								
							],
							
]; } else{
    $gridColumnsExcel = [
    ['class' => 'yii\grid\SerialColumn'],
							['class' => 'yii\grid\CheckboxColumn'],
							'name',
							'solnet_customer_id',
							'billing_address:ntext',
							[
								'attribute'=>'installation_address',
								'value'=>'linkcustomerpackage.installation_address'
							],
							'mobile_no',
							'state.state',
							'customer_type',
							[
								'attribute'=>'package_title',
								'value'=>'linkcustomerpackage.package.package_title'
							],
							[
								'attribute'=>'package_speed',
								'value'=>'linkcustomerpackage.package_speed'
							],
							[
								'attribute'=>'speed_type',
								'value'=>'linkcustomerpackage.speed.speed_type',
								
							],
							[
								'attribute'=>'package_price',
								'value'=>'linkcustomerpackage.package_price',
								
							],
							'status',
								
							[
								 'attribute'=>'fiber_installed',
								 'label'=>'Fiber Installed',
								 'filter'=>[''=>'All','power'=>'Power','dig'=>'Wireless','FTTH' => 'FTTH'],
								  'format'=>'raw',
								 'value' => function($data){
									 if($data->fiber_installed == null){
										 return '-';
									 }else{
										if($data->fiber_installed == 'dig'){
									 		return 'Wireless';
									 	}else{
										 return $data->fiber_installed;
									 	}
									 }
								 }
								
							],
							'it_pic',
							'optional_email',
							[
								'attribute'=>'bundling_package',
								'value'=>'linkcustomerpackage.bundling_package'
							],
							[
								'attribute'=>'Sales Person',
								'value'=>'user.name'
							],
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->agent_name!="" || $data->agent_name!=null)
									{
										return $data->agent_name;
									}
									else
									{
										return "";
									}
								}
							],
							'is_disconnected',
							[
								'attribute'=>'Is Disconnected',
								'value'=> 'linkcustomerpackage.is_disconnected'
								
							],
							
];
}

?>

<p>
<?php echo Html::a('Reset Filters', ['/customer/billing'], ['class' => 'btn btn-success']) ?>
<?php if(Yii::$app->user->identity->fk_role_id != '22') { ?>
<?php echo '&nbsp;'.Html::a('Delete Selected','javascript:void(0)',['class' => 'btn btn-danger','onClick'=>'javascript:deleteAll()']) ?>
<?php echo'&nbsp;'. Html::a('Add Existing Customer', ['/customer/addexisting'], ['class' => 'btn btn-primary']) ; ?>
<?php } ?>
<?php
	// Renders a export dropdown menu
 /*echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumnsExcel,
	'filename'=>'billing_customer'.date('Ymdhis')
]);*/
?>
<?php

	 $form = ActiveForm::begin([
			'id' => 'limit-form',
			'method'=>'get',
			'action' => Url::to(['customer/billing']),
		]);
?>		
<div class="row">
	<div class="col-md-12">
		<div class="col-md-3 text-right"><label>Select Records per page :-</label></div>
		<div class="col-md-4">
				<?php echo Select2::widget([
				'model' => $searchModel,
				'name'=>'limit',
				//'attribute' => 'limit',
				'data' => array('20'=>'1-20','100'=>'1-100','500'=>'1-500','1000'=>'1-1000'),
				'value'=>$limit,
				'options' => [
					'placeholder' => 'Records per page',
					'multiple' => false,
					
				]
			]);?>
		</div>
		<div class="col-md-3"><?php echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);?></div>
	</div>	
</div>
<?php
	ActiveForm::end();
?>
</br>
<?php
//if role is not NOC then show the total
if(Yii::$app->user->identity->fk_role_id!='8'   &&  Yii::$app->user->identity->fk_role_id != '22'){
?>
<div class="row">
	<div class="col-md-12 ">
		<div class="col-md-4 ">
			<label>Total Package Price (For only active customers) : <?php echo number_format($totalPackagePrice,2) ?></label>
		</div>
		
		<div class="col-md-4 ">
			<label>Total Service Price (For only active customers) : <?php echo number_format($totalService,2) ?></label>
		</div>
		
		<div class="col-md-4 ">
			<label>Total Price (Package+Service) : <?php echo number_format($totalService+$totalPackagePrice,2) ?></label>
		</div>
		
 	</div>
 </div>
<?php } ?>
 </p>

 <div class="alert-success alert fade in" id="success_status" style="display:none"> </div>
 <?php if(Yii::$app->session->hasFlash('success_status')) : ?>
            <div class="alert-success alert fade in">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?php echo Yii::$app->session->getFlash('success_status'); ?>
            </div>
 <?php endif; ?>
        
 <?php Pjax::begin(['id'=>'customer-grid']); ?>
 
 	
      
      
<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form">
				<div class="customer-index">

					<?php echo kartik\grid\GridView::widget([
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'columns' => $gridColumns,
						'showFooter'=>true,
						
						'rowOptions' => function ($model, $index, $widget, $grid){
								if($model->linkcustomerpackage->is_disconnected=='yes'){
									return ['style'=>'background:#e0ebeb; color:#476b6b; '];
								}
    						}
					]); ?>
				</div>
			</div>
		</div>
	</div>
	<?php Pjax::end(); ?>
	
	<?php 

Modal::begin([
    'id'     => "modal",
    //'header' => '<h3 class="text-center">'.$text.'</h3>',
]);

echo "<div id='modalContent'></div>";
Modal::end();


$this->registerJs(
    "$(document).on('ready pjax:success', function() {
            $('.list').click(function(e){
               e.preventDefault(); //for prevent default behavior of <a> tag.
               $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));
           });
        });
    ");

?>
	<script type="text/javascript">
		function deleteAll(){ 
			var ids = $('#w4').yiiGridView('getSelectedRows');
			if(ids.length >  0){
			if(confirm("All package details related to this customers will be deleted\n\nAre you sure to delete this customers?")){
				$.ajax({
					type     : 'POST',
					data     : { ids : ids },
					cache    : false,
					url  	 : "<?php echo yii::$app->request->baseUrl.'/customer/billingmultipledelete' ?>",
					success  : function(response) {
													$.pjax.reload({container:'#customer-grid',async:false});
													$(window).scrollTop($('.box').offset().top);

													if(response == 'success'){
														$("#success").css("display", "block");
														$('#success').html("Customers deleted successfully");

													}
												$( "#success" ).fadeOut(5000);
												$.pjax.reload({container: '#customer-grid',async:false});

						}

					});
				}
			}else{
				alert("Please select the records to delete");
			}
		}
	</script>