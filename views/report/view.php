<?php
use yii\helpers\Url;
$headerAddressInvoice  = "";
$headerTelephoneTelephone = "";
$headerSignatureEmail = "";


if(isset($headerAddress))
  $headerAddressInvoice = $headerAddress;
elseif(isset($model->customer->state->header_address))
  $headerAddressInvoice = $model->customer->state->header_address;

if(isset($headerTelephone))
  $headerTelephoneTelephone = $headerTelephone;
elseif(isset($model->customer->state->header_telephones))
  $headerTelephoneTelephone = $model->customer->state->header_telephones;

if(isset($signatureEmail))
  $headerSignatureEmail = $signatureEmail;
elseif(isset($model->customer->state->signature_email_id))
  $headerSignatureEmail = $model->customer->state->signature_email_id;

  if(isset($isMultiple)){
?>

<div style="max-width:800px;margin:auto;padding:30px;font-size:16px;line-height:24px;font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;color:#555; page-break-after: always">
  <?php } else {?>
  
<div style="max-width:800px;margin:auto;padding:30px;font-size:16px;line-height:24px;font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;color:#555; ">
  <?php } ?>
  <table cellpadding="7" cellspacing="0" style="line-height: inherit;text-align: left;width: 100%;">
    <tr class="top">
      <td colspan="2" style="padding: 5px;vertical-align: top;">
	  <table style=" line-height: inherit;text-align: left;width: 100%;border-spacing:0;">
          <tr>
           <?php if($header==1){ ?>
            <td style="border-bottom:solid 3px #4286d4;padding-right:30px;">
			<img width="160px" src="<?php echo Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/web/images/solnet-logo.png'; ?>" >
			</td>  
			<td style="border-bottom:solid 3px #4286d4; font-size:12px !important;">
           	  <span style="font-size:22; color:#4286d4; ">PT. SOLNET</span> <span  style="font-size:22;">INDONESIA</span><br />
                <p style=" padding:0 0 5px 0; margin:0px;"><!-- Jalan Sunset Road No. 271D , Seminyak, Badung, Bali, 80361 -->
                <?php echo $headerAddressInvoice;?>
                <br />
                <?php echo $headerTelephoneTelephone;?>
				<!-- Telp :  0361 4741691,0361 4741692,0361 4741693 --> <br> Website : www.solnet.net.id<br /></p>
				</td>
            <?php } ?>
            <td style="text-align:right; vertical-align:top;">  </td>
          </tr>
        </table></td>
    </tr>
    <tr class="information">
      <td colspan="2"><table style="width:100%;">
          <tr>
            <td style="padding:30px 0px;">
            	<b style="font-size: 13px;">Customer Name : </b><?php echo ucfirst($model->customer->name); ?><br/>
            	<b style="font-size: 13px;">Address</b> : <?php echo nl2br($model->customer->billing_address); ?><br/>
            	<b style="font-size: 13px;">State</b> : <?php echo $model->customer->state->state; ?><br/>
            	<b style="font-size: 13px;">Country</b> : <?php echo $model->customer->country->country; ?>
            </td>
            <td style="text-align:right;padding:30px 0px;">
            	<b style="font-size: 13px;">Customer ID : </b><?php echo $model->customer->solnet_customer_id;  ?><br/>
            	<b style="font-size: 13px;">Invoice No  : </b><?php echo $model->invoice_number;  ?><br/>
				<b style="font-size: 13px;">Printed Date: </b><?php echo date('d-M-Y',strtotime($model->invoice_date));  ?><br/>
           		<b style="font-size: 13px;">Terms 	   : </b><?php if(isset($model->linkcustomepackage->payment_term) && !empty($model->linkcustomepackage->payment_term)) { echo $model->linkcustomepackage->payment_term.' day(s)'; }else { echo '-'; } ?><br/>
           		<b style="font-size: 13px;">Due Date    : </b><?php echo date('d-M-Y',strtotime($model->due_date)); ?><br/>
           		<b style="font-size: 13px;">PO/WO/Contract no :</b><?php if(isset($model->po_wo_number) && !empty($model->po_wo_number)) { echo $model->po_wo_number; } else { echo '-'; } ?>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td style="background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold;">PACKAGE DETAILS</td>
      <td style="background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold;"></td>
    </tr>
	<?php 
	//echo "<pre>"; print_r($model->linkcustomerpackage); exit;
	if(isset($model->linkcustomepackage->bundling_package) && (!empty($model->linkcustomepackage->bundling_package)|| ($model->linkcustomepackage->bundling_package == '<p>&nbsp;</p>') )){ ?>
	  <tr class="item">
      <td style="border-bottom: 1px solid #eee;"><b>Bundling Package</b><?php echo $model->linkcustomepackage->bundling_package; ?> </td>
      <!--<td style="border-top: 2px solid #eee;font-weight: bold; text-align:right;padding-bottom:20px;border-bottom: 1px solid #eee;"> <?php //echo $model->linkcustomepackage->bundling_package; ?> </td> -->
    </tr>
	
	<?php } ?>

	
     <tr class="item">
      <td style="border-bottom: 1px solid #eee;"><?php echo $model->linkcustomepackage->package->package_title; ?></td>
      <td style="border-top: 2px solid #eee;font-weight: bold; text-align:right;padding-bottom:20px;border-bottom: 1px solid #eee;"> <?php echo $model->linkcustomepackage->currency->currency." ".number_format($model->current_invoice_amount,2); ?> </td>
    </tr>
	
    <tr class="item">
      <td style="border-bottom: 1px solid #eee;"> Speed </td>
      <td style="text-align:right;border-bottom: 1px solid #eee;"> <?php echo $model->linkcustomepackage->package_speed.' '.$model->linkcustomepackage->speed->speed_type; ?> </td>
    </tr>
    <tr class="item">
      <td style="border-bottom: 1px solid #eee;"> Usage Period </td>
      <td style="text-align:right; border-bottom: 1px solid #eee;"><?php echo date('d-m-Y',strtotime($model->usage_period_from)).' - '.date('d-m-Y',strtotime($model->usage_period_to)); ?> </td>
    </tr>
    <!-- <tr>
      <td style="background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold;">SUMMARY OF CHARGES</td>
      <td style="background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold; text-align:right">Amount</td>
    </tr>
    <tr>
      <td style="border-bottom: 1px solid #eee;">Balance from previous bill</td>
      <td style="text-align:right;border-bottom: 1px solid #eee;"> <?php //echo $model->linkcustomepackage->currency->currency.' '.number_format($model->last_due_amount,2); ?> </td>
    </tr> -->
    <tr>
      <td style="border-bottom: 1px solid #eee;">Total current charges</td>
      <td style="text-align:right;border-bottom: 1px solid #eee; "> <?php echo $model->linkcustomepackage->currency->currency.' '.number_format($model->current_invoice_amount,2); ?> </td>
    </tr>
    <tr class="total">
      <td style="padding-bottom:20px;"></td>
     <!-- <td style=" border-top: 2px solid #eee;font-weight: bold; text-align:right;padding-bottom:20px;"> Total Charges : <?php //echo ($model->total_invoice_amount).' '.$model->linkcustomepackage->currency->currency ?> </td>-->
    </tr>

    <tr>
      <td style=" background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold;"> DETAILS OF CURRENT CHARGES </td>
      <td style=" background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold; text-align:right;"> Amount </td>
    </tr>

    <?php if(!empty($model->installation_fee)) { ?>
    <tr class="item">
      <td style="border-bottom: 1px solid #eee;"> Installation Fee </td>
      <td style="text-align:right; border-bottom: 1px solid #eee;"><?php echo $model->linkcustomepackage->currency->currency." ".number_format($model->installation_fee,2); ?> </td>
    </tr>
    <?php } ?>
   <?php if(!empty($model->linkcustomepackage->other_service_fee) || !empty($model->other_service_fee)) { ?>
    <tr class="item">
      <td style="border-bottom: 1px solid #eee;"> Other Fee </td>
      <td style="text-align:right; border-bottom: 1px solid #eee;"><?php echo $model->linkcustomepackage->currency->currency." ".number_format($model->linkcustomepackage->other_service_fee + $model->other_service_fee,2); ?> </td>
    </tr>
      <?php if(!empty($model->comment_for_other_service_fee)) { ?>
          <tr class="item">
            <td style="border-bottom: 1px solid #eee;">Other Service Description</td>
            <td style="text-align:right; border-bottom: 1px solid #eee;"><?php echo $model->comment_for_other_service_fee; ?> </td>
          </tr>
      <?php } ?>
    <?php } ?>
    <?php if(!empty($model->vat)) { ?>
    <tr class="item">
      <td style="border-bottom: 1px solid #eee;"> VAT </td>
      <td style="text-align:right; border-bottom: 1px solid #eee;"><?php echo $model->linkcustomepackage->currency->currency." ".number_format($model->vat,2); ?> </td>
    </tr>
    <?php } ?>
    <tr class="total">
      <td style="padding-bottom:20px;"><span style="font-weight: bold; text-align:right; padding-bottom:20px;">TOTAL PAYABLE AMOUNT:</span></td>
      <td style=" border-top: 1px solid #eee;font-weight: bold; text-align:right; padding-bottom:20px;"><?php echo $model->linkcustomepackage->currency->currency." ".number_format($model->total_invoice_amount,2); ?> </td>
    </tr>
	
	<tr>
      <td style=" background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold;">INSTALLATION ADDRESS</td>
      <td style=" background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold; text-align:right;"> </td>
    </tr>
	<tr class="item">
      <td style="border-bottom: 1px solid #eee;"> <?php echo $model->linkcustomepackage->installation_address; ?></td>
      <td style="text-align:right;border-bottom: 1px solid #eee;"></td>
    </tr>
	

    <tr>
      <td style=" background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold;">PAYMENT INSTRUCTION</td>
      <td style=" background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold; text-align:right;"> Details </td>
    </tr>
    <tr class="item">
      <td style="border-bottom: 1px solid #eee;"> Please write a cheque or Transfer the full amount to</td>
      <td style="text-align:right;border-bottom: 1px solid #eee;"></td>
    </tr>
    <?php if($model->linkcustomepackage->is_solnet_bank=='yes'){ ?>
    <tr class="item">
      <td style="border-bottom: 1px solid #eee;"> Bank Name    </td>
      <td style="text-align:right;border-bottom: 1px solid #eee;">
      <?php if(!empty($model->linkcustomepackage->bank->bank_name)){ echo $model->linkcustomepackage->bank->bank_name; } ?></td>
    </tr>
    <tr class="item last">
      <td style="border-bottom: 1px solid #eee;"> Account Name </td>
      <td style="text-align:right; border-bottom: 1px solid #eee;">
      	<?php if(!empty($model->linkcustomepackage->bank->account_name)){ echo $model->linkcustomepackage->bank->account_name; } ?>
      </td>
    </tr>
    <tr class="item last">
      <td style="border-bottom: 1px solid #eee;">Bank Account No</td>
      <td style="text-align:right; border-bottom: 1px solid #eee;">
      	<?php if(!empty($model->linkcustomepackage->bank->account_no)) { echo $model->linkcustomepackage->bank->account_no; } ?>
      </td>
    </tr>
    <?php }elseif($model->linkcustomepackage->is_solnet_bank=='no'){ 	?>
    <tr class="item last">
      <td style="border-bottom: 1px solid #eee;"> Bank Account </td>
      <td style="text-align:right; border-bottom: 1px solid #eee;">
      	<?php if(isset($model->linkcustomepackage->virtual_acc_no)){ echo $model->linkcustomepackage->virtual_acc_no; } ?>
      </td>
    </tr>
    <tr class="item last">
      <td style="border-bottom: 1px solid #eee;">Bank Name</td>
      <td style="text-align:right; border-bottom: 1px solid #eee;">
      	<?php if(isset($model->linkcustomepackage->bank_name)){ echo $model->linkcustomepackage->bank_name; } ?>
      </td>
    </tr>
    <?php } if($sign == 1){ ?>
		<tr><td colspan="2" style="text-align:center; padding-top:20px;">
		  Please kindly provide the receipt of payment and email to <b><?php echo $headerSignatureEmail;?></b> <br>
		This is a computer generated invoice, hence do not need any signature
		</td></tr>
	<?php } ?>
  </table>
  
</div>



