<?php
namespace app\components;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use app\models\State;
use app\models\Settings;
use app\models\Customerinvoice;
use app\models\Linkcustomepackage;
use app\models\Customer;
use app\models\EmailLogs;
use app\models\CustomerService;
use app\models\CustomerServiceDetail;
class CustomComponents extends Component
{
	/* Function to fetch vat from state table */
	public function GetVat($id)
	{
		
		$objState = State::find()->where(['state_id'=>$id])->one();
		if(!empty($objState)){
			return $objState->vat;
		}
		else{
			return 0;
		}
	}



public static function logActivity($logArray = array()) {
			 if (!empty($logArray)) {
			 	//print_r($logArray);die;
				 $systemUserId = $logArray['fk_user_id'];
				 $module = $logArray['module'];
				 $action = $logArray['action'];
				 $message = $logArray['message'];
				 $created = $logArray['created'];
				

				$command= Yii::$app->db->createCommand(
        	"INSERT INTO  tbllogs ( module, fk_user_id, action, message, created)VALUES( :module, :system_user_id, :action, :message, :created)");
				$command->bindValue(':module', $module);
				$command->bindValue(':system_user_id', $systemUserId);
				$command->bindValue(':action', $action);
				$command->bindValue(':message', $message);
				$command->bindValue(':created', $created);
				$sql_result = $command->execute();

				$commandUpdate= Yii::$app->db->createCommand('UPDATE tbl_login_temp SET last_activity_time="'.$created.'" WHERE fk_user_id="'.$systemUserId.'" AND login_time LIKE "%'.date('Y-m-d').'%"');
				$updateResult = $commandUpdate->execute();
								 
			 }
		  }

  	public function emailLogActivity($emailLogs = array())
  	{
  		if(!empty($emailLogs))
  		{
  			$model = new EmailLogs();
  			$model->email_to = $emailLogs['email_to'];
  			$model->subject = $emailLogs['subject'];

  			if(isset($emailLogs['is_user']))
  				$model->is_user = $emailLogs['is_user'];
  			if(isset($emailLogs['is_customer']))
  				$model->is_customer = $emailLogs['is_customer'];

  			$model->sent_to_id = $emailLogs['sent_to_id'];
  			$model->sent_by = $emailLogs['sent_by'];

  			if($emailLogs['sent_by']=='User')
  				$model->sent_by_user_id = $emailLogs['sent_by_user_id'];
  			
  			$model->sent_date = $emailLogs['sent_date'];
  			if($model->save())
  			{
  				return true;
  			}
  			else
  			{
  				return false;
  			}
  		}
  	}		  
	
	
	
	/* Function to generate invoice number backup*/ 
	 /*public function GetInvoiceId($lastInvoiceId = 0) {
  
		  $prefix = INVOICE_ID_PREFIX;
		  //$incID = ($lastInvoiceId > 0) ? $lastInvoiceId : INVOICE_INCR_ID;
		  $objGetlastInvoice = Settings::find()->where(['name'=>'INVOICE_INCR_ID'])->one();
		  if(!empty($objGetlastInvoice))
		  {
			 $incID = $objGetlastInvoice->value;
		  }
		  $incFlag = 0;
		  $currentMonth = date("m");
		  $currentYear = date("Y");
		 if ($incID > 0) {
		   $lastInvoice = Customerinvoice::find()->orderBy('created_at DESC')->one();
		   if($lastInvoice) {
			$lastDate = $lastInvoice->created_at;
			$month = date("m",strtotime($lastDate));
			$year = date("Y",strtotime($lastDate));

			if($currentMonth == $month){
			 if($currentYear!=$year){
			  $incFlag = 1;
			 }
			}
			if($currentMonth != $month) {
			 $incFlag = 1;
			}
		   } else { 
			$incFlag = 1; 
		   }
	 }
		  if($incFlag==1){
		   $incID = 1;
		  } else {
		   $incID = $incID + 1;
		   }



		  $incIdFormat = str_pad($incID, 7, '0', STR_PAD_LEFT);
		  $invoiceId = $incIdFormat."/".INVOICE_ID_PREFIX."/".$currentMonth."/".$currentYear;
		  $returnArray = array(); 
		  $returnArray['increment_value'] = $incID;
		  $returnArray['current_invoice_id'] = $invoiceId;

		  return $returnArray;
		 }*/
	
		//new function
		 public function GetInvoiceId($stateId) {
		  //$incID = ($lastInvoiceId > 0) ? $lastInvoiceId : INVOICE_INCR_ID;
		  $getStatePrefix = State::find()->select(['state_prefix','invoice_increment_id'])->where(['state_id'=>$stateId])->one();
		  if(!empty($getStatePrefix))
		  {
			 $incID = $getStatePrefix->invoice_increment_id;
			 $prefix = $getStatePrefix->state_prefix;
		  }
		  $incFlag = 0;
		  $currentMonth = date("m");
		  $currentYear = date("Y");
		 if ($incID > 0) {
		   $lastInvoice = Customerinvoice::find()->joinWith(['customer'])->where(['fk_state_id'=>$stateId])->orderBy('created_at DESC')->one();

		   if($lastInvoice) {

			$lastDate = $lastInvoice->created_at;
			$month = date("m",strtotime($lastDate));
			$year = date("Y",strtotime($lastDate));

			if($currentMonth == $month){
			 if($currentYear!=$year){
			  $incFlag = 1;
			 }
			}
			if($currentMonth != $month) {
			 $incFlag = 1;
			}
		   } else { 
			$incFlag = 1; 
		   }
	 }
		  if($incFlag==1){
		   $incID = 1;
		  } else {
		   $incID = $incID + 1;
		   }
		   
		  $incIdFormat = str_pad($incID, 7, '0', STR_PAD_LEFT);
		  $invoiceId = $incIdFormat."/".$prefix."/".$currentMonth."/".$currentYear;
		  $returnArray = array(); 
		  $returnArray['increment_value'] = $incID;
		  $returnArray['current_invoice_id'] = $invoiceId;
		  
		  return $returnArray;
		 }

	/*
	*  function to generate customer ID backup
	*/
	/*public function getCustomerId()
	{
		$intCustId = 0;
		$returnArray = array();
		$objGetlastCustID = Settings::find()->where(['name'=>'CUST_INC_ID'])->one();
		if(!empty($objGetlastCustID))
		{
			$intCustId = str_pad($objGetlastCustID->value, 6, '0', STR_PAD_LEFT);
		}
		$returnArray['increment_value']=$objGetlastCustID->value;
		$returnArray['current_cust_id']=CUSTOMER_ID_PREFIX.$intCustId;
		
		return $returnArray;
	}*/

	//new function
	public function getCustomerId($stateId)
	{
		$intCustId = 0;
		$returnArray = array();
		$getStatePrefix = State::find()->select(['state_prefix','customer_increment_id'])->where(['state_id'=>$stateId])->one();
		if($getStatePrefix)
		{
			$intCustId = str_pad($getStatePrefix->customer_increment_id, 6, '0', STR_PAD_LEFT);
		}
		$returnArray['increment_value']=$getStatePrefix->customer_increment_id;
		$returnArray['current_cust_id']=$getStatePrefix->state_prefix.$intCustId;
		return $returnArray;
		
	}
	
	/*
	*  function to update the increament value for invoice (invoice number) backup
	*/
	 /*public function updateInvoiceIncrementValue($incID) {
		  $connection = yii::$app->db;
		  $sql   = 'UPDATE settings SET value = '.$incID.' where name = "INVOICE_INCR_ID"';
		  $command = $connection->createCommand($sql);
		  $result= $command->execute();
	 }*/
	 //new function
	 public function updateInvoiceIncrementValue($stateId,$incID) {
		  $connection = yii::$app->db;
		  $sql   = 'UPDATE tblstate SET invoice_increment_id = '.$incID.' where state_id = '.$stateId;
		  $command = $connection->createCommand($sql);
		  $result= $command->execute();
	 }	


	/*
	*  function to update the increament value for customer ID (solnet customre ID) backup
	*/
	/*public function updateCustomerIncrementValue($incID) {
		  
		  $connection = yii::$app->db;
		  $sql   = 'UPDATE settings SET value = '.$incID.' where name = "CUST_INC_ID"';
		  $command = $connection->createCommand($sql);
		  $result= $command->execute();
	 }*/

	 //new function
	 public function updateCustomerIncrementValue($stateId,$incID) {
		  
		  $connection = yii::$app->db;
		  $sql   = 'UPDATE tblstate SET customer_increment_id = '.$incID.' where state_id = '.$stateId;
		  $command = $connection->createCommand($sql);
		  $result= $command->execute();
	 }
	
	/*
	* Function to generate the invoice for activate and reactivate the customer.
	*/
	public function GenerateInvoice($id,$type='activate',$arrGetCustID='')
	{

			if(!empty($id))
			{

				$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();

				$strPaymenType = $modelLinkCustPackage->payment_type;
				$strInstallationFee = $modelLinkCustPackage->installation_fee;
				$strOtherFee = $modelLinkCustPackage->other_service_fee;
				$model  = Customer::find()->where(['customer_id'=>$id])->one();
				
				if(isset($model->state->vat) && !empty($model->state->vat))
				{
					$floatVat = $model->state->vat;
				}
				else{
					$floatVat = 0;
				}

				$strLastDayOfMonth 	 = date('Y-m-t');
				$strLastDayOfNextMonth = date("Y-m-t", strtotime("+1 month"));

				$modelCustomerInvoice = new Customerinvoice();
				
				$modelCustomerInvoice->po_wo_number = $model->po_wo_number;
				$modelCustomerInvoice->scenario = 'invoice';
				$modelCustomerInvoice->invoice_type   	 = 'normal';
				$modelCustomerInvoice->invoice_date   	 = date('Y-m-d h:i:s');
				$strInvoiceStartDate = $modelLinkCustPackage->invoice_start_date;
				$floatPackagePrice   = $modelLinkCustPackage->package_price;
				$modelCustomerInvoice->fk_customer_id 	 = $id;
				$modelCustomerInvoice->fk_cust_pckg_id 	 = $modelLinkCustPackage->cust_pck_id;
				$modelCustomerInvoice->usage_period_from = $strInvoiceStartDate;
				$modelCustomerInvoice->usage_period_to 	 = $strLastDayOfMonth;
				$strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
				$modelCustomerInvoice->next_invoice_date =  $strNextMonthFirstDate; 
				$modelCustomerInvoice->next_usage_date_from =  $strNextMonthFirstDate;
				$modelCustomerInvoice->status = 'unpaid';
				$modelCustomerInvoice->created_at 		  	  = date('Y-m-d h:i:s');
				$intNumberOfDaysInMonth =  date('t');
				$intPackagePerDayPrice = round($floatPackagePrice/$intNumberOfDaysInMonth);

				$diff = abs(strtotime($strLastDayOfMonth) - strtotime($strInvoiceStartDate));
				$intYears = floor($diff / (365*60*60*24));
				$intMonths = floor(($diff - $intYears * 365*60*60*24) / (30*60*60*24));
				$intDays = floor(($diff - $intYears * 365*60*60*24 - $intMonths*30*60*60*24)/ (60*60*24));
				
				$intTotalInvoiceAmt    = round($intPackagePerDayPrice*$intDays);
				$modelCustomerInvoice->current_invoice_amount = $intTotalInvoiceAmt;
				$modelCustomerInvoice->total_invoice_amount   = $intTotalInvoiceAmt;
				$modelCustomerInvoice->pending_amount 		  = $intTotalInvoiceAmt;
				

				$arrInvoice = Yii::$app->customcomponents->GetInvoiceId($model->fk_state_id);	
				if(!empty($arrInvoice))
				{
					$modelCustomerInvoice->invoice_number = $arrInvoice['current_invoice_id'];
				}
				if($strPaymenType=='term')
				{	

					$intTermDays   = $modelLinkCustPackage->payment_term;
					$arrInvoice = Yii::$app->customcomponents->GetInvoiceId($model->fk_state_id);


					if(!empty($intTermDays)){

						$strDueDate = date('Y-m-d',strtotime($modelLinkCustPackage->invoice_start_date.'+'.$intTermDays.'days'));
						$modelCustomerInvoice->due_date		  = 	$strDueDate;
					}
					if(strtotime($strLastDayOfMonth)>strtotime($strInvoiceStartDate))
					{

						$intTotalAmt = round($intTotalInvoiceAmt);
						$modelCustomerInvoice->installation_fee  = $strInstallationFee;
						$modelCustomerInvoice->other_service_fee = $strOtherFee;

						if(!empty($strInstallationFee) || !empty($strOtherFee))
						{
							$intTotalAmt = $intTotalAmt + $strInstallationFee + $strOtherFee;
							$modelCustomerInvoice->total_invoice_amount   = $intTotalAmt;
							$modelCustomerInvoice->pending_amount 		  = $intTotalAmt;
						}
						if(!empty($floatVat)){
								$intTotalAmtWithVat1 = ($intTotalAmt * $floatVat)/100;
								$intTotalAmtWithVat = round($intTotalAmtWithVat1);
								
								$modelCustomerInvoice->total_invoice_amount   = $intTotalAmtWithVat + $intTotalAmt;
								$modelCustomerInvoice->pending_amount 		  = $intTotalAmtWithVat + $intTotalAmt;
								$modelCustomerInvoice->vat 		  = $intTotalAmtWithVat;
						}

						if($modelCustomerInvoice->save())
						{
							$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
							if($type=='activate'){
								//$arrInvoice = Yii::$app->customcomponents->updateCustomerIncrementValue($arrGetCustID['increment_value']+1);
							}
							return 1;
						}
					}else{
						return 0;
					}
				}
				elseif($strPaymenType=='bulk')
				{
					//$intTotalAmt = $modelLinkCustPackage->package_price;
					$intTotalAmt = $modelLinkCustPackage->bulk_price;
					$modelCustomerInvoice->usage_period_from = $modelLinkCustPackage->bulk_pay_start;
					$modelCustomerInvoice->usage_period_to 	 = $modelLinkCustPackage->bulk_pay_end;
					$modelCustomerInvoice->due_date 		 = $strInvoiceStartDate;
					$strNextInvoiceDate = date('Y-m-d', strtotime('first day of +1 month', strtotime($modelLinkCustPackage->bulk_pay_end)));
					$modelCustomerInvoice->next_invoice_date = $strNextInvoiceDate;
					$modelCustomerInvoice->next_usage_date_from = $strNextInvoiceDate;
					$modelCustomerInvoice->installation_fee  = $strInstallationFee;
					$modelCustomerInvoice->other_service_fee = $strOtherFee;
					/*$modelCustomerInvoice->current_invoice_amount = $modelLinkCustPackage->package_price;
					$modelCustomerInvoice->total_invoice_amount   = $modelLinkCustPackage->package_price;
					$modelCustomerInvoice->pending_amount 		  = $modelLinkCustPackage->package_price;*/
					$modelCustomerInvoice->current_invoice_amount = $modelLinkCustPackage->bulk_price;
					$modelCustomerInvoice->total_invoice_amount   = $modelLinkCustPackage->bulk_price;
					$modelCustomerInvoice->pending_amount 		  = $modelLinkCustPackage->bulk_price;

					if(!empty($strInstallationFee) || !empty($strOtherFee))
					{
						//$intTotalAmt = $modelLinkCustPackage->package_price + $strInstallationFee + $strOtherFee;
						$intTotalAmt = $modelLinkCustPackage->bulk_price + $strInstallationFee + $strOtherFee;
						$modelCustomerInvoice->total_invoice_amount   = $intTotalAmt;
						$modelCustomerInvoice->pending_amount 		  = $intTotalAmt;
					}
					if(!empty($floatVat)){
							$intTotalAmtWithVat1 = ($intTotalAmt * $floatVat)/100;
							$intTotalAmtWithVat = round(intTotalAmtWithVat1);
							$modelCustomerInvoice->total_invoice_amount   = $intTotalAmtWithVat + $intTotalAmt;
							$modelCustomerInvoice->pending_amount 		  = $intTotalAmtWithVat + $intTotalAmt;
							$modelCustomerInvoice->vat 		  = $intTotalAmtWithVat;
					}

					if($modelCustomerInvoice->save())
					{
						$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
						if($type=='activate'){
							//$arrInvoice = Yii::$app->customcomponents->updateCustomerIncrementValue($arrGetCustID['increment_value']+1);
						}
						return 1;
					}

				}
				elseif($strPaymenType=='advance')
				{
					
					$modelCustomerInvoice->due_date  = 	$strInvoiceStartDate; // due date should be invoice start date
					if(strtotime($strLastDayOfMonth)>strtotime($strInvoiceStartDate))
					{
						
						$intTotalAmt = $intTotalInvoiceAmt;
						if(strtotime(date('Y-m-01'))==strtotime($strInvoiceStartDate))
						{
							$intTotalAmt = $floatPackagePrice;
							$modelCustomerInvoice->total_invoice_amount   = $intTotalAmt;
							$modelCustomerInvoice->pending_amount 		  = $intTotalAmt;
						}

						$modelCustomerInvoice->installation_fee  = $strInstallationFee;
						$modelCustomerInvoice->other_service_fee = $strOtherFee;
						$modelCustomerInvoice->current_invoice_amount   = $intTotalAmt;
						if(!empty($strInstallationFee) || !empty($strOtherFee))
						{
							$intTotalAmt = $intTotalAmt + $strInstallationFee + $strOtherFee;
							$modelCustomerInvoice->total_invoice_amount   = $intTotalAmt;
							$modelCustomerInvoice->pending_amount 		  = $intTotalAmt;
						}
						if(!empty($floatVat)){
								$intTotalAmtWithVat1 = ($intTotalAmt * $floatVat)/100;
								$intTotalAmtWithVat =  round($intTotalAmtWithVat1);
								$modelCustomerInvoice->total_invoice_amount   = $intTotalAmtWithVat + $intTotalAmt;
								$modelCustomerInvoice->pending_amount 		  = $intTotalAmtWithVat + $intTotalAmt;
								$modelCustomerInvoice->vat 		  = $intTotalAmtWithVat;
						}
						if($modelCustomerInvoice->save())
						{
								$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
								$newModelCustomerInvoice = new Customerinvoice();
								$newModelCustomerInvoice->scenario = 'invoice';
								$newModelCustomerInvoice->po_wo_number = $model->po_wo_number;
								$newModelCustomerInvoice->invoice_type   	 = 'normal';
								$newModelCustomerInvoice->invoice_date   	 = date('Y-m-d h:i:s');  // invoice date should be date of activation

								$newModelCustomerInvoice->fk_customer_id 	 = $id;
								$newModelCustomerInvoice->fk_cust_pckg_id 	 = $modelLinkCustPackage->cust_pck_id;
								$newModelCustomerInvoice->usage_period_from = $strNextMonthFirstDate;   // useage periode from should be next month 1st day
								$newModelCustomerInvoice->usage_period_to 	 = $strLastDayOfNextMonth; // useage periode to should be last day of current month

								$newModelCustomerInvoice->next_invoice_date =  $strNextMonthFirstDate; // next_invoice_date should be next month 1st day
								$strNextToNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
								$newModelCustomerInvoice->next_usage_date_from =  $strNextToNextMonthFirstDate; // next_usage_date_from should be next to next month 1st day
								$newModelCustomerInvoice->status = 'unpaid';
								$newModelCustomerInvoice->created_at 		  	  = date('Y-m-d h:i:s');
								$intTotalAmt = $floatPackagePrice;
								if(!empty($floatVat)){
									$intTotalAmtWithVat1 = ($floatPackagePrice * $floatVat)/100;
									$intTotalAmtWithVat = round($intTotalAmtWithVat1);
									$newModelCustomerInvoice->vat 		  = $intTotalAmtWithVat;
									$intTotalAmt += $intTotalAmtWithVat;
								}
								$newModelCustomerInvoice->current_invoice_amount = $floatPackagePrice;   //current_invoice_amount should be package price
								$newModelCustomerInvoice->total_invoice_amount   = $intTotalAmt;	 //total_invoice_amount should be package price
								$newModelCustomerInvoice->pending_amount 		 = $intTotalAmt;	 //pending_amount should be package price

								//$newModelCustomerInvoice->due_date  = 	$strLastDayOfMonth;		// due_date should be last day of current month
							
							
								$newModelCustomerInvoice->due_date  = 	date('Y-m-t');		// due_date should be last + 4 day of current month
								$arrInvoice = Yii::$app->customcomponents->GetInvoiceId($model->fk_state_id);	
								if(!empty($arrInvoice))
								{
									$newModelCustomerInvoice->invoice_number = $arrInvoice['current_invoice_id'];
								}
								if($newModelCustomerInvoice->save())
								{
									$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
									if($type=='activate'){
										//$arrInvoice = Yii::$app->customcomponents->updateCustomerIncrementValue($arrGetCustID['increment_value']+1);
									}
									return 1;
								}

						}
					}/*else{
									
									$newModelCustomerInvoice = new Customerinvoice();
									$newModelCustomerInvoice->scenario = 'invoice';
									$intTotalAmt = $floatPackagePrice;
									$newModelCustomerInvoice->installation_fee  = $strInstallationFee;
									$newModelCustomerInvoice->other_service_fee = $strOtherFee;
									$newModelCustomerInvoice->total_invoice_amount   = $floatPackagePrice;	 //total_invoice_amount should be package price
									$newModelCustomerInvoice->pending_amount 		 = $floatPackagePrice;	 //pending_amount should be package price
									if(!empty($strInstallationFee) || !empty($strOtherFee))
									{
										$intTotalAmt = $intTotalAmt + $strInstallationFee + $strOtherFee;
										$newModelCustomerInvoice->total_invoice_amount   = $intTotalAmt ;
										$newModelCustomerInvoice->pending_amount 		  = $intTotalAmt;
									}
									if(!empty($floatVat)){
										
									$intTotalAmtWithVat = ($intTotalAmt * $floatVat)/100;
									$newModelCustomerInvoice->vat 		  = $intTotalAmtWithVat;
									$intTotalAmt += $intTotalAmtWithVat;
										$newModelCustomerInvoice->total_invoice_amount   = $intTotalAmt;
										$newModelCustomerInvoice->pending_amount 		  = $intTotalAmt;
									}
									
									$newModelCustomerInvoice->invoice_type   	 = 'normal';
									$newModelCustomerInvoice->invoice_date   	 = date('Y-m-d h:i:s');  // invoice date should be date of activation

									$newModelCustomerInvoice->fk_customer_id 	 = $id;
									$newModelCustomerInvoice->fk_cust_pckg_id 	 = $modelLinkCustPackage->cust_pck_id;
									$newModelCustomerInvoice->usage_period_from  = $strNextMonthFirstDate;   // useage periode from should be next month 1st day
									$newModelCustomerInvoice->usage_period_to 	 = $strLastDayOfNextMonth; // useage periode to should be last day of current month

									$newModelCustomerInvoice->next_invoice_date =  $strNextMonthFirstDate; // next_invoice_date should be next month 1st day
									$strNextToNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
									$newModelCustomerInvoice->next_usage_date_from =  $strNextToNextMonthFirstDate; // next_usage_date_from should be next to next month 1st day
									$newModelCustomerInvoice->status = 'unpaid';
									$newModelCustomerInvoice->created_at 		  	  = date('Y-m-d h:i:s');

									$newModelCustomerInvoice->current_invoice_amount = $floatPackagePrice;   //current_invoice_amount should be package price

									$newModelCustomerInvoice->due_date  = 	date('Y-m-d', strtotime($strNextMonthFirstDate. ' + 3 days'));
										//$strLastDayOfMonth;		// due_date should be last day of current month
									$arrInvoice = Yii::$app->customcomponents->GetInvoiceId();	
									if(!empty($arrInvoice))
									{
										$newModelCustomerInvoice->invoice_number = $arrInvoice['current_invoice_id'];
									}
									if($newModelCustomerInvoice->save())
									{
										$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($arrInvoice['increment_value']);
										if($type=='activate'){
											//$arrInvoice = Yii::$app->customcomponents->updateCustomerIncrementValue($arrGetCustID['increment_value']+1);
										}
										return 1;
									}

						}*/
					
				}

		}
	}
	
	
	/*
	*  function to get state using country id
	*/
	public function Getstate($id)
	{
		if(!empty($id))
		{
			$objState = State::find()->where(['fk_country_id'=>$id,'status'=>'active'])->all();
			if(!empty($objState)) {
				  foreach($objState as $state){
					   echo "<option value='".$state->state_id."'>".$state->state."</option>";
				  }
     		}
			 else{
				  echo "<option>-</option>";
			 }
		}
	}
		
	public function GenerateServiceInvoice($id,$type='',$arrGetCustID=''){
			if(!empty($id))
			{
				$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
				$serviceDetails = CustomerService::find()->where(['fk_customer_id'=>$id])->one();
				$strPaymenType = $serviceDetails->payment_type;
				$strInstallationFee = 0;
				$strOtherFee = 0;
				
				
				if($serviceDetails->payment_type=='bulk'){
					$packagePrice = $serviceDetails->s_bulk_price;
				}else{
					$packagePrice = $this->getServicePrice($serviceDetails->customer_service_id);
				}
			
				
				$model  = Customer::find()->where(['customer_id'=>$id])->one();
				
				if(isset($model->state->vat) && !empty($model->state->vat))
				{
					$floatVat = $model->state->vat;
				}
				else{
					$floatVat = 0;
				}

				$strLastDayOfMonth 	 = date('Y-m-t');
				$strLastDayOfNextMonth = date("Y-m-t", strtotime("+1 month"));

				$modelCustomerInvoice = new Customerinvoice();
				
				$modelCustomerInvoice->po_wo_number = $model->po_wo_number;
				$modelCustomerInvoice->scenario = 'invoice';
				$modelCustomerInvoice->invoice_type   	 = 'service';
				$modelCustomerInvoice->invoice_date   	 = date('Y-m-d h:i:s');
				$strInvoiceStartDate = $serviceDetails->s_invoice_start_date;
				$floatPackagePrice   = $packagePrice;
				$modelCustomerInvoice->fk_customer_id 	 = $id;
				$modelCustomerInvoice->fk_cust_pckg_id 	 = $modelLinkCustPackage->cust_pck_id;
				$modelCustomerInvoice->usage_period_from = $strInvoiceStartDate;
				$modelCustomerInvoice->usage_period_to 	 = $strLastDayOfMonth;
				$strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
				$modelCustomerInvoice->next_invoice_date =  $strNextMonthFirstDate; 
				$modelCustomerInvoice->next_usage_date_from =  $strNextMonthFirstDate;
				$modelCustomerInvoice->status = 'unpaid';
				$modelCustomerInvoice->created_at 		  	  = date('Y-m-d h:i:s');
				$intNumberOfDaysInMonth =  date('t');
				$intPackagePerDayPrice = round($floatPackagePrice/$intNumberOfDaysInMonth);

				$diff = abs(strtotime($strLastDayOfMonth) - strtotime($strInvoiceStartDate));
				$intYears = floor($diff / (365*60*60*24));
				$intMonths = floor(($diff - $intYears * 365*60*60*24) / (30*60*60*24));
				$intDays = floor(($diff - $intYears * 365*60*60*24 - $intMonths*30*60*60*24)/ (60*60*24));

				$intTotalInvoiceAmt    = round($intPackagePerDayPrice*$intDays);
				$modelCustomerInvoice->current_invoice_amount = $intTotalInvoiceAmt;
				$modelCustomerInvoice->total_invoice_amount   = $intTotalInvoiceAmt;
				$modelCustomerInvoice->pending_amount 		  = $intTotalInvoiceAmt;



				$arrInvoice = Yii::$app->customcomponents->GetInvoiceId($model->fk_state_id);	
				if(!empty($arrInvoice))
				{
					$modelCustomerInvoice->invoice_number = $arrInvoice['current_invoice_id'];
				}
				if($strPaymenType=='term')
				{	

					$intTermDays   = $serviceDetails->term_period;
					$arrInvoice = Yii::$app->customcomponents->GetInvoiceId($model->fk_state_id);


					if(!empty($intTermDays)){

						$strDueDate = date('Y-m-d',strtotime($serviceDetails->s_invoice_start_date.'+'.$intTermDays.'days'));
						$modelCustomerInvoice->due_date		  = 	$strDueDate;
					}
					if(strtotime($strLastDayOfMonth)>strtotime($strInvoiceStartDate))
					{

						$intTotalAmt = round($intTotalInvoiceAmt);
						$modelCustomerInvoice->installation_fee  = $strInstallationFee;
						$modelCustomerInvoice->other_service_fee = $strOtherFee;

						if(!empty($strInstallationFee) || !empty($strOtherFee))
						{
							$intTotalAmt = $intTotalAmt + $strInstallationFee + $strOtherFee;
							$modelCustomerInvoice->total_invoice_amount   = $intTotalAmt;
							$modelCustomerInvoice->pending_amount 		  = $intTotalAmt;
						}
						if(!empty($floatVat)){
								$intTotalAmtWithVat1 = ($intTotalAmt * $floatVat)/100;
								$intTotalAmtWithVat = round($intTotalAmtWithVat1);
								
								$modelCustomerInvoice->total_invoice_amount   = $intTotalAmtWithVat + $intTotalAmt;
								$modelCustomerInvoice->pending_amount 		  = $intTotalAmtWithVat + $intTotalAmt;
								$modelCustomerInvoice->vat 		  = $intTotalAmtWithVat;
						}

						if($modelCustomerInvoice->save())
						{
							$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
							if($type=='activate'){
								//$arrInvoice = Yii::$app->customcomponents->updateCustomerIncrementValue($arrGetCustID['increment_value']+1);
							}
							return 1;
						}
					}else{
						return 0;
					}
				}
				elseif($strPaymenType=='bulk')
				{
					//$intTotalAmt = $modelLinkCustPackage->package_price;
					$intTotalAmt = $serviceDetails->s_bulk_price;
					$modelCustomerInvoice->usage_period_from = $serviceDetails->s_bulk_start_date;
					$modelCustomerInvoice->usage_period_to 	 = $serviceDetails->s_bulk_end_date;
					$modelCustomerInvoice->due_date 		 = $strInvoiceStartDate;
					$modelCustomerInvoice->invoice_type   	 = 'service';
					$strNextInvoiceDate = date('Y-m-d', strtotime('first day of +1 month', strtotime($serviceDetails->s_bulk_end_date)));
					$modelCustomerInvoice->next_invoice_date = $strNextInvoiceDate;
					$modelCustomerInvoice->next_usage_date_from = $strNextInvoiceDate;
					$modelCustomerInvoice->installation_fee  = $strInstallationFee;
					$modelCustomerInvoice->other_service_fee = $strOtherFee;
					/*$modelCustomerInvoice->current_invoice_amount = $modelLinkCustPackage->package_price;
					$modelCustomerInvoice->total_invoice_amount   = $modelLinkCustPackage->package_price;
					$modelCustomerInvoice->pending_amount 		  = $modelLinkCustPackage->package_price;*/
					$modelCustomerInvoice->current_invoice_amount = $serviceDetails->s_bulk_price;
					$modelCustomerInvoice->total_invoice_amount   = $serviceDetails->s_bulk_price;
					$modelCustomerInvoice->pending_amount 		  = $serviceDetails->s_bulk_price;

					if(!empty($strInstallationFee) || !empty($strOtherFee))
					{
						//$intTotalAmt = $modelLinkCustPackage->package_price + $strInstallationFee + $strOtherFee;
						$intTotalAmt = $modelLinkCustPackage->bulk_price + $strInstallationFee + $strOtherFee;
						$modelCustomerInvoice->total_invoice_amount   = $intTotalAmt;
						$modelCustomerInvoice->pending_amount 		  = $intTotalAmt;
					}
					if(!empty($floatVat)){
							$intTotalAmtWithVat1 = ($intTotalAmt * $floatVat)/100;
							$intTotalAmtWithVat = round(intTotalAmtWithVat1);
							$modelCustomerInvoice->total_invoice_amount   = $intTotalAmtWithVat + $intTotalAmt;
							$modelCustomerInvoice->pending_amount 		  = $intTotalAmtWithVat + $intTotalAmt;
							$modelCustomerInvoice->vat 		  = $intTotalAmtWithVat;
					}

					if($modelCustomerInvoice->save())
					{
						$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
						if($type=='activate'){
							//$arrInvoice = Yii::$app->customcomponents->updateCustomerIncrementValue($arrGetCustID['increment_value']+1);
						}
						return 1;
					}

				}
				elseif($strPaymenType=='advance')
				{
					
					$modelCustomerInvoice->due_date  = 	$strInvoiceStartDate; // due date should be invoice start date
					if(strtotime($strLastDayOfMonth)>strtotime($strInvoiceStartDate))
					{
						
						$intTotalAmt = $intTotalInvoiceAmt;
						if(strtotime(date('Y-m-01'))==strtotime($strInvoiceStartDate))
						{
							$intTotalAmt = $floatPackagePrice;
						}
						$modelCustomerInvoice->installation_fee  = $strInstallationFee;
						$modelCustomerInvoice->other_service_fee = $strOtherFee;
						$modelCustomerInvoice->current_invoice_amount   = $intTotalAmt;
						if(!empty($strInstallationFee) || !empty($strOtherFee))
						{
							$intTotalAmt = $intTotalAmt + $strInstallationFee + $strOtherFee;
							$modelCustomerInvoice->total_invoice_amount   = $intTotalAmt;
							$modelCustomerInvoice->pending_amount 		  = $intTotalAmt;
						}
						if(!empty($floatVat)){
								$intTotalAmtWithVat1 = ($intTotalAmt * $floatVat)/100;
								$intTotalAmtWithVat =  round($intTotalAmtWithVat1);
								$modelCustomerInvoice->total_invoice_amount   = $intTotalAmtWithVat + $intTotalAmt;
								$modelCustomerInvoice->pending_amount 		  = $intTotalAmtWithVat + $intTotalAmt;
								$modelCustomerInvoice->vat 		  = $intTotalAmtWithVat;
						}
						if($modelCustomerInvoice->save())
						{
								$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
								$newModelCustomerInvoice = new Customerinvoice();
								$newModelCustomerInvoice->scenario = 'invoice';
								$newModelCustomerInvoice->po_wo_number = $model->po_wo_number;
								$newModelCustomerInvoice->invoice_type   	 = 'service';
								$newModelCustomerInvoice->invoice_date   	 = date('Y-m-d h:i:s');  // invoice date should be date of activation

								$newModelCustomerInvoice->fk_customer_id 	 = $id;
								$newModelCustomerInvoice->fk_cust_pckg_id 	 = $modelLinkCustPackage->cust_pck_id;
								$newModelCustomerInvoice->usage_period_from = $strNextMonthFirstDate;   // useage periode from should be next month 1st day
								$newModelCustomerInvoice->usage_period_to 	 = $strLastDayOfNextMonth; // useage periode to should be last day of current month

								$newModelCustomerInvoice->next_invoice_date =  $strNextMonthFirstDate; // next_invoice_date should be next month 1st day
								$strNextToNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
								$newModelCustomerInvoice->next_usage_date_from =  $strNextToNextMonthFirstDate; // next_usage_date_from should be next to next month 1st day
								$newModelCustomerInvoice->status = 'unpaid';
								$newModelCustomerInvoice->created_at 		  	  = date('Y-m-d h:i:s');
								$intTotalAmt = $floatPackagePrice;
								if(!empty($floatVat)){
									$intTotalAmtWithVat1 = ($floatPackagePrice * $floatVat)/100;
									$intTotalAmtWithVat = round($intTotalAmtWithVat1);
									$newModelCustomerInvoice->vat 		  = $intTotalAmtWithVat;
									$intTotalAmt += $intTotalAmtWithVat;
								}
								$newModelCustomerInvoice->current_invoice_amount = $floatPackagePrice;   //current_invoice_amount should be package price
								$newModelCustomerInvoice->total_invoice_amount   = $intTotalAmt;	 //total_invoice_amount should be package price
								$newModelCustomerInvoice->pending_amount 		 = $intTotalAmt;	 //pending_amount should be package price

								//$newModelCustomerInvoice->due_date  = 	$strLastDayOfMonth;		// due_date should be last day of current month
							
							
								$newModelCustomerInvoice->due_date  = 	date('Y-m-t');		// due_date should be last + 4 day of current month
								$arrInvoice = Yii::$app->customcomponents->GetInvoiceId($model->fk_state_id);	
								if(!empty($arrInvoice))
								{
									$newModelCustomerInvoice->invoice_number = $arrInvoice['current_invoice_id'];
								}
								if($newModelCustomerInvoice->save())
								{
									$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
									if($type=='activate'){
										//$arrInvoice = Yii::$app->customcomponents->updateCustomerIncrementValue($arrGetCustID['increment_value']+1);
									}
									return 1;
								}

						}
					}
				}

		}
	}
	
	
	public function getServicePrice($service_id){
		
		if(!empty($service_id)){
			
		  $connection = yii::$app->db;
		  $sql   = "SELECT sum(`service_price`*`service_quantity`) as 'total_price' FROM `customer_service_details` WHERE `fk_cs_id` = ".$service_id;
		  $command = $connection->createCommand($sql);
		  $result= $command->queryAll();
		  if(!empty($result) && isset($result[0]['total_price'])){
			  return $result[0]['total_price'];
		  }else{
			  return 0;
		  }
		  
		  
		  
		}
		
		
		
		
	}
	
	public function updateInvoiceStatus($customerId){
		
		  $connection = yii::$app->db;
		  $sql   = 'UPDATE tblcustomer SET invoice_generated = "1" where customer_id = '.$customerId;
		  $command = $connection->createCommand($sql);
		  $result= $command->execute();
		  
	}
	
	public function updateServiceInvoiceStatus($customerId){
		
		  $connection = yii::$app->db;
		  $sql   = 'UPDATE tblcustomer SET service_invoice_generated = "1" where customer_id = '.$customerId;
		  $command = $connection->createCommand($sql);
		  $result= $command->execute();
		  
	}
}

