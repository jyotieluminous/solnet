<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\export\ExportMenu;
use app\models\Currency;
use app\models\Customerpayment;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if($currency)
{
  $intCurrency = $currency;
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

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<h4 align="center">Summary Collection Report</h4>
	         <h5>Payment Report</h5>
					<table  border="0" cellspacing="0" cellpadding="10"  >
                      <tr class="table-header">
                        <td height="50" width="30" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><strong>Date</strong></td>
                        <td width="100" align="center" style="border:solid 1px #000; border-top:solid 1px #000;" colspan="4"><strong><?php echo $strCurrency;?></strong></td>
                        
                      </tr>
                      <tr>
                       
                        <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong></strong></td>
                         
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;width:25%" class='table-colspan2'><strong>Payment</strong></td>
                         <td width="100" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:35%" ><strong>Invoice ID</strong></td>
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:30%" ><strong>Customer </strong></td>
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:30%" ><strong>Status</strong></td>
                      </tr>
                    <?php 
                     $totalPayment = 0;
                    if($paymentData)
                    {
                        foreach($paymentData as $key=>$value)
                        {
                          $totalPayment = $totalPayment + $value['amount_paid'];
                    ?>
                    <tr>
                        <td style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><?php echo date("d-m-Y",strtotime($value['payment_date'])); ?></td>
                        <td style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><?php echo number_format($value['amount_paid'],2); ?></td>
                         <td style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><?php echo $value['invoice_number']; ?></td>
                          <td style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><?php echo $value['customer']['name']; ?></td>
                         <?php foreach($getStatus as $sKey=>$sValue)
                         {
                            if(isset($sValue['status']) && $value['fk_invoice_id']==$sKey)
                            {
                          ?>
                          <td style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><?php echo $sValue['status']; ?></td>
                          <?php  
                            }
                         }?>
                    </tr>
                    <?php 
                	   }
                    }
                    else
                    {
                   ?>
                   <tr><td colspan="5" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><b>No records found</b></td></tr>
                   <?php 
                    }
                	?>
                  <tfoot>
                        <tr>
                            <td style="border:solid 1px #000; border-top:solid 1px #000;"><b>Total</b></td>
                            <td style="border:solid 1px #000; border-top:solid 1px #000;" colspan="4"><b><?php echo number_format($totalPayment,2);?></b></td>
                  </tr>
                  </tfoot>
                    </table>
                    
                    <h5>Bank Deposit Report</h5>
                    <table  border="0" cellspacing="0" cellpadding="10"  >

                      <tr class="table-header">
                        <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;"><strong>Date</strong></td>
                        <td width="100" align="center" style="border:solid 1px #000; border-top:solid 1px #000;" colspan="3"><strong><?php echo $strCurrency;?></strong></td>
                        
                      </tr>
                      <tr>
                       
                        <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong></strong></td>
                         
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;" ><strong>Deposit</strong></td>
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:15%" ><strong>Invoice ID</strong></td>
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:30%" ><strong>Customer </strong></td>
                      </tr>
                    <?php 
                     $totalDeposit = 0;
                    if($depositData)
                    {
                        foreach($depositData as $key=>$value)
                        {
                           $totalDeposit = $totalDeposit + $value['amount'];
                    ?>
                    <tr>
                        <td style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><?php echo date("d-m-Y",strtotime($value['deposit_date'])); ?></td>
                        <td style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><?php echo number_format($value['amount'],2); ?></td>
                         <td style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><?php echo $value['invoice_number']; ?></td>
                         <td style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><?php echo $value['customer']['name']; ?></td>
                    </tr>
                    <?php 
                       }
                    }
                    else
                    {
                   ?>
                   <tr><td colspan="4" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:15%"><b>No records found</b></td></tr>
                   <?php 
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <td style="border:solid 1px #000; border-top:solid 1px #000;"><b>Total</b></td>
                            <td style="border:solid 1px #000; border-top:solid 1px #000;" colspan="3"><b><?php echo number_format($totalDeposit,2);?></b></td>
                  </tr>
                  </tfoot> 
                    </table>
				  
<div class="page-break"></div>
</body>
</html>