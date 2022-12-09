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
use app\models\CustomerService;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer List For Service Invoices';
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
							],
							'it_pic',
							'optional_email',
							[
								'attribute'=>'bundling_package',
								'value'=>'linkcustomerpackage.bundling_package'
							],*/
							
							
							[
								 'attribute'=>'status',
								 'label'=>'Status',
								 'filter'=>[''=>'All','active'=>'Active','inactive'=>'Inactive'],
								
							],
							'linkcustomerpackage.is_disconnected',
						/*[
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
							], */
							[
								'header'=>'Action',
								'options'=>['width'=>100],
								'template'=>'{view} {update} ',
								'buttons' => [
									'view' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-eye"></span>', 
											['customer/billview','id'=>$model->customer_id]);
									},
									'update' => function ($url,$model) {
										$arrCust = CustomerService::find()->where(['fk_customer_id'=>$model->customer_id])->one();
										if(empty($arrCust)){
										return Html::a(
											'<span class="fa fa-plus"></span>', 
											['service/add','id'=>$model->customer_id]);
										}else{
											return Html::a(
											'<span class="fa fa-pencil"></span>', 
											['service/updateservice','id'=>$model->customer_id]);
										}
									},
									
								],
								'class' => 'yii\grid\ActionColumn'
							],
];


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
<?php echo Html::a('Reset Filters', ['/service/addservice'], ['class' => 'btn btn-success']) ?>

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
			'action' => Url::to(['service/addservice']),
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