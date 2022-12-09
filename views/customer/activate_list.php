<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use app\models\Customer;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pending Activation List';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['CustomerSearch']['order_received_date']))
{
	$strDate = $_GET['CustomerSearch']['order_received_date'];
}
else{
	$strDate = '';
}
/*echo "<pre>";
print_r($dataProvider->getModels());die;*/

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
}
?>

<p>
<?php echo Html::a('Reset Filters', ['/customer/pending'], ['class' => 'btn btn-success']) ?>
<?php echo '&nbsp;'.Html::a('Delete Selected','javascript:void(0)',['class' => 'btn btn-danger','onClick'=>'javascript:deleteAll()']) ?>
 </p>

 <div class="alert-success alert fade in" id="success_status" style="display:none"> </div>
 <?php if(Yii::$app->session->hasFlash('success_status')) : ?>
            <div class="alert-success alert fade in">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?php echo Yii::$app->session->getFlash('success_status'); ?>
            </div>
 <?php endif; ?>

 <?php Pjax::begin(['id'=>'activate-customer-grid']); ?>




<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form">
				<div class="customer-index">
					<?php echo kartik\grid\GridView::widget([
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'showFooter' => true,
						'id'=>'grid',
						'columns' => [
							['class' => 'yii\grid\SerialColumn'],
							['class' => 'yii\grid\CheckboxColumn'],
							'name',
							'billing_address:ntext',
							[
								'attribute'=>'installation_address',
								'value'=>'linkcustomerpackage.installation_address'
							],
							'mobile_no',
							[
								'attribute'=>'package_title',
								'value'=>'linkcustomerpackage.package.package_title'
							],
							'state.state',
							[
								'attribute'=>'package_speed',
								'value'=>'linkcustomerpackage.package_speed',
								'footer'=>'Total'
							],
							[
						        'attribute' => 'package_price',
								'value' => function($data){

									 $price = number_format($data->linkcustomerpackage->package_price,2);
									 return $price;
								},  
								'footer' => $floatTotBalance,
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
									 return Html::a('<img src='.$url.' class="switch-button" />', 'javascript:void(0);', ['title' => $strAltText,'onclick'=>"javascript:changeStatus('".$data->status."','".$data->customer_id."','".$redirect_url."','activate-customer-grid','Customer status changed successfully.')"]);

								 }
							],
							
							[
								'attribute'=>'sales_person',
								'filter'=>Arrayhelper::map((new Customer)->getUserName(), 'user_id', 'name'),
								'value'=>function($data)
								{
									return $data->user->name;
								}
								//'value'=>'user.user_name'
							],
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->agent_name!=null || $data->agent_name!="")
										return $data->agent_name;
									else
										return "-";
								}
							],
							[
								'attribute'=>'Activation Date',
								//'value'=>'linkcustomerpackage.activation_date'
								'value'=>function($data)
										{
											return date('d-m-Y',strtotime($data->linkcustomerpackage->activation_date));
										}
							],
							'additional_info',
							[
								'header'=>'Action',
								'options'=>['width'=>100],
								'template'=>'{activate} {view} {delete} {link}',//
								'buttons' => [
									'activate' => function ($url,$model) {
										if(!empty($model->is_invoice_activated) && $model->status=='active')
										{
											return Html::a('<span class="fa fa-play-circle"></span>',['customer/activate','id'=>$model->customer_id]);
										}
									},
									'view' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-eye"></span>',
											['customer/activateview','id'=>$model->customer_id]);
									},
									'delete' => function ($url,$model) {
										return Html::a(
											'<span class="fa fa-trash"></span>',
											['customer/activatesingledelete','id'=>$model->customer_id],['data-confirm'=>"Are you sure you want to delete this record?"]);
									},
									'link' => function ($url,$model,$key) {
										return Html::a('<i class="fa fa-file-pdf-o"></i>',['/customer/activatepdf','id'=>$model->customer_id],['target' => '_blank', 'data-pjax' => 0]);
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
	<?php Pjax::end(); ?>
	<script type="text/javascript">
		function deleteAll(){
			var ids = $('#grid').yiiGridView('getSelectedRows');
			if(ids.length >  0){
			if(confirm("Are you sure to delete this customers?")){
				$.ajax({
					type     : 'POST',
					data     : { ids : ids },
					cache    : false,
					url  	 : "<?php echo yii::$app->request->baseUrl.'/customer/activatemultipledelete' ?>",
					success  : function(response) {
													$.pjax.reload({container:'#activate-customer-grid',async:false});
													$(window).scrollTop($('.box').offset().top);

													if(response == 'success'){
														$("#success").css("display", "block");
														$('#success').html("Selected customers deleted successfully");

													}
												$( "#success" ).fadeOut(5000);
												$.pjax.reload({container: '#activate-customer-grid',async:false});

						}

					});
				}
			}else{
				alert("Please select the records to delete");
			}
		}
	</script>
