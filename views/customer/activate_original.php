<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\widgets\Pjax;
use yii\bootstrap\Modal; 
use yii\helpers\Url; 


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
	$strDate = '';
}
?>
<p><?= Html::a('Reset Filters', ['customer/pendinginstallation'], ['class' => 'btn btn-success']) ?></p>

 <div class="alert-success alert fade in" id="success" style="display:none"> </div>
 <?php Pjax::begin(['id'=>'installation-grid']); ?>
 

      
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
						
							'name',
							'billing_address:ntext',
							'mobile_no',
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
								'attribute'=>'order_received_date',
								'value'=>function($data){
										return date_format(date_create($data->linkcustomerpackage->order_received_date),'Y-m-d');
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
							'user.name',
							
							[
			                 'class'    => 'yii\grid\ActionColumn',
						    'header'   => 'Action',
						    'template' => '{activate}',
						    'buttons'  => [

						        'activate' => function ($url, $data) {
						            $url = Url::to(['customer/activateinstallation', 'id' => $data->customer_id]);

						            return Html::a(' <span class=" 	glyphicon glyphicon-exclamation-sign" title = "Activate User" ></span> ', 'javascript:void(0)', ['class' => 'list', 'value' => $url]);
						        },
						    ],
						  ],
						],
					]); ?>
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