<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : customer_contract.php
# Created on : 28th June 2017 by Swati Jadhav.
# Update on  : 28th June 2017 by Swati Jadhav.
# Purpose : List all diconnected customers
############################################################################################
*/

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use yii\widgets\Pjax;
use yii\bootstrap\Modal; 
use yii\helpers\Url; 
use yii\widgets\ActiveForm;
use app\models\Speed;
use app\models\Currency;
use app\models\Customer;
use yii\helpers\ArrayHelper; 
use app\models\Linkcustomepackage;
use kartik\export\ExportMenu;


/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contract Report';
$this->params['breadcrumbs'][] = $this->title;
$strStartDate=$strEndDate=$strFromDate='';
if(isset($_GET['start_date']) && isset($_GET['end_date']))
{
	 $strStartDate 	= $_GET['start_date'];
	 $strEndDate 	= $_GET['end_date'];
	 $strFromDate 	= $_GET['from_date'];
}
// else{
// 	$strStartDate = date('Y-d-m');
//     $strEndDate	  = date('Y-d-m');
// }
// echo "<pre>";
// print_r($strStartDate);die;

?>
<?php $gridColumns = [
						
						[
						'attribute'=>'solnet_customer_id',
						'value'=>'customer.solnet_customer_id'
						],
						
						
						[
						'attribute'=>'name',
						'value'=>'customer.name'
						],

						[
							'attribute'=>'user_type',
							'value'=>'customer.user_type',

							
						],
						
						'contract_number',

						[
							'attribute'=>'package_title',
							//'label'=>' Package Title',
							'value'=>'package.package_title'
						],
						[
							'attribute'=>'sales_person',
							'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
							'value'=>function($data)
							{
								return $data->customer->user->name;
							}
							//'value'=>'user.user_name'
						],
						[
							'attribute'=>'agent_name',
							'value'=>function($data)
							{
								if($data->customer->agent_name!=null || $data->customer->agent_name!="")
									return $data->customer->agent_name;
								else
									return "-";
							}
						],
						[
							'attribute'=>'speed_type',
							'value'=>'speed.speed_type',
							'filter' => Html::activeDropDownList($searchModel, 'fk_speed_id', ArrayHelper::map(Speed::find()->asArray()->all(), 'speed_id', 'speed_type'),['class'=>'form-control','prompt' => '']),
							'footer'=>'Total'
						],

						[
					         'attribute' => 'package_price',
					         'value' => function($data){
								return number_format($data->package_price,2);
							 },
					         'footer' => strip_tags(Linkcustomepackage::getContractTotal()),
		               
   						],
							[
							'attribute'=>'currency',
							'value'=>'currency.currency',
							'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'fk_currency_id'),['class'=>'form-control','prompt' => '']),
						],
						
						[
							'attribute'=>'first_invoice_date',
							'value'=> function($data){

								if($data->customer->first_invoice_date=='0000-00-00 00:00:00' || $data->customer->first_invoice_date==Null || $data->customer->first_invoice_date=="")
								{
									return date("d-m-Y",strtotime($data->invoice_start_date));
								}
								else
								{
									return date("d-m-Y",strtotime($data->customer->first_invoice_date));
								}
                	 		},
							
							'filter' => DatePicker::widget([
										'name' => 'LinkcustomepackageSearch[first_invoice_date]',
										//'value'=>$strDate,
										'template' => '{addon}{input}',
											'clientOptions' => [
												'autoclose' => true,
												'format' => 'dd-mm-yyyy'
											]
									])
						],
						[
							'attribute'=>'contract_start_date',
							'filter' => DatePicker::widget([
										'name' => 'LinkcustomepackageSearch[contract_start_date]',
										//'value'=>$strDate,
										'template' => '{addon}{input}',
											'clientOptions' => [
												'autoclose' => true,
												'format' => 'dd-mm-yyyy'
											]
									])
						],

						[
							'attribute'=>'contract_end_date',
						
							'filter' => DatePicker::widget([
										'name' => 'LinkcustomepackageSearch[contract_end_date]',
										//'value'=>$strDate,
										'template' => '{addon}{input}',
											'clientOptions' => [
												'autoclose' => true,
												'format' => 'dd-mm-yyyy'
											]
									])
							 
						],
						[
							'attribute'=>'Remaining months',
							'value'=>function($data){
								if($data->payment_type != 'bulk'){
								 	$date_now = date("Y-m-d");
										if($date_now < $data->contract_end_date && $date_now != $data->contract_end_date){
											if($data->contract_end_date != $data->contract_start_date){
												$strNowDate 		 = strtotime(date('Y-m-d'));
												$strContEndDateDate  = strtotime($data->contract_end_date);

												$year1 = date('Y', $strNowDate);
												$year2 = date('Y', $strContEndDateDate);

												$month1 = date('m', $strNowDate);
												$month2 = date('m', $strContEndDateDate);

												$strMonthDiffernce = (($year2 - $year1) * 12) + ($month2 - $month1);
												
												return $strMonthDiffernce;
											}
											else
											{
												return '-';
											}
										}else{
											return '-';
										}
					                }
					                else
					                {
					                	return '-';
					                }
				                },
							],
							[
								'attribute'=>'Remaining Unbill amount',
								'value'=>function($data){
									if($data->payment_type != 'bulk'){
									 	$date_now = date("Y-m-d");
										if($date_now < $data->contract_end_date && $date_now != $data->contract_end_date){
											if($data->contract_end_date != $data->contract_start_date){
												$strNowDate 		 = strtotime(date('Y-m-d'));
												$strContEndDateDate  = strtotime($data->contract_end_date);

												$year1 = date('Y', $strNowDate);
												$year2 = date('Y', $strContEndDateDate);

												$month1 = date('m', $strNowDate);
												$month2 = date('m', $strContEndDateDate);

												$strMonthDiffernce = (($year2 - $year1) * 12) + ($month2 - $month1);
												
							                    $intUnbilledAmount 	= $data->package_price*$strMonthDiffernce;
							                    return number_format($intUnbilledAmount,2);
											}
											else
											{
												return '-';
											}
										}else{
											return '-';
										}
					                }
					                else
					                {
					                	return '-';
					                }
				                },
				                'footer' => strip_tags(Linkcustomepackage::getUnbilledTotal()),
							],
							[
							'attribute'=>'Total Booking amount',
							'value'=>function($data){
								if($data->payment_type != 'bulk'){	
									if($data->contract_end_date != $data->contract_start_date){
										$strContStartDateDate   = strtotime($data->contract_start_date);
										$strContEndDateDate 	= strtotime($data->contract_end_date);
										$strOtherServiceFee 	= $data->other_service_fee;
										$strInstallationFee 	= $data->installation_fee;

										$year2 = date('Y', $strContStartDateDate);
										$year1 = date('Y', $strContEndDateDate);

										$month2 = date('m', $strContStartDateDate);
										$month1 = date('m', $strContEndDateDate);

										$strMonthDiffernce = (($year1 - $year2 ) * 12) + ($month1 - $month2);
										
										//echo '<pre>';print_r($strMonthDiffernce);echo '<pre><br/>';die;

					                    $intBookedAmount = $data->package_price*($strMonthDiffernce)+$strOtherServiceFee+$strInstallationFee;
					                    return number_format($intBookedAmount,2);
					                }
					                else
					                {
				                		return '-';
				                	}
				                }
				                else
				                {
				                	return '-';
				                }
			                },
			                'footer' => strip_tags(Linkcustomepackage::getBookedAmountTotal()),
						],

						'contract_status',

						
							
					] ?>

