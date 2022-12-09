<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customers';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['CustomerSearch']['order_received_date']))
{
	$strDate = $_GET['CustomerSearch']['order_received_date'];
}
else{
	$strDate = '';
}

?>
<p>
<?php echo Html::a('Add Customer', ['create'], ['class' => 'btn btn-primary']) ; ?>
<?php echo '&nbsp;'.Html::a('Reset Filters', ['/customer/index'], ['class' => 'btn btn-success']) ?>
<?php echo '&nbsp;'.Html::a('&nbsp;&nbsp;Delete Selected','javascript:void(0)',['class' => 'btn btn-danger','onClick'=>'javascript:deleteAll()']) ?>
 <?php echo '&nbsp;'.Html::a('Trashed Records', ['/customer/trashed'], ['class' => 'btn btn-warning']) ?>
 </p>

 <div class="alert-success alert fade in" id="success" style="display:none"> </div>
 <?php Pjax::begin(['id'=>'customer-grid']); ?>
 
<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form">
				<div class="customer-index">

					<?= GridView::widget([
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'id'=>'grid',

						'columns' => [
							['class' => 'yii\grid\SerialColumn'],
							['class' => 'yii\grid\CheckboxColumn'],
							
							'name',
							
							
							[
								'attribute'=>'user_type',
								'filter'=>['home'=>'Home','corporate'=>'Corporate'],
								
							],
							'billing_address:ntext',
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
								'attribute'=>'installation_address',
								'value'=>'linkcustomerpackage.installation_address'
							],
							
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
													'format' => 'dd-mm-yyyy'
												]
										])
								 
							],
							[
								'label'=>'Sales Person',
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
							
							'additional_info',
							[
								'class' => 'yii\grid\ActionColumn',
								'header'=>'Action',
								'options'=>['width'=>100],
								'template'=>'{view} {update} {delete}', //{link}
								'buttons' => [
								/*'link' => function ($url,$model,$key) {
										return Html::a('<i class="fa fa-file-pdf-o"></i>',['/customer/pdf','id'=>$model->customer_id],['target'=>'_blank']);
								},*/
							],
								//'class' => 'yii\grid\ActionColumn'
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