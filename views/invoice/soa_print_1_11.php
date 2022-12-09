<?php
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Customerinvoice;

$floatTotalAmount = 0;
$floatTotalUnpaid = 0;
$floatTotalPaid = 0;
$strCurrency = "";


if(!empty($dataProvider->getModels()))
{
	$strCurrency = $model->linkcustomepackage->currency->currency;

	$model1 = $dataProvider->getModels();

 foreach ($model1 as $key => $val) {
	 	$floatTotalPaid += $val->paid_amount;
	 	$floatTotalUnpaid += $val->pending_amount;
	 	$floatTotalAmount += $val->total_invoice_amount;
    }
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>

<body>
<img width="260" border="0" height="80" style="display:block;padding-left:15px;" alt="logo" src="<?php echo Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/web/images/solnet.png'; ?>">
<!--<span style="font-size:26px; color:#4286d4; ">PT. SOLNET</span> <span  style="font-size:26px;">INDONESIA</span><br />
                <p style=" padding:0 0 5px 0; margin:0px;">Komplek Rafflesia Business Center Blok E No. 01 Batam Center - Indonesia<br />
Tel : +62 778 472711Fax: +62 778 472600 Website : www.solnet.net.id</p>-->
<hr/>
<h4 align="center"><b><u>STATEMENT OF ACCOUNT</u></b></h4>
<div class="row">
	<div class="col-md-12">
		<div class="col-md-6">
			<label><strong>Customer Name :</strong></label>
			<?php echo $modelCust->name; ?>
		</div>
		<div class="col-md-6" style="margin:0px 0px -10px 450px;">
			<label><strong>Date Printed :</strong></label>
			<?php echo date('d-m-Y'); ?>
		</div>
	</div>
	<div class="col-md-12">
		<div class="col-md-6">
			<label><strong>Address :</strong></label>
			<?php echo $modelCust->billing_address; ?>
		</div>
	</div>
</div>
<br/><br/>
<p><b>We would like to inform you about the invoice past due situation on your account. As folllows :</b></p>
<table cellpadding="5" cellspacing="5" border="1" width="100%" style="font-size:8pt;  border-collapse: collapse; ">
	<tr>
		<th align="left" width= "19%">Invoice Number</th>
		<th align="left" width= "12%">Invoice Date</th>
		<th align="left" width= "15%">Total Invoice Amount</th>
		<th align="left"  width= "15%">Paid Amount</th>
		<th align="left"  width= "15%" >Invoice Balance</th>
		<th align="left">Term</th>
		<th align="left" width= "12%">Due Date</th>
		<th align="left" width= "6%">Past Due</th>
	</tr>
	<?php
	if(!empty($dataProvider->getModels()))
	{
		$model1 = $dataProvider->getModels();
 		foreach ($model1 as $key => $val)
		{
			?>
			<tr>
				<td align="left"><?php if(isset($val->invoice_number) && !empty($val->invoice_number)) { echo $val->invoice_number; }else { echo '-'; }?></td>
				<td align="left"><?php if(isset($val->invoice_date) && !empty($val->invoice_date)) { echo  Yii::$app->formatter->asDate($val->invoice_date, 'php:d-m-Y'); }else { echo '-'; }?></td>
				<td align="left"><?php if(isset($val->total_invoice_amount) && !empty($val->total_invoice_amount)) { echo$strCurrency." ".number_format($val->total_invoice_amount, 2); }else { echo '-'; }?></td>
				<td align="left"><?php if(isset($val->paid_amount) && !empty($val->paid_amount)) { echo $strCurrency." ". number_format($val->paid_amount, 2); }else { echo '-'; }?></td>
				<td align="left"><?php if(isset($val->pending_amount) && !empty($val->pending_amount)) { echo  $strCurrency." ".number_format($val->pending_amount, 2); }else { echo '-'; }?></td>
				<td align="left"><?php if(isset($val->linkcustomepackage->payment_term) && !empty($val->linkcustomepackage->payment_term)) { echo $val->linkcustomepackage->payment_term; }else { echo '-'; }?></td>
				<td align="left"><?php if(isset($val->due_date) && !empty($val->due_date)) { echo Yii::$app->formatter->asDate($val->due_date, 'php:d-m-Y'); }else { echo '-'; }?></td>
				<td align="left"><?php if(isset($val->due_date) && !empty($val->due_date)) { echo $model->getnumberofdays($val->due_date); }else { echo '-'; }?></td>
			</tr>

			<?php
			}
		?>
		<tr>
			<td colspan="2" align="left"><b>Total</b></td>
			<td><?php echo '<b>'. $strCurrency." ". '<b>'.number_format($floatTotalAmount,2).'</b>'; ?></td>
			<td><?php echo '<b>'. $strCurrency." ".'<b>'.number_format($floatTotalPaid,2).'</b>'; ?></td>
			<td><?php echo '<b>'. $strCurrency." ".'<b>'.number_format($floatTotalUnpaid,2).'</b>'; ?></td>
		</tr>
		<?php
	}
		?>

	</tr>
</table>
<br/>
<p><b>Please make proper payment arrangement for the invoices that are past due to avoid temporary disconnection.</b></p>
<h3>Thank you for your business!</h3>
</body>
</html>
