<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DateRangePicker;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Currency;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Revenue Report';
$this->params['breadcrumbs'][] = $this->title;
$strEndDate = '';
if(isset($_GET['CustomerinvoiceSearch']['start_date']))
{
	$strDate = $_GET['CustomerinvoiceSearch']['start_date'];
}
else{
	$strDate = '';
}
if(isset($_GET['CustomerinvoiceSearch']['end_date']))
{
	$strEndDate = $_GET['CustomerinvoiceSearch']['end_date'];
}
else{
	$strEndDate = '';
}
if(isset($_GET['CustomerinvoiceSearch']['currency_id']))
{
	$intCurrency = $_GET['CustomerinvoiceSearch']['currency_id'];
}
else{
	$intCurrency = 1;
}
if($intCurrency==1){
	$strCurrency = ' IDR';
}elseif($intCurrency==2){
	$strCurrency = ' SGD';
}elseif($intCurrency==3)
{
	$strCurrency = ' USD';
}
$floatTotalUnpaid = 0;
$floatTotalPaid = 0;
$floatTotalInstallation = 0;
$floatTotalOther    = 0;
$floatRecurring = 0;
$floatTotalPackegePrice    = 0;
$totalInvoice = 0;

if(!empty($dataProvider->getModels())) 
{
	$model = $dataProvider->getModels();
	
 	foreach ($model as $key => $val) {
		$floatTotalUnpaid += $val->UnpaidTotal;
		$floatTotalPaid += $val->PaidTotal;
		$floatTotalInstallation += $val->InstallationFee;
		$floatTotalOther += $val->OtherServiceFee;
		/*$floatRecurring = ($val->InvoiceTotal-($val->InstallationFee + $val->OtherServiceFee));
		$floatTotalRecurring += $floatRecurring;*/
		$floatTotalPackegePrice += $val->CurrentInvoiceAmount;
		$totalInvoice += $val->InvoiceTotal;
    }
}
$strActive1 = '';
$strActive2 = '';
$strActive3 = '';
if(isset($_GET['tab']) && !empty($_GET['tab']))
{
	if($_GET['tab']=='package'){
		$strActive2 = 'active';
		
	}elseif($_GET['tab']=='bank'){
		$strActive3 = 'active';
	}else{
		$strActive1 = 'active';
	}
}else{
	$strActive1 = 'active';
}

/*echo "<pre>";
print_r($recurring);die;*/
$gridColumns = [
					['class' => 'yii\grid\SerialColumn'],
					[
						'label'=>'Package Title',
						'attribute'=>'package_id',
						'filter'=>$packageList,
						'value'=>'linkcustomepackage.package.package_title',
						'format'=>'raw',
						'footer'=>'Total'
					],
					[
						'attribute'=> 'UnpaidTotal',
						'value'=>function($data){
							return number_format($data->UnpaidTotal,2);
						},
						'format'=>'raw',
						'footer' =>$strCurrency.' '.number_format($floatTotalUnpaid,2),
					],
					[
						'attribute'=> 'PaidTotal',
						'value'=>function($data){
							return number_format($data->PaidTotal,2);
						},
						'format'=>'raw',
						'footer' => $strCurrency.' '.number_format($floatTotalPaid,2),
					],
					
					[
						'attribute'=> 'InstallationFee',
						'value'=>function($data){
							return number_format($data->InstallationFee,2);
						},
						'format'=>'raw',
						'footer' =>$strCurrency.' '.number_format($floatTotalInstallation,2),
					],
					[
						'attribute'=> 'OtherServiceFee',
						'value'=>function($data){
							return number_format($data->OtherServiceFee,2);
						},
						'format'=>'raw',
						'footer' => $strCurrency.' '.number_format($floatTotalOther,2),
					],
					[
						'attribute'=> 'monthly_billing_generator',
						//'label'=>'Monthly Billing Generator',
						'label'=>'Total Package Price',
						'value'=>function($data){
							return number_format($data->CurrentInvoiceAmount,2);
						},
						'format'=>'raw',
						'footer' =>$strCurrency.' '. number_format($floatTotalPackegePrice,2),
					],
					[
						'attribute'=> 'total_revenue',
						//'label'=>'Monthly Billing Generator',
						'label'=>'Total Invoice Out(Paid+Unpaid)',
						'value'=>function($data){
							return number_format($data->InvoiceTotal,2);
						},
						'format'=>'raw',
						'footer' =>$strCurrency.' '. number_format($totalInvoice,2),
					],
					
				];
	

?>
<p>