<p><?php echo Html::a('Reset Filters', ['customerpackage/customercontract'], ['class' => 'btn btn-success']) ;
	
	// Renders a export dropdown menu
 	echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'filename'=>'Customer_contract'.date('Ymdhis')
		]);
?>
</p>

 <div class="alert-success alert fade in" id="success" style="display:none"> </div>
 <?php $form = ActiveForm::begin(['method' => 'get']); ?>
    <div class="row">
        <div class="col-md-3 ">
	        <select name="from_date" class="form-control">
	        	<option value="startDate" <?php if(isset($strFromDate)){if($strFromDate=='startDate'){echo 'selected';}} ?>>Contract Starting From</option>
	        	<option value="endDate" <?php if(isset($strFromDate)){if($strFromDate=='endDate'){echo 'selected';} }?>>Contract Ending From</option>
	        </select>
        </div>
        <div class="col-md-6">
	        <?php echo DateRangePicker::widget([
	        'name' => 'start_date',
	        'value' => $strStartDate,
	        'nameTo' => 'end_date',
	        'valueTo' => $strEndDate,
	        'clientOptions' => [
	                    'autoclose'=>true,
	                    'format' => 'dd-mm-yyyy'
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
						'showFooter' => true,
						'columns' => [
							['class' => 'yii\grid\SerialColumn'],
							
							[
							'attribute'=>'solnet_customer_id',
							'value'=>'customer.solnet_customer_id',
							],
														
							[
							'attribute'=>'name',
							'value'=>'customer.name',
							
							],
							[
								'attribute'=>'state',
								'value'=>function($data)
								{
									return $data->customer->state->state;
								},
								
							],
							[
							'attribute'=>'user_type',
							'value'=>'customer.user_type',
							'filter'=>[''=>'All','home'=>'Home','corporate'=>'Corporate']
							],
							
							
							[
								'attribute'=>'contract_number',
								'value'=>function($data){
									if(empty($data->contract_number)){
										return '--';
									}
									else{
										return $data->contract_number;
									}
								}
							],
							[
								'attribute'=>'sales_person',
								'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
								'value'=>function($data)
								{
									return $data->customer->user->name;
								}
								//'value'=>'user.user_name'
							],
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->customer->agent_name!=null || $data->customer->agent_name!="")
										return $data->customer->agent_name;
									else
										return "-";
								}
							],
							[
							'attribute'=>'package_title',
							'value'=>'package.package_title',
							'label'=>'Package Title',
								
							],
							'package_speed',

							[
								'attribute'=>'speed_type',
								'value'=>'speed.speed_type',
								'filter' => Html::activeDropDownList($searchModel, 'fk_speed_id', ArrayHelper::map(Speed::find()->asArray()->all(), 'speed_id', 'speed_type'),['class'=>'form-control','prompt' => '']),
								'footer' =>'Total'
							],

							[
					         'attribute' => 'package_price',
					         'value' => function($data){
								return number_format($data->package_price,2);
							 },
					         'footer' => $strCurrencyId=Linkcustomepackage::getContractTotal(),
			               
       						],
       						[
								'attribute'=>'currency',
								'value'=>'currency.currency',
								'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' => '']),
							],
														
							[
								'label'=>'First Invoice Date',
								'value'=>function($data){

									if($data->customer->first_invoice_date=='0000-00-00 00:00:00' || $data->customer->first_invoice_date==Null || $data->customer->first_invoice_date=="")
									{
										return date("d-m-Y",strtotime($data->invoice_start_date));
									}
									else
									{
										return date("d-m-Y",strtotime($data->customer->first_invoice_date));
									}
				                  },
								'filter' => DatePicker::widget([
											'name' => 'LinkcustomepackageSearch[first_invoice_date]',
											//'value'=>$strDate,
											'template' => '{addon}{input}',
												'clientOptions' => [
													'autoclose' => true,
													'format' => 'dd-mm-yyyy'
												]
										])
							],

							[
								'attribute'=>'contract_start_date',
								'value'=>function($data){
				                    return date('d-m-Y',strtotime($data->contract_start_date));
				                  },
								'filter' => DatePicker::widget([
											'name' => 'LinkcustomepackageSearch[contract_start_date]',
											//'value'=>$strDate,
											'template' => '{addon}{input}',
												'clientOptions' => [
													'autoclose' => true,
													'format' => 'dd-mm-yyyy'
												]
										])
							],
							[
								'attribute'=>'contract_end_date',
								'value'=>function($data){
				                    return date('d-m-Y',strtotime($data->contract_end_date));
				                  },
							
								'filter' => DatePicker::widget([
											'name' => 'LinkcustomepackageSearch[contract_end_date]',
											//'value'=>$strDate,
											'template' => '{addon}{input}',
												'clientOptions' => [
													'autoclose' => true,
													'format' => 'dd-mm-yyyy'
												]
										])
								 
							],
							[
								'attribute'=>'Remaining months',
								'value'=>function($data){
									if($data->payment_type != 'bulk'){
									 	$date_now = date("Y-m-d");
										if($date_now < $data->contract_end_date && $date_now != $data->contract_end_date){
											if($data->contract_end_date != $data->contract_start_date){
												$strNowDate 		 = strtotime(date('Y-m-d'));
												$strContEndDateDate  = strtotime($data->contract_end_date);

												$year1 = date('Y', $strNowDate);
												$year2 = date('Y', $strContEndDateDate);

												$month1 = date('m', $strNowDate);
												$month2 = date('m', $strContEndDateDate);

												$strMonthDiffernce = (($year2 - $year1) * 12) + ($month2 - $month1);
												
												return $strMonthDiffernce;
											}
											else
											{
												return '-';
											}
										}else{
											return 0;
										}
					                }
					                else
					                {
					                	return '-';
					                }
				                },
							],
							[
								'attribute'=>'Remaining Unbill amount',
								'value'=>function($data){
									if($data->payment_type != 'bulk'){
									 	$date_now = date("Y-m-d");
										if($date_now < $data->contract_end_date && $date_now != $data->contract_end_date){
											if($data->contract_end_date != $data->contract_start_date){
												$strNowDate 		 = strtotime(date('Y-m-d'));
												$strContEndDateDate  = strtotime($data->contract_end_date);

												$year1 = date('Y', $strNowDate);
												$year2 = date('Y', $strContEndDateDate);

												$month1 = date('m', $strNowDate);
												$month2 = date('m', $strContEndDateDate);

												$strMonthDiffernce = (($year2 - $year1) * 12) + ($month2 - $month1);
												
							                    $intUnbilledAmount 	= $data->package_price*$strMonthDiffernce;
							                    return number_format($intUnbilledAmount,2);
											}
											else
											{
												return '-';
											}
										}else{
											return 0;
										}
					                }
					                else
					                {
					                	return '-';
					                }
				                },
				                'footer' => '<b>'.strip_tags(Linkcustomepackage::getUnbilledTotal()).'</b>',
							],
							[
								'attribute'=>'Total Booking amount',
								'value'=>function($data){
									if($data->payment_type != 'bulk'){	
										if($data->contract_end_date != $data->contract_start_date){
											$strContStartDateDate   = strtotime($data->contract_start_date);
											$strContEndDateDate 	= strtotime($data->contract_end_date);
											$strOtherServiceFee 	= $data->other_service_fee;
											$strInstallationFee 	= $data->installation_fee;

											$year2 = date('Y', $strContStartDateDate);
											$year1 = date('Y', $strContEndDateDate);

											$month2 = date('m', $strContStartDateDate);
											$month1 = date('m', $strContEndDateDate);

											$strMonthDiffernce = (($year1 - $year2 ) * 12) + ($month1 - $month2);
											
											//echo '<pre>';print_r($strMonthDiffernce+1);echo '<pre><br/>';die;

						                    $intBookedAmount = $data->package_price*($strMonthDiffernce+1)+$strOtherServiceFee+$strInstallationFee;
						                    return number_format($intBookedAmount,2);
						                }
						                else
						                {
					                		return '-';
					                	}
					                }
					                else
					                {
					                	return '-';
					                }
				                },
				                'footer' => '<b>'.strip_tags(Linkcustomepackage::getBookedAmountTotal()).'</b>',
							],
							[
			                 'attribute'=>'contract_status',
			                 'filter'=>[''=>'All','sent'=>'Sent','not_sent' => 'Not sent','returned'=>'Returned','no_contract'=>'No contract'],

			                 'value'=>function($data){

									if(empty($data->contract_status)){

										return '--';
									}
									else{
										return $data->contract_status;
									}
								},
			                ],
			                [
			                	'attribute'=>'is_disconnected',
			                	'label'=>'Is Disconnected',
			                	'filter'=>[''=>'All','yes'=>'Yes','no'=>'No'],
			                	'format'=>'raw',
			                	'value' =>'is_disconnected',
			                ],
			                [
								 'attribute'=>'status',
								 'label'=>'Status',
								 'filter'=>[''=>'All','active'=>'Active','inactive'=>'Inactive'],
								 'format'=>'raw',
								 'value' => function($data){
									return ucwords($data->customer->status);
									
								 }
							],
							[
			                 'class'    => 'yii\grid\ActionColumn',
						    'header'   => 'Action',
						    'template' => '{view} {update} {print}',
						    'buttons'  => [

						    	'view'=>/*function ($url, $data) {
						            $url = Url::to(['customerpackage/contractview', 'id' => $data->cust_pck_id]);

						            return Html::a(' <span class=" 	glyphicon glyphicon-eye-open" title = "View" ></span> ', 'javascript:void(0)', [ 'value' => $url]);
						        	},*/function ($url, $data) {
						           
						            return  Html::a('<i class="fa fa-eye"></i>', ['/customerpackage/contractview','id'=>$data->cust_pck_id], [   
			                            //'data-toggle'=>'tooltip', 
			                            'title'=>'View',
			                            //'target'=>'_blank',
			                            //'data-pjax'=>'0'
			                       	 ]);
						        },

						        'update' => function ($url, $data) {
						            $url = Url::to(['customerpackage/editcontract', 'id' => $data->cust_pck_id]);

						            return Html::a(' <span class=" 	glyphicon glyphicon-edit" title = "Edit" ></span> ', 'javascript:void(0)', ['class' => 'list', 'value' => $url]);
						        	},

						        'print' => function ($url, $data) {
						           
						            return  Html::a('<i class="fa fa-print"></i>', ['/customerpackage/printcontract','id'=>$data->cust_pck_id], [   
			                            //'data-toggle'=>'tooltip', 
			                            'title'=>'Print',
			                            'target'=>'_blank',
			                            'data-pjax'=>'0'
			                       	 ]);
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
     //'headerOptions' => ['id' => 'modalHeader'],
    'header' => '<h3 class="text-center">Customer Contract</h3>',
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