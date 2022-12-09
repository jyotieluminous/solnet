<html>
<head>
<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> -->
</head>
<?php
if($_SESSION['currency']==1){
  $_SESSION['currency'] = ' Rp';
}elseif($_SESSION['currency']==2){
  $_SESSION['currency'] = ' SGD';
}elseif($_SESSION['currency']==3)
{
  $_SESSION['currency'] = ' USD';
}
?>
<body>
    <h4 align="center">Sales Person Revenue Report</h4>
    <h6>Year: <?php echo $_SESSION['year'];?> <br>
    Currency:  <?php echo $_SESSION['currency'];?><h6>
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
                         <td  align="center" width="50" class="table-td" ><strong>Total</strong></td>
                      </tr>
                      <tr>
                        <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;"><strong></strong></td>
                         <td align="center" style="border:solid 1px #000; border-top:solid 1px #000; border-bottom:solid 1px #000;background-color: #a2ace6;" colspan="12"><strong>Total revenue</strong></td>
                        
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

				
<div class="page-break"></div>
</body>
</html>