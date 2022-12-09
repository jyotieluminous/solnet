<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;
use kartik\export\ExportMenu;

$this->title = 'Statement Of Account';
$floatTotalAmount = 0;
$floatTotalUnpaid = 0;
$floatTotalPaid = 0;
$strCurrency = '';
if(!empty($dataProvider->getModels()))
{

	$model1 = $dataProvider->getModels();

 foreach ($model1 as $key => $val) {

	 	$floatTotalPaid += $val->paid_amount;
	 	$floatTotalUnpaid += $val->pending_amount;
	 	$floatTotalAmount += $val->total_invoice_amount;
    }
	$strCurrency = $val->linkcustomepackage->currency->currency;
}


$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

						[
							'attribute'=>'invoice_number',
							'value'=>'invoice_number',
						],
						[
							'attribute'=>'invoice_date',
							'value'=>function($data){
								return date('d-m-Y',strtotime($data->invoice_date));
							},
							'footer' =>'Total'
						],
						[
							'attribute'=>'total_invoice_amount',
							'value'=>  function($data){
								return number_format($data->total_invoice_amount,2);
							},
							'footer' => $strCurrency.' '.number_format($floatTotalAmount,2),
						],
						[
							'attribute'=>'paid_amount',
							'value'=> function($data){
								return number_format($data->paid_amount,2);
							},
							'footer' => $strCurrency.' '.number_format($floatTotalPaid,2)
						],
						[
							'label'=>'invoice Balance',
							'attribute'=>'pending_amount',
							'value'=> function($data){
								return number_format($data->pending_amount);
							},
							'footer' =>$strCurrency.' '.number_format($floatTotalUnpaid,2),
						],
						[
							'label'=>'Currency',
							'filter'=>false,
							'attribute'=>'pending_amount',
							'value'=>'linkcustomepackage.currency.currency',
						],
						[
							'attribute'=>'payment_term',
							'value'=> function($data){
										if(isset($data->linkcustomepackage->payment_term) && !empty($data->linkcustomepackage->payment_term))
										{
											return $data->linkcustomepackage->payment_term.' Day(s)';
										}else{
											return '-';
										}
									}
						],
						[
							'attribute'=>'due_date',
							'value'=>function($data){
								return date('d-m-Y',strtotime($data->due_date));
							}
						],
						[
							'attribute'=>'no_of_days_past_due',
							'value'=>function($data){ return $data->getnumberofdays($data->due_date); }

						],

];
?>
	<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form View-Customer-sec">

				<div class="row">
					<div class="col-md-12">
						<h2> Select Customer </h2>
						<!--<div class="col-md-4"></div> -->
						<div class="form-group required col-md-4">

						<label>Solnet Customer ID</label><br/>
							<?php
							if(isset($_GET['id']) && !empty($_GET['id']))
							{
								$intCustID = $_GET['id'];
							}
							else
							{
								$intCustID = '';
							}

								echo Select2::widget([
										'name' => 'solnet_customer_id',
										'data' => $data,
										'value'=> $intCustID,
										'options' => [
										'placeholder' => 'Select a Solnet Customer ID',
											'multiple' => false
										],
										'pluginOptions' => [
      										'allowClear' => true
								    	],
									'pluginEvents' => [

											"select2:selecting" => "function() {}",
											"select2:select" => "function() {
																				var customer_id = $('#w0').val();
																				window.location.href = '".yii::$app->request->baseUrl."/invoice/soa/'+customer_id;
											}",

										]
									]);
							?>

						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<?php
				$form = ActiveForm::begin(['action' => Yii::$app->request->BaseUrl.'/invoice/soa','method'=>'GET']);
				echo Html::hiddenInput('id', $intCustID ,['id'=>'customer_id']);
				?>
				<?php if(isset($_GET['id']) && !empty($_GET['id'])) { ?>

				<div class="row">
					<div class="col-md-12">
						<h2> Customer Details </h2>
						<div class="col-md-6">
							<label>Customer Name</label>
							<?php echo ucfirst($modelCust->name); ?>
						</div>
						<div class="col-md-6">
							<label>Package Title</label>
							<?php echo ucfirst($modelCust->linkcustomerpackage->package->package_title); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h2> Type of Statement Account </h2>

					<div class="col-md-4">
					<?php
						if(!empty($_GET['Customerinvoice']['soa_type']))
						{
							$model->soa_type = $_GET['Customerinvoice']['soa_type'];
						}
					?>
					<?php echo $form->field($model, 'soa_type')->radioList(array('due'=>'Generate SOA Past Due','date'=>'Generate SOA by Date
'),['itemOptions' => ['class' =>'code-type-radio']]); ?>
					</div>
					<div class="col-md-4"></div>
				</div>
				</div>
				<div class="col-md-4"></div>
				<div class="row" style="display: none;" id="datepicker">
					<div class="col-md-12">
						<div class="col-md-4"></div>
						<div class="col-md-4">
						<?php
							if(!empty($_GET['Customerinvoice']['soa_type']) && $_GET['Customerinvoice']['soa_type']=='date' ){
								$model->soa_date = $_GET['Customerinvoice']['soa_date'];
							}
						?>
							<?php echo $form->field($model, 'soa_date')->widget(
									DatePicker::className(), [
										'clientOptions' => [
											'autoclose' => true,
											'format' => 'dd-mm-yyyy',
										]
								]);?>
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<div class="col-md-12">
						<div class="col-md-4"></div>
							<div class="col-md-4">
								<div class="form-group">
									<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>
									<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
								</div>
							</div>
							<div class="col-md-4"></div>
				</div>

				<?php
					}
				?>
				<?php ActiveForm::end(); ?>
				<?php
				if(isset($_GET['id']) && $_GET['id'] && !empty($_GET['Customerinvoice']['soa_type']))
				{
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6">
						<?php
						 echo '<b>Export To Excel </b> :- '.ExportMenu::widget([
							'dataProvider' => $dataProvider,
							'columns' => $gridColumns,
							'filename'=>'customer_invoice'.date('Ymdhis')
						]);
						?>
						</div>
						<div class="col-md-6 text-right">
						<?php
							if(!empty($_GET['Customerinvoice']['soa_type'])){
							echo Html::a('<i class="fa fa-print"></i> Print', ['/invoice/soaprint','id'=>$_GET['id'],'type'=>$_GET['Customerinvoice']['soa_type'],'date'=>$_GET['Customerinvoice']['soa_date']], [
							'class'=>'btn btn-danger',
							'data-toggle'=>'tooltip',
							'title'=>'Will open the generated PDF file in a new window',
							'target'=>'_blank'
						]);
							}
				?>
						</div>
					</div>
					<div class="col-md-12">
						<?php echo kartik\grid\GridView::widget([
												'dataProvider' => $dataProvider,
												'filterModel' => $searchModel,
												'showFooter' =>true,
												'columns' => $gridColumns,
											]);
										?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>



	</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		var strSoaType = $("input[name='Customerinvoice[soa_type]']:checked").val();
		if(strSoaType=='date')
		{
			$('#datepicker').show();
		}
		$("input:radio[name='Customerinvoice[soa_type]']").change(function(){
			if (this.value == 'date') {
				$('#datepicker').show();
			}
			else if (this.value == 'due') {
				$('#datepicker').hide();
			}
		});

	});
</script>
