<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : ServiceController.php
# Created on : 10th Dec 2018 by Shruti Shah
# Update on  : 10th Dec 2018 by Shruti Shah
# Purpose : Manage Services.
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
use app\models\CustomerService;
use app\models\CustomerServiceDetail;
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
/**
 * ServiceController implements the CRUD actions for Customer model.
 */
class ServiceController extends Controller
{
    /**
     * @inheritdoc
     */

	public function behaviors()
    {
		$behaviors['access'] = [
			'class' => AccessControl::className(),
                        'only' => ['create', 'update','index','view','delete','billing','pending','billview','activateview','pdf','activatepdf','billpdf','activate','deletemultiple','delete','plan','pendinginstallation','activateinstallation','installationview','installationprint','togglestatus','billingsingledelete','billingmultipledelete','activatemultipledelete','activatesingledelete','disconnect','reactivate','addexisting','salesperson'],
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
	
	public function actionAddservice($limit=''){
		
		$searchModel = new CustomerSearch();
        $model = new Customer();
		$serviceModel = new CustomerService();
		$serviceDetails = new CustomerServiceDetail();
		
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
		$queryParams['CustomerSearch']['is_disconnected']='no';
		$queryParams['CustomerSearch']['status']='active';
		

        $dataProvider = $searchModel->search($queryParams);
        //get the total package price
        $resultTotalPrice = $model->getTotalPrice();

        return $this->render('service_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalPackagePrice'=>$resultTotalPrice,
            'user'=>			$arrSalesPerson,
			'serviceModel' =>	$serviceModel ,
			'serviceDetail' => $serviceDetails,
			
			
        ]);
		
		 
	}
	
	public function actionAdd($id){
		
		$model = $this->findModel($id);
		$serviceModel = new CustomerService();
		$serviceDetails = new CustomerServiceDetail();
		$modelUpdateService = array();
		$arrService =	array();
		
		
		$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
		$modelUpdateService = CustomerService::find()->where(['fk_customer_id'=>$id])->one();
		if(!empty($modelUpdateService)){
			$arrService = $modelUpdateService->service;
			
		}
		
		$modelLinkCustPackage->scenario = 'create';

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
			if($serviceModel->load(Yii::$app->request->post())){
				
				$serviceModel->fk_customer_id = $id;
				$serviceModel->created_at = date('Y-m-d H:i:s');
				$serviceModel->updated_at = date('Y-m-d H:i:s');
				
				if($serviceModel->save()){
					$customerServiceId = $serviceModel->customer_service_id;
					$arrPostData= Yii::$app->request->post();
					if(!empty($arrPostData['service'])){
				
						foreach($arrPostData['service'] as $key => $value){
							$serviceDetails = new CustomerServiceDetail();
							$serviceDetails->isNewRecord = true;
							$serviceDetails->fk_cs_id = $customerServiceId;
							$serviceDetails->service = $value;
							$serviceDetails->service_price = $arrPostData['service_price'][$key];
							$serviceDetails->service_quantity = $arrPostData['service_quantity'][$key];
							$serviceDetails->created_on = date('Y-m-d H:i:s');
							$serviceDetails->updated_on = date('Y-m-d H:i:s');
							$serviceDetails->save();
									
						} 
						// Generate Invoice
						$resultInvoice  = yii::$app->customcomponents->GenerateServiceInvoice($id);
						
						
						// Log Activity
						$session = Yii::$app->session;
						$session->setFlash('success','Customer service added successfully.');
						
						$logArray = array();
						$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
						$logArray['module'] = 'Add Service';
						$logArray['action'] = 'add';
						$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has added service to a customer "'.$model->name.'"';
						$logArray['created'] = date('Y-m-d H:i:s');
						Yii::$app->customcomponents->logActivity($logArray);
						/************Log Activity*********/
						return $this->redirect(['addservice']);
						
					}
					
				}else{
					echo "not saved"; exit;
				}
				
			}
			
		
		}
		return $this->render('add_service', [
                'model' => $model,
				'modelLinkCustPackage'=>$modelLinkCustPackage,
				'stateList'=>$stateListData,
				'countryList'=>$countryListData,
				'speedList'=>$speedListData,
				'currencyList'=>$currencyListData,
				'packageList'=>$packageListData,
				'serviceModel' => $serviceModel,
				'serviceDetailModel' => $serviceDetails,
				'updateService' => $arrService,
            ]);
		
	}
	
	
	public function actionUpdateservice($id){
		
		$model = $this->findModel($id);
		$serviceModel = $this->findServiceModel($id);
		$serviceDetail = new CustomerServiceDetail();
		
		$serviceDetailModel =  CustomerServiceDetail::find()->where(['fk_cs_id'=>$serviceModel->customer_service_id])->all();
		
		$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
		
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
		
			if (Yii::$app->request->isPost) {
				if($serviceModel->load(Yii::$app->request->post())){
					
					$serviceModel->updated_at = date('Y-m-d H:i:s');
					$serviceModel->save();
					// Update service details
					
					$isDeleted = CustomerServiceDetail::deleteAll('fk_cs_id = :fk_cs_id', [':fk_cs_id' => $serviceModel->customer_service_id]);
					//if($isDeleted || empty($serviceModel->customer_service_id)){
						$arrPostData= Yii::$app->request->post();
						if(!empty($arrPostData['service'])){
							$customerServiceId = $serviceModel->customer_service_id;
							foreach($arrPostData['service'] as $key => $value){
								$serviceDetails = new CustomerServiceDetail();
								$serviceDetails->isNewRecord = true;
								$serviceDetails->fk_cs_id = $customerServiceId;
								$serviceDetails->service = $value;
								$serviceDetails->service_price = $arrPostData['service_price'][$key];
								$serviceDetails->service_quantity = $arrPostData['service_quantity'][$key];
								$serviceDetails->created_on = date('Y-m-d H:i:s');
								$serviceDetails->updated_on = date('Y-m-d H:i:s');
								$serviceDetails->save();
										
							} 
							$session = Yii::$app->session;
							$session->setFlash('success','Customer service updated successfully.');
							// Log Activity
							
							$logArray = array();
							$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
							$logArray['module'] = 'Update Service';
							$logArray['action'] = 'add';
							$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated service to a customer "'.$model->name.'"';
							$logArray['created'] = date('Y-m-d H:i:s');
							Yii::$app->customcomponents->logActivity($logArray);
							/************Log Activity*********/
							return $this->redirect(['addservice']);
						}
					//}
					
					
				}
				
				
			}
		
		
		return $this->render('edit_service', [
                'model' => $model,
				'modelLinkCustPackage'=>$modelLinkCustPackage,
				'stateList'=>$stateListData,
				'countryList'=>$countryListData,
				'speedList'=>$speedListData,
				'currencyList'=>$currencyListData,
				'packageList'=>$packageListData,
				'serviceModel' => $serviceModel,
				'serviceDetailModel' => $serviceDetailModel,
				'serviceDetail'=> $serviceDetail,
				
				
            ]);
		
		
		
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
     * Displays a single Customer details with package details for sales admin.
     * @param integer $id
     *
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	/**
     * Displays a single Customer details with package details for billing admin.
     * @param integer $id
     *
     */
    public function actionBillview($id)
    {
        return $this->render('billing_view', [
            'model' => $this->findModel($id),
        ]);
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
	* Function to get vat based on state
	* @param integer $id
	*
    */

	public function actionGetvat($id)
	{
		return Yii::$app->customcomponents->GetVat($id);
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
	
	protected function findServiceModel($id){
		$model = '';
		if (($model = CustomerService::findOne(['fk_customer_id'=>$id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
		return $model;
	}
}
