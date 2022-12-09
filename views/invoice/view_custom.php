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
				<b style="font-size: 13px;">Printed Date: </b><?php echo date('d-m-Y');  ?><br/>
           		<b style="font-size: 13px;">Terms 	   : </b><?php if(isset($serviceModel->term_period) && !empty($serviceModel->term_period)) { echo $serviceModel->term_period.' day(s)'; }else { echo '-'; } ?><br/>
           		<b style="font-size: 13px;">Due Date    : </b><?php echo date('d-M-Y',strtotime($model->due_date)); ?><br/>
           		<b style="font-size: 13px;">PO/WO/Contract no :</b><?php if(isset($model->po_wo_number) && !empty($model->po_wo_number)) { echo $model->po_wo_number; } else { echo '-'; } ?>
            </td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td style="background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold;">SERVICE CHARGES</td>
      <td style="background: #eee none repeat scroll 0 0;border-bottom: 1px solid #ddd;font-weight: bold;"></td>
    </tr>
	<tr>
		<td colspan="2">
			<table style="width:100%;">
			<tr>
				<th style="border-bottom: 1px solid #eee; padding:10px 0;" align="left">
					No.
				</th>
				
				<th style="border-bottom: 1px solid #eee; padding:10px 0;" align="center">
					Item Description
				</th>
				
				<th style="border-bottom: 1px solid #eee; padding:10px 0;" align="center">
					Quantity
				</th>
				
				<th style="border-bottom: 1px solid #eee; padding:10px 0;" align="center">
					Unit Price
				</th>
				
				<th style="border-bottom: 1px solid #eee; padding:10px 0;" align="right">
					Total
				</th>				
			</tr>
								
					<?php 
			$totalPayable = 0;
			$i=1;
			foreach($serviceDetail as $key => $value){  ?>
			
					  <tr style="width:100%;">
						<td style="border-bottom: 1px solid #eee; padding:10px 0;" align="left"><?php echo $i; ?></td>
						<td style="border-bottom: 1px solid #eee; padding:10px 0;" align="left"><?php echo trim($value->description); ?></td>
						<td style="border-bottom: 1px solid #eee; padding:10px 0;" align="center"><?php echo trim($value->quantity); ?></td>
						<td style="border-bottom: 1px solid #eee; padding:10x 0;" align="center"><?php echo trim(number_format($value->price,2)); ?></td>
            <?php $intTotal =  $value->price*$value->quantity;?>
						<td style="border-bottom: 1px solid #eee; padding:10px 0;" align="right"><?php echo trim(number_format($intTotal,2)); ?></td>
					  </tr>
					
			<?php  
			$totalPayable = $totalPayable + ($value->price*$value->quantity);
			$i++; } ?>
			
				
		
			
			</table>
		</td>
	</tr>		



   <?php if(!empty($model->vat)) { ?>
    <tr class="item">
      <td style="border-bottom: 1px solid #eee;"> VAT </td>
      <td style="text-align:right; border-bottom: 1px solid #eee;"><?php echo $model->linkcustomepackage->currency->currency." ".number_format($model->vat,2); ?> </td>
    </tr>
    <?php } ?>
    <tr class="total">
      <td style="padding-bottom:20px;"><span style="font-weight: bold; text-align:right; padding-bottom:20px;">TOTAL PAYABLE AMOUNT:</span></td>
      <td style="font-weight: bold; text-align:right; padding-bottom:20px;"><?php echo $model->linkcustomepackage->currency->currency." ".number_format($totalPayable,2); ?> </td>
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



