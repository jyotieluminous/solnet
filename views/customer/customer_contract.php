<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use yii\widgets\Pjax;
use yii\bootstrap\Modal; 
use yii\helpers\Url; 
use yii\widgets\ActiveForm;
use app\models\Speed;
use yii\helpers\ArrayHelper; 


/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contract Report';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_REQUEST['from_date'])){
	echo 'here';die;
    if ($_POST['from_date'] =='startDate' ){
    	 $selected = "selected=\"selected\"";
         echo "<option value=\"".$_POST['from_date']."\" $selected>Contract Starting From</option>\n ";
     }
    else{
        $selected = "selected=\"selected\"";
         echo "<option value=\"".$_POST['from_date']."\" $selected>Contract Ending From</option>\n ";
    }

}
?>
<p><?= Html::a('Reset Filters', ['customer/customercontract'], ['class' => 'btn btn-success']) ?></p>

 <div class="alert-success alert fade in" id="success" style="display:none"> </div>
 <?php $form = ActiveForm::begin(['method' => 'get']); ?>
    <div class="row">
        <div class="col-md-3 ">
	        <select name="from_date" class="form-control">
	        	<option value="startDate">Contract Starting From</option>
	        	<option value="endDate">Contract Ending From</option>
	        </select>
        </div>
        <div class="col-md-6">
	        <?php echo DateRangePicker::widget([
	        'name' => 'start_date',
	        'value' => '',
	        'nameTo' => 'end_date',
	        'valueTo' => '',
	        'clientOptions' => [
	                    'autoclose'=>true,
	                    'format' => 'yyyy-mm-dd'
	                ]
	         ]);?>
         </div>
    
         <div class="col-md-3">
             <?php echo Html::submitButton('Search',['class' => 'btn btn-success']) ?>
         </div>
    </div>  
<?php  ActiveForm::end(); ?>
<br>
 <?php Pjax::begin(['id'=>'contract-grid']); ?>

<div class="box box-default">
		<div class="box-body">
			<div class="horizontal-scroll"> 
				<div class="tbllanguage-form">
					<div class="customercontract-index">

					<?php echo GridView::widget([
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'id'=>'grid',
						'columns' => [
							['class' => 'yii\grid\SerialColumn'],
							
							'solnet_customer_id',
							
							
							[
							'attribute'=>'name',
							
							],
							
							[
								'attribute'=>'contractnumber',
								'label'=>'Contract Number',
								'value'=>'linkcustomerpackage.contract_number'
							],
							[
								//'label'=>'Package Title',
								'attribute'=>'customerpackage',
								'label'=>' Package',
								'value'=>'linkcustomerpackage.package.package_title'
							],

							[
								'attribute'=>'package_speed',
								'value'=>'linkcustomerpackage.package_speed'
							],

							[
								'attribute'=>'speed_type',
								'value'=>'speed.speed_type',
								'filter' => Html::activeDropDownList($searchModel, 'linkcustomepackage.fk_speed_id', ArrayHelper::map(Speed::find()->asArray()->all(), 'speed_id', 'speed_type'),['class'=>'form-control','prompt' => '']),
								'footer' =>'Total'
							],

							[
								'attribute'=>'package_price',
								'value'=>'linkcustomerpackage.package_price'
							],
							
							//'first_invoice_date',
							[
								'attribute'=>'first_invoice_date',
								
								'filter' => DatePicker::widget([
											'name' => 'CustomerSearch[first_invoice_date]',
											//'value'=>$strDate,
											'template' => '{addon}{input}',
												'clientOptions' => [
													'autoclose' => true,
													'format' => 'yyyy-mm-dd'
												]
										])
							],
							/*[
								'attribute'=>'contract_start_date',
								'value'=>'linkcustomerpackage.contract_start_date',
								'filter' => DatePicker::widget([
											'name' => 'CustomerSearch[contract_start_date]',
											//'value'=>$strDate,
											'template' => '{addon}{input}',
												'clientOptions' => [
													'autoclose' => true,
													'format' => 'yyyy-mm-dd'
												]
										])
							],
*/
							[
								//'attribute'=>'contractenddate',
							'label'=>'Contract End Date',
								'value'=>'linkcustomerpackage.contract_end_date',
								'filter' => DatePicker::widget([
											'name' => 'CustomerSearch[contract_end_date]',
											//'value'=>$strDate,
											'template' => '{addon}{input}',
												'clientOptions' => [
													'autoclose' => true,
													'format' => 'yyyy-mm-dd'
												]
										])
								 
							],

							

							[
								'attribute'=>'contractstatus ',
								'label'=>'Contract Status ',
								'value'=>'linkcustomerpackage.contract_status'
							],

							[
								'attribute'=>'Status ',
								'value'=>function($data){
									if($data->linkcustomerpackage->is_disconnected=='yes'){
									return 'Disconnected'; 
									}else{
										return 'Active';
									}
								}
							],
							
							/*[
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
								 
							],*/
							
							
							[
			                 'class'    => 'yii\grid\ActionColumn',
						    'header'   => 'Action',
						    'template' => '{update} {print}',
						    'buttons'  => [

						        'update' => function ($url, $data) {
						            $url = Url::to(['customer/editcontract', 'id' => $data->customer_id]);

						            return Html::a(' <span class=" 	glyphicon glyphicon-edit" title = "Edit" ></span> ', 'javascript:void(0)', ['class' => 'list', 'value' => $url]);
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