echo '<table border="1px" >
        
                      <tr>
                        <th><strong>Date</strong></td>
                        <th><strong>'.$strCurrency.'</strong></td>
                      </tr>
                      <tr>
                       
                        <td><strong></strong></td>
                         
                         <th><strong>Payment</strong></td>
                         <th><strong>Invoice ID</strong></td>
                         <th><strong>Customer </strong></td>
                         <th><strong>Status</strong></td>
                      </tr>
                      ';
        $totalPayment = 0;
                    if($paymentData)
                    {
                        foreach($paymentData as $key=>$value)
                        {
                          $totalPayment = $totalPayment + $value['amount_paid'];
                          echo '<tr>
                        <td >'. date("d-m-Y",strtotime($value["payment_date"])).'</td>
                        <td>'. number_format($value["amount_paid"],2).'</td>
                         <td>'. $value["invoice_number"].'</td>
                          <td>'. $value['customer']['name'].'</td>';
                            foreach($getStatus as $sKey=>$sValue)
                             {
                                if(isset($sValue['status']) && $value['fk_invoice_id']==$sKey)
                                {
                                    echo '<td>'.$sValue['status'].'</td>';
                                }
                            }
                        }
                    }
                    else
                    {
                   '<tr><td ><b>No records found</b></td></tr>';
                    }
                    '<tfoot>
                        <tr>
                            <td"><b>Total</b></td>
                            <td><b>'.number_format($totalPayment,2).'</b></td>
                  </tr>
                  </tfoot>
                  <tr></tr>
                     <tr></tr></table>';
                     echo '<table border="1px" >
                     <tr>Deposite</tr>

                      <tr>
                        <th><strong>Date</strong></td>
                        <th><strong>'.$strCurrency.'</strong></td>
                      </tr>
                      <tr>
                       
                        <td><strong></strong></td>
                         
                         <th><strong>Payment</strong></td>
                         <th><strong>Invoice ID</strong></td>
                         <th><strong>Customer </strong></td>
                      </tr>';
                    $totalDeposit = 0;
                    if($depositData)
                    {
                        foreach($depositData as $key=>$value)
                        {
                           $totalDeposit = $totalDeposit + $value['amount'];
                          echo '<tr>
                        <td >'. date("d-m-Y",strtotime($value["deposit_date"])).'</td>
                        <td>'. number_format($value["amount"],2).'</td>
                         <td>'. $value["invoice_number"].'</td>
                          <td>'. $value['customer']['name'].'</td>';
                            
                        }
                    }
                    else
                    {
                   '<tr><td ><b>No records found</b></td></tr>';
                    }
                    '<tfoot>
                        <tr>
                            <td"><b>Total</b></td>
                            <td><b>'.number_format($totalDeposit,2).'</b></td>
                  </tr>
                  </tfoot>
                    </table>';