<?php echo '&nbsp;'.Html::a('Reset Filters', ['/report/revenue'], ['class' => 'btn btn-success']) ?>
 
 </p>
 <?php
	// Renders a export dropdown menu
 echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
	'filename'=>'revenue_report_'.date('Ymdhis')
]);
?>

 <div class="alert-success alert fade in" id="success" style="display:none"> </div>
 <div class="box box-default">
 <div class="box-body">
 <div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
              <li class="<?php echo $strActive1; ?>"><a href="#tab_1" data-toggle="tab">Revenue Report</a></li>
              <li class="<?php echo $strActive2; ?>"><a href="#tab_2" data-toggle="tab">Recurring Revenue Report</a></li>
              
            </ul>
            </div>
            <div class="tab-content">
              <div class="tab-pane <?php echo $strActive1; ?>" id="tab_1">

			<div class="tbllanguage-form">
				<div class="customer-index">
					<?php $form = ActiveForm::begin([
				'action' => ['report/revenue'],
				'method' =>'GET',
				'options' => [
                	'class' => 'form-horizontal form-bordered'
                 ],
                'fieldConfig' => [
                	'template' => '{label}<div class="col-sm-6">{input}</div>{error}',
                    'labelOptions' => ['class' => 'col-sm-2 control-label']
                 ]
             ]); 
		?>
<div class="row">
	<div class="col-md-12">
		
		<div class="col-md-3"><label>Select Invoice Date Range :-</label></div>
		<div class="col-md-3">
			<?php echo DateRangePicker::widget([
				'name' => 'CustomerinvoiceSearch[start_date]',
				'value' => $strDate,
				'nameTo' => 'CustomerinvoiceSearch[end_date]',
				'valueTo' => $strEndDate,
				'clientOptions' => [
										'autoclose' => true,
										'format' => 'dd-mm-yyyy'
									]
			]);?>
		</div>
		<div class="col-md-2">
			<label>Select Currency :-</label>
		</div>
        <div class="col-md-2">
            <?php echo Html::dropDownList("CustomerinvoiceSearch[currency_id]", $intCurrency,ArrayHelper::map(Currency::find()->all(), 'currency_id','currency'),array(
                      'class'=>'form-control',
                       )); ?>
		</div>
		<div class="col-md-2"><?php echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);?></div>
	</div>
</div>
 <?php ActiveForm::end();?>
					<?php echo GridView::widget([
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'id'=>'grid',
						'showFooter' =>true,
						'columns' => $gridColumns,
						'showFooter' => true,
					]); ?>
				</div>
			</div>
			<div class="row">
			    <div class="col-xs-12">
				<div class="table-responsive">
				      	<table  border="0" cellspacing="0" cellpadding="10" class="table table-bordered" style="width:50%; float:left;margin-right: 40px;">
						<tr>
							 <td align="left" ><strong>Package Title</strong></td>
							 <td align="left" ><strong>Recurring amount</strong></td>
						</tr>
						<?php 
							if($recurring)
							{
								$total = 0;
								foreach($recurring as $key=>$value)
								{
									$total = $total+$value['Recurring'];
						?>
						<tr>
							 <td align="left" ><?php echo $value['package_title'];?></td>
							 <td align="left" ><?php echo number_format($value['Recurring'],2);?></td>
						</tr>
						<?php			
							}
						?>
						<tr>
							 <td align="left" >Service Charge</td>
							 <td align="left" ><?php echo number_format($intTotalServiceChage,2);?></td>
						</tr>
						<tfoot>
                        <tr>
                            <td ><b>Total</b></td>
                            <td ><b><?php $intGrandTotal = $total + $intTotalServiceChage;
                            	echo number_format($intGrandTotal,2);?></b>
                            </td>
		                  </tr>
		                  </tfoot>
		                  <?php			
								
							}
						?>
						</table>
				</div>
				</div>
				</div>
		
	
	</div>
	<?php /*echo "<pre>";
	print_r($recurringState);die;*/
	?>
	<div class="tab-pane <?php echo $strActive2; ?>" id="tab_2">
	<div class="row">
		<div class="col-xs-12">
			<div class="table-responsive">
				<div class="table-responsive">
				      	<table  border="0" cellspacing="0" cellpadding="10" class="table table-bordered" >
						<tr>
							 <td align="left" ><strong>Package Title</strong></td>
							 <td align="center" colspan="10"><strong>Recurring amount</strong></td>
						</tr>
						<tr>
						<td></td>
							<?php foreach($stateList as $key=>$value)
							{
								echo "<td>";
								echo "<b>".$value."</b>";
								echo "</td>";
							}
							
							?>
						</tr>

							<?php 
							$total = 0;
								foreach($recurringState as $keyRec=>$valueRec)
								{
									/*if($keyRec!=null)
									{*/
										echo "<tr>";
										echo "<td>";
										echo "<b>".$valueRec['package_title']."</b>";
										echo "</td>";
									/*}
									elseif($keyRec==null)
									{*/
										
									//}	
										foreach($valueRec['states'] as $keyState=>$valueState)
										{
											
											echo "<td>";
											echo number_format($valueState,2) ;
											echo "</td>";
										}	
									
									echo "</tr>";
								}
								
							?>
							<tfoot>
							<tr>
							<td><b>Total</b></td>
							<?php
							foreach($recurringStateTotal as $key=>$val)
							{
								$total = $total + $val['Recurring'];
								echo "<td>".number_format($val['Recurring'],2)."</td>";
							}
							?>
							</tr>
								<tr>
									<td><b>Grand Total</b></td>
									<td><?php echo number_format($total,2);?></td>
								</tr>
							</tfoot>
						</table>
						</div>
			</div>
		</div>
	</div>
		
	</div>
	</div>
</div>
</div>