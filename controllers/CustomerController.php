<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : CustomerController.php
# Created on : 5th June 2017 by Suraj Malve.
# Update on  : 5th June 2017 by Suraj Malve.
# Purpose : Manage Customer.
############################################################################################
*/


namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\CustomerSearch;
use app\models\Linkcustomepackage;
use app\models\Customerinvoice;
use app\models\Package;
use app\models\Speed;
use app\models\Currency;
use app\models\Bank;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\State;
use app\models\Country;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use kartik\mpdf\Pdf;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\db\Expression;
/* EQUIPEMENT TASK */
use app\models\CustomerEquipments;
use app\models\Equipments;
use app\models\EquipmentsMacs;
use app\models\Settings;
use app\models\Customerlogs;
/* END EQUIPEMENT TASK */
//Yii::$app->cache->flush();
/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
{
    /**
     * @inheritdoc
     */

	public function behaviors()
    {
		$behaviors['access'] = [
			'class' => AccessControl::className(),
                        'only' => ['create', 'update','index','view','delete','billing','pending','billview','activateview','pdf','activatepdf','billpdf','activate','deletemultiple','delete','plan','pendinginstallation','activateinstallation','installationview','installationprint','togglestatus','billingsingledelete','billingmultipledelete','activatemultipledelete','activatesingledelete','disconnect','reactivate','addexisting','salesperson','addequipment','deleteequipment'],
			'rules' => [
                        [
                        'allow' => true,
                        'roles' => ['@'],
                                        'matchCallback' => function($rules, $action){
                                               $action = Yii::$app->controller->action->id;
                                                $controller = Yii::$app->controller->id;
                                                $route = "$controller/$action";
                                                $post = Yii::$app->request->post();
                                                if(\Yii::$app->user->can($route)){

                                                        return true;
                                                }
                                        }
                    ],
                 ],
		];
		return $behaviors;
    }

    /**
     * Lists all Customer which is added by logged in sales admin.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerSearch();
		//$searchModel->linkcustomerpackage->is_current_package= 'yes';
		$searchModel->is_deleted= '0';
		$queryParams = Yii::$app->request->queryParams;
		if(Yii::$app->user->identity->fk_role_id=='3'){
			$queryParams['CustomerSearch']['fk_user_id']=Yii::$app->user->identity->user_id;
		}
		
		$queryParams['CustomerSearch']['installation_status']='no';
		//$queryParams['CustomerSearch']['is_current_package']='yes';
		/*$sessionData = Yii::$app->session;
		
		if($sessionData->get('user_state_id'))
		{
			$stateId = $sessionData->get('user_state_id');
			$queryParams['CustomerSearch']['fk_state_id']=$stateId;
		}*/
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


	/**
     * Lists all Customer for a billing admin.
     * @return mixed
     */
   /* public function actionBilling($limit='')
    {

        $searchModel = new CustomerSearch();
        $model = new Customer();
        $arrSalesPerson = array();
		$queryParams = Yii::$app->request->queryParams;
		if(!empty($limit) && !isset($queryParams["CustomerSearch"]["limit"])){
			$queryParams["CustomerSearch"]["limit"]   = $limit;
		}
		elseif(!isset($queryParams["CustomerSearch"]["limit"]))
		{
			$queryParams["CustomerSearch"]["limit"]   = '20';
		}

		if(Yii::$app->user->identity->fk_role_id=='3'){
				$queryParams['CustomerSearch']['fk_user_id']=Yii::$app->user->identity->user_id;
			}
		
		$objSalesPerson = Customer::find()->joinWith('user')->select(['fk_user_id','tblusers.name'])->distinct()->all();
		if($objSalesPerson)
		{
			foreach($objSalesPerson as $key=>$value)
			{
				$arrSalesPerson[$key]['user_id'] = $value->fk_user_id;
				$arrSalesPerson[$key]['name'] = $value->name;
			}
		}
	
		$queryParams['CustomerSearch']['installation_status']='yes';
		$queryParams['CustomerSearch']['is_deleted']='0';
		$queryParams['CustomerSearch']['is_invoice_activated']='yes';
		if(!isset($queryParams['CustomerSearch']['status']))
		{
			$queryParams['CustomerSearch']['status']='active';
		}
		

        $dataProvider = $searchModel->search($queryParams);
        //get the total package price
        $resultTotalPrice = $model->getTotalPrice();

        return $this->render('billing_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalPackagePrice'=>$resultTotalPrice,
            'user'=>$arrSalesPerson
        ]);
    } */
	
	
		/**
     * Lists all Customer for a billing admin.
     * @return mixed
     */
    public function actionBilling($limit='')
    {

        $searchModel = new CustomerSearch();
        $model = new Customer();
        $arrSalesPerson = array();
		$queryParams = Yii::$app->request->queryParams;
		
		if(Yii::$app->user->identity->fk_role_id=='22'){
			//$dataProvider = $searchModel->searchNOC($queryParams);
			$dataProvider = $searchModel->search($queryParams);
		}else{
					
			if(!empty($limit) && !isset($queryParams["CustomerSearch"]["limit"])){
				$queryParams["CustomerSearch"]["limit"]   = $limit;
			}
			elseif(!isset($queryParams["CustomerSearch"]["limit"]))
			{
				$queryParams["CustomerSearch"]["limit"]   = '20';
			}

			if(Yii::$app->user->identity->fk_role_id=='3'){
					$queryParams['CustomerSearch']['fk_user_id']=Yii::$app->user->identity->user_id;
				}
			$queryParams['CustomerSearch']['speed_id']='0';
			$queryParams['CustomerSearch']['installation_status']='yes';
			$queryParams['CustomerSearch']['is_deleted']='0';
			$queryParams['CustomerSearch']['is_invoice_activated']='yes';
			if(!isset($queryParams['CustomerSearch']['status']))
			{
				$queryParams['CustomerSearch']['status']='active';
			}
			//print_r($queryParams);die;

			$dataProvider = $searchModel->search($queryParams);
			//get the total package price
			
			
		}
			/*************To fetch speed from table************/
		$arrSpeed 	= Speed::find()->where(['status'=>'active'])->all();
		$speedListData	= ArrayHelper::map($arrSpeed,'speed_id','speed_type');

		$resultTotalPrice = $model->getTotalPrice();
		$objSalesPerson = Customer::find()->joinWith('user')->select(['tblcustomer.fk_user_id','tblusers.name'])->distinct()->all();
		if($objSalesPerson)
			{
				foreach($objSalesPerson as $key=>$value)
				{
					$arrSalesPerson[$key]['user_id'] = $value->fk_user_id;
					$arrSalesPerson[$key]['name'] = $value->name;
				}
			}
			
			//echo Yii::$app->user->identity->fk_role_id;die;
        return $this->render('billing_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalPackagePrice'=>$resultTotalPrice,
            'speedList'=>$speedListData,
            'user'=>$arrSalesPerson
        ]);
    }
    
	/************ Create inventry for billing customers functionality Add on :: 01-05-2018 *********/
	public function actionAddbillingcustomerequipment($id)
	{
		$session = Yii::$app->session;
		$model   = new CustomerEquipments;
		if(Yii::$app->request->post())
    	{	
    		$arrPostData = Yii::$app->request->post();
    		//echo '<pre>';print_r($arrPostData);echo '<pre>';die;
    		$connection = Yii::$app->getDb();
    		foreach ($arrPostData['CustomerEquipments']['fk_equipments_id'] as $key => $arrFkEquIdValue) {
				if(isset($arrFkEquIdValue[0]) && $arrFkEquIdValue[0] != ''){
					$intEquMentId = $arrFkEquIdValue[0];
	    			if(isset($arrPostData['quntity'][$key])){
	    				$intQuntity   = $arrPostData['quntity'][$key];
	    			}
	    			if(isset($arrPostData['price'][$key])){
	    				$intPrice   = $arrPostData['price'][$key];
	    			}
	    			if(isset($arrPostData['inv_type'][$key])){
	    				$intEquType   = $arrPostData['inv_type'][$key];
	    			}

	    			//Update  fin_equipments table quntity
	    			$command = $connection->createCommand('UPDATE fin_equipments SET quantity_out = quantity_out + '.$intQuntity.', quantity_in_hand = quantity_in_hand - '.$intQuntity.' WHERE equipment_id ='.$intEquMentId);
					$command->execute();

					//insert equipments to tbl_customer_equipments.
	    			$connection->createCommand()->insert('tbl_customer_equipments',
	                [	
	                    'fk_equipments_id' 	=> $intEquMentId,
	                    'fk_customer_id' 	=> $id,
	                    'quantity' 			=> $intQuntity,
	                    'price' 			=> $intPrice,
	                    'euipment_type' 	=> $intEquType,
	                    'added_by' 			=> yii::$app->user->identity->user_id,
	                    'status' 			=> 'active',
	                ])
	                ->execute();
	                $intLastInsertedId = $connection->getLastInsertID();
	                
	                if(isset($arrPostData['mac_address'][$key])){
	                	foreach ($arrPostData['mac_address'][$key] as $arrMacKey => $arrMacValue) {
	                		$command = $connection->createCommand('UPDATE fin_equipments_macs SET assigned_status = "Assigned", assigned_by = '.yii::$app->user->identity->user_id.' , assigned_id = '.$intLastInsertedId.' , assigned_to = "coustomer" WHERE equipments_mac_id ='.$arrMacValue);
							$command->execute();
	                	}
	                }
	            }else{
	            	$session->setFlash('error_msg',"Please Enter a values");
					return $this->redirect(Yii::$app->request->referrer);
	            }

    		}
            $session->setFlash('success_msg',"Equipments Added Successfully");
			return $this->redirect(Yii::$app->request->referrer);
		}
		$arrEquipData = Equipments::find()->all();
		$arrEquipList = ArrayHelper::map($arrEquipData,'equipment_id','model_type');
		
        return  $this->render('add_billing_customer_equipment',
       	[
	       	'id'			=> $id,
       		'model'			=> $model,
	       	'arrEquipList'	=> $arrEquipList
	    ]);
	}
	
	public function actionGetbillingcustomerequpment($intId,$id)
	{
		$hmtl = '';
		$intCnt = 1;
		if(!empty($id)){
			$arrResultEqu = Equipments::find()->where(['equipment_id'=>$id])->all();
			if(!empty($arrResultEqu)) {
				  foreach($arrResultEqu as $value){
				  	if($value->type == 'Normal'){
				  		$hmtl .= "<input type='hidden' name='intCnt' id='intCnt' class='form-control intCnt' value='".$intCnt."'>
				  					<input type='hidden' name='inv_type[]' value='Normal'>
				  					<div class='col-md-2'>
				  						<label>Brand Name</label><input type='text' value='".$value->brand_name."' name='brandName[".$intId."][]' class='form-control' readonly >
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Quntity In Hand </label>
				  						<input type='text' name='inHandQuntity[]' class='form-control' value='".$value->quantity_in_hand."' readonly> 
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Quntity</label>
				  						<input type='text' value='' name='quntity[]' class='form-control equmQuntity' onkeypress='javascript:return isNumber(event)' required> 
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Price</label>
				  						<input type='text' value='' name='price[]' id='price' class='form-control priceClass equmPrice' onkeypress='javascript:return isNumber(event)' required> 
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Total</label>
				  						<input type='text' value='' name='total' class='form-control equmTotal_Temp equmTotal' onkeypress='javascript:return isNumber(event)' readonly> 
				  					</div>";
				  	}else{
						$arrResult = EquipmentsMacs::find()->where(['fk_equipment_id'=>$value->equipment_id,'assigned_status' =>'Not_Assigned'])->all();
						$hmtl .=  "<input type='hidden' name='inv_type[]' value='MAC'>
									<div class='col-md-2'>
									<label>Brand Name</label>
				  					<input type='text' value='".$value->brand_name."' name='brandName[".$intId."][]' class='form-control' readonly >
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Mac Address</label>
				  						<select name='mac_address[".$intId."][]' class='form-control multipleSelect' multiple required>";
				  						foreach ($arrResult as $arrValue) {
										  $hmtl .=  "<option value='".$arrValue->equipments_mac_id."'>".$arrValue->serial_number."</option>";
										}
						$hmtl .=  "</select>
				  					</div>
				  					<div class='col-md-1'>
				  						<label>Quntity In Hand </label>
				  						<input type='text' name='inHandQuntity[]' class='form-control' value='".$value->quantity_in_hand."' readonly> 
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Quntity</label>
				  						<input type='text' value='' name='quntity[]' class='form-control equmQuntity' onkeypress='javascript:return isNumber(event)' required>
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Price</label>
				  						<input type='text' value='' name='price[]' id='price' class='form-control priceClass equmPrice' onkeypress='javascript:return isNumber(event)' required> 
				  					</div>
				  					<div class='col-md-1'>
				  						<label>Total</label>
				  						<input type='text' value='' name='total' class='form-control equmTotal_Temp equmTotal' onkeypress='javascript:return isNumber(event)' readonly> 
				  					</div>";
				  	}
				  	return $hmtl;
				  }
				  $intCnt++;
     		}else{
				  echo "-";
			 }
		}
	}
	
	public function actionGetcustomerequipment($id)
    {
    	$intNoOfEqu = '';
    	$model = new CustomerEquipments;
		if(Yii::$app->request->post())
    	{	
    		$arrPostData = Yii::$app->request->post();
    		$intNoOfEqu  = $arrPostData['nofoequipment'];
		}
		$arrEquipData = Equipments::find()->all();
		$arrEquipList = ArrayHelper::map($arrEquipData,'equipment_id','model_type');
		
        return  $this->render('add_billing_customer_equipment',
       	[
	       	'id'			=> $id,
       		'model'			=> $model,
	       	'arrEquipList'	=> $arrEquipList,
	       	'intNoOfEqu'	=> $intNoOfEqu
	    ]);
    }
    /********* End Create inventry for billing customers code *********/
    
    
	/**
     * Lists all Customer of Activate new customer.
     * @return mixed
     */
    public function actionPending()
    {
        $searchModel = new CustomerSearch();
		$queryParams = Yii::$app->request->queryParams;
		$queryParams['CustomerSearch']['installation_status']='yes';
		$queryParams['CustomerSearch']['is_deleted']='0';
		$queryParams['CustomerSearch']['is_invoice_activated']='no';
		/*$sessionData = Yii::$app->session;
		
		if($sessionData->get('user_state_id'))
		{
			$stateId = $sessionData->get('user_state_id');
			$queryParams['CustomerSearch']['fk_state_id']=$stateId;
		}*/
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('activate_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	/**
     * Download the pdf of customer added by the sales admin.
     *
     */
	public function actionDownload($strFileName)
	{
		$path=Yii::getAlias('@webroot').'/uploads/user_docs/'.$strFileName;

		if (file_exists($path)) {
			return Yii::$app->response->sendFile($path);
		}
	}

    /**
     * Displays a single Customer details with package details for sales admin.
     * @param integer $id
     *
     */
    public function actionView($id)
    {        
        /* EQUIPEMENT TASK */
        $equpmentModel 	   = new CustomerEquipments();
    	$arrResultEqupment = CustomerEquipments::find()->joinWith(['equmentData','equmentMacData'])->where(['fk_customer_id'=>$id])->all();
    	
        return $this->render('view', [
            'model' 			=> $this->findModel($id),
        	'arrResultEqupment' => $arrResultEqupment,
        ]);
        /* END EQUIPEMENT TASK */
    }

	/**
     * Displays a single Customer details with package details for billing admin.
     * @param integer $id
     *
     */
    public function actionBillview($id)
    {
        /* EQUIPEMENT TASK */
        $equpmentModel 	   = new CustomerEquipments();
    	$arrResultEqupment = CustomerEquipments::find()->joinWith(['equmentData','equmentMacData'])->where(['fk_customer_id'=>$id])->all();

        return $this->render('billing_view', [
            'model' 			=> $this->findModel($id),
            'arrResultEqupment' => $arrResultEqupment,
        ]);
		/* END EQUIPEMENT TASK */
    }

	/**
     * Displays a single Customer details with package details for billing admin.
     * @param integer $id
     *
     */
    public function actionActivateview($id)
    {
        return $this->render('activate_view', [
            'model' => $this->findModel($id),
        ]);
    }

	 /**
     * Generate a pdf for single Customer details for sales admin.
     * @param integer $id
     *
     */
	public function actionPdf($id) {
		$model = $this->findModel($id);
		$pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' => $this->renderPartial('view', [
            'model' => $model,
        ]),
		'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => $model->name,
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
            //'SetHeader' => ['Generated By: Solnet: ' . date("Y-m-d h:i:s")],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
    ]);

		return $pdf->render();
	}


	/**
	*	Function to generate the pdf of activated customers
	*/
	public function actionActivatepdf($id) {
		$pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' => $this->renderPartial('activate_view', [
            'model' => $this->findModel($id),
        ]),
		'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Customer Details',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
            //'SetHeader' => ['Generated By: Solnet: ' . date("r")],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
    ]);

		return $pdf->render();
	}

	 /**
     * Generate a pdf for single Customer details for sales admin.
     * @param integer $id
     * @return mixed
     */
	public function actionBillpdf($id) {

		// get your HTML raw content without any layouts or scripts
   		/*$content = $this->renderPartial('bill_pdf', [
            'model' => $this->findModel($id),
        ]);
		$pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        
        'content' => $content,
		
        'options' => [
            'title' => 'Customer Details',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
            'SetHeader' => ['Generated By: Solnet: ' . date("r")],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
    ]);

		return $pdf->render();*/

		 
    $html = $this->renderPartial('bill_pdf', [
            'model' => $this->findModel($id),
        ]);
  
    $mpdf = new \mPDF();
    $mpdf->SetHeader('Generated By: Solnet: ' . date("r"));
    $mpdf->WriteHTML($html);
    $mpdf->setFooter('{PAGENO} of {nbpg} pages||{PAGENO} of {nbpg} pages');
    $mpdf->SetTitle("Customer Details");
    return $mpdf->Output('customerdetails_'.$id.'.'.'pdf', "I");
	}

    /**
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model 	= new Customer();
		$modelLinkCustPackage	= new Linkcustomepackage();
		$modelCustomerlogs	= new Customerlogs();
		
		//$modelLinkCustPackage->scenario = 'create';
		/*************To fetch state from table************/
		$arrState 	= State::find()->where(['status'=>'active','fk_country_id'=>1])->all();
		$stateListData	= ArrayHelper::map($arrState,'state_id','state');
		/*************To fetch state from table************/

		/*************To fetch state from table************/
		$arrCountry 	= Country::find()->where(['status'=>'active'])->all();
		$countryListData	= ArrayHelper::map($arrCountry,'country_id','country');
		/*************To fetch state from table************/

		/*************To fetch package from table************/
		$arrPackage 	= Package::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
		$packageListData	= ArrayHelper::map($arrPackage,'package_id','package_title');
		/*************To fetch state from table************/

		/*************To fetch speed from table************/
		$arrSpeed 	= Speed::find()->where(['status'=>'active'])->all();
		$speedListData	= ArrayHelper::map($arrSpeed,'speed_id','speed_type');
		/*************To fetch state from table************/

		/*************To fetch currency from table************/
		$arrCurrency 	= Currency::find()->where(['status'=>'active'])->all();
		$currencyListData	= ArrayHelper::map($arrCurrency,'currency_id','currency');
		/*************To fetch state from table************/

		$model->is_invoice_activated='no';
		$model->created_at=date('Y-m-d h:i:s');
		$model->fk_user_id = Yii::$app->user->identity->user_id;

		if (Yii::$app->request->isPost) {
			$intTimeStamp = date('Ymdhis');
			$filesArray = UploadedFile::getInstance($model, 'filepath');
			if(!empty($filesArray)){
				$filesArray->saveAs('uploads/user_docs/' . 'User_'.$intTimeStamp. '.' . $filesArray->extension);
				$filepath='User_'.$intTimeStamp.'.'.$filesArray->extension;
			}else{
				$filepath= '';
			}
			$arrPostData =  Yii::$app->request->post();

		 }


        if ($model->load(Yii::$app->request->post()) ) {

			$model->filepath = $filepath;
			if($arrPostData['Customer']['is_address_same']==1)
			{
				$model->is_address_same = 'yes';
			}
			else
			{
				$model->is_address_same = 'no';
			}
			$model->it_pic = $arrPostData['Customer']['it_pic'];
			$model->optional_email = $arrPostData['Customer']['optional_email'];			
			if($model->save())
			{
				
				$modelCustomerlogs->fk_user_id = $model->customer_id;
				$modelCustomerlogs->action = 'create';
				$modelCustomerlogs->query = json_encode($arrPostData);
				$modelCustomerlogs->controller = 'CustomerController';
				$modelCustomerlogs->ip_address = $this->get_client_ip();
				$modelCustomerlogs->hostname = gethostname();
				$modelCustomerlogs->agent = json_encode($this->getBrowser());
				$modelCustomerlogs->log_date = date('Y-m-d H:i:s');	
				$modelCustomerlogs->fk_system_user_id = Yii::$app->user->identity->user_id;	
				$modelCustomerlogs->save();
				$strPaymentType = $arrPostData['Linkcustomepackage']['payment_type'];
				$modelLinkCustPackage->bundling_package = $arrPostData['Linkcustomepackage']['bundling_package'];

				if($strPaymentType =='term'){
					$modelLinkCustPackage->payment_term = $arrPostData['Linkcustomepackage']['payment_term'];
					$modelLinkCustPackage->term_grace_period = $arrPostData['Linkcustomepackage']['term_grace_period'];
					//$modelLinkCustPackage->bulk_pay_start = '0000-00-00';
					//$modelLinkCustPackage->bulk_pay_end = '0000-00-00';
				}elseif($strPaymentType =='bulk'){
					$modelLinkCustPackage->bulk_pay_start = Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['start_date'], 'php:Y-m-d');
					$modelLinkCustPackage->bulk_pay_end =  Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['end_date'], 'php:Y-m-d');
					$modelLinkCustPackage->bulk_grace_period = $arrPostData['Linkcustomepackage']['bulk_grace_period'];
				}else{
					$modelLinkCustPackage->payment_term = '';
					$modelLinkCustPackage->pay_in_advanc_grace_period = $arrPostData['Linkcustomepackage']['pay_in_advanc_grace_period'];
				}

				$modelLinkCustPackage->fk_customer_id = $model->customer_id;
				$modelLinkCustPackage->fk_user_id = Yii::$app->user->identity->user_id;
				$modelLinkCustPackage->created_at  = date('Y-m-d h:i:s');
				$modelLinkCustPackage->order_received_date = date('Y-m-d h:i:s');
				//$modelLinkCustPackage->activation_date  = '0000-00-00 00:00:00';
				if($modelLinkCustPackage->load(Yii::$app->request->post()) && $modelLinkCustPackage->save())
				{
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Create Customer';
					$logArray['action'] = 'create';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has created a customer "'.$model->name.'"';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					return $this->redirect(['view', 'id' => $model->customer_id]);
				}

			}


        }

		return $this->render('create', [
                'model' => $model,
				'modelLinkCustPackage'=>$modelLinkCustPackage,
				'stateList'=>$stateListData,
				'countryList'=>$countryListData,
				'speedList'=>$speedListData,
				'currencyList'=>$currencyListData,
				'packageList'=>$packageListData
            ]);
    }

	/**
	* Function to get vat based on state
	* @param integer $id
	*
    */

	public function actionGetvat($id)
	{
		return Yii::$app->customcomponents->GetVat($id);
	}

	public function actionGetgraceperiodtype($id)
	{
		$arrResult = Settings::find()->where(['name'=>$id])->one();
		if(count($arrResult))
		{
			return $arrResult['value'];
		}
		else
		{
			return '0';
		}
		
	}





    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
    	$modelCustomerlogs	= new Customerlogs();
		$model = $this->findModel($id);
		$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
		//$modelLinkCustPackage->scenario = 'create';

		/*************To fetch state from table************/
		$arrCountry 	= Country::find()->where(['status'=>'active'])->all();
		$countryListData	= ArrayHelper::map($arrCountry,'country_id','country');
		/*************To fetch state from table************/

		/*************To fetch package from table************/
		$arrPackage 	= Package::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
		$packageListData	= ArrayHelper::map($arrPackage,'package_id','package_title');
		/*************To fetch state from table************/

		/*************To fetch speed from table************/
		$arrSpeed 	= Speed::find()->where(['status'=>'active'])->all();
		$speedListData	= ArrayHelper::map($arrSpeed,'speed_id','speed_type');
		/*************To fetch state from table************/

		/*************To fetch currency from table************/
		$arrCurrency 	= Currency::find()->where(['status'=>'active'])->all();
		$currencyListData	= ArrayHelper::map($arrCurrency,'currency_id','currency');
		/*************To fetch state from table************/


		/*************To fetch state from table************/
		$arrState 	= State::find()->where(['status'=>'active','fk_country_id'=>$model->fk_country_id])->all();
		$stateListData	= ArrayHelper::map($arrState,'state_id','state');
		/*************To fetch state from table************/


		$strFilePath = $model->filepath;

		$model->is_invoice_activated='no';
		$model->updated_at=date('Y-m-d h:i:s');
		$model->fk_user_id = Yii::$app->user->identity->user_id;

		if (Yii::$app->request->isPost) {
			$intTimeStamp = date('Ymdhis');
			$filesArray = UploadedFile::getInstance($model, 'filepath');
			if(!empty($filesArray)){
				$filesArray->saveAs('uploads/user_docs/' . 'User_'.$intTimeStamp. '.' . $filesArray->extension);
				$filepath='User_'.$intTimeStamp.'.'.$filesArray->extension;
				if(!empty($strFilePath)){
					if(file_exists(Yii::$app->basePath."/web/uploads/user_docs/".$strFilePath)){
						unlink(Yii::$app->basePath."/web/uploads/user_docs/".$strFilePath);
					}
				}
			}else{
				$filepath = $strFilePath;
			}
			$arrPostData =  Yii::$app->request->post();

		 }

		
		if ($model->load(Yii::$app->request->post()) && $modelLinkCustPackage->load(Yii::$app->request->post()) ) {


			$model->filepath = $filepath;
			if($arrPostData['Customer']['is_address_same']==1)
			{
				$model->is_address_same = 'yes';
			}
			else
			{
				$model->is_address_same = 'no';
			}
			
			$model->it_pic = $arrPostData['Customer']['it_pic'];
			$model->optional_email = $arrPostData['Customer']['optional_email'];
			if($model->save())
			{
				$modelCustomerlogs->fk_user_id = $model->customer_id;
				$modelCustomerlogs->action = 'update';
				$modelCustomerlogs->query = json_encode($arrPostData);
				$modelCustomerlogs->controller = 'CustomerController';
				$modelCustomerlogs->ip_address = $this->get_client_ip();
				$modelCustomerlogs->hostname = gethostname();
				$modelCustomerlogs->agent = json_encode($this->getBrowser());
				$modelCustomerlogs->log_date = date('Y-m-d H:i:s');	
				$modelCustomerlogs->fk_system_user_id = Yii::$app->user->identity->user_id;				
				$modelCustomerlogs->save();

				$strPaymentType = $arrPostData['Linkcustomepackage']['payment_type'];
				$modelLinkCustPackage->payment_type = $strPaymentType;
				$modelLinkCustPackage->bundling_package = $arrPostData['Linkcustomepackage']['bundling_package'];
 				if($strPaymentType =='term'){
					$modelLinkCustPackage->payment_term = $arrPostData['Linkcustomepackage']['payment_term'];
					$modelLinkCustPackage->term_grace_period = $arrPostData['Linkcustomepackage']['term_grace_period'];
					//$modelLinkCustPackage->bulk_pay_start = '0000-00-00';
					//$modelLinkCustPackage->bulk_pay_end = '0000-00-00';
				}elseif($strPaymentType =='bulk'){
					$modelLinkCustPackage->bulk_pay_start = Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['start_date'], 'php:Y-m-d');
					$modelLinkCustPackage->bulk_pay_end =  Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['end_date'], 'php:Y-m-d');
					$modelLinkCustPackage->bulk_grace_period = $arrPostData['Linkcustomepackage']['bulk_grace_period'];
				}else{
					$modelLinkCustPackage->payment_term = '';
					$modelLinkCustPackage->pay_in_advanc_grace_period = $arrPostData['Linkcustomepackage']['pay_in_advanc_grace_period'];
				}

				$modelLinkCustPackage->fk_customer_id = $model->customer_id;
				$modelLinkCustPackage->updated_at  = date('Y-m-d h:i:s');
				//$modelLinkCustPackage->order_received_date = date('Y-m-d h:i:s');
				
				
				if($modelLinkCustPackage->save())
				{
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Update Customer';
					$logArray['action'] = 'update';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated a customer "'.$model->name.'"';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					return $this->redirect(['view', 'id' => $model->customer_id]);
				}
				
			}


        }else {

        }
		return $this->render('update', [
                'model' => $model,
				'modelLinkCustPackage'=>$modelLinkCustPackage,
				'stateList'=>$stateListData,
				'countryList'=>$countryListData,
				'speedList'=>$speedListData,
				'currencyList'=>$currencyListData,
				'packageList'=>$packageListData
            ]);
    }

	/*
	*  Function To activate customers with generating the invoices
	*/

	 public function actionActivate($id)
    {
		$model = $this->findModel($id);

		$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
		
		$modelLinkCustPackage->scenario = 'activate';
		$invoiceStartDate = "0000-00-00 00:00:00";
		$contractStartDate = "0000-00-00";
		$contractEndDate = "0000-00-00";

		/*************To fetch package from table************/
		$arrPackage 	= Package::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
		$packageListData	= ArrayHelper::map($arrPackage,'package_id','package_title');
		/*************To fetch state from table************/

		/*************To fetch speed from table************/
		$arrSpeed 	= Speed::find()->where(['status'=>'active'])->all();
		$speedListData	= ArrayHelper::map($arrSpeed,'speed_id','speed_type');
		/*************To fetch state from table************/

		/*************To fetch currency from table************/
		$arrCurrency 	= Currency::find()->where(['status'=>'active'])->all();
		$currencyListData	= ArrayHelper::map($arrCurrency,'currency_id','currency');
		/*************To fetch state from table************/

    /*************To fetch currency from table************/
    $arrBank 	= Bank::find()->where(['status'=>'active','is_deleted'=> '0'])->all();
    $bankListData	= ArrayHelper::map($arrBank,'bank_id','account_no');
    /*************To fetch state from table************/


		$arrPostData = Yii::$app->request->post();
		if(!empty($arrPostData)){
			
			if($arrPostData['Linkcustomepackage']['is_solnet_bank'] == 'solnet'){
				$modelLinkCustPackage->is_solnet_bank = 'yes';
			}else{
				$modelLinkCustPackage->is_solnet_bank = 'no';
			}
			
			$invoiceStartDate =  Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['invoice_start_date'], 'php:Y-m-d');
			$contractStartDate = Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['contract_start_date'],'php:Y-m-d');
			$contractEndDate =	 Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['contract_end_date'],'php:Y-m-d');
			$model->first_invoice_date = Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['invoice_start_date'],'php:Y-m-d H:i:s');
			
		//}
			$modelLinkCustPackage->invoice_start_date = $invoiceStartDate;
			$modelLinkCustPackage->contract_start_date = $contractStartDate;
			$modelLinkCustPackage->contract_end_date = $contractEndDate;
			
			$modelLinkCustPackage->bank_id = $arrPostData['Linkcustomepackage']['bank_id'];
			$modelLinkCustPackage->bank_name = $arrPostData['Linkcustomepackage']['bank_name'];
			$modelLinkCustPackage->virtual_acc_no = $arrPostData['Linkcustomepackage']['virtual_acc_no'];
			$modelLinkCustPackage->account_name = $arrPostData['Linkcustomepackage']['account_name'];
		
		
			$arrGetCustID = yii::$app->customcomponents->getCustomerId($model->fk_state_id);


			$model->solnet_customer_id = $arrGetCustID['current_cust_id'];
			$model->is_invoice_activated = 'yes';
			$modelLinkCustPackage->updated_at = date('Y-m-d h:i:s');
		
		
		
		if ($modelLinkCustPackage->save() && $model->save()) {

			$arrInvoice = Yii::$app->customcomponents->updateCustomerIncrementValue($model->fk_state_id,$arrGetCustID['increment_value']+1);
			$resultInvoice  = yii::$app->customcomponents->GenerateInvoice($id,'activate',$arrGetCustID);
			if($resultInvoice==1)
			{
				$session = Yii::$app->session;
				$session->setFlash('success','Customer activated successfully.');
				/************Log Activity*********/
				$logArray = array();
				$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
				$logArray['module'] = 'Activate Customer';
				$logArray['action'] = 'update';
				$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has activated the invoice for a customer "'.$model->name.'"';
				$logArray['created'] = date('Y-m-d H:i:s');
				Yii::$app->customcomponents->logActivity($logArray);
				/************Log Activity*********/
				return $this->redirect(['customer/pending']);

			}elseif($resultInvoice==0)
			{
				$session = Yii::$app->session;
				$session->setFlash('success','Customer activated successfully.');
				/************Log Activity*********/
				$logArray = array();
				$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
				$logArray['module'] = 'Activate Customer';
				$logArray['action'] = 'update';
				$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has activated the invoice for a customer "'.$model->name.'"';
				$logArray['created'] = date('Y-m-d H:i:s');
				Yii::$app->customcomponents->logActivity($logArray);
				/************Log Activity*********/
				return $this->redirect(['customer/pending']);
			}
		} 
	
	}else {
            return $this->render('activate_customer', [
                'model' => $model,
				'modelLinkCustPackage'=>$modelLinkCustPackage,
				'speedList'=>$speedListData,
				'currencyList'=>$currencyListData,
				'packageList'=>$packageListData,
				'bankList' => $bankListData
            ]);
        }
    }

	/*
	*  Function To delete multiple customers
	*/
	public function actionDeletemultiple()
	{
		$ids = yii::$app->request->post('ids');
		if(!empty($ids))
		{
			if(Customer::deleteAll(['customer_id'=>$ids]))
			{
				if(Linkcustomepackage::deleteAll(['fk_customer_id'=>$ids]))
				{
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Delete Multiple Customer';
					$logArray['action'] = 'delete';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted multiple customers';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					return 'success';
				}
				else
				{
					return 'failed';
				}
			}
			else
			{
				return 'failed';
			}

		}
		else
		{
			return 'failed';
		}

	}

    /**
     * Deletes an existing Customer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$session = Yii::$app->session;
		$model = $this->findModel($id);
		$strDate = new Expression('NOW()');
		$model->is_deleted = '1';
		$model->updated_at = $strDate;
		$model->save();
		
		/************Log Activity*********/
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Delete Customer';
			$logArray['action'] = 'delete';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted a customer';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			/************Log Activity*********/
			return $this->redirect(['index']);
		
        //$qryDelete = $this->findModel($id)->delete();
		/*if($qryDelete){
			$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id])->one();
			$modelLinkCustPackage->delete();
			$session->setFlash('success',CUSTOMER_DELETE_SUCCESSFULL);
			
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Delete Customer';
			$logArray['action'] = 'delete';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted a customer';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			
			return $this->redirect(['index']);
		}*/

    }


	/*
	* Function to update billing information
	*/
	public function actionPlan($id)
	{
		$model = $this->findModel($id);
		$modelCustomerlogs	= new Customerlogs();
		$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
		//echo "<pre>"; print_r($modelLinkCustPackage);die;
		$strIsSolnetBank = isset($modelLinkCustPackage->is_solnet_bank) ? $modelLinkCustPackage->is_solnet_bank : '';
		$strBankId		 = isset($modelLinkCustPackage->bank_id) ? $modelLinkCustPackage->bank_id : '';
		$strBankName 	 = isset($modelLinkCustPackage->bank_name) ? $modelLinkCustPackage->bank_name : '';
		$strVirtualAcc	 = isset($modelLinkCustPackage->virtual_acc_no) ? $modelLinkCustPackage->virtual_acc_no : ''; 
		$strAccName 	 = isset($modelLinkCustPackage->account_name) ? $modelLinkCustPackage->account_name : '';
		$strActDate 	 = isset($modelLinkCustPackage->activation_date) ? $modelLinkCustPackage->activation_date : '';


		if($model->is_address_same=='yes')
		{
			$strBillAddress = $model->billing_address;
		}
		else{
			$strBillAddress = $modelLinkCustPackage->installation_address;
		}
		/*************To fetch state from table************/
		$arrState 	= State::find()->where(['status'=>'active','fk_country_id'=>$model->fk_country_id])->all();
		$stateListData	= ArrayHelper::map($arrState,'state_id','state');
		/*************To fetch state from table************/

		/*************To fetch state from table************/
		$arrCountry 	= Country::find()->where(['status'=>'active'])->all();
		$countryListData	= ArrayHelper::map($arrCountry,'country_id','country');
		/*************To fetch state from table************/

		/*************To fetch package from table************/
		$arrPackage 	= Package::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
		$packageListData	= ArrayHelper::map($arrPackage,'package_id','package_title');
		/*************To fetch state from table************/

		/*************To fetch speed from table************/
		$arrSpeed 	= Speed::find()->where(['status'=>'active'])->all();
		$speedListData	= ArrayHelper::map($arrSpeed,'speed_id','speed_type');
		/*************To fetch state from table************/

		/*************To fetch currency from table************/
		$arrCurrency 	= Currency::find()->where(['status'=>'active'])->all();
		$currencyListData	= ArrayHelper::map($arrCurrency,'currency_id','currency');
		/*************To fetch state from table************/

		/*************To fetch currency from table************/
		$arrBank 	= Bank::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
		$bankListData	= ArrayHelper::map($arrBank,'bank_id','account_no');
		/*************To fetch state from table************/
		$strFilePath = $model->filepath;
		$connection = Yii::$app->db;
		if (Yii::$app->request->post()) {
			$intTimeStamp = date('Ymdhis');
			$filesArray = UploadedFile::getInstance($model, 'filepath');
			if(!empty($filesArray)){
				$filesArray->saveAs('uploads/user_docs/' . 'User_'.$intTimeStamp. '.' . $filesArray->extension);
				$filepath='User_'.$intTimeStamp.'.'.$filesArray->extension;
				if(!empty($strFilePath)){
					if(file_exists(Yii::$app->basePath."/web/uploads/user_docs/".$strFilePath)){
						unlink(Yii::$app->basePath."/web/uploads/user_docs/".$strFilePath);
					}
				}
			}else{
				$filepath = $strFilePath;
			}
		 }

		$arrPostData =  Yii::$app->request->post();
		if($modelLinkCustPackage->load(Yii::$app->request->post()) || $model->load(Yii::$app->request->post()))
		{
				$strTab = $arrPostData['tab'];
				if($strTab=='personal')
				{
					//$model->it_pic = $arrPostData['Customer']['it_pic'];
					//$model->optional_email = $arrPostData['Customer']['optional_email'];
					if ($model->load(Yii::$app->request->post()) && $model->save())
					{
						
						if($model->is_address_same=='1'){
						$strAddress = $model->billing_address;
						$strIsSame = 'yes';
						}else{
							$strAddress = $arrPostData['Linkcustomepackage']['installation_address'];
							$strIsSame = 'no';
						}

						$command = $connection->createCommand("UPDATE linkcustomepackage SET installation_address='".$strAddress."' WHERE fk_customer_id=".$id." AND is_current_package='yes'");
						$updateResult = $command->execute();

						$command = $connection->createCommand("UPDATE tblcustomer SET filepath='".$filepath."',is_address_same='".$strIsSame."' WHERE customer_id=".$id."");
						$updateFilePath = $command->execute();

						$modelCustomerlogs->fk_user_id = $id;
						$modelCustomerlogs->fk_system_user_id = Yii::$app->user->identity->user_id;
						$modelCustomerlogs->action = 'update';
						$modelCustomerlogs->query = json_encode($arrPostData);
						$modelCustomerlogs->controller = 'CustomerController';
						$modelCustomerlogs->ip_address = $this->get_client_ip();
						$modelCustomerlogs->hostname = gethostname();
						$modelCustomerlogs->agent = json_encode($this->getBrowser());
						$modelCustomerlogs->log_date = date('Y-m-d H:i:s');				
						$modelCustomerlogs->save();

						/************Log Activity*********/
						$logArray = array();
						$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
						$logArray['module'] = 'Update Customer Personal Details';
						$logArray['action'] = 'update';
						$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated a customer\'s personal details named as "'.$model->name.'"';
						$logArray['created'] = date('Y-m-d H:i:s');
						Yii::$app->customcomponents->logActivity($logArray);
						/************Log Activity*********/
						Yii::$app->session->setFlash('success', CUSTOMER_PERSONAL_UPDATE_SUCCESSFULL);
            				return $this->redirect(['plan', 'id' => $id]);

					}
				}
				elseif($strTab=='package')
				{

					$modelLinkCustPackage->scenario = 'package';
					$updateResult = 0;
					if($modelLinkCustPackage->validate()){
						$command = $connection->createCommand("UPDATE linkcustomepackage SET is_current_package='no',updated_at='".date('Y-m-d h:i:s')."' WHERE fk_customer_id=".$id." AND is_current_package='yes'");
						$updateResult = $command->execute();
					}
					//echo $updateResult;die;
					if ($updateResult == 1)
					{
						$modelLinkCustPackage = new Linkcustomepackage();
						$modelLinkCustPackage->package_speed= $arrPostData['Linkcustomepackage']['package_speed'];
						$modelLinkCustPackage->fk_package_id= $arrPostData['Linkcustomepackage']['fk_package_id'];
						$modelLinkCustPackage->fk_speed_id= $arrPostData['Linkcustomepackage']['fk_speed_id'];
						$modelLinkCustPackage->package_price= $arrPostData['Linkcustomepackage']['package_price'];
						$modelLinkCustPackage->other_service_fee= $arrPostData['Linkcustomepackage']['other_service_fee'];
						$modelLinkCustPackage->installation_fee= $arrPostData['Linkcustomepackage']['installation_fee'];
						$modelLinkCustPackage->fk_currency_id= $arrPostData['Linkcustomepackage']['fk_currency_id'];
						$modelLinkCustPackage->payment_type= $arrPostData['Linkcustomepackage']['payment_type'];

						$modelLinkCustPackage->contract_number= $arrPostData['Linkcustomepackage']['contract_number'];
						
						$modelLinkCustPackage->invoice_start_date=  Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['invoice_start_date'], 'php:Y-m-d H:i:s');
						$modelLinkCustPackage->contract_end_date= Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['contract_end_date'], 'php:Y-m-d H:i:s');
						$modelLinkCustPackage->contract_start_date= Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['contract_start_date'], 'php:Y-m-d H:i:s') ;
						$modelLinkCustPackage->activation_date= $strActDate;
						$modelLinkCustPackage->installation_address=$strBillAddress;
						$modelLinkCustPackage->bundling_package=$arrPostData['Linkcustomepackage']['bundling_package'];
						//$modelLinkCustPackage->updated_at = date('Y-m-d h:i:s');
						if($strIsSolnetBank=='yes')
						{
							$modelLinkCustPackage->is_solnet_bank= 'yes';
							$modelLinkCustPackage->bank_id = $strBankId;
						}elseif($strIsSolnetBank=='no'){
							$modelLinkCustPackage->bank_name = $strBankName;
							$modelLinkCustPackage->virtual_acc_no = $strVirtualAcc;
							$modelLinkCustPackage->account_name = $strAccName;
						}

						if($arrPostData['Linkcustomepackage']['payment_type']=='bulk')
						{
								$modelLinkCustPackage->bulk_pay_start =  Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['start_date'], 'php:Y-m-d H:i:s');
								$modelLinkCustPackage->bulk_pay_end = Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['end_date'], 'php:Y-m-d H:i:s');
								$modelLinkCustPackage->bulk_price = $arrPostData['Linkcustomepackage']['bulk_price'];
								$modelLinkCustPackage->payment_term = '';
								$modelLinkCustPackage->bulk_grace_period = $arrPostData['Linkcustomepackage']['bulk_grace_period'];
						}
						elseif($arrPostData['Linkcustomepackage']['payment_type']=='term')
						{
								$modelLinkCustPackage->payment_term = $arrPostData['Linkcustomepackage']['payment_term'];
								$modelLinkCustPackage->term_grace_period = $arrPostData['Linkcustomepackage']['term_grace_period'];
						}
						else
						{
								/*$modelLinkCustPackage->bulk_pay_start = '0000-00-00';
								$modelLinkCustPackage->bulk_pay_end = '0000-00-00';*/
								$modelLinkCustPackage->payment_term = '';
								$modelLinkCustPackage->pay_in_advanc_grace_period = $arrPostData['Linkcustomepackage']['pay_in_advanc_grace_period'];
						}
						$modelLinkCustPackage->fk_customer_id = $id;
						$modelLinkCustPackage->is_current_package = 'yes';
						//$modelLinkCustPackage->order_received_date = date('Y-m-d h:i:s');
						$modelLinkCustPackage->created_at = date('Y-m-d h:i:s');
						
						if($modelLinkCustPackage->save()){

							$modelCustomerlogs->fk_user_id = $id;
							$modelCustomerlogs->fk_system_user_id = Yii::$app->user->identity->user_id;
							$modelCustomerlogs->action = 'update';
							$modelCustomerlogs->query = json_encode($arrPostData);
							$modelCustomerlogs->controller = 'CustomerController';
							$modelCustomerlogs->ip_address = $this->get_client_ip();
							$modelCustomerlogs->hostname = gethostname();
							$modelCustomerlogs->agent = json_encode($this->getBrowser());
							$modelCustomerlogs->log_date = date('Y-m-d H:i:s');				
							$modelCustomerlogs->save();


							Yii::$app->session->setFlash('success', CUSTOMER_PACKAGE_UPDATE_SUCCESSFULL);
							/************Log Activity*********/
							$logArray = array();
							$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
							$logArray['module'] = 'Update Customer Package Details';
							$logArray['action'] = 'update';
							$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated a customer\'s package details named as "'.$model->name.'"';
							$logArray['created'] = date('Y-m-d H:i:s');
							Yii::$app->customcomponents->logActivity($logArray);
							/************Log Activity*********/
            				return $this->redirect(['plan', 'id' => $id,'tab'=>'package']);
						}
						else{
							echo '<pre>';
							print_r($modelLinkCustPackage->getErrors());
							die;
						}
					}
					else
					{
						$error = \yii\widgets\ActiveForm::validate($modelLinkCustPackage);
						Yii::$app->response->format = trim(Response::FORMAT_JSON);
						return $error;
					}
				}
				elseif($strTab=='bank')
				{
					$strbankType = $arrPostData['Linkcustomepackage']['bank_type'];
					if($strbankType=='virtual')
					{
						$modelLinkCustPackage->scenario = 'bank';
						$modelLinkCustPackage->is_solnet_bank = 'no';
						$modelLinkCustPackage->account_name = $arrPostData['Linkcustomepackage']['account_name'];
						$modelLinkCustPackage->bank_name = $arrPostData['Linkcustomepackage']['bank_name'];
						$modelLinkCustPackage->virtual_acc_no = $arrPostData['Linkcustomepackage']['virtual_acc_no'];
					}else{
						$modelLinkCustPackage->scenario = 'solnet';
						$modelLinkCustPackage->is_solnet_bank = 'yes';
						$modelLinkCustPackage->bank_id = $arrPostData['Linkcustomepackage']['bank_id'];
					}

					if($modelLinkCustPackage->save())
					{
						$session = Yii::$app->session;
						$session->setFlash('success',CUSTOMER_BANKE_UPDATE_SUCCESSFULL);
						/************Log Activity*********/
						$logArray = array();
						$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
						$logArray['module'] = 'Update Customer Bank Details';
						$logArray['action'] = 'update';
						$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated a customer\'s bank details named as "'.$model->name.'"';
						$logArray['created'] = date('Y-m-d H:i:s');
						Yii::$app->customcomponents->logActivity($logArray);
						/************Log Activity*********/
						return $this->redirect(['plan', 'id' => $id,'tab'=>'bank']);
					}
					else
					{
						$error = \yii\widgets\ActiveForm::validate($modelLinkCustPackage);
						Yii::$app->response->format = trim(Response::FORMAT_JSON);
						return $error;
					}

				}


        }


		return $this->render('edit_billing', [
					'model' => $model,
					'modelLinkCustPackage'=>$modelLinkCustPackage,
					'stateList'=>$stateListData,
					'countryList'=>$countryListData,
					'speedList'=>$speedListData,
					'currencyList'=>$currencyListData,
					'packageList'=>$packageListData,
					'bankList'=>$bankListData
            	]);
	}

	/*
	*  Function To get bank details from bank model
	*/
	public function actionGetbankdetails($id)
	{
		$objBank = Bank::find()->where(['bank_id'=>$id])->one();
		$arrBank = array();
		if(!empty($objBank))
		{
			$arrBank['0'] 	 = $objBank->bank_name;
			$arrBank['1'] 	 = $objBank->account_name;
			$arrBank['2'] = $objBank->bank_branch;
			$arrBank['3'] 	 = $objBank->currency->currency;
			echo  json_encode($arrBank);
		}
		else{
			return $arrBank;
		}
	}

	/**
	* list all the customer whoes installation are pending
	*/
	 public function actionPendinginstallation()
    {
        $searchModel = new CustomerSearch();
        $modelCustomer = new Customer();
        $arrSalesPerson = array();
		$queryParams = Yii::$app->request->queryParams;
		$queryParams = Yii::$app->request->queryParams;
		$arrSalesPerson = $modelCustomer->getUserName();

		$queryParams['CustomerSearch']['installation_status']='no';
		$queryParams['CustomerSearch']['is_deleted']=0;
		/*$sessionData = Yii::$app->session;
		
		if($sessionData->get('user_state_id'))
		{
			$stateId = $sessionData->get('user_state_id');
			$queryParams['CustomerSearch']['fk_state_id']=$stateId;
		}*/
        $dataProvider = $searchModel->searchPendingInstallation($queryParams);
		

		$intTotal = $searchModel->getTotal($queryParams);		
        if(Yii::$app->user->identity->fk_role_id =='8' || Yii::$app->user->identity->fk_role_id =='23' || Yii::$app->user->identity->fk_role_id =='25' || Yii::$app->user->identity->fk_role_id =='22'){    
	        return $this->render('activate', [
	            'searchModel' 	=> $searchModel,
	            'dataProvider' 	=> $dataProvider,
	            'user'			=> $arrSalesPerson,
				'model' 		=> $modelCustomer,
				'intTotal' 	    => $intTotal,
	        ]);
	    }elseif( Yii::$app->user->identity->fk_role_id =='24' ){
	    		return $this->render('activate_noc', [
	            'searchModel' 	=> $searchModel,
	            'dataProvider' 	=> $dataProvider,
	            'user'			=> $arrSalesPerson,
				'model' 		=> $modelCustomer,
				'intTotal' 	    => $intTotal,
	        ]);
	    }else{
	        return $this->render('activate_new', [
	            'searchModel' 	=> $searchModel,
	            'dataProvider' 	=> $dataProvider,
	            'user'			=> $arrSalesPerson,
				'model' 		=> $modelCustomer,
				'intTotal' 	    => $intTotal,
	        ]);
	    }
    }
	
	
	/**
	* list all the deleted customers in pending installation list
	*/
	 public function actionPendinginstallationtrash()
    {
        $searchModel = new CustomerSearch();
        $modelCustomer = new Customer();
        $arrSalesPerson = array();
		$queryParams = Yii::$app->request->queryParams;
		$queryParams = Yii::$app->request->queryParams;
		$arrSalesPerson = $modelCustomer->getUserName();

		$queryParams['CustomerSearch']['installation_status']='no';
		$queryParams['CustomerSearch']['is_deleted']=1;
		/*$sessionData = Yii::$app->session;
		
		if($sessionData->get('user_state_id'))
		{
			$stateId = $sessionData->get('user_state_id');
			$queryParams['CustomerSearch']['fk_state_id']=$stateId;
		}*/
        $dataProvider = $searchModel->searchPendingInstallation($queryParams);

        return $this->render('pending_trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user'=>$arrSalesPerson
        ]);
    }
	
	

    /**
	* popup modal of specific customer details to activate him
	*/
     public function actionActivateinstallation($id)
    {
		/* EQUIPEMENT TASK */		
    	/*******To fetch data from Linkcustomepackage,package and customer model currency*****/
		$model=linkcustomepackage::find()->joinWith(['customer as c','package','currency'])->where(['fk_customer_id'=>$id,'is_current_package'=>'yes','c.is_deleted'=>'0','c.installation_status'=>'no'])->one();

		$equpmentModel     = new CustomerEquipments();
    	$arrResultEqupment = CustomerEquipments::find()->joinWith(['equmentData','equmentMacData'])->where(['fk_customer_id'=>$id])->all();

       	return  $this->renderAjax('activateinstallation',['model'=>$model,'id'=>$id,'arrResultEqupment'=>$arrResultEqupment,'equpmentModel' => $equpmentModel]);
       	/* END EQUIPEMENT TASK */
		
    }

    /**
	* submit activation date and make customer active and reomove from pending installation
	*/
     public function actionSubmitactivation($id)
    {

    	 $model= new Linkcustomepackage();
    	 /* EQUIPEMENT TASK */
    	 $arrPostData = Yii::$app->request->post();
    	 /* END EQUIPEMENT TASK */
    	 /*******To fetch  activation date*****/
      	 $arrDate=Yii::$app->request->post('Linkcustomepackage');
    	 //$strInstallationDate = $arrDate['activation_date'];
      $strInstallationDate =  Yii::$app->formatter->asDate($arrDate['activation_date'], 'php:Y-m-d');

    		 if ($model->load(Yii::$app->request->post()) && $model->validate()){
    		 	if(!empty($strInstallationDate)){

		         	/* EQUIPEMENT TASK */
					if (isset($arrPostData['CustomerEquipments']['price'])) {
	    		 		foreach ($arrPostData['CustomerEquipments']['price'] as $key => $arrFkEquIdValue) {
	    		 			$query = Yii::$app->db->createCommand()->update('tbl_customer_equipments', ['price' => $arrFkEquIdValue[0]], ['id' => $key])->execute();
	    		 		}
    		 		}
    		 		/* END EQUIPEMENT TASK */
		         	$query=Yii::$app->db->createCommand()
							->update('linkcustomepackage', ['activation_date' =>$strInstallationDate,'updated_at'=>date('Y-m-d h:i:s') ], ['fk_customer_id' =>$id])
							->execute();

					$query=Yii::$app->db->createCommand()
							->update('tblcustomer', ['installation_status' =>'yes','updated_at'=>date('Y-m-d h:i:s') ],
							 ['customer_id' =>$id])
							->execute();
					if($query){
						$modelCustomer=Customer::findOne($id);
						$logArray = array();
		                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
		                $logArray['module'] = 'Pending Installation';
		                $logArray['action'] = 'update';
		                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has activated "'.$modelCustomer->name.'" ';
		                $logArray['created'] = date('Y-m-d H:i:s');
		                Yii::$app->customcomponents->logActivity($logArray);

		            Yii::$app->session->setFlash('success', PENDING_ACTIVATE_SUCCESSFULL);
		            return $this->redirect(['pendinginstallation']);
		        }
		      }
    	}

	   }


    /**
     * Display a single pending installation details .
     * @param integer $id
     * @return mixed
     */
    public function actionInstallationview($id)
    {

    	 /*******To fetch  pending installation details from Linkcustomepackage Model*****/
    	$model= Linkcustomepackage::find()->joinWith(['customer','package','currency'])->where(['fk_customer_id'=>$id])->one();
    	return $this->render('installation_view',['model'=>$model]);
    }


    /**
     * Generate a pdf to print single pending installation details .
     * @param integer $id
     * @return mixed
     */
    public function actionInstallationprint($id) {
       	 $model=Linkcustomepackage::find()->joinWith(['customer','package','currency','speed'])->where(['fk_customer_id'=>$id])->one();

	        $pdf = new Pdf([
		        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
		        'content' => $this->renderPartial('installation_view', [
		            'model' => $model,
		        ]),
		        'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
		        'options' => [
		            'title' => $model->customer->name,
		            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
		        ],
		        'methods' => [
		            //'SetHeader' => ['Generated By: Solnet'],
		            'SetFooter' => ['|Page {PAGENO}|'],
		        ]
	    	]);
	        $logArray = array();
	        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
	        $logArray['module'] = 'Pending Installation';
	        $logArray['action'] = 'update';
	        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has printed pending installation view' ;
	        $logArray['created'] = date('Y-m-d H:i:s');
	        Yii::$app->customcomponents->logActivity($logArray);

	        return $pdf->render();

    }

	/**
	* change status from active to inactive and vice a versa
	*/

	public function actionTogglestatus()
	{
		$id	=	Yii::$app->request->post('id');
		$status	=	Yii::$app->request->post('page_status');
		$model = $this->findModel($id);
		if(!empty($model) && !empty($status) && !empty($id))
		{
			if($status=='active'){
			$model->status='inactive';
			}else if($status=='inactive'){
				$model->status='active';
			}
			$model->updated_at = new Expression('NOW()');
			if($model->save())
			{
				$logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Customer';
                $logArray['action'] =$model->status;
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the customer "'.$model->name.'" status to "'.$model->status.'"';
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);
				return true;
			}
			else
			{
				//return false;
				return $model->getErrors();
			}
		}
	}

	public function actionBillingsingledelete($id)
	{

		$model = $this->findModel($id);
		if(!empty($model) )
		{
			$strDate = new Expression('NOW()');
			$model->is_deleted = '1';
			$model->updated_at = $strDate;
			if($model->save())
			{
				$objLinkcustpackage = Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
				if(!empty($objLinkcustpackage))
				{
					$objLinkcustpackage->updated_at = $strDate;
					$objLinkcustpackage->disconnection_date = $strDate;
					$objLinkcustpackage->is_disconnected = 'yes';
					if($objLinkcustpackage->save())
					{
						$session = Yii::$app->session;
						$session->setFlash('success','Customer deleted successfully.');
						/************Log Activity*********/
						$logArray = array();
						$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
						$logArray['module'] = 'Delete Billing Customer';
						$logArray['action'] = 'delete';
						$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted the billing customer "'.$model->name.'".';
						$logArray['created'] = date('Y-m-d H:i:s');
						Yii::$app->customcomponents->logActivity($logArray);
						/************Log Activity*********/
						return $this->redirect(['billing']);
					}
				}
			}

		}
	}

	/**
	* Delete multiple customers from billing customers.
	*/

	public function actionBillingmultipledelete()
	{
		$ids = yii::$app->request->post('ids');
		$strDate = new Expression('NOW()');
		if(Customer::updateAll(['is_deleted' => '1','updated_at'=>$strDate],['customer_id'=>$ids]))
		{
			if(Linkcustomepackage::updateAll(['is_disconnected'=>'yes','disconnection_date' => $strDate,'updated_at'=>$strDate],['fk_customer_id'=>$ids,'is_current_package' => 'yes']));
			{
				$session = Yii::$app->session;
				$session->setFlash('success',CUSTOMER_BILLING_DELETE_SUCCESSFULL);
				/************Log Activity*********/
				$logArray = array();
				$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
				$logArray['module'] = 'Delete Multiple Billing Customer';
				$logArray['action'] = 'delete';
				$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted multiple billing customer.';
				$logArray['created'] = date('Y-m-d H:i:s');
				Yii::$app->customcomponents->logActivity($logArray);
				/************Log Activity*********/
				return $this->redirect(['billing']);
			}
		}
	}

	/**
	* Delete multiple customers from activate customers.
	*/

	public function actionActivatemultipledelete()
	{
		$ids = yii::$app->request->post('ids');
		$strDate = new Expression('NOW()');
		if(Customer::updateAll(['is_deleted' => '1','updated_at'=>$strDate],['customer_id'=>$ids]))
		{
				$session = Yii::$app->session;
				$session->setFlash('success',CUSTOMER_ACTIVATE_DELETE_SUCCESSFULL);
				/************Log Activity*********/
				$logArray = array();
				$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
				$logArray['module'] = 'Delete Multiple Customer';
				$logArray['action'] = 'delete';
				$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted multiple customer whose installation was completed.';
				$logArray['created'] = date('Y-m-d H:i:s');
				Yii::$app->customcomponents->logActivity($logArray);
				/************Log Activity*********/
				return $this->redirect(['customer/pending']);
		}
	}

	/**
	* Delete single customer from activate customers.
	*/

	public function actionActivatesingledelete($id)
	{
		$model = $this->findModel($id);
		$model->is_deleted = '1';
		$strDate = new Expression('NOW()');
		$model->updated_at = $strDate;
		if(!empty($model))
		{
			if($model->save())
			{
					$session = Yii::$app->session;
					$session->setFlash('success',CUSTOMER_ACTIVATE_DELETE_SUCCESSFULL);
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Delete Customer';
					$logArray['action'] = 'delete';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted the customer whose installation was completed.';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					return $this->redirect(['customer/pending']);
			}
		}
	}

	/**
	*  Function to show ajax popup and disconnect customer
	*/
	public function actionDisconnect($id)
	{
		$model = $this->findModel($id);
		$objLinkcustpackage = Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
		
		$objLinkcustpackage->scenario = 'disconnect';
		$objLinkcustpackage->is_disconnected = 'yes';
		$objLinkcustpackage->updated_at		 = date('Y-m-d h:i:s');
		$objLinkcustpackage->fk_user_id =Yii::$app->user->identity->user_id;
		$model->status = 'inactive';
		$model->updated_at = date('Y-m-d h:i:s');
		
		if($objLinkcustpackage->load(Yii::$app->request->post())){
			$objLinkcustpackage->disconnection_date =  Yii::$app->formatter->asDate($objLinkcustpackage->disconnection_date, 'php:Y-m-d');
			$objLinkcustpackage->reason_for_disconnection = Yii::$app->request->post()['Linkcustomepackage']['reason_for_disconnection'];
			
			if($objLinkcustpackage->save() && $model->save()){
				
					$session = Yii::$app->session;
					$session->setFlash('success',CUSTOMER_DISCONNECT_SUCCESSFULL);
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Disconnect Customer';
					$logArray['action'] = 'update';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has disconnected the customer "'.$model->name.'".';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					return $this->redirect(['customer/billing']);
			}
		} else{
					echo $this->renderAjax('disconnect',['modellinkcustpckg'=>$objLinkcustpackage,'model'=>$model]);
			}
		
	}


	/**
	*  Function to show ajax popup and disconnect customer
	*/
	public function actionReactivate($id)
	{
		$model = $this->findModel($id);
		/*************To fetch package from table************/
		$arrPackage 	= Package::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
		$packageListData	= ArrayHelper::map($arrPackage,'package_id','package_title');
		/*************To fetch state from table************/

		/*************To fetch speed from table************/
		$arrSpeed 	= Speed::find()->where(['status'=>'active'])->all();
		$speedListData	= ArrayHelper::map($arrSpeed,'speed_id','speed_type');
		/*************To fetch state from table************/


		$objLinkcustpackage = Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
		//$intPreviousPackgId = $objLinkcustpackage->fk_package_id;
		$intCurrencyId 		= $objLinkcustpackage->fk_currency_id;
		$intOtherServiceFee	= $objLinkcustpackage->other_service_fee;
		$intInstallationFee = $objLinkcustpackage->installation_fee;
		$strInstallAddress  = $objLinkcustpackage->installation_address;
		$strContractStartDate = $objLinkcustpackage->contract_start_date;
		$strContractEndDate   = $objLinkcustpackage->contract_end_date;
		$strIsSolnetBank	  = $objLinkcustpackage->is_solnet_bank;
		$intBankId 			  = $objLinkcustpackage->bank_id;
		$strBankName		  = $objLinkcustpackage->bank_name;
		$strVirtualAcc 		  = $objLinkcustpackage->virtual_acc_no;
		$strAccName			  = $objLinkcustpackage->account_name;
		$strContractNumber	  = $objLinkcustpackage->contract_number;
		$strPackageSpeed 	  = $objLinkcustpackage->package_speed;
		$strActivationDate    = $objLinkcustpackage->activation_date;
		$objLinkcustpackage->scenario = 'reactivate';
		$objLinkcustpackage->is_current_package = 'no';
		$objLinkcustpackage->updated_at		 = date('Y-m-d h:i:s');
		$objLinkcustpackage->fk_user_id =Yii::$app->user->identity->user_id;
		//$objLinkcustpackage->fk_package_id = $intPreviousPackgId;

		$model->status = 'active';
		$model->updated_at = date('Y-m-d h:i:s');

		if($objLinkcustpackage->load(Yii::$app->request->post())  && $model->save())
		{
			$connection = Yii::$app->db;

			$arrPostData = Yii::$app->request->post();
			$command = $connection->createCommand("UPDATE linkcustomepackage SET is_current_package='no', reactivation_date='".Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['reactivation_date'], 'php:Y-m-d H:i:s')."' WHERE fk_customer_id=".$id." AND is_disconnected='yes' AND is_current_package='yes'");
			$updateResult = $command->execute();
			if($updateResult)
			{
				$newLinkCustPcg = new Linkcustomepackage();
				$newLinkCustPcg->fk_customer_id = $id;
				$newLinkCustPcg->fk_package_id = $arrPostData['Linkcustomepackage']['fk_package_id'];
				$newLinkCustPcg->fk_speed_id = $arrPostData['Linkcustomepackage']['fk_speed_id'];
				$newLinkCustPcg->fk_currency_id = $intCurrencyId;
				$newLinkCustPcg->package_speed = $strPackageSpeed;
				$newLinkCustPcg->package_price = $arrPostData['Linkcustomepackage']['package_price'];
				$newLinkCustPcg->other_service_fee = $intOtherServiceFee;
				$newLinkCustPcg->installation_fee = $intInstallationFee;
				$newLinkCustPcg->installation_address=$strInstallAddress;
				//$newLinkCustPcg->order_received_date = date('Y-m-d h:i:s');
				$newLinkCustPcg->activation_date = $strActivationDate;
				$newLinkCustPcg->contract_start_date = $strContractStartDate;
				$newLinkCustPcg->contract_end_date = $strContractEndDate;
				$newLinkCustPcg->invoice_start_date =  Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['invoice_start_date'], 'php:Y-m-d H:i:s');
				$newLinkCustPcg->is_solnet_bank = $strIsSolnetBank;
				$newLinkCustPcg->payment_type = $arrPostData['Linkcustomepackage']['payment_type'];
				$newLinkCustPcg->bank_id=$intBankId;
				$newLinkCustPcg->bank_name = $strBankName;
				$newLinkCustPcg->virtual_acc_no=$strVirtualAcc;
				$newLinkCustPcg->account_name = $strAccName;
				$newLinkCustPcg->contract_number=$strContractNumber;
				$newLinkCustPcg->is_current_package = 'yes';
				$newLinkCustPcg->created_at = date('Y-m-d h:i:s');
				$newLinkCustPcg->fk_user_id =Yii::$app->user->identity->user_id;
				$strPaymentType = $arrPostData['Linkcustomepackage']['payment_type'];

					if($strPaymentType=='bulk'){
						$newLinkCustPcg->bulk_pay_start = Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['start_date'], 'php:Y-m-d H:i:s');
						$newLinkCustPcg->bulk_pay_end = Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['end_date'], 'php:Y-m-d H:i:s');
					}elseif($strPaymentType=='term'){
						$newLinkCustPcg->payment_term = $arrPostData['Linkcustomepackage']['payment_term'];
					}else{
						$newLinkCustPcg->bulk_pay_start = '0000-00-00';
						$newLinkCustPcg->bulk_pay_end = '0000-00-00';
						$newLinkCustPcg->payment_term = '';
					}

				if($newLinkCustPcg->save())
				{
					$resultInvoice  = yii::$app->customcomponents->GenerateInvoice($id,'reactivate');
					if($resultInvoice==1)
					{
						$session = Yii::$app->session;
						$session->setFlash('success','Customer activated successfully.');
						/************Log Activity*********/
						$logArray = array();
						$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
						$logArray['module'] = 'Reactivate Customer';
						$logArray['action'] = 'update';
						$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has reactivated the customer "'.$model->name.'".';
						$logArray['created'] = date('Y-m-d H:i:s');
						Yii::$app->customcomponents->logActivity($logArray);
						/************Log Activity*********/
						return $this->redirect(['customer/billing']);

					}elseif($resultInvoice==0)
					{
						$session = Yii::$app->session;
						$session->setFlash('success','Customer activated successfully.');
						/************Log Activity*********/
						$logArray = array();
						$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
						$logArray['module'] = 'Reactivate Customer';
						$logArray['action'] = 'update';
						$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has reactivated the customer "'.$model->name.'".';
						$logArray['created'] = date('Y-m-d H:i:s');
						Yii::$app->customcomponents->logActivity($logArray);
						/************Log Activity*********/
						return $this->redirect(['customer/billing']);
					}
				}
			}
		}else{
			echo $this->renderAjax('reactivate',[
				'modellinkcustpckg'=>$objLinkcustpackage,
				'model'=>$model,
				'speedList'=>$speedListData,
				'packageList'=>$packageListData
			]);
		}
	}

	 /**
     * Creates a new Customer model.
     * This function is for adding existing customer
     * @return mixed
     */
    public function actionAddexisting()
    {
    	//echo '<pre>';print_r($_POST);die;
        $model 	= new Customer();
        $modelCustomerlogs	= new Customerlogs();
		$modelLinkCustPackage	= new Linkcustomepackage();
		//$modelLinkCustPackage->scenario = 'create';
		/*************To fetch state from table************/
		$arrState 	= State::find()->where(['status'=>'active'])->all();
		$stateListData	= ArrayHelper::map($arrState,'state_id','state');
		/*************To fetch state from table************/

		/*************To fetch state from table************/
		$arrCountry 	= Country::find()->where(['status'=>'active'])->all();
		$countryListData	= ArrayHelper::map($arrCountry,'country_id','country');
		/*************To fetch state from table************/

		/*************To fetch package from table************/
		$arrPackage 	= Package::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
		$packageListData	= ArrayHelper::map($arrPackage,'package_id','package_title');
		/*************To fetch state from table************/

		/*************To fetch speed from table************/
		$arrSpeed 	= Speed::find()->where(['status'=>'active'])->all();
		$speedListData	= ArrayHelper::map($arrSpeed,'speed_id','speed_type');
		/*************To fetch state from table************/

		/*************To fetch currency from table************/
		$arrCurrency 	= Currency::find()->where(['status'=>'active'])->all();
		$currencyListData	= ArrayHelper::map($arrCurrency,'currency_id','currency');
		/*************To fetch state from table************/

		/*************To fetch currency from table************/
		$arrBank 	= Bank::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
		$bankListData	= ArrayHelper::map($arrBank,'bank_id','account_no');
		/*************To fetch state from table************/

		$model->is_invoice_activated='yes';
		$model->installation_status='yes';

		$model->created_at=date('Y-m-d h:i:s');
		$model->fk_user_id = Yii::$app->user->identity->user_id;

		if (Yii::$app->request->isPost) {
			$intTimeStamp = date('Ymdhis');
			$filesArray = UploadedFile::getInstance($model, 'filepath');
			if(!empty($filesArray)){
				$filesArray->saveAs('uploads/user_docs/' . 'User_'.$intTimeStamp. '.' . $filesArray->extension);
				$filepath='User_'.$intTimeStamp.'.'.$filesArray->extension;
			}else{
				$filepath= '';
			}
			$arrPostData =  Yii::$app->request->post();

		 }


        if ($model->load(Yii::$app->request->post() ) ) {


			$model->filepath = $filepath;
			$arrPostData =  Yii::$app->request->post();
			//print_r($arrPostData);
			$form_token_param = $arrPostData['_csrf'];
        	
        	if(isset($_SESSION['FORM_TOKEN']) && $_SESSION['FORM_TOKEN']==$form_token_param)
				{
					$id = $_SESSION['customer_id'];
					return $this->redirect(['billview', 'id' => $id]);
				}	
				else
				{
					$_SESSION['FORM_TOKEN'] = $form_token_param;
				}
			if($arrPostData['Customer']['is_address_same']==1)
			{
				$model->is_address_same = 'yes';
			}
			else
			{
				$model->is_address_same = 'no';
			}
			$arrGetCustID = yii::$app->customcomponents->getCustomerId($model->fk_state_id);

			$model->solnet_customer_id = $arrGetCustID['current_cust_id'];
			$model->it_pic = $arrPostData['Customer']['it_pic'];
			$model->optional_email = $arrPostData['Customer']['optional_email'];
			
			if($model->save())
			{
				$_SESSION['customer_id'] = $model->customer_id;
			if($modelLinkCustPackage->load(Yii::$app->request->post())){
				$strPaymentType = $arrPostData['Linkcustomepackage']['payment_type'];
				$modelLinkCustPackage->bundling_package = $arrPostData['Linkcustomepackage']['bundling_package'];

				//$modelLinkCustPackage->pay_in_advanc_grace_period= isset($arrPostData['Linkcustomepackage']['pay_in_advanc_grace_period']) ? trim($$arrPostData['Linkcustomepackage']['pay_in_advanc_grace_period']) : '0';
				//$modelLinkCustPackage->bulk_grace_period= isset($arrPostData['Linkcustomepackage']['bulk_grace_period']) ? trim($$arrPostData['Linkcustomepackage']['bulk_grace_period']) : '0';
				//$modelLinkCustPackage->term_grace_period= isset($arrPostData['Linkcustomepackage']['term_grace_period']) ? trim($$arrPostData['Linkcustomepackage']['term_grace_period']) : '0';
				
				$modelLinkCustPackage->pay_in_advanc_grace_period = $arrPostData['Linkcustomepackage']['pay_in_advanc_grace_period'];

				$modelLinkCustPackage->bulk_grace_period = $arrPostData['Linkcustomepackage']['bulk_grace_period'];
				$modelLinkCustPackage->term_grace_period = $arrPostData['Linkcustomepackage']['term_grace_period'];
				
				if($strPaymentType!='bulk'){
					//$modelLinkCustPackage->bulk_pay_start = '0000-00-00';
					//$modelLinkCustPackage->bulk_pay_end = '0000-00-00';
				}else{
					$modelLinkCustPackage->bulk_pay_start =Yii::$app->formatter->asDate( $arrPostData['Linkcustomepackage']['start_date'], 'php:Y-m-d');
					$modelLinkCustPackage->bulk_pay_end = Yii::$app->formatter->asDate( $arrPostData['Linkcustomepackage']['end_date'], 'php:Y-m-d');
				}

				$modelLinkCustPackage->fk_customer_id = $model->customer_id;
				$modelLinkCustPackage->created_at  = date('Y-m-d h:i:s');
				//$modelLinkCustPackage->order_received_date = date('Y-m-d h:i:s');
				//$modelLinkCustPackage->activation_date  = '0000-00-00 00:00:00';
				$modelLinkCustPackage->contract_number  = $model->contract_number;
				
				$modelLinkCustPackage->contract_start_date  = Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['contract_start_date'], 'php:Y-m-d');
				$modelLinkCustPackage->contract_end_date  =Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['contract_end_date'], 'php:Y-m-d');
				$modelLinkCustPackage->invoice_start_date = Yii::$app->formatter->asDate($arrPostData['Linkcustomepackage']['invoice_start_date'], 'php:Y-m-d H:i:s');
				$modelLinkCustPackage->is_disconnected='no';
				$modelLinkCustPackage->is_current_package='yes';

				/*if($modelLinkCustPackage->load(Yii::$app->request->post()) && $modelLinkCustPackage->save())
				{*/

					if($arrPostData['Linkcustomepackage']['is_solnet_bank'] == 'solnet'){
						$modelLinkCustPackage->is_solnet_bank = 'yes';
					}else{
						$modelLinkCustPackage->is_solnet_bank = 'no';
					}

					if($modelLinkCustPackage->save()){

						$modelCustomerlogs->fk_user_id = $model->customer_id;
						$modelCustomerlogs->fk_system_user_id = Yii::$app->user->identity->user_id;
						$modelCustomerlogs->action = 'update';
						$modelCustomerlogs->query = json_encode($arrPostData);
						$modelCustomerlogs->controller = 'CustomerController';
						$modelCustomerlogs->ip_address = $this->get_client_ip();
						$modelCustomerlogs->hostname = gethostname();
						$modelCustomerlogs->agent = json_encode($this->getBrowser());
						$modelCustomerlogs->log_date = date('Y-m-d H:i:s');				
						$modelCustomerlogs->save();


						$arrInvoice = Yii::$app->customcomponents->updateCustomerIncrementValue($model->fk_state_id,$arrGetCustID['increment_value']+1);
						/************Log Activity*********/
						$logArray = array();
						$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
						$logArray['module'] = 'Add Existing Customer';
						$logArray['action'] = 'Create';
						$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has Added a existing customer "'.$model->name.'"';
						$logArray['created'] = date('Y-m-d H:i:s');
						Yii::$app->customcomponents->logActivity($logArray);
						/************Log Activity*********/
						return $this->redirect(['billview', 'id' => $model->customer_id]);
					}
					
				}

			}else{
				echo'<pre>';print_r($model->errors);die;
			}


        }

      /*  else{
				echo'<pre>';print_r($model->errors);die;
			}*/

		return $this->render('add_existing', [
                'model' => $model,
				'modelLinkCustPackage'=>$modelLinkCustPackage,
				'stateList'=>$stateListData,
				'countryList'=>$countryListData,
				'speedList'=>$speedListData,
				'currencyList'=>$currencyListData,
				'packageList'=>$packageListData,
				'bankList'=>$bankListData
            ]);
    }





	public function actionState($id){
		if(!empty($id)){

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
	
	
		public function actionSalesperson($customerId){
		
		$model = $this->findModel($customerId);
		
		if($model->load(Yii::$app->request->post())){
			$model->save();
			return $this->redirect(['customer/billing']);
		}
		
		
		if(!empty($customerId)){
			$objSalesPerson = Customer::find()->joinWith('user')->select(['fk_user_id','tblusers.name'])->distinct()->all();
			
			
			if($objSalesPerson)
			{
				foreach($objSalesPerson as $key=>$value)
				{
					$arrSalesPerson[$value->fk_user_id] = $value->name;
					
				}
			}
			
			return $this->render('add_sales', [
				'arrSales' => $arrSalesPerson,
				'model' => 	$model
				
            ]);
			
		}
		
		
	}
	
	
	public function actionEditdetails($id){
	
	 $model = $this->findModel($id);
	 return  $this->render('editdetails',['model'=>$model,'id'=>$id]);
	
	}
	
	public function actionSubmitdetails($id){
		
    	 $model= new Customer();
		 $modelCustomer = $this->findModel($id);
		 
      	 $arrDetails =Yii::$app->request->post('Customer');
		 if(!empty($arrDetails)){
			 $strPhoneNo = $arrDetails['phone_number'];
			 $strRemark = $arrDetails['remarks'];
			 $strFiber = $arrDetails['fiber_installed'];
			 $query=Yii::$app->db->createCommand()
					->update('tblcustomer', ['phone_number' =>$strPhoneNo,'remarks'=>$strRemark, 'fiber_installed' => $strFiber  ],
							['customer_id' =>$id])
							->execute();
					if($query){
						$modelCustomer=Customer::findOne($id);
						$logArray = array();
		                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
		                $logArray['module'] = 'Pending Installation';
		                $logArray['action'] = 'update';
		                $logArray['message'] = 'The details are updated for '.$modelCustomer->name;
		                $logArray['created'] = date('Y-m-d H:i:s');
		                Yii::$app->customcomponents->logActivity($logArray);

		            Yii::$app->session->setFlash('success', 'Details updated successfully');
		            return $this->redirect(['pendinginstallation']);
		        }
		 }
	}
	
	
	/**
     * Lists all Customer which are deleted.
     * @return mixed
     */
    public function actionTrashed()
    {
        $searchModel = new CustomerSearch();
		$searchModel->is_deleted= '1';
		$queryParams = Yii::$app->request->queryParams;
		//if(Yii::$app->user->identity->fk_role_id=='3'){
			//$queryParams['CustomerSearch']['fk_user_id']=Yii::$app->user->identity->user_id;
		//}
		
		$queryParams['CustomerSearch']['installation_status']='no';
		$queryParams['CustomerSearch']['is_deleted']='1';
        $dataProvider = $searchModel->search($queryParams);
		//echo "<pre>"; print_r($dataProvider); exit;

        return $this->render('trashed', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	public function actionRestore($id){
		$model = Customer::findOne(['customer_id'=>$id]);
		$strDate = new Expression('NOW()');
		$model->is_deleted = '0';
		$model->updated_at = $strDate;
		$model->save();
		
		/************Log Activity*********/
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Restore Customer';
			$logArray['action'] = 'update';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has restored a customer';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			/************Log Activity*********/
			//return $this->redirect(['index']);
		
		return $this->redirect(['customer/trashed']);
	}
	
	public function actionDeletepermanent($id){
		$session = Yii::$app->session;
		$qryDelete = Customer::findOne(['customer_id'=>$id])->delete();
		
		if($qryDelete){
			$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id])->one();
			$modelLinkCustPackage->delete();
			$session->setFlash('success',CUSTOMER_DELETE_SUCCESSFULL);
			
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Delete Customer';
			$logArray['action'] = 'delete';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted a customer';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			
			return $this->redirect(['customer/trashed']);
		}
		
	}
	
	 /**
     * Delete from pending installation
     */
    public function actionDeletepending($id)
    {
		$session = Yii::$app->session;
		$model = $this->findModel($id);
		$strDate = new Expression('NOW()');
		$model->is_deleted = '1';
		$model->updated_at = $strDate;
		$model->save();
		
		/************Log Activity*********/
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Delete Customer';
			$logArray['action'] = 'delete';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted a customer';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			/************Log Activity*********/
			return $this->redirect(['customer/pendinginstallationtrash']);

    }
	
	public function actionRestorepending($id){
		$model = Customer::findOne(['customer_id'=>$id]);
		$strDate = new Expression('NOW()');
		$model->is_deleted = '0';
		$model->updated_at = $strDate;
		$model->save();
		
		/************Log Activity*********/
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Restore Customer';
			$logArray['action'] = 'update';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has restored a customer';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			/************Log Activity*********/
			//return $this->redirect(['index']);
		
		return $this->redirect(['customer/pendinginstallationtrash']);
	}
	
	public function actionDeletepermanentpending($id){
		$session = Yii::$app->session;
		$qryDelete = Customer::findOne(['customer_id'=>$id])->delete();
		
		if($qryDelete){
			$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id])->one();
			$modelLinkCustPackage->delete();
			$session->setFlash('success',CUSTOMER_DELETE_SUCCESSFULL);
			
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Delete Customer';
			$logArray['action'] = 'delete';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted a customer';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			
			return $this->redirect(['customer/pendinginstallationtrash']);
		}
		
	}
	/* EQUIPEMENT TASK */
	/************ Create inventry functionality Add on :: 01-05-2018 *********/
	public function actionAddequipment($id)
	{
		$session 	= Yii::$app->session;
		$model = new CustomerEquipments;
		if(Yii::$app->request->post())
    	{	
    		$arrPostData = Yii::$app->request->post();
    		
    		//echo '<pre>';print_r($arrPostData);echo '<pre>';die;

    		$connection = Yii::$app->getDb();
    		foreach ($arrPostData['CustomerEquipments']['fk_equipments_id'] as $key => $arrFkEquIdValue) {
				if(isset($arrFkEquIdValue[0]) && $arrFkEquIdValue[0] != ''){
					$intEquMentId = $arrFkEquIdValue[0];
	    			if(isset($arrPostData['quntity'][$key])){
	    				$intQuntity   = $arrPostData['quntity'][$key];
	    			}
	    			if(isset($arrPostData['inv_type'][$key])){
	    				$intEquType   = $arrPostData['inv_type'][$key];
	    			}

	    			//Update  fin_equipments table quntity
	    			$command = $connection->createCommand('UPDATE fin_equipments SET quantity_out = quantity_out + '.$intQuntity.', quantity_in_hand = quantity_in_hand - '.$intQuntity.' WHERE equipment_id ='.$intEquMentId);
					$command->execute();

					//insert equipments to tbl_customer_equipments.
	    			$connection->createCommand()->insert('tbl_customer_equipments',
	                [	
	                    'fk_equipments_id' 	=> $intEquMentId,
	                    'fk_customer_id' 	=> $id,
	                    'quantity' 			=> $intQuntity,
	                    'euipment_type' 	=> $intEquType,
	                    'added_by' 			=> yii::$app->user->identity->user_id,
	                    'status' 			=> 'active',
	                ])->execute();

	                $intLastInsertedId = $connection->getLastInsertID();
	                
	                //Update mac address stuatus
	                if(isset($arrPostData['mac_address'][$key])){
	                	foreach ($arrPostData['mac_address'][$key] as $arrMacKey => $arrMacValue) {
	                		$command = $connection->createCommand('UPDATE fin_equipments_macs SET assigned_status = "Assigned", assigned_by = '.yii::$app->user->identity->user_id.' , assigned_id = '.$intLastInsertedId.' , assigned_to = "coustomer" WHERE equipments_mac_id ='.$arrMacValue);
							$command->execute();
	                	}
	                }
	            }else{
	            	$session->setFlash('error_msg',"Please Enter a values");
					return $this->redirect(Yii::$app->request->referrer);
	            }

    		}
            $session->setFlash('success_msg',"Equipments Added Successfully");
			return $this->redirect(Yii::$app->request->referrer);
		}
		$arrEquipData = Equipments::find()->all();
		$arrEquipList = ArrayHelper::map($arrEquipData,'equipment_id','model_type');
		
        return  $this->render('add_equipment',
       	[
	       	'id'			=> $id,
       		'model'			=> $model,
	       	'arrEquipList'	=> $arrEquipList
	    ]);
	}
	
	public function actionGetbrandname($intId,$id)
	{
		$hmtl = '';
		if(!empty($id)){
			$arrResultEqu = Equipments::find()->where(['equipment_id'=>$id])->all();
			if(!empty($arrResultEqu)) {
				foreach($arrResultEqu as $value){
				  	if($value->type == 'Normal'){
				  		$hmtl .="<input type='hidden' name='inv_type[]' value='normal'>
				  					<div class='col-md-2'>
				  						<label>Brand Name</label><input type='text' value='".$value->brand_name."' name='brandName[".$intId."][]' class='form-control' readonly >
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Quantity In Hand </label>
				  						<input type='text' name='inHandQuntity[]' class='form-control' value='".$value->quantity_in_hand."' readonly> 
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Quantity</label>
				  						<input type='text' value='' name='quntity[]' class='form-control' onkeypress='javascript:return isNumber(event)' required> 
				  					</div>";
				  	}else{
						$arrResult = EquipmentsMacs::find()->where(['fk_equipment_id'=>$value->equipment_id,'assigned_status' =>'Not Assigned'])->all();
						/*$arrResult = EquipmentsMacs::find()->where(['fk_equipment_id'=>$value->equipment_id])->all();*/
						$hmtl .=  "<input type='hidden' name='inv_type[]' value='mac'>
									<div class='col-md-2'>
									<label>Brand Name</label>
				  					<input type='text' value='".$value->brand_name."' name='brandName[".$intId."][]' class='form-control' readonly >
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Quantity In Hand </label>
				  						<input type='text' name='inHandQuntity[]' class='form-control' value='".$value->quantity_in_hand."' readonly> 
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Quantity</label>
				  						<input type='text' name='quntity[]' class='form-control' onkeypress='javascript:return isNumber(event)' required>
				  					</div>
				  					<div class='col-md-2'>
				  						<label>Mac Address</label>
				  						<select name='mac_address[".$intId."][]' class='form-control multipleSelect' multiple required>";
				  						foreach ($arrResult as $arrValue) {
										  $hmtl .=  "<option value='".$arrValue->equipments_mac_id."'>".$arrValue->serial_number."</option>";
										}
						$hmtl .=  "</select>
				  					</div>";
				  	}
				  	return $hmtl;
				}
     		}
			 else{
				  echo "-";
			 }
		}
	}
	public function actionGetequipment($id)
    {
    	$intNoOfEqu = '';
    	$model = new CustomerEquipments;
		if(Yii::$app->request->post())
    	{	
    		$arrPostData = Yii::$app->request->post();
    		$intNoOfEqu  = $arrPostData['nofoequipment'];
		}
		$arrEquipData = Equipments::find()->all();
		$arrEquipList = ArrayHelper::map($arrEquipData,'equipment_id','model_type');
		
        return  $this->render('add_equipment',
       	[
	       	'id'			=> $id,
       		'model'			=> $model,
	       	'arrEquipList'	=> $arrEquipList,
	       	'intNoOfEqu'	=> $intNoOfEqu
	    ]);
    }
    public function actionDeleteequipment($id)
    {
    	$session 				 = Yii::$app->session;
    	$modelEquipments 		 = new Equipments;
    	$modelEquipmentsMacs 	 = new EquipmentsMacs;

    	$modelCustomerEquipments = CustomerEquipments::findOne(['id' => $id]);
    	
    	$connection = Yii::$app->getDb();

    	//echo '<pre>';print_r($modelCustomerEquipments);echo '</pre>';die;

    	$command = $connection->createCommand('UPDATE `fin_equipments` SET quantity_out = quantity_out - '.$modelCustomerEquipments->quantity.', quantity_in_hand = quantity_in_hand + '.$modelCustomerEquipments->quantity.' WHERE equipment_id ='.$modelCustomerEquipments->fk_equipments_id);
		$command->execute();


    	$command1 = $connection->createCommand('UPDATE `fin_equipments_macs` SET assigned_status = "Not Assigned", assigned_by = 0 ,assigned_id = 0 ,assigned_to = NULL WHERE assigned_id ='.$id);
		$command1->execute();
    	
    	$modelCustomerEquipments->delete();

		$session->setFlash('success_msg',"Equipments Deleted Successfully");
		return $this->redirect(Yii::$app->request->referrer);
    }
    /********* End Create inventry code *********/

    public function actionCostcenter()
    {
    	$searchModel       = new CustomerEquipments();
        $euipmentModel     = new Equipments();
        $macEuipmentModel  = new EquipmentsMacs();
        $modelCustomer     = new Customer();

        $arrSalesPerson = array();
        $arrSalesPerson = $modelCustomer->getUserName();
       
        $queryParams    = Yii::$app->request->queryParams;

        if(isset($_REQUEST['start_date']) && isset($_REQUEST['end_date']) ){
           $strStartDate = $_REQUEST['start_date'];
           $strEndDate   = $_REQUEST['end_date'];
           $queryParams['dataSearch']['start_date']   = $strStartDate;
           $queryParams['dataSearch']['end_date']   	= $strEndDate;
        }

        $dataProvider = $euipmentModel->searchEquipmentRecord($queryParams); 

        //echo '<pre>';print_r($dataProvider);echo '</pre>';die;

        return $this->render('cost_control', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'user'         => $arrSalesPerson
        ]);
    }
    /* END EQUIPEMENT TASK */
    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne(['customer_id'=>$id,'is_deleted'=>'0'])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

     public function actionCustomerhistory($id='')
    {
    	
    	$modelTemporaryDisconnectDates = Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_disconnected'=>'yes'])->all();
    	$modelFirstActivationDate = Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_disconnected'=>'no'])->all();
    	$modelCustomerlogs = Customerlogs::find()->where(['fk_user_id'=>$id])->orderBy(['log_id' => SORT_DESC])->all();

    	$ids = $tabs = array();
    	if(count($modelCustomerlogs)>0)
    	{
    		foreach($modelCustomerlogs as $reactivationdate) {
    		   	
    		   $finalData =  json_decode($reactivationdate->query, true); 
			   if(!empty($finalData['tab']))
		        { 
		            $ids[] =  $finalData['tab'];
		        }

			}
			if(count($ids))
			{
			 $tabs = array_unique($ids);
			}
			else
			{
				$tabs = [];
			}

    	}
    	/*echo "<pre>";
    	print_r($modelTemporaryDisconnectDates);die;*/
    	return $this->render('customerhistory_list', [
            'modelTemporaryDisconnectDates' => $modelTemporaryDisconnectDates,
            'modelCustomerlogs' => $modelCustomerlogs,
            'modelFirstActivationDate'=>$modelFirstActivationDate,
            'tabs'=>$tabs,
        ]);
        
    }
    public function actionTempdisconnect($id)
    {
    	$model = $this->findModel($id);
		$objLinkcustpackage = Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();		
		
		$objLinkcustpackage->is_temp_disconnected = 'yes';
		$objLinkcustpackage->updated_at		 = date('Y-m-d h:i:s');
		$objLinkcustpackage->fk_user_id =Yii::$app->user->identity->user_id;
		$model->status = 'inactive';
		$model->updated_at = date('Y-m-d h:i:s');
		
		if($objLinkcustpackage){
			$objLinkcustpackage->disconnection_date =  Yii::$app->formatter->asDate($objLinkcustpackage->disconnection_date, 'php:Y-m-d');
			
			if($objLinkcustpackage->save() && $model->save()){
				
					$session = Yii::$app->session;
					$session->setFlash('success',CUSTOMER_DISCONNECT_SUCCESSFULL);
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Disconnect Customer';
					$logArray['action'] = 'update';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has temporary disconnected the customer "'.$model->name.'".';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					return $this->redirect(['customer/billing']);
			}
		} else{
					$session->setFlash('success_msg',"Error while user update temporary disconnected");
					return $this->redirect(['customer/billing']);
			}
    }

    public function actionTempdisconnectactive($id)
    {
    	$model = $this->findModel($id);
		$objLinkcustpackage = Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();		
		
		$objLinkcustpackage->is_temp_disconnected = 'no';
		$objLinkcustpackage->updated_at		 = date('Y-m-d h:i:s');
		$model->status = 'active';
		$model->updated_at = date('Y-m-d h:i:s');
		
		if($objLinkcustpackage){
			$objLinkcustpackage->disconnection_date =  Yii::$app->formatter->asDate($objLinkcustpackage->disconnection_date, 'php:Y-m-d');
			
			if($objLinkcustpackage->save() && $model->save()){
				
					$session = Yii::$app->session;
					$session->setFlash('success',CUSTOMER_DISCONNECT_SUCCESSFULL);
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Disconnect Customer';
					$logArray['action'] = 'update';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has temporary active the customer "'.$model->name.'".';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					return $this->redirect(['customer/tempusers']);
			}
		} else{
					$session->setFlash('success_msg',"Error while user update temporary disconnected");
					return $this->redirect(['customer/tempusers']);
			}
    }

   public function actionTempusers($limit='')
    {

        $searchModel = new CustomerSearch();
        $model = new Customer();
        $arrSalesPerson = array();
		$queryParams = Yii::$app->request->queryParams;
		
		if(Yii::$app->user->identity->fk_role_id=='22'){
			//$dataProvider = $searchModel->searchNOC($queryParams);
			$dataProvider = $searchModel->search($queryParams);
		}else{
					
			if(!empty($limit) && !isset($queryParams["CustomerSearch"]["limit"])){
				$queryParams["CustomerSearch"]["limit"]   = $limit;
			}
			elseif(!isset($queryParams["CustomerSearch"]["limit"]))
			{
				$queryParams["CustomerSearch"]["limit"]   = '20';
			}

			if(Yii::$app->user->identity->fk_role_id=='3'){
					$queryParams['CustomerSearch']['fk_user_id']=Yii::$app->user->identity->user_id;
				}
			
			$queryParams['CustomerSearch']['installation_status']='yes';
			$queryParams['CustomerSearch']['is_deleted']='0';
			$queryParams['CustomerSearch']['is_invoice_activated']='yes';
			if(!isset($queryParams['CustomerSearch']['status']))
			{
				$queryParams['CustomerSearch']['status']='active';
			}
			

			$dataProvider = $searchModel->searchtemp($queryParams);
			//get the total package price
			
			
		}
		$resultTotalPrice = $model->getTotalPrice();
		$objSalesPerson = Customer::find()->joinWith('user')->select(['fk_user_id','tblusers.name'])->distinct()->all();
		if($objSalesPerson)
			{
				foreach($objSalesPerson as $key=>$value)
				{
					$arrSalesPerson[$key]['user_id'] = $value->fk_user_id;
					$arrSalesPerson[$key]['name'] = $value->name;
				}
			}
			
			//echo Yii::$app->user->identity->fk_role_id;die;
        return $this->render('temp_billing_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalPackagePrice'=>$resultTotalPrice,
            'user'=>$arrSalesPerson
        ]);
    }

    public function get_client_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	public function getBrowser() {
		  $u_agent = $_SERVER['HTTP_USER_AGENT'];
		  $bname = 'Unknown';
		  $platform = 'Unknown';
		  $version= "";

		  // First get the platform?
		  if (preg_match('/linux/i', $u_agent)) {
		    $platform = 'linux';
		  } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		    $platform = 'mac';
		  } elseif (preg_match('/windows|win32/i', $u_agent)) {
		    $platform = 'windows';
		  }

		  // Next get the name of the useragent yes seperately and for good reason
		  if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
		    $bname = 'Internet Explorer';
		    $ub = "MSIE";
		  } elseif(preg_match('/Firefox/i',$u_agent)) {
		    $bname = 'Mozilla Firefox';
		    $ub = "Firefox";
		  } elseif(preg_match('/Chrome/i',$u_agent)) {
		    $bname = 'Google Chrome';
		    $ub = "Chrome";
		  } elseif(preg_match('/Safari/i',$u_agent)) {
		    $bname = 'Apple Safari';
		    $ub = "Safari";
		  } elseif(preg_match('/Opera/i',$u_agent)) {
		    $bname = 'Opera';
		    $ub = "Opera";
		  } elseif(preg_match('/Netscape/i',$u_agent)) {
		    $bname = 'Netscape';
		    $ub = "Netscape";
		  }

		  // finally get the correct version number
		  $known = array('Version', $ub, 'other');
		  $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		  if (!preg_match_all($pattern, $u_agent, $matches)) {
		    // we have no matching number just continue
		  }

		  // see how many we have
		  $i = count($matches['browser']);
		  if ($i != 1) {
		    //we will have two since we are not using 'other' argument yet
		    //see if version is before or after the name
		    if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
		      $version= $matches['version'][0];
		    } else {
		      $version= $matches['version'][1];
		    }
		  } else {
		    $version= $matches['version'][0];
		  }

		  // check if we have a number
		  if ($version==null || $version=="") {$version="?";}

		return array(
		  'userAgent' => $u_agent,
		  'name'      => $bname,
		  'version'   => $version,
		  'platform'  => $platform,
		  'pattern'    => $pattern
		  );
		}

}
