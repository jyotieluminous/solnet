<?php
   use yii\helpers\Html;
   use yii\grid\GridView;
   use dosamigos\datepicker\DatePicker;
   use yii\widgets\Pjax;
   use yii\helpers\Url;
   use kartik\export\ExportMenu;
   use yii\bootstrap\Modal; 
   use yii\widgets\ActiveForm;
   use kartik\select2\Select2;
   use yii\helpers\ArrayHelper;
   use app\models\Customer;
   use app\models\State;
   use app\models\Country;
   use app\models\Package;
   use app\models\Speed;
   use app\models\Currency;
   use app\models\Tblusers;
   /* @var $this yii\web\View */
   /* @var $searchModel app\models\CustomerSearch */
   /* @var $dataProvider yii\data\ActiveDataProvider */
   
   $this->title = 'Customers History';
   $this->params['breadcrumbs'][] = $this->title;
   ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<div class="customer-view">
<div class="box box-default">
   <div class="box-body">
      <?php 
         if(Yii::$app->user->identity->fk_role_id=='8' || Yii::$app->user->identity->fk_role_id=='23' || Yii::$app->user->identity->fk_role_id=='24' || Yii::$app->user->identity->fk_role_id=='25')
         {
         ?>
      <div class="tbllanguage-form View-Customer-sec">
         <h2 align="center">Disconnect Dates</h2>
         <?php if(count($modelTemporaryDisconnectDates)>0){?>
         <div class="row">
            <div class="col-md-12">
               <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
                  <thead>
                     <tr>
                        <th>User Name:</th>
                        <th>Disconnect By :</th>
                        <th>Disconnection  Date  :</th>
                        <th>Disconnection Reason :</th>
                        <th>Modify Date :</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($modelTemporaryDisconnectDates as $discnnectdate) {?>
                     <tr>
                        <td> <?php
                           $arrCustomer   = Customer::find()->where(['customer_id'=>$discnnectdate['fk_customer_id']])->one();
                           echo ucfirst($arrCustomer['name']);
                            
                            ?></td>
                        <td> <?php
                           $arrUserName   = Tblusers::find()->where(['user_id'=>$discnnectdate['fk_user_id']])->one();
                           echo ucfirst($arrUserName['name']);
                            
                            ?></td>
                        <td><?php echo $discnnectdate['disconnection_date'];?></td>
                        <td><?php echo $discnnectdate['reason_for_disconnection'];?></td>
                        <td><?php echo $discnnectdate['updated_at'];?></td>
                     </tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>
         </div>
         <?php } ?>
      </div>
      <div class="tbllanguage-form View-Customer-sec">
         <h2 align="center">Reactivation Dates</h2>
         <?php if(count($modelTemporaryDisconnectDates)>0){ ?>
         <div class="row">
            <?php foreach ($modelTemporaryDisconnectDates as $discnnectdate) {?>
            <div class="col-md-12">
               <div class="col-md-6 form-group">
                  <label>Reactivation  Date  : </label>                  
                  <?php 
                     if($reactivationdate['reactivation_date'] != '0000-00-00 00:00:00' && !empty($reactivationdate['reactivation_date'])) {  echo date("d-m-Y H:i:s",  strtotime($reactivationdate['reactivation_date'])); }else { echo '-'; }
                     
                     ?>
               </div>
               <div class="form-group col-md-6">
                  <label>Modify Date :</label>
                  <?php echo $discnnectdate['updated_at'];?>
               </div>
            </div>
            <?php } ?>
         </div>
         <?php } else {?>
         <div class="row">
            <div class="col-md-12"  style="text-align: center;">
               <label>No Record found</label>
            </div>
         </div>
         <?php } ?>
      </div>
      <?php 
         }
         else
         {
         ?>
      <div class="tbllanguage-form View-Customer-sec">
         <h2 align="center">Disconnect Dates</h2>
         <?php if(count($modelTemporaryDisconnectDates)>0){?>
         <div class="row">
            <div class="col-md-12">
               <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
                  <thead>
                     <tr>
                        <th>User Name:</th>
                        <th>Disconnect By :</th>
                        <th>Disconnection  Date  :</th>
                        <th>Disconnection Reason :</th>
                        <th>Modify Date :</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($modelTemporaryDisconnectDates as $discnnectdate) {?>
                     <tr>
                        <td> <?php
                           $arrCustomer   = Customer::find()->where(['customer_id'=>$discnnectdate['fk_customer_id']])->one();
                           echo ucfirst($arrCustomer['name']);
                            
                            ?></td>
                        <td> <?php
                           $arrUserName   = Tblusers::find()->where(['user_id'=>$discnnectdate['fk_user_id']])->one();
                           echo ucfirst($arrUserName['name']);
                            
                            ?></td>
                        <td><?php echo $discnnectdate['disconnection_date'];?></td>
                        <td><?php echo $discnnectdate['reason_for_disconnection'];?></td>
                        <td><?php echo $discnnectdate['updated_at'];?></td>
                     </tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>
         </div>
         <?php } ?>
      </div>
      <div class="tbllanguage-form View-Customer-sec">
         <h2 align="center" data-toggle="collapse" href="#ReactivationDates" role="button" aria-expanded="false" aria-controls="ReactivationDates">Reactivation Dates</h2>
         <?php if(count($modelTemporaryDisconnectDates)>0){ ?>
         <div class="row collapse" id="ReactivationDates">
            <div class="row">
               <div class="col-md-12">
                  <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
                     <thead>
                        <tr>
                           <th>User Name:</th>
                           <th>Reactivation By :</th>
                           <th>Reactivation Date</th>
                           <th>Modify Date :</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php foreach ($modelTemporaryDisconnectDates as $reactivationdate) {?>
                        <tr>
                           <td> <?php
                              $arrCustomer   = Customer::find()->where(['customer_id'=>$discnnectdate['fk_customer_id']])->one();
                              echo ucfirst($arrCustomer['name']);
                               
                               ?></td>
                           <td> <?php
                              $arrUserName   = Tblusers::find()->where(['user_id'=>$discnnectdate['fk_user_id']])->one();
                              echo ucfirst($arrUserName['name']);
                               
                               ?></td>
                           <td> <?php
                              if($reactivationdate['reactivation_date'] != '0000-00-00 00:00:00' && !empty($reactivationdate['reactivation_date'])) { 
                                  echo date("d-m-Y",  strtotime($reactivationdate['reactivation_date'])); 
                              }                   
                              ?></td>
                           <td> 
                              <?php echo $discnnectdate['updated_at'];?>
                           </td>
                        </tr>
                        <?php } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <?php } else {?>
         <div class="row">
            <div class="col-md-12"  style="text-align: center;">
               <label>No Record found</label>
            </div>
         </div>
         <?php } ?>
      </div>
      <div class="tbllanguage-form View-Customer-sec">
         <h2 align="center" data-toggle="collapse" href="#FirstActivationDates" role="button" aria-expanded="false" aria-controls="FirstActivationDates"> First Activation Date</h2>
         <?php if(count($modelFirstActivationDate)>0){ ?>
         <div class="row collapse" id="FirstActivationDates">
            <div class="col-md-12">
               <div class="col-md-6 form-group">
                  <label>Installation Completed Date  : </label>
                  <?php 
                     if($modelFirstActivationDate[0]['activation_date'] != '0000-00-00 00:00:00' && isset($modelFirstActivationDate[0]['activation_date']) && !empty($modelFirstActivationDate[0]['activation_date'])) {  echo date("d-m-Y",  strtotime($modelFirstActivationDate[0]['activation_date'])); }else { echo '-'; }
                     
                     ?>
               </div>
               <div class="form-group col-md-6">
                  <label>User Name: </label>
                  <?php
                     $arrCustomer   = Customer::find()->where(['customer_id'=>$modelFirstActivationDate[0]['fk_customer_id']])->one();
                     echo ucfirst($arrCustomer['name']);
                      
                      ?>
               </div>
              
               <div class="form-group col-md-6">
                  <label>Modify Date :</label>
                  <?php echo $modelFirstActivationDate[0]['updated_at'];?>
               </div>
            </div>
         </div>
         <?php } else {?>
         <div class="row">
            <div class="col-md-12"  style="text-align: center;">
               <label>No Record found</label>
            </div>
         </div>
         <?php } ?>
      </div>
      <div class="tbllanguage-form View-Customer-sec">
         <h2 align="center" data-toggle="collapse" href="#CustomersModifyPersonalDetails" role="button" aria-expanded="false" aria-controls="CustomersModifyPersonalDetails">Customers Modify Personal Details</h2>
         <?php if(count($modelCustomerlogs)>0){ ?>
         <div class="row collapse" id="CustomersModifyPersonalDetails">
            <div class="tbllanguage-form">
               <div class="customer-index">
                  <div id="grid-container" class="table-responsive kv-grid-container">
                     <?php if (in_array("personal", $tabs)) {?>
                     <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
                        <thead>
                           <tr>
                              <th>User Type</th>
                              <th>Name</th>
                              <th>KTP / SIM / Passport No</th>
                              <th>Billing Address</th>
                              <th>Customer Type</th>
                              <th>Fiber Installed</th>
                              <th>Country</th>
                              <th>State</th>
                              <th>Email Address</th>
                              <th>Optional Email</th>
                              <th>Mobile No</th>
                              <th>Fixed Line No</th>
                              <th>Po/Wo number</th>
                              <th>Additional Info</th>
                              <th>Enable Disconnection</th>
                              <th>Microtik Ip</th>
                              <th>Invoice Send Via</th>
                              <th>VAT</th>
                              <th>Installation Address</th>
                              <th>Email Finance</th>
                              <th>Modify By</th>
                              <th>Modify Date</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($modelCustomerlogs as $reactivationdate) {
                              $finalData =  json_decode($reactivationdate->query, true); 
                              
                              if (in_array("personal", $tabs) && isset($finalData['Customer']) && count($finalData['Customer']) > 1) {
                              ?>
                           <div class="col-md-12">
                              <tr>
                                 <td>
                                    <?php if(isset($finalData['Customer']['user_type']) && !empty($finalData['Customer']['user_type'])) { echo $finalData['Customer']['user_type']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['name']) && !empty($finalData['Customer']['name'])) { echo $finalData['Customer']['name']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['ktp_pass_no']) && !empty($finalData['Customer']['ktp_pass_no'])) { echo $finalData['Customer']['ktp_pass_no']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['billing_address']) && !empty($finalData['Customer']['billing_address'])) { echo $finalData['Customer']['billing_address']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['customer_type']) && !empty($finalData['Customer']['customer_type'])) { echo $finalData['Customer']['customer_type']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['fiber_installed']) && !empty($finalData['Customer']['fiber_installed'])) { echo $finalData['Customer']['fiber_installed']; }else { echo '-'; }?>
                                 </td>
                                 <td><?php
                                    $fk_country_id = $fk_state_id = '';
                                    if(isset($finalData['Customer']['fk_country_id']) && !empty($finalData['Customer']['fk_country_id'])) { 
                                       $fk_country_id =  $finalData['Customer']['fk_country_id']; 
                                    }
                                    if(isset($finalData['Customer']['fk_state_id']) && !empty($finalData['Customer']['fk_state_id'])) { 
                                       $fk_state_id =  $finalData['Customer']['fk_state_id']; 
                                    }
                                    
                                    
                                    
                                    $arrCountry    = Country::find()->where(['status'=>'active','country_id'=>$fk_country_id])->one();
                                     echo ucfirst($arrCountry['country']); ?></td>
                                 <td><?php
                                    $arrState   = State::find()->where(['status'=>'active','state_id'=>$fk_state_id])->one();
                                     echo ucfirst($arrState['state']); ?></td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['email_address']) && !empty($finalData['Customer']['email_address'])) { echo $finalData['Customer']['email_address']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['optional_email']) && !empty($finalData['Customer']['optional_email'])) { echo $finalData['Customer']['optional_email']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['mobile_no']) && !empty($finalData['Customer']['mobile_no'])) { echo $finalData['Customer']['mobile_no']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['fixed_line_no']) && !empty($finalData['Customer']['fixed_line_no'])) { echo $finalData['Customer']['fixed_line_no']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['po_wo_number']) && !empty($finalData['Customer']['po_wo_number'])) { echo $finalData['Customer']['po_wo_number']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['additional_info']) && !empty($finalData['Customer']['additional_info'])) { echo $finalData['Customer']['additional_info']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['enable_disconnection']) && !empty($finalData['Customer']['enable_disconnection'])) { echo $finalData['Customer']['enable_disconnection']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['microtik_ip']) && !empty($finalData['Customer']['microtik_ip'])) { echo $finalData['Customer']['microtik_ip']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['invoice_send_via']) && !empty($finalData['Customer']['invoice_send_via'])) { echo $finalData['Customer']['invoice_send_via']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['vat']) && !empty($finalData['Customer']['vat'])) { echo $finalData['Customer']['vat']; }else { echo '-'; }?>
                                 </td>
                                  
                                  <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['installation_address']) && !empty($finalData['Linkcustomepackage']['installation_address'])) { echo $finalData['Linkcustomepackage']['installation_address']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Customer']['email_finance']) && !empty($finalData['Customer']['email_finance'])) { echo $finalData['Customer']['email_finance']; }else { echo '-'; }?>
                                 </td>
                                 <td> <?php
                                    $arrUserName   = Tblusers::find()->where(['user_id'=>$reactivationdate->fk_system_user_id])->one();
                                    echo ucfirst($arrUserName['name']);
                                     
                                     ?></td>
                                 <td>
                                    <?php
                                       echo date("d-m-Y H:i:s",  strtotime($reactivationdate->log_date));
                                       ?>
                                 </td>
                              </tr>
                           </div>
                           <?php } } ?>
                        </tbody>
                     </table>
                     <?php } ?>
                  </div>
               </div>
            </div>
         </div>
         <?php } else {?>
         <div class="row">
            <div class="col-md-12"  style="text-align: center;">
               <label>No Record found</label>
            </div>
         </div>
         <?php } ?>
      </div>
      <div class="tbllanguage-form View-Customer-sec">
         <h2 align="center" data-toggle="collapse" href="#CustomersModifyPackageDetails" role="button" aria-expanded="false" aria-controls="CustomersModifyPackageDetails">Customers Modify Package Details</h2>
         <?php if(count($modelCustomerlogs)>0){ ?>
         <div class="row collapse" id="CustomersModifyPackageDetails">
            <div class="tbllanguage-form">
               <div class="customer-index">
                  <div id="grid-container" class="table-responsive kv-grid-container">
                     
                     <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
                        <thead>
                           <tr>
                              <th>Package</th>
                              <th>Bundling Package</th>
                              <th>Package Speed</th>
                              <th>Speed</th>
                              <th>Package Price</th>
                              <th>Currency</th>
                              <th>Payment Type</th>
                              <th>Payment Term (days)</th>
                              <th>Grace period (in days)</th>
                              <th>Installation Fee</th>
                              <th>Other Service Fee</th>
                              <th>Contract Number</th>
                              <th>Invoice Start Date</th>
                              <th>Contract Start Date</th>
                              <th>Contract End Date</th>
                              <th>Modify By</th>
                              <th>Modify Date</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($modelCustomerlogs as $reactivationdate) {
                              $finalData =  json_decode($reactivationdate->query, true); 
                              if (isset($finalData['Linkcustomepackage']) && count($finalData['Linkcustomepackage']) > 1) {
                              ?>
                           <div class="col-md-12">
                              <tr>
                                 <?php
                                    $fk_package_id = $fk_speed_id = $fk_currency_id = $payment_type = '';
                                    if(isset($finalData['Linkcustomepackage']['fk_package_id']) && !empty($finalData['Linkcustomepackage']['fk_package_id'])) { 
                                       $fk_package_id = $finalData['Linkcustomepackage']['fk_package_id']; 
                                    }
                                    
                                    if(isset($finalData['Linkcustomepackage']['fk_speed_id']) && !empty($finalData['Linkcustomepackage']['fk_speed_id'])) { 
                                       $fk_speed_id = $finalData['Linkcustomepackage']['fk_speed_id']; 
                                    }
                                    
                                    if(isset($finalData['Linkcustomepackage']['fk_currency_id']) && !empty($finalData['Linkcustomepackage']['fk_currency_id'])) { 
                                       $fk_currency_id = $finalData['Linkcustomepackage']['fk_currency_id']; 
                                    }
                                    if(isset($finalData['Linkcustomepackage']['payment_type']) && !empty($finalData['Linkcustomepackage']['payment_type'])) { 
                                       $payment_type = $finalData['Linkcustomepackage']['payment_type']; 
                                    }
                                    
                                    $arrPackage    = Package::find()->where(['status'=>'active','package_id'=>$fk_package_id])->one();
                                    $arrPackageSpeed    = Speed::find()->where(['status'=>'active','speed_id'=>$fk_speed_id])->one();
                                    $arrCurrency    = Currency::find()->where(['status'=>'active','currency_id'=>$fk_currency_id])->one();
                                      ?>
                                 <td><?php echo ucfirst($arrPackage['package_title']); ?></td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['bundling_package']) && !empty($finalData['Linkcustomepackage']['bundling_package'])) { echo $finalData['Linkcustomepackage']['bundling_package']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['package_speed']) && !empty($finalData['Linkcustomepackage']['package_speed'])) { echo $finalData['Linkcustomepackage']['package_speed']; }else { echo '-'; }?>
                                 </td>
                                 <td><?php echo ucfirst($arrPackageSpeed['speed_type']); ?></td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['package_price']) && !empty($finalData['Linkcustomepackage']['package_price'])) { echo $finalData['Linkcustomepackage']['package_price']; }else { echo '-'; }?>
                                 </td>
                                 <td><?php echo ucfirst($arrCurrency['currency']); ?></td>
                                 <td><?php 
                                    $packageType = '';
                                    if($payment_type == 'term')
                                          $packageType = 'TERM PAYMENT';
                                    else if($payment_type == 'bulk')
                                          $packageType = 'BULK PAYMENT';
                                    else
                                          $packageType = 'PAY IN ADVANC';
                                    
                                    echo $packageType; ?></td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['payment_term']) && !empty($finalData['Linkcustomepackage']['payment_term'])) { echo $finalData['Linkcustomepackage']['payment_term']; }else { echo '-'; }?>
                                 </td>
                                 <td><?php 
                                    $packageGraceperiod = '';
                                    if($payment_type == 'term')
                                          $packageGraceperiod = $finalData['Linkcustomepackage']['term_grace_period'];
                                    else if($payment_type == 'bulk')
                                          $packageGraceperiod = $finalData['Linkcustomepackage']['bulk_grace_period'];
                                    else if($payment_type == 'advance')
                                          $packageGraceperiod = $finalData['Linkcustomepackage']['pay_in_advanc_grace_period'];
                                    
                                    echo $packageGraceperiod; ?></td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['installation_fee']) && !empty($finalData['Linkcustomepackage']['installation_fee'])) { echo $finalData['Linkcustomepackage']['installation_fee']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['other_service_fee']) && !empty($finalData['Linkcustomepackage']['other_service_fee'])) { echo $finalData['Linkcustomepackage']['other_service_fee']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['contract_number']) && !empty($finalData['Linkcustomepackage']['contract_number'])) { echo $finalData['Linkcustomepackage']['contract_number']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['invoice_start_date']) && !empty($finalData['Linkcustomepackage']['invoice_start_date'])) { echo $finalData['Linkcustomepackage']['invoice_start_date']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['contract_start_date']) && !empty($finalData['Linkcustomepackage']['contract_start_date'])) { echo $finalData['Linkcustomepackage']['contract_start_date']; }else { echo '-'; }?>
                                 </td>
                                 <td>
                                    <?php if(isset($finalData['Linkcustomepackage']['contract_end_date']) && !empty($finalData['Linkcustomepackage']['contract_end_date'])) { echo $finalData['Linkcustomepackage']['contract_end_date']; }else { echo '-'; }?>
                                 </td>
                                 <td> <?php
                                    $arrUserName   = Tblusers::find()->where(['user_id'=>$reactivationdate->fk_system_user_id])->one();
                                    echo ucfirst($arrUserName['name']);
                                     
                                     ?></td>
                                 <td>
                                    <?php
                                       echo date("d-m-Y H:i:s",  strtotime($reactivationdate->log_date));
                                       ?>
                                 </td>
                              </tr>
                           </div>
                           <?php } } ?>
                        </tbody>
                     </table>
                     
                  </div>
               </div>
            </div>
         </div>
         <?php } else {?>
         <div class="row">
            <div class="col-md-12"  style="text-align: center;">
               <label>No Record found</label>
            </div>
         </div>
         <?php } ?>
      </div>
      <?php
         }
            ?>
   </div>
</div>