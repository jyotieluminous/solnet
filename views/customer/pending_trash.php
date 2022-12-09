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

$this->title = 'Pending Installation Recycle Bin';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['CustomerSearch']['order_received_date']))
{
	$strDate = $_GET['CustomerSearch']['order_received_date'];
}
else{
	$strDate = "";
}

//echo "<pre>";
//print_r($dataProvider->getModels()[0]);die;
?>
<p><?= Html::a('Reset Filters', ['customer/pendinginstallationtrash'], ['class' => 'btn btn-success']) ?>

</p>

 <div class="alert-success alert fade in" id="success" style="display:none"> </div>
 <?php Pjax::begin(['id'=>'installation-grid']); ?>

<div class="box box-default">
		<div class="box-body">
			<div class="horizontal-scroll">
				<div class="tbllanguage-form">
					<div class="customer-index">

					<?= GridView::widget([
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'id'=>'grid',
						'columns' => [
							['class' => 'yii\grid\SerialColumn'],

							[
							'attribute'=>'name',
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
								'value'=>'linkcustomerpackage.speed.speed_type'
							],
							'it_pic',

							[
								'attribute'=>'order_received_date',
								'value'=>function($data){
										return date_format(date_create($data->linkcustomerpackage->order_received_date),'d-m-Y');
								},
								'filter' => DatePicker::widget([
											'name' => 'CustomerSearch[order_received_date]',
											'value'=>$strDate,
											'template' => '{addon}{input}',
												'clientOptions' => [
													'autoclose' => true,
													'format' => 'yyyy-mm-dd'
												]
										])

							],
							[
								//'label'=>'Sales Person',
								'attribute'=>'sales_person',
								'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
								'value'=>function($data)
								{
									return $data->user->name;
								}
								//'value'=>'user.name'
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
							'additional_info',
							[
			                 'class'    => 'yii\grid\ActionColumn',
						    'header'   => 'Action',
						    'template' => ' {restore} {delete}',
						    'buttons'  => [


						    	'restore' => function ($url,$data) {
                       			 return Html::a('<i class="fa fa-check"></i>',['customer/restorepending','id'=>$data->customer_id],['data-confirm'=>"Are you sure you want to restore this record?"]);
                				 },

						        'delete' => function ($url, $data) {
						            return Html::a(
											'<span class="fa fa-trash"></span>', 
											['customer/deletepermanentpending','id'=>$data->customer_id],['data-confirm'=>"Are you sure you want to delete this record permanently?"]);
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
