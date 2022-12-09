<?php
use yii\helpers\Url;
$depositIDRTotal=$depositSDDTotal=$depositUSDTotal=0;
$paymentIDRTotal=$paymentSDDTotal=$paymentUSDTotal=0;
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<h4 align="center">Summary Collection Report</h4>
<table  border="0" cellspacing="0" cellpadding="0" align="center" style="  color:#000;">
  
 </tr>
	<tr>
	 <td >
	  <table  border="0" cellspacing="0" cellpadding="0" >
          <tr>
            <td width="400"  style="font-size:16px;" align="left" colspan="2"> Duration: <b> <?php echo $strStartDate .' To '. $strEndDate ?></b></td>
          </tr>
          <tr>
            <td  colspan="2">&nbsp;</td>
          </tr>
  
           <tr>
            <td colspan="2" >
                <table  border="0" cellspacing="0" cellpadding="10" >
	                  <tr>
	                    <td height="50" width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;"><strong>Date</strong></td>
	                    <td width="300" align="center" style="border:solid 1px #000; border-top:solid 1px #000;" colspan="2"><strong>IDR</strong></td>
	                    <td width="200" align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-right:solid 1px #000;" colspan="2"><strong>USD</strong></td>
	                     <td width="200" align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-right:solid 1px #000;" colspan="2"><strong>SDD</strong></td>
	                  </tr>
		              <tr>
		                <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong></strong></td>
		                 <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>Deposit</strong></td>
		                 <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>Payment</strong></td>
		                 <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>Deposit</strong></td>
		                 <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>Payment</strong></td>
		                 <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>Deposit</strong></td>
		                 <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>Payment</strong></td>
		              </tr>
                
                  <?php 
						foreach($arrSummary as $key => $date){  ?>  <tr>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><?php echo $key; ?></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][1]['deposit'])){
								print_r($arrSummary[$key][1]['deposit']);
								
								$depositIDRTotal+=$arrSummary[$key][1]['deposit']; 
							}else{
								echo "--";
							} ?> </td>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][1]['payment'])){
								 print_r($arrSummary[$key][1]['payment']);
								 $paymentIDRTotal+=$arrSummary[$key][1]['payment'];
							}else{
								echo "--";
							} ?></td>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][2]['deposit'])){
								print_r($arrSummary[$key][2]['deposit']);
								$depositSDDTotal+=$arrSummary[$key][2]['deposit'];
							}else{
								echo "--";
							} ?></td>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][2]['payment'])){
								 print_r($arrSummary[$key][2]['payment']);
								 $paymentSDDTotal+=$arrSummary[$key][2]['payment'];
							}else{
								echo "--";
							} ?></td>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][3]['deposit'])){
								print_r($arrSummary[$key][3]['deposit']);
								$depositUSDTotal+=$arrSummary[$key][3]['deposit'];
							}else{
								echo "--";
							} ?></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][3]['payment'])){
								 print_r($arrSummary[$key][3]['payment']);
								 $paymentUSDTotal+=$arrSummary[$key][3]['payment'];
							}else{
								echo "--";
							} ?></td>

						</tr>
						<?php } ?>
						<tr>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>Total Amount<strong>  </td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>
							<?php echo $depositIDRTotal;?><strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>
							<?php echo $paymentIDRTotal;?><strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>
							<?php echo $depositSDDTotal;?><strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<strong>
							<?php  echo $paymentSDDTotal; ?>
							<strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>
							<?php echo $depositUSDTotal;?></strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<strong>
							<?php  echo $paymentUSDTotal; ?></strong></td>

						</tr>
                </table>
             </td>
           </tr>
        </table>
	  </td>
	</tr>
</table>
<div class="page-break"></div>
</body>
</html>