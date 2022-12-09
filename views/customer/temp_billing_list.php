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

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Temporary Disconnect Users';
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
	if(Yii::$app->user->identity->fk_role_id=='22'){
		
		$limit = '0';
		$_SESSION['limit'] = 0;
	}
	
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
//Get total package price on the page
if(!empty($dataProvider->getModels())) 
{
	$model = $dataProvider->getModels();
	
	foreach($dataProvider->getModels() as $key=>$value)
	{
		$floatTotalPackagePrice += $value->linkcustomerpackage->package_price;
		
	    $floatTotBalance = number_format($floatTotalPackagePrice,2);
	    
	}
}


	//echo Yii::$app->user->identity->fk_role_id;
	//die;
	if(Yii::$app->user->identity->fk_role_id=='8' || Yii::$app->user->identity->fk_role_id=='23' || Yii::$app->user->identity->fk_role_id=='25'){
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
							[
								'attribute'=>'fiber_installed',
								'filter'=>[''=>'All','power'=>'Power','dig'=>'Wireless','FTTH'=>'FTTH'],
								'value'=>function($data)
								{
									if($data->fiber_installed!="" || $data->fiber_installed!=null)
									{
										return $data->fiber_installed;
									}
									else
									{
										return "-";
									}
								}
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
									'add' => function ($url,$model,$key) {
											return Html::a('<i class="fa fa-plus"></i>', ['/customer/addequipment','id'=>$model->customer_id],['title'=>'Add Equipments','target'=>'_blank']);
									},
									
								],
								'class' => 'yii\grid\ActionColumn'
							],
    			];
	}elseif(Yii::$app->user->identity->fk_role_id=='24'){
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
							//'mobile_no',
							'state.state',
							
							//'customer_type',
							[
								'attribute'=>'package_title',
								'value'=>'linkcustomerpackage.package.package_title'
							],
							/*[
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
							],*/
							[
								'attribute'=>'fiber_installed',
								'filter'=>[''=>'All','power'=>'Power','dig'=>'Wireless','FTTH'=>'FTTH'],
								'value'=>function($data)
								{
									if($data->fiber_installed!="" || $data->fiber_installed!=null)
									{
										return $data->fiber_installed;
									}
									else
									{
										return "-";
									}
								}
							],
						//	'linkcustomerpackage.is_disconnected',
							/*[
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
							],*/
							[
								'header'=>'Action',
								'options'=>['width'=>100],
								'template'=>'{view} {add}',
								'buttons' => [
									'view' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-eye"></span>', 
											['customer/billview','id'=>$model->customer_id]);
									},
									'add' => function ($url,$model,$key) {
											return Html::a('<i class="fa fa-plus"></i>', ['/customer/addequipment','id'=>$model->customer_id],['title'=>'Add Equipments','target'=>'_blank']);
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
								'attribute'=>'fiber_installed',
								'filter'=>[''=>'All','power'=>'Power','dig'=>'Wireless','FTTH'=>'FTTH'],
								'value'=>function($data)
								{
									if($data->fiber_installed!="" || $data->fiber_installed!=null)
									{
										return $data->fiber_installed;
									}
									else
									{
										return "-";
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
								 
								 'label'=>'Status',
								 
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
									return $strAltText;
									
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
								'template'=>'{temp_disconnect}',
								'buttons' => [
									
									'temp_disconnect' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-plug"></span>', 
											['customer/tempdisconnectactive','id'=>$model->customer_id],['data-confirm'=>"Are you sure you want to active temporary disconnect record?"]);
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
						
							/*[
								'attribute'=>'package_price',
								'value'=> function($data) {
									return number_format($data->linkcustomerpackage->package_price,2);
									
								},
								'footer' => $floatTotBalance,
							],*/
							[
								'attribute'=>'fiber_installed',
								'filter'=>[''=>'All','power'=>'Power','dig'=>'Wireless','FTTH'=>'FTTH'],
								'value'=>function($data)
								{
									if($data->fiber_installed!="" || $data->fiber_installed!=null)
									{
										return $data->fiber_installed;
									}
									else
									{
										return "-";
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
							[
								'attribute'=>'fiber_installed',
								'filter'=>[''=>'All','power'=>'Power','dig'=>'Wireless','FTTH'=>'FTTH'],
								'value'=>function($data)
								{
									if($data->fiber_installed!="" || $data->fiber_installed!=null)
									{
										return $data->fiber_installed;
									}
									else
									{
										return "-";
									}
								}
							],
							'it_pic',
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
							
				]; 
	} else{
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
							[
								'attribute'=>'fiber_installed',
								'filter'=>[''=>'All','power'=>'Power','dig'=>'Wireless','FTTH'=>'FTTH'],
								'value'=>function($data)
								{
									if($data->fiber_installed!="" || $data->fiber_installed!=null)
									{
										return $data->fiber_installed;
									}
									else
									{
										return "-";
									}
								}
							],
							'status',
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

<?php

	 $form = ActiveForm::begin([
			'id' => 'limit-form',
			'method'=>'get',
			'action' => Url::to(['customer/tempusers']),
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
if(Yii::$app->user->identity->fk_role_id!='8' &&  Yii::$app->user->identity->fk_role_id != '22' &&  Yii::$app->user->identity->fk_role_id != '24' &&  Yii::$app->user->identity->fk_role_id != '25' ){
?>
<div class="row">
 	<div class="col-md-11 text-right">
 		<label>Total Package Price (For only active customers) : <?php echo number_format($totalPackagePrice,2) ?></label>
 	</div>
 	
 </div>
<?php }?>
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