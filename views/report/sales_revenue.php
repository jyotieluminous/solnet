<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DateRangePicker;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Currency;
use kartik\export\ExportMenu;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sales Person Revenue Report';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_POST['start_date']))
{
 $strStartDate = $_POST['start_date'];

}
else{ 
    $strStartDate = date('Y'); // hard-coded '01' for first day   
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

<?php echo '&nbsp;'.Html::a('Reset Filters', ['/report/salesrevenue'], ['class' => 'btn btn-success']);echo '&nbsp';
echo Html::a(' Print',['salesrevenueprint','year'=>$year,'currency'=>$currency], ['class' => 'btn btn-primary','target'=>'_blank']);echo '&nbsp';
 ?>
 
 </p>

 <div class="alert-success alert fade in" id="success" style="display:none"> </div>
 <?php $form = ActiveForm::begin(['action' => ['report/salesrevenue'],'method' => 'post']); ?>
  <div class="row">
    <div class="col-md-12">
        <div class="col-md-3" align="right">
        <b>Select Year:</b>
        </div>
        <div class="col-md-3">
            <?php echo DatePicker::widget([
            'name' => 'CustomerinvoiceSearch[start_date]',
            'value' => $year,
            'clientOptions' => [
                        'autoclose'=>true,
                        'minViewMode'=>'years',
                        'format' => 'yyyy'
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
         <div class="col-md-2">
             <?php echo Html::submitButton('Search',['class' => 'btn btn-success']) ?>
         </div>
    </div>  
  </div>
<?php  ActiveForm::end(); ?>
 <br>
<div class="box box-default">
	<div class="box-body">
		<div class="horizontal-scroll">
			<div class="container">
			  <div class="row">
			    <div class="col-xs-12">
			      <div class="table-responsive">

			      	<table  border="0" cellspacing="0" cellpadding="10" class="table table-bordered" >
                      <tr class="table-header">
                        <td height="50" align="center" class="table-td"><strong>Sales Persons</strong></td>
                        <td  align="center" width="50" class="table-td" ><strong>January</strong></td>
                        <td  align="center" width="50" class="table-td" ><strong>February</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>March</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>April</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>May</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>June</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>July</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>August</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>Spetember</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>October</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>November</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong>December</strong></td>
                         <td  align="center" width="50" class="table-td" ><strong><b>Total</b></strong></td>
                      </tr>
                      <tr>
                        <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong></strong></td>
                         <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;background-color: #a2ace6;" colspan="12"><strong>Total revenue</strong></td>
                        <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong></strong></td>
                      </tr>   

                      <!--Data-->
                      <?php
                        $totalMonth = 0;
                        foreach ($reportData as $key => $value) 
                        {
                            $totalMonth = $totalMonth + $value['totalAmount'];
                          echo '<tr>';
                          
                             echo '<td class = "table-td">';
                                echo $value['sales_person'];
                             echo '</td>';
                             //Data for January
                             echo '<td class = "table-td">';
                             if(isset($value['Jan']) && !empty($value['Jan']['revenue']))
                             {
                                echo number_format($value['Jan']['revenue'],2);
                             }
                             else
                             {  
                              echo "-";
                             }
                             echo '</td>';
                            
                             //Data for February
                             echo '<td class = "table-td">';
                             if(isset($value['Feb']) && !empty($value['Feb']['revenue']))
                             {
                                echo number_format($value['Feb']['revenue'],2);
                             }
                             else
                             {                       
                              echo "-";
                             }
                             echo '</td>';
                            
                             //Data for March
                             echo '<td class = "table-td">';
                             if(isset($value['Mar']) && !empty($value['Mar']['revenue']))
                             {                         
                                echo number_format($value['Mar']['revenue'],2);
                             }
                             else
                             {                            
                              echo "-";
                             }
                                
                             echo '</td>';
                             
                             //Data for April
                             echo '<td class = "table-td">';
                             if(isset($value['Apr']) && !empty($value['Apr']['revenue']))
                             {
                                echo number_format($value['Apr']['revenue'],2);
                             }
                             else
                             {
                              echo "-";
                             }
                             echo '</td>';
                            
                             //Data for May
                             echo '<td class = "table-td">';
                             if(isset($value['May']) && !empty($value['May']['revenue']))
                             {
                                echo number_format($value['May']['revenue'],2);
                             }
                             else
                             {
                              echo "-";
                             }
                             echo '</td>';
                            
                             //Data for June
                             echo '<td class = "table-td">';
                             if(isset($value['Jun']) && !empty($value['Jun']['revenue']))
                             {
                               echo number_format($value['Jun']['revenue'],2);
                             }
                             else
                             {
                              echo "-";
                             }
                             echo '</td>';
                            
                            //Data for July
                             echo '<td class = "table-td">';
                             if(isset($value['Jul']) && !empty($value['Jul']['revenue']))
                             {
                                echo number_format($value['Jul']['revenue'],2);
                             }
                             else
                                echo "-";
                             echo '</td>';
                            
                             //Data for August
                             echo '<td class = "table-td">';
                             if(isset($value['Aug']) && !empty($value['Aug']['revenue']))
                             {
                                echo number_format($value['Aug']['revenue'],2);
                             }
                             else
                                echo "-";
                             echo '</td>';
                            
                             //Data for Sept
                             echo '<td class = "table-td">';
                             if(isset($value['Sept']) && !empty($value['Sept']['revenue']))
                             {
                                echo number_format($value['Sept']['revenue'],2);
                             }
                             else
                                echo "-";
                             echo '</td>';
                            
                             //Data for Oct
                             echo '<td class = "table-td">';
                             if(isset($value['Oct']) && !empty($value['Oct']['revenue']))
                             {
                                echo number_format($value['Oct']['revenue'],2);
                             }
                             else
                                echo "-";
                             echo '</td>';

                             //Data for Nov
                             echo '<td class = "table-td">';
                             if(isset($value['Nov']) && !empty($value['Nov']['revenue']))
                             {
                                echo number_format($value['Nov']['revenue'],2);
                             }
                             else
                                echo "-";
                             echo '</td>';
                            
                             //Data for Dec
                             echo '<td class = "table-td">';
                             if(isset($value['Dec']) && !empty($value['Dec']['revenue']))
                             {
                                echo number_format($value['Dec']['revenue'],2);
                             }
                             else
                                echo "-";
                             echo '</td>';
                             echo '<td class = "table-td">';
                               echo number_format($value['totalAmount'],2);
                             echo '</td>';
                          echo "</tr>"; 
                        }

                      ?>
                      <tfoot>
                        <tr>
                            <td class = "table-td"><b>Total</b></td>
                            <?php 
                            if($total && $totalMonth)
                            {
                                foreach ($total as $key => $value)
                                {
                                    echo '<td class = "table-td">';
                                    echo '<b>'.number_format($value,2).'</b>';
                                    echo "</td>";
                                }
                            
                            
                            ?>
                            <td class = "table-td"><?php echo '<b>'.number_format($totalMonth,2).'</b>';?></td>
                            <?php }
                            else{
                              echo '<td class = "table-td" colspan="13"><b><center>No Records</center></b></td>';  
                            } 
                            ?>
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
