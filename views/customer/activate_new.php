<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pending Installation';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['CustomerSearch']['order_received_date']))
{
	$strDate = $_GET['CustomerSearch']['order_received_date'];
}
else{
	$strDate = "";
}

$floatTotBalance 		= 0;
$floatServiceBalance 	= 0;
$floatTotalPackagePrice = 0;

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

//echo "<pre>";
//print_r($dataProvider->getModels()[0]);die;
?>
<p><?= Html::a('Reset Filters', ['customer/pendinginstallation'], ['class' => 'btn btn-success']) ?>
<?php echo '&nbsp;'.Html::a('Trashed Records', ['customer/pendinginstallationtrash'], ['class' => 'btn btn-warning']) ?>
</p>

 <div class="alert-success alert fade in" id="success" style="display:none"> </div>
 <?php Pjax::begin(['id'=>'installation-grid']); ?>

<div class="box box-default">
		<div class="box-body">
			<div class="horizontal-scroll">
				<?php
				//if role is not NOC then show the total
				if(Yii::$app->user->identity->fk_role_id =='1')
				{
				?>
				<div class="row">
					<div class="col-md-12 text-right">
						<label>Grand Total Package Price : <?php echo number_format($intTotal,2); ?></label>
					</div>
				</div>
				<?php } ?>
				
				<div class="tbllanguage-form">
					<div class="customer-index">
					<?= GridView::widget([
						'dataProvider'  => $dataProvider,
						'filterModel'   => $searchModel,
						'id'			=> 'grid',
        				'showFooter'	=> true,
						'columns' => [
							['class' => 'yii\grid\SerialColumn'],
							[
								'attribute' => 'name',
								//'options'=>['width'=>200],
							],
								'billing_address:ntext',
							[
								'attribute'=>'installation_address',
								'value'=>'linkcustomerpackage.installation_address'
							],
								'mobile_no',
								'state.state',
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
								'footer'=>'<b>Total</b>'
							],
							[
								'attribute'=>'package_price',
								'value' => function($data){
									return number_format($data->linkcustomerpackage->package_price,2);
								},
								'footer' => $floatTotBalance
							],
							[
								'attribute'=>'fiber_installed',
								'filter'=>array(''=>'All','power'=>'Power','dig'=>'Wireless','FTTH'=>'FTTH'),
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
							[
								'attribute'=>'order_received_date',
								'value'=>function($data){
									return date_format(date_create($data->linkcustomerpackage->order_received_date),'d-m-Y');
								},
								'filter' => DatePicker::widget([
									'name' 		=> 'CustomerSearch[order_received_date]',
									'value'		=> $strDate,
									'template' 	=> '{addon}{input}',
									'clientOptions' => [
										'autoclose' => true,
										'format' 	=> 'yyyy-mm-dd'
									]
								])
							],
							[
								//'label'=>'Sales Person',
								'attribute'	=> 'sales_person',
								'filter'	=> Arrayhelper::map($user, 'user_id', 'name'),
								'value'		=> function($data)
								{
									return $data->user->name;
								}
								//'value'=>'user.name'
							],
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->agent_name!=null || $data->agent_name!=""){
										return $data->agent_name;
									}else{
										return "-";
									}
								}
							],
							[
								'attribute'=>'remarks',
								'value'=>function($data)
								{
									if($data->remarks!=null || $data->remarks!="")
										return $data->remarks;
									else
										return "-";
								}
							],
								'additional_info',
							[
			                 	'class'    => 'yii\grid\ActionColumn',
						    	'header'   => 'Action',
						    	'template' => '{activate} {view} {print} {edit} {trash}',
						    	'buttons'  => [
							    	'view' => function ($url,$data) {
	                       			 	return Html::a('<i class="fa fa-eye"></i>',['customer/installationview','id'=>$data->customer_id]);
	                				},
							        'activate' => function ($url, $data) {
							            $url = Url::to(['customer/activateinstallation', 'id' => $data->customer_id]);
							            return Html::a(' <span class=" 	glyphicon glyphicon-exclamation-sign" title = "Activate User" ></span> ', 'javascript:void(0)', ['class' => 'list', 'value' => $url]);
							        },
							        'print' => function ($url, $data) {
				                        return  Html::a('<i class="fa fa-print"></i>', ['/customer/installationprint','id'=>$data->customer_id], ['title'=>'Print','target'=>'_blank','data-pjax'=>'0']);
				                    },
									'edit' => function ($url, $data) {
							            $url = Url::to(['customer/editdetails', 'id' => $data->customer_id]);
										return Html::a('<i class="fa fa-edit"></i>',[$url]);
							        },
									'trash' => function ($url, $data) {
										return Html::a('<span class="fa fa-trash"></span>',['customer/deletepending','id'=>$data->customer_id],['data-confirm'=>"Are you sure you want to delete this record ?"]);
							        },
							    ],
						  	],
						],
					]); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php Pjax::end(); ?>
<?php
Modal::begin([
    'id'     => "modal",
    'header' => '<h3 class="text-center">Activate Customer</h3>',
]);
echo "<div id='modalContent'></div>";
Modal::end();

Modal::begin([
    'id'     => "modal_details",
    'header' => '<h3 class="text-center">Edit Details</h3>',
]);

echo "<div id='modalContentDet'></div>";
Modal::end();

$this->registerJs(
    "$(document).on('ready pjax:success', function() {
            $('.list').click(function(e){
               e.preventDefault(); //for prevent default behavior of <a> tag.
               $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));			   
           });
        });
    ");
    
$this->registerJs(
    "$(document).on('ready pjax:success', function() {
            $('.list2').click(function(e){
               e.preventDefault(); //for prevent default behavior of <a> tag.
			   $('#modal_details').modal('show').find('#modalContentDet').load($(this).attr('value'));
           });
        });
    ");
?>
<script type="text/javascript">
	function deleteAll(){
		var ids = $('#grid').yiiGridView('getSelectedRows');
		if(ids.length >  0){
			if(confirm("Are you sure you want to delete the selected customers?")){
				$.ajax({
					type     : 'POST',
					data     : { ids : ids },
					cache    : false,
					url  	 : "<?php echo yii::$app->request->baseUrl.'/customer/deletemultiple' ?>",
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
