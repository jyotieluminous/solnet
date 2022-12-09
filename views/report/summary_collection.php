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
?>

<p>
		<?PHP if(yii::$app->controller->action->id=='summarycollectionreport'){
        echo Html::a(' Reset Filters',['summarycollectionreport'], ['class' => 'btn btn-success']); echo '&nbsp';
         echo Html::a(' Print',['summaryprint'], ['class' => 'btn btn-primary','target'=>'_blank']);echo '&nbsp';
         echo Html::a(' Excel',['summaryexcel'], ['class' => 'btn btn-primary','target'=>'_blank']);
        ?>
       
 </p>

      <?php $form = ActiveForm::begin(['method' => 'post']); ?>
  <div class="row">
        <div class="col-md-3 " align="right">
        <b>Select Date Range:</b>
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
    
<?php  ActiveForm::end(); }?>
<br>
<div class="box box-default">
	<div class="box-body">
		<div class="horizontal-scroll">
			<div class="container">
			  <div class="row">
			    <div class="col-xs-12">
			      <div class="table-responsive">

					<?php 
					$depositIDRTotal=0;
					$depositSDDTotal=0;$depositUSDTotal=0;
					$paymentIDRTotal=0;$paymentSDDTotal=0;
					$paymentUSDTotal=0;?>
					<table  border="0" cellspacing="0" cellpadding="10" class="table table-bordered" >
                      <tr class="table-header">
                        <td height="50" width="50" align="center" style="border:solid 1px #000; border-top:solid 1px #000;"><strong>Date</strong></td>
                        <td width="300" align="center" style="border:solid 1px #000; border-top:solid 1px #000;" colspan="2"><strong>IDR</strong></td>
                        <td width="200" align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-right:solid 1px #000;" colspan="2"><strong>USD</strong></td>
                         <td width="200" align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-right:solid 1px #000;" colspan="2"><strong>SDD</strong></td>
                      </tr>
                      <tr>
                       
                        <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong></strong></td>
                         <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;" class='table-colspan1'><strong>Deposit</strong></td>
                         <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;" class='table-colspan2'><strong>Payment</strong></td>
                         <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;" class='table-colspan1'><strong>Deposit</strong></td>
                         <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;" class='table-colspan2'><strong>Payment</strong></td>
                         <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;" class='table-colspan1'><strong>Deposit</strong></td>
                         <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;" class='table-colspan2'><strong>Payment</strong></td>
                      </tr>
                    
                      <?php 
							foreach($arrSummary as $key => $date){  ?>  <tr>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><?php 
	                       		echo date("d-m-Y",strtotime($key)); ?>
	                        </td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][1]['deposit'])){
								print_r(number_format($arrSummary[$key][1]['deposit'],2));
								
								$depositIDRTotal+=$arrSummary[$key][1]['deposit']; 
							}else{
								echo "--";
							} ?> </td>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][1]['payment'])){
								 print_r(number_format($arrSummary[$key][1]['payment'],2));
								 $paymentIDRTotal+=$arrSummary[$key][1]['payment'];
							}else{
								echo "--";
							} ?></td>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][2]['deposit'])){
								print_r(number_format($arrSummary[$key][2]['deposit'],2));
								$depositSDDTotal+=$arrSummary[$key][2]['deposit'];
							}else{
								echo "--";
							} ?></td>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][2]['payment'])){
								 print_r(number_format($arrSummary[$key][2]['payment'],2));
								 $paymentSDDTotal+=$arrSummary[$key][2]['payment'];
							}else{
								echo "--";
							} ?></td>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][3]['deposit'])){
								print_r(number_format($arrSummary[$key][3]['deposit'],2));
								$depositUSDTotal+=$arrSummary[$key][3]['deposit'];
							}else{
								echo "--";
							} ?></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<?php if(isset($arrSummary[$key][3]['payment'])){
								 print_r(number_format($arrSummary[$key][3]['payment'],2));
								 $paymentUSDTotal+=$arrSummary[$key][3]['payment'];
							}else{
								echo "--";
							} ?></td>

							</tr>
							<?php } ?>
							<tr>
							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>Total Amount<strong>  </td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>
							<?php echo number_format($depositIDRTotal,2);?><strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>
							<?php echo number_format($paymentIDRTotal,2);?><strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>
							<?php echo number_format($depositSDDTotal,2);?><strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<strong>
							<?php  echo number_format($paymentSDDTotal,2); ?>
							<strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong>
							<?php echo number_format($depositUSDTotal,2);?></strong></td>

							<td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;">
							<strong>
							<?php  echo number_format($paymentUSDTotal,2); ?></strong></td>

							</tr>
                    </table>
				  </div>
				</div>
			</div>
		</div>
	</div>
  </div>
</div>


	
	