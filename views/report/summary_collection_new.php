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

$this->title = 'Summary Collection Report';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_POST['start_date']) && isset($_POST['end_date']))
{
 $strStartDate = $_POST['start_date'];
 $strEndDate = $_POST['end_date'];
}
else{
    /*$strStartDate = '';
    $strEndDate='';*/
    $strStartDate = date('01-m-Y'); // hard-coded '01' for first day
    $strEndDate  = date('t-m-Y');
}
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

<p>
		<?PHP if(yii::$app->controller->action->id=='summarycollectionreport'){
        echo Html::a(' Reset Filters',['summarycollectionreport'], ['class' => 'btn btn-success']); echo '&nbsp';
         echo Html::a(' Print',['summaryprint'], ['class' => 'btn btn-primary','target'=>'_blank']);echo '&nbsp';
         //echo Html::a(' Excel',['summaryexcel'], ['class' => 'btn btn-primary','target'=>'_blank']);
        ?>
       
 </p>

      <?php $form = ActiveForm::begin(['method' => 'post']); ?>
  <div class="row">
    <div class="col-md-12">
        <div class="col-md-2 " align="right">
        <b>Select Date Range:</b>
        </div>
        <div class="col-md-3">
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
        <div class="col-md-2" align="right">
            <b>Select Currency :-</b>
          </div>
              <div class="col-md-2">
                  <?php echo Html::dropDownList("fk_currency_id", $intCurrency,ArrayHelper::map(Currency::find()->all(), 'currency_id','currency'),array(
                            'class'=>'form-control',
                             )); ?>
          </div> 
         <!-- <div class="col-md-3">
             <?php //echo Html::submitButton('Search',['class' => 'btn btn-success']) ?>
         </div> -->
     </div>
    </div> <br> 
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-2" align="right">
            <b>Select Invoice :-</b>
          </div>
              <div class="col-md-3">
                  <?php 
                 
                    echo Select2::widget([
                        'name' => 'customer_invoice_id',
                        'id'=>'invoice_id',
                        'value'=>$invoice_id,
                        'data' => $invoice,
                        'options' => [
                            'placeholder' => 'Select Invoice Number',
                            'multiple' => false
                        ],
                    ])
                 
                   ?>
          </div> 
          <div class="col-md-2" align="right">
            <b>Select Customer :-</b>
          </div>
              <div class="col-md-3">
                  <?php 
                 
                    echo Select2::widget([
                        'name' => 'customer_id',
                        'id'=>'customer_id',
                        'value'=>$customer_id,
                        'data' => $customer,
                        'options' => [
                            'placeholder' => 'Select Customer Number',
                            'multiple' => false
                        ],
                    ])
                 
                   ?>
          </div>
          <div class="col-md-2" align="left">
             <?php echo Html::submitButton('Search',['class' => 'btn btn-success']) ?>
         </div>
        </div>
    </div>
<?php  ActiveForm::end(); }?>
<br>
<div class="box box-default">
	<div class="box-body">
		<div class="horizontal-scroll">
			  <div class="row">
			    <div class="col-xs-12">
			      <div class="table-responsive" style="width:90%;margin: 1px auto;">
					   <table  border="0" cellspacing="0" cellpadding="10" class="table table-bordered" style="width:48%; float:left;margin-right: 40px;">
                      <tr class="table-header">
                        <td height="50" width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:20%"><strong>Date</strong></td>
                        <td width="100" align="center" style="border:solid 1px #000; border-top:solid 1px #000;" colspan="4"><strong><?php echo $strCurrency;?></strong></td>
                        
                      </tr>
                      <tr>
                       
                        <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong></strong></td>
                         
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;width:20%" class='table-colspan2'><strong>Payment</strong></td>
                         <td width="100" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:30%" ><strong>Invoice ID</strong></td>
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:30%" ><strong>Customer </strong></td>
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;width:25%" ><strong>Status</strong></td>
                         
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
                        <td class="table-td"><?php echo date("d-m-Y",strtotime($value['payment_date'])); ?></td>
                        <td class="table-td"><?php echo number_format($value['amount_paid'],2); ?></td>
                         <td class="table-td"><?php echo $value['invoice_number']; ?></td>
                         <td class="table-td"><?php echo $value['customer']['name']; ?></td>
                         <?php foreach($getStatus as $sKey=>$sValue)
                         {
                            if(isset($sValue['status']) && $value['fk_invoice_id']==$sKey)
                            {
                          ?>
                          <td class="table-td"><?php echo $sValue['status']; ?></td>
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
                   <tr><td colspan="5" align="center" class="table-td"><b>No records found</b></td></tr>
                   <?php 
                    }
                	?>
                  <tfoot>
                        <tr>
                            <td class = "table-td"><b>Total</b></td>
                            <td class = "table-td" colspan="4"><b><?php echo number_format($totalPayment,2);?></b></td>
                  </tr>
                  </tfoot> 
                    </table>

                    <table  border="1" cellspacing="0" cellpadding="10" class="table table-bordered" style="width:48%;">

                      <tr class="table-header">
                        <td height="50" width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;"><strong>Date</strong></td>
                        <td width="100" align="center" style="border:solid 1px #000; border-top:solid 1px #000;" colspan="3"><strong><?php echo $strCurrency;?></strong></td>
                        
                      </tr>
                      <tr>
                       
                        <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong></strong></td>
                         
                         <td width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;text-align: center;" class='table-colspan2'><strong>Deposit</strong></td>
                         <td width="80" align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>Invoice ID</strong></td>
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
                        <td class="table-td"><?php echo date("d-m-Y",strtotime($value['deposit_date'])); ?></td>
                        <td class="table-td"><?php echo number_format($value['amount'],2); ?></td>
                         <td style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;border-right:solid 1px #000;"><?php echo $value['invoice_number']; ?></td>
                         <td class="table-td"><?php echo $value['customer']['name']; ?></td>
                    </tr>
                    <?php 
                       }
                    }
                    else
                    {
                   ?>
                   <tr><td colspan="4" align="center" class="table-td"><b>No records found</b></td></tr>
                   <?php 
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <td class = "table-td"><b>Total</b></td>
                            <td class = "table-td" colspan="3"><b><?php echo number_format($totalDeposit,2);?></b></td>
                  </tr>
                  </tfoot> 
                    </table>       
				  </div>
				</div>
			</div>
		</div>
  </div>
</div>


	
	