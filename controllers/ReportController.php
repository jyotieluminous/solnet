<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : ReportController.php
# Created on : 5th July 2017 by Swati Jadhav.
# Update on  : 5th July 2017 by Swati Jadhav.
# Purpose : Manage Report Details.
############################################################################################
*/

namespace app\controllers;
use Yii;
use app\models\Customer;
use app\models\Currency;
use app\models\Customerpayment;
use app\models\Bankdeposit;
use app\models\Package;
use app\models\CustomerSearch;
use app\models\CustomerinvoiceSearch;
use app\models\CustomerpaymentSearch;
use app\models\State;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\models\TbluserStates;
use app\models\Customerinvoice;
use app\models\Linkcustomepackage;
class ReportController extends Controller
{

    public function behaviors()
    {
        $behaviors['access'] = [
            'class' => AccessControl::className(),
                        'only' => ['signup','paymentreport',
                        'summarycollectionreport','salesrevenue','revenue'],
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


    public function actionSignup()
    {
        $searchModel = new CustomerSearch();
        $model = new Customer();

		$postedArray = Yii::$app->request->get();
		$queryParams = Yii::$app->request->queryParams;
        $sales_id="";
        $startDate = "";
        $endDate = "";
        $total = 0;
		  if(Yii::$app->user->identity->fk_role_id=='3')
             {
                $sales_id = Yii::$app->user->identity->user_id;
             }
		
		if(!empty($postedArray))
		{
            if(isset($postedArray['CustomerSearch']['sales_person']) && !empty($postedArray['CustomerSearch']['sales_person']))
            {
                $sales_id = $postedArray['CustomerSearch']['sales_person'];
				 if(Yii::$app->user->identity->fk_role_id=='3')
				{
					 $sales_id = Yii::$app->user->identity->user_id;
				}
				
            }
			if(!empty($postedArray['CustomerSearch']['start_date']) && !empty($postedArray['CustomerSearch']['end_date']))
			{
				$strStartDate	= $postedArray['CustomerSearch']['start_date'];
				$strEndDate		=	$postedArray['CustomerSearch']['end_date'];
        //$model->Created = Yii::$app->formatter->asDate($_POST['modelName']['Created'], 'php:Y-m-d H:i:s');
                $startDate = Yii::$app->formatter->asDate($strStartDate, 'php:Y-m-d');
                $endDate = Yii::$app->formatter->asDate($strEndDate, 'php:Y-m-d');
				/*-------added below 2 lines for passing query parameters--------*/
				 $queryParams["CustomerSearch"]["start_date"]   = $startDate;
				 $queryParams["CustomerSearch"]["end_date"] 	= $endDate;
				/*-------added below 2 lines for passing query parameters--------*/
			}
		}
        $total = $model->getTotalPriceSignUp($sales_id,$startDate,$endDate);

		//$queryParams['CustomerSearch']['status']='active';
		$queryParams['CustomerSearch']['is_invoice_activated']='yes';
		$queryParams['CustomerSearch']['linkcustomepackage.is_current_package']='yes';
		$queryParams['CustomerSearch']['linkcustomepackage.is_disconnected']='no';
		$queryParams['CustomerSearch']['is_deleted']='0';
        if(Yii::$app->user->identity->fk_role_id=='3')
             {
                $queryParams['CustomerSearch']['fk_user_id']=Yii::$app->user->identity->user_id;
             }
        /*$sessionData = Yii::$app->session;
        
        if($sessionData->get('user_state_id'))
        {
            $stateId = $sessionData->get('user_state_id');
            $queryParams['CustomerSearch']['fk_state_id']=$stateId;
        }   */  
        $dataProvider = $searchModel->search($queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'totalPrice'=>$total
        ]);
    }


    /**
     * Lists all Customerpayment models.
     * @return mixed
     */
     public function actionPaymentreport()
    {
        $searchModel = new CustomerpaymentSearch();
        $modelCustomer = new Customer();
        $arrSalesPerson = array();
        $arrSalesPerson = $modelCustomer->getUserName();
        $queryParams = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('payment_report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user'=>$arrSalesPerson
        ]);
    }


    protected function invoice($id)
    {
        if (($model = Customerinvoice::findOne($id)) !== null) {
            if($model->customer->is_deleted==1){
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPdf($id,$state)
    {
        $signatureEmailId   = "accountbali@solnet.net.id";
        $headerAddress      = "";
        $headerTelephone    = "";
        $model              = Customerinvoice::findOne($id);
        $invoicetype        = $model->invoice_type;
        $serviceModel       = array();
        $arrServiceDetails  = array();
        $arrResultData      = $this->invoice($id);
        if($state!="")
        {
            $signatureHeaders = State::find()->select(['signature_email_id','header_address','header_telephones'])->where(['state_id'=>$state])->one();
            if($signatureHeaders->signature_email_id!="")
                $signatureEmailId = $signatureHeaders->signature_email_id;
            else
                $signatureEmailId = "accountbali@solnet.net.id";

            if($signatureHeaders->header_address!="")
                $headerAddress = $signatureHeaders->header_address;

            if($signatureHeaders->header_telephones!="")
                $headerTelephone = $signatureHeaders->header_telephones;
        }

        ini_set('memory_limit', '512M'); 
        $flgSign  = 0;
        $session = Yii::$app->session;
        $intPrintValue = $session->get('print_header');
        $flgSign = $session->get('signature');
        /*echo $intPrintValue;
        echo $flgSign;die;*/
        
        // For Service Invoice
        if($invoicetype == 'service'){
            $serviceModel =  CustomerService::find()->where(['fk_customer_id'=>$model->fk_customer_id])->one();
            if(!empty($serviceModel)){
                $arrServiceDetails = $serviceModel->service;
            }   
            
            $content = $this->renderPartial('view_service', [
                'model'          => $arrResultData,
                'header'         => $intPrintValue,
                'sign'           => $flgSign, 
                'signatureEmail' => $signatureEmailId,
                'headerAddress'  => $headerAddress,
                'headerTelephone'=> $headerTelephone,
                'serviceModel'   => $serviceModel,
                'serviceDetail'  => $arrServiceDetails
            ]);
        
            $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'content' => $this->renderPartial('view_service', [
                'model'          => $arrResultData,
                'header'         => $intPrintValue,
                'sign'           => $flgSign,
                'signatureEmail' => $signatureEmailId,
                'headerAddress'  => $headerAddress,
                'headerTelephone'=> $headerTelephone,
                'serviceModel'   => $serviceModel,
                'serviceDetail'  => $arrServiceDetails
            ]),


            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'options' => [
                'title' => 'Customer Details',
                'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy',

            ],
            'methods' => [
                //'SetHeader' => ['Generated By: Solnet '],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);

            
            
        }elseif($invoicetype == 'custom_service'){
            $customService =   ServiceInvoice::find()->where(['fk_customer_id'=>$model->fk_customer_id,'fk_invoice_id' => $id])->all();
            
            $content =$this->renderPartial('view_custom', [
                'model'             => $arrResultData,
                'header'            => $intPrintValue,
                'sign'              => $flgSign, 
                'signatureEmail'    => $signatureEmailId,
                'headerAddress'     => $headerAddress,
                'headerTelephone'   => $headerTelephone,
                'serviceDetail'     => $customService
            ]);
        
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
                'content' => $this->renderPartial('view_custom', [
                    'model'             => $arrResultData,
                    'header'            => $intPrintValue,
                    'sign'              => $flgSign,
                    'signatureEmail'    => $signatureEmailId,
                    'headerAddress'     => $headerAddress,
                    'headerTelephone'   => $headerTelephone,
                    'serviceDetail'     => $customService
                ]),


                'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
                'options' => [
                    'title' => 'Customer Details',
                    'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy',

                ],
                'methods' => [
                    //'SetHeader' => ['Generated By: Solnet '],
                    'SetFooter' => ['|Page {PAGENO}|'],
                ]
            ]);

        }else{
                

            $content =$this->renderPartial('view', [
                'model'             => $arrResultData,
                'header'            => $intPrintValue,
                'sign'              => $flgSign, 
                'signatureEmail'    => $signatureEmailId,
                'headerAddress'     => $headerAddress,
                'headerTelephone'   => $headerTelephone
            ]);
            
            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
                    'content' => $this->renderPartial('view', [
                    'model'             => $arrResultData,
                    'header'            => $intPrintValue,
                    'sign'              => $flgSign,
                    'signatureEmail'    => $signatureEmailId,
                    'headerAddress'     => $headerAddress,
                    'headerTelephone'   => $headerTelephone
                ]),


                'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
                'options' => [
                    'title' => 'Customer Details',
                    'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy',

                ],
                'methods' => [
                    //'SetHeader' => ['Generated By: Solnet '],
                    'SetFooter' => ['|Page {PAGENO}|'],
                ]
            ]);
                
        }
        echo $pdf->render(); // call the mpdf api output as needed
    }



     /**
     * Displays a single Customerpayment model.
     * @param integer $id
     * @return mixed
     */
    public function actionPaymentview($id)
    {

        $model=Customerpayment::find()->joinWith(['customer','invoice','currency'])->where(['payment_id'=>$id])->one();

        return $this->render('payment_view',['model'=>$model]);
    }



    /**
     * Generate a pdf to print single payment collection details .
     * @param integer $id
     * @return mixed
     */
    public function actionPaymentprint($id) {
        $model=Customerpayment::find()->joinWith(['customer','invoice',
            'currency'])->where(['payment_id'=>$id])->one();

        $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' => $this->renderPartial('payment_view', [
            'model' => $model,
        ]),
        'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Payment collection Details',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
            //'SetHeader' => ['Generated By: Solnet'],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
     ]);
        $logArray = array();
        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
        $logArray['module'] = 'Report';
        $logArray['action'] = 'print';
        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has printed the payment collection report of "'.$model->customer->name.'" ';
        $logArray['created'] = date('Y-m-d H:i:s');
        Yii::$app->customcomponents->logActivity($logArray);

        return $pdf->render();
    }


     /**
     * Lists all Summary collection reports
     * @return mixed
     */
     /*public function actionSummarycollectionreport()
    {
        $session = Yii::$app->session;
        unset($_SESSION['startdate']);
        unset($_SESSION['enddate']);
         $modelUserStates = new TbluserStates();
        $strStartDate=$strEndDate='';
        $uniquePayDates=$uniqueDepDates=$arrayCombine=
        $arrBankdepositSumarray=$arrPaymentSum=$arrSummary=
        $arrCurrencyName=array();;

        $query = new Query;
        $query2 = new Query;
        $arrCurrencyName= $query->select('*')//get all currency
                ->from('tblcurrency')
                ->all();


         if(isset($_POST['start_date']) &&
                isset($_POST['end_date'])){
             $strStartDate=$_POST['start_date'];
             $strEndDate=$_POST['end_date'];
             $session->set('startdate', $strStartDate);
             $session->set('enddate', $strEndDate);


            // get sum of amount according to currency and deposit date
            $arrBankdepositSum=$query->select('sum(amount) as total,fk_currency_id,deposit_date');
            $query->from('tblbankdeposit')->join('LEFT JOIN', 'tblcustomer as C1', 'C1.customer_id = tblbankdeposit.fk_customer_id');

            if(!empty($strStartDate)&&!empty($strEndDate)){
             $dtStartdate=date("Y-m-d ",strtotime($strStartDate));
             $dtEnddate=date("Y-m-d ",strtotime($strEndDate));
            $query->where(['between','deposit_date',$dtStartdate,$dtEnddate]);
            }

            $query->groupBy(['deposit_date','fk_currency_id']);
            $sessionData = Yii::$app->session;
            if(!empty($sessionData->get('user_state_id')))
            {
                if($sessionData->get('user_state_id')=="all")
                {
                    $stateList = $modelUserStates->getUserStates();
                    $query->andWhere(['in',['C1.fk_state_id'],$stateList]);     
                }
                else
                {
                    $stateId = $sessionData->get('user_state_id');
                    $query->andWhere(['C1.fk_state_id'=>$stateId]);
                }       
            }
            else
            {
                $query->andWhere(['fk_state_id'=>null]);
            }
            $arrBankdepositSum= $query->all();




            //get sum of paid amount according to currency and payment date
            $arrPaymentSum=$query2->select('payment_date, sum(amount_paid) as total,fk_currency_id');
            $query2->from('tblcustomerpayment')->join('LEFT JOIN', 'tblcustomer', 'tblcustomer.customer_id = tblcustomerpayment.fk_customer_id');

             if(!empty($strStartDate)&&!empty($strEndDate)){
            $dtStartdate=date("Y-m-d ",strtotime($strStartDate));
            $dtEnddate=date("Y-m-d ",strtotime($strEndDate));
            $query2->where(['between','payment_date',$dtStartdate,$dtEnddate]);
            }

            $query2->groupBy(['payment_date','fk_currency_id']);
            
            $sessionData = Yii::$app->session;
            if(!empty($sessionData->get('user_state_id')))
            {
                if($sessionData->get('user_state_id')=="all")
                {
                    $stateList = $modelUserStates->getUserStates();
                    $query2->andWhere(['in',['tblcustomer.fk_state_id'],$stateList]);     
                }
                else
                {
                    $stateId = $sessionData->get('user_state_id');
                    $query2->andWhere(['tblcustomer.fk_state_id'=>$stateId]);
                }       
            }
            else
            {
                $query2->andWhere(['fk_state_id'=>null]);
            }
            $arrPaymentSum=$query2->all();

         }
         else{
            $strStartDate = date('Y-m-01'); // hard-coded '01' for first day
            $strEndDate  = date('Y-m-t');

            $arrBankdepositSum=$query->select('sum(amount) as total,fk_currency_id,deposit_date')
                ->from('tblbankdeposit')->join('LEFT JOIN', 'tblcustomer as C1', 'C1.customer_id = tblbankdeposit.fk_customer_id')
                ->where(['between','deposit_date',$strStartDate,$strEndDate])
                ->groupBy(['deposit_date','fk_currency_id']);

            $sessionData = Yii::$app->session;
            if(!empty($sessionData->get('user_state_id')))
            {
                if($sessionData->get('user_state_id')=="all")
                {
                    $stateList = $modelUserStates->getUserStates();
                    $query->andWhere(['in',['C1.fk_state_id'],$stateList]);     
                }
                else
                {
                    $stateId = $sessionData->get('user_state_id');
                    $query->andWhere(['C1.fk_state_id'=>$stateId]);
                }       
            }
            else
            {
                $query->andWhere(['fk_state_id'=>null]);
            }


             $arrBankdepositSum =  $query->all();

             $arrPaymentSum=$query2->select('payment_date, sum(amount_paid) as total,fk_currency_id')
                ->from('tblcustomerpayment')->join('LEFT JOIN', 'tblcustomer', 'tblcustomer.customer_id = tblcustomerpayment.fk_customer_id')
                ->where(['between','payment_date',$strStartDate,$strEndDate])
                ->groupBy(['payment_date','fk_currency_id']);
                $sessionData = Yii::$app->session;
                if(!empty($sessionData->get('user_state_id')))
                {
                    if($sessionData->get('user_state_id')=="all")
                    {
                        $stateList = $modelUserStates->getUserStates();
                        $query2->andWhere(['in',['tblcustomer.fk_state_id'],$stateList]);     
                    }
                    else
                    {
                        $stateId = $sessionData->get('user_state_id');
                        $query2->andWhere(['tblcustomer.fk_state_id'=>$stateId]);
                    }       
                }
                else
                {
                    $query2->andWhere(['fk_state_id'=>null]);
                }
               
                $arrPaymentSum=$query2->all();

            }
        
		if(!empty($arrBankdepositSum)){
			$uniqueDepDates = array_unique(array_map(function ($i) { return $i['deposit_date']; }, $arrBankdepositSum));
			 rsort($uniqueDepDates);

		}

		if(!empty($arrPaymentSum)){
			$uniquePayDates = array_unique(array_map(function ($i) { return $i['payment_date']; }, $arrPaymentSum));
			rsort($uniquePayDates);

		}

		$arrayCombine = array_unique(array_merge($uniqueDepDates,$uniquePayDates));
		rsort($arrayCombine);
        
		if(!empty($arrayCombine)){
			$arrCurrency = ['1','2','3'];

			foreach($arrayCombine as $key => $value){
				foreach($arrCurrency as $keyCur => $valueCur){
					//foreach($uniqueDepDates as $depKey => $depVal){
						// DEPOSIT IN BANK
						foreach($arrBankdepositSum as $bkey => $bValue){
						if($valueCur == $bValue['fk_currency_id'] && $value == $bValue['deposit_date'] )
							$arrSummary[$value][$valueCur]['deposit'] = $bValue['total'];
						}
						// PAYMENT BY CUSTOMER
						foreach($arrPaymentSum as $pkey => $pValue){
                            
						if($valueCur == $pValue['fk_currency_id'] && $value == $pValue['payment_date'] )
							$arrSummary[$value][$valueCur]['payment'] = $pValue['total'];
						}

					//}
				}

			}
            
		}

        return $this->render('summary_collection',['arrSummary'=>
            $arrSummary,'arrCurrencyName'=>$arrCurrencyName]);
    }*/

    // new function
    public function actionSummarycollectionreport()
    {
        ini_set('max_execution_time', '300');
        ini_set('max_execution_time', '0');

        $modelPayment = new Customerpayment();
        $modelBankDeposit = new Bankdeposit();
        $modelUserStates = new TbluserStates();
        $paymentData = array();
        $depositData = array();
        $whereInvoice = array();
        $whereCustomerPay = array();
        $whereCustomerDep = array();
        $getStatus = array();
        $sessionData = Yii::$app->session;
        $invoice_id="";
        $customer_id="";
        $getStatus = $modelPayment->getStatusPayment();
        $queryPay = new Query();
        $queryDeposit = new Query();
        
        if(isset($_POST['customer_invoice_id']) && $_POST['customer_invoice_id']!="")
        {
            $whereInvoice = ['fk_invoice_id'=>$_POST['customer_invoice_id']];
            $invoice_id = $_POST['customer_invoice_id'];
            $_SESSION['invoice_id'] = $invoice_id;
        }
        else
        {
            unset($_SESSION['invoice_id']);
        }

        if(isset($_POST['customer_id']) && $_POST['customer_id']!="")
        {
            $whereCustomerPay = ['tblcustomerpayment.fk_customer_id'=>$_POST['customer_id']];
            $whereCustomerDep = ['tblbankdeposit.fk_customer_id'=>$_POST['customer_id']];
            $customer_id = $_POST['customer_id'];
            $_SESSION['cust_id'] = $customer_id;
        }
        else
        {
            unset($_SESSION['cust_id']);
        }

        if(!empty($sessionData->get('user_state_id')))
        {
            if($sessionData->get('user_state_id')=="all")
            {
                $stateList = $modelUserStates->getUserStates();
                $whereState = ['in',['fk_state_id'],$stateList];
            }
            else
            {
                $stateId = $sessionData->get('user_state_id');
                $whereState = ['fk_state_id'=>$stateId];
            }       
        }
        else
        {
            $whereState = ['fk_state_id'=>null];
        }
        
        if(isset($_POST['start_date']) && isset($_POST['end_date']))
        {
            $startDate = date('Y-m-d',strtotime($_POST['start_date']));
            $endDate = date('Y-m-d',strtotime($_POST['end_date']));
            $_SESSION['start_date_summary'] = $startDate;
            $_SESSION['end_date_summary'] = $endDate;
        }
        else
        {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
            $_SESSION['start_date_summary'] = $startDate;
            $_SESSION['end_date_summary'] = $endDate;
        }
        if(isset($_POST['fk_currency_id']) && !empty($_POST['fk_currency_id']))
        {
            $currency = $_POST['fk_currency_id'];
            $_SESSION['currency'] = $currency;
        }
        else
        {
            $currency  = 1;
             $_SESSION['currency'] = $currency;
        }

        $arrInvoiceId    = Customerinvoice::find()->select(['customer_invoice_id','invoice_number','fk_customer_id'])->orderBy(['customer_invoice_id'=>SORT_DESC])->asArray()->all();
        $invoiceIdListData   = ArrayHelper::map($arrInvoiceId,'customer_invoice_id','invoice_number');

        $arrCustomerId    = Customerpayment::find()->joinWith('customer')->select(['fk_customer_id','name'])->orderBy(['fk_customer_id'=>SORT_DESC])->asArray()->all();
        $customerIdListData   = ArrayHelper::map($arrCustomerId,'fk_customer_id','name');


        $paymentData = Customerpayment::find()->select(['payment_id','fk_invoice_id','tblcustomerinvoice.invoice_number','amount_paid','payment_date','tblcustomerpayment.fk_customer_id'])
        ->where(['between','payment_date',$startDate,$endDate])
        ->andWhere(['fk_currency_id'=>$currency])
        ->andWhere($whereState)
        ->andWhere($whereInvoice)
        ->andWhere($whereCustomerPay)
        ->joinWith(['invoice','customer.state'])
        ->asArray()->all();
        /*echo "<pre>";
        print_r($paymentData);die;*/
        $depositData = Bankdeposit::find()->select(['deposit_date','amount','fk_invoice_id','tblcustomerinvoice.invoice_number','tblbankdeposit.fk_customer_id'])
        ->where(['between','deposit_date',$startDate,$endDate])
        ->andWhere(['fk_currency_id'=>$currency])
        ->andWhere($whereState)
        ->andWhere($whereInvoice)
        ->andWhere($whereCustomerDep)
        ->joinWith(['customerinvoice','customer.state','customer'])
        ->asArray()->all();
        /*echo "<pre>";
        print_r($depositData);die;*/
         return $this->render('summary_collection_new',['paymentData'=>
            $paymentData,'depositData'=>$depositData,'currency'=>$currency,'invoice'=>$invoiceIdListData,'invoice_id'=>$invoice_id,'customer'=>$customerIdListData,'customer_id'=>$customer_id,'getStatus'=>$getStatus]);
       
    }
    public function actionSummaryprint()
    {

        $modelPayment = new Customerpayment();
        $modelBankDeposit = new Bankdeposit();
        $modelUserStates = new TbluserStates();
        $paymentData = array();
        $depositData = array();
        $whereInvoice = array();
        $whereCustomerDep = array();
        $whereCustomerPay = array();
        $getStatus = array();
        $sessionData = Yii::$app->session;
        $invoice_id="";
        $customer_id = "";
        $getStatus = $modelPayment->getStatusPayment();

        if(isset($_SESSION['invoice_id']) && $_SESSION['invoice_id']!="")
        {
            $whereInvoice = ['fk_invoice_id'=>$_SESSION['invoice_id']];
            $invoice_id = $_SESSION['invoice_id'];
        }
        if(isset($_SESSION['cust_id']) && $_SESSION['cust_id']!="")
        {
            $whereCustomerPay = ['tblcustomerpayment.fk_customer_id'=>$_SESSION['cust_id']];
            $whereCustomerDep = ['tblbankdeposit.fk_customer_id'=>$_SESSION['cust_id']];
            $customer_id = $_SESSION['cust_id'];
        }
        if(!empty($sessionData->get('user_state_id')))
        {
            if($sessionData->get('user_state_id')=="all")
            {
                $stateList = $modelUserStates->getUserStates();
                $whereState = ['in',['fk_state_id'],$stateList];
            }
            else
            {
                $stateId = $sessionData->get('user_state_id');
                $whereState = ['fk_state_id'=>$stateId];
            }       
        }
        else
        {
            $whereState = ['fk_state_id'=>null];
        }
        
        if(isset($_SESSION['start_date_summary']) && isset($_SESSION['start_date_summary']))
        {
            $startDate = date('Y-m-d',strtotime($_SESSION['start_date_summary']));
            $endDate = date('Y-m-d',strtotime($_SESSION['end_date_summary']));
        }
        else
        {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }
        if(isset($_SESSION['currency']) && !empty($_SESSION['currency']))
        {
            $currency = $_SESSION['currency'];
        }
        else
        {
            $currency  = 1;
        }

        $arrInvoiceId    = Customerinvoice::find()->select(['customer_invoice_id','invoice_number','fk_customer_id'])->orderBy(['customer_invoice_id'=>SORT_DESC])->asArray()->all();
        $invoiceIdListData   = ArrayHelper::map($arrInvoiceId,'customer_invoice_id','invoice_number');
        $arrCustomerId    = Customerpayment::find()->joinWith('customer')->select(['fk_customer_id','name'])->orderBy(['fk_customer_id'=>SORT_DESC])->asArray()->all();
        $customerIdListData   = ArrayHelper::map($arrCustomerId,'fk_customer_id','name');

        $paymentData = Customerpayment::find()->select(['payment_id','fk_invoice_id','tblcustomerinvoice.invoice_number','amount_paid','payment_date','tblcustomerpayment.fk_customer_id'])
        ->where(['between','payment_date',$startDate,$endDate])
        ->andWhere(['fk_currency_id'=>$currency])
        ->andWhere($whereState)
        ->andWhere($whereInvoice)
        ->andWhere($whereCustomerPay)
        ->joinWith(['invoice','customer.state'])
        ->asArray()->all();
       
        $depositData = Bankdeposit::find()->select(['deposit_date','amount','fk_invoice_id','tblcustomerinvoice.invoice_number','tblbankdeposit.fk_customer_id'])
        ->where(['between','deposit_date',$startDate,$endDate])
        ->andWhere(['fk_currency_id'=>$currency])
        ->andWhere($whereState)
        ->andWhere($whereInvoice)
        ->andWhere($whereCustomerDep)
        ->joinWith(['customerinvoice','customer.state','customer'])
        ->asArray()->all();
       // echo $paymentData->createCommand()->getRawSql();die;
         $content=  $this->renderPartial('summary_collection_new_print',['paymentData'=>
            $paymentData,'depositData'=>$depositData,'currency'=>$currency,'invoice'=>$invoiceIdListData,'invoice_id'=>$invoice_id,'customer'=>$customerIdListData,'customer_id'=>$customer_id,'getStatus'=>$getStatus],true);
         
        $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' =>$content,
        'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Summary collection Reports',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
           // 'SetHeader' => ['Generated By: Solnet'],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
     ]);
        $logArray = array();
        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
        $logArray['module'] = 'Report';
        $logArray['action'] = 'Print';
        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has printed the Summary Collection Report' ;
        $logArray['created'] = date('Y-m-d H:i:s');
        Yii::$app->customcomponents->logActivity($logArray);
        return $pdf->render();
       
    }

    public function actionSummaryexcel()
    {
        $modelPayment = new Customerpayment();
        $modelBankDeposit = new Bankdeposit();
        $modelUserStates = new TbluserStates();
        $paymentData = array();
        $depositData = array();
        $whereInvoice = array();
        $whereCustomerDep = array();
        $whereCustomerPay = array();
        $getStatus = array();
        $sessionData = Yii::$app->session;
        $invoice_id="";
        $customer_id = "";
        $getStatus = $modelPayment->getStatusPayment();

        if(isset($_SESSION['invoice_id']) && $_SESSION['invoice_id']!="")
        {
            $whereInvoice = ['fk_invoice_id'=>$_SESSION['invoice_id']];
            $invoice_id = $_SESSION['invoice_id'];
        }
        if(isset($_SESSION['cust_id']) && $_SESSION['cust_id']!="")
        {
            $whereCustomerPay = ['tblcustomerpayment.fk_customer_id'=>$_SESSION['cust_id']];
            $whereCustomerDep = ['tblbankdeposit.fk_customer_id'=>$_SESSION['cust_id']];
            $customer_id = $_SESSION['cust_id'];
        }
        if(!empty($sessionData->get('user_state_id')))
        {
            if($sessionData->get('user_state_id')=="all")
            {
                $stateList = $modelUserStates->getUserStates();
                $whereState = ['in',['fk_state_id'],$stateList];
            }
            else
            {
                $stateId = $sessionData->get('user_state_id');
                $whereState = ['fk_state_id'=>$stateId];
            }       
        }
        else
        {
            $whereState = ['fk_state_id'=>null];
        }
        
        if(isset($_SESSION['start_date_summary']) && isset($_SESSION['start_date_summary']))
        {
            $startDate = date('Y-m-d',strtotime($_SESSION['start_date_summary']));
            $endDate = date('Y-m-d',strtotime($_SESSION['end_date_summary']));
        }
        else
        {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }
        if(isset($_SESSION['currency']) && !empty($_SESSION['currency']))
        {
            $currency = $_SESSION['currency'];
        }
        else
        {
            $currency  = 1;
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
        $arrInvoiceId    = Customerinvoice::find()->select(['customer_invoice_id','invoice_number','fk_customer_id'])->orderBy(['customer_invoice_id'=>SORT_DESC])->asArray()->all();
        $invoiceIdListData   = ArrayHelper::map($arrInvoiceId,'customer_invoice_id','invoice_number');
        $arrCustomerId    = Customerpayment::find()->joinWith('customer')->select(['fk_customer_id','name'])->orderBy(['fk_customer_id'=>SORT_DESC])->asArray()->all();
        $customerIdListData   = ArrayHelper::map($arrCustomerId,'fk_customer_id','name');

        $paymentData = Customerpayment::find()->select(['payment_id','fk_invoice_id','tblcustomerinvoice.invoice_number','amount_paid','payment_date','tblcustomerpayment.fk_customer_id'])
        ->where(['between','payment_date',$startDate,$endDate])
        ->andWhere(['fk_currency_id'=>$currency])
        ->andWhere($whereState)
        ->andWhere($whereInvoice)
        ->andWhere($whereCustomerPay)
        ->joinWith(['invoice','customer.state'])
        ->asArray()->all();
       
        $depositData = Bankdeposit::find()->select(['deposit_date','amount','fk_invoice_id','tblcustomerinvoice.invoice_number','tblbankdeposit.fk_customer_id'])
        ->where(['between','deposit_date',$startDate,$endDate])
        ->andWhere(['fk_currency_id'=>$currency])
        ->andWhere($whereState)
        ->andWhere($whereInvoice)
        ->andWhere($whereCustomerDep)
        ->joinWith(['customerinvoice','customer.state','customer'])
        ->asArray()->all();

        $filename = 'summarycollection-'.Date('YmdGis').'.xls';
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=".$filename);
        
        echo '<table border="1px" >
        
                      <tr>
                        <th><strong>Date</strong></th>
                        <th><strong>'.$strCurrency.'</strong></th>
                      </tr>
                      <tr>
                       
                        <td><strong></strong></td>
                         
                         <th><strong>Payment</strong></th>
                         <th><strong>Invoice ID</strong></th>
                         <th><strong>Customer </strong></th>
                         <th><strong>Status</strong></th>
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
                        <th><strong>Date</strong></th>
                        <th><strong>'.$strCurrency.'</strong></th>
                      </tr>
                      <tr>
                       
                        <td><strong></strong></td>
                         
                         <th><strong>Deposit</strong></th>
                         <th><strong>Invoice ID</strong></th>
                         <th><strong>Customer </strong></th>
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
    }
     /**
     * Generate a pdf to print single summary collection details .
     * @param integer $id
     * @return mixed
     */
    /*public function actionSummaryprint() {
        $session = Yii::$app->session;
        $strStartDate=$strEndDate='';
        $uniquePayDates=$uniqueDepDates=$arrayCombine=
        $arrBankdepositSumarray=$arrPaymentSum=$arrSummary=
        $arrCurrencyName=array();;

        $query = new Query;
        $arrCurrencyName= $query->select('*')//get all currency
                ->from('tblcurrency')
                ->all();


         if(isset($session['startdate']) &&
                isset($session['enddate'])){

            $strStartDate=$session['startdate'];
            $strEndDate=$session['enddate'];

            // get sum of amount according to currency and deposit date
            $arrBankdepositSum=$query->select('sum(amount) as total,fk_currency_id,deposit_date');
            $query->from('tblbankdeposit');

            if(!empty($strStartDate)&&!empty($strEndDate)){
            $dtStartdate=date("Y-m-d ",strtotime($strStartDate));
            $dtEnddate=date("Y-m-d ",strtotime($strEndDate));
            $query->where(['between','deposit_date',$dtStartdate,$dtEnddate]);
            }

            $query->groupBy(['deposit_date','fk_currency_id']);
            $arrBankdepositSum= $query->all();

            //get sum of paid amount according to currency and payment date
            $arrPaymentSum=$query->select('payment_date, sum(amount_paid) as total,fk_currency_id');
            $query->from('tblcustomerpayment');

             if(!empty($strStartDate)&&!empty($strEndDate)){
            $dtStartdate=date("Y-m-d ",strtotime($strStartDate));
            $dtEnddate=date("Y-m-d ",strtotime($strEndDate));
            $query->where(['between','payment_date',$dtStartdate,$dtEnddate]);
            }

            $query->groupBy(['payment_date','fk_currency_id']);
            $arrPaymentSum=$query->all();

         }
         else{

            $strStartDate = date('Y-m-01'); // hard-coded '01' for first day
            $strEndDate  = date('Y-m-t');//last day of current month

            $arrBankdepositSum=$query->select('sum(amount) as total,fk_currency_id,deposit_date')
                ->from('tblbankdeposit')
                ->groupBy(['deposit_date','fk_currency_id'])
                ->where(['between','deposit_date',$strStartDate,$strEndDate])
                ->all();

             $arrPaymentSum=$query->select('payment_date, sum(amount_paid) as total,fk_currency_id')
                ->from('tblcustomerpayment')
                ->where(['between','payment_date',$strStartDate,$strEndDate])
                ->groupBy(['payment_date','fk_currency_id'])
                ->all();

            }


        if(!empty($arrBankdepositSum)){
            $uniqueDepDates = array_unique(array_map(function ($i) { return $i['deposit_date']; }, $arrBankdepositSum));
             rsort($uniqueDepDates);

        }

        if(!empty($arrPaymentSum)){
            $uniquePayDates = array_unique(array_map(function ($i) { return $i['payment_date']; }, $arrPaymentSum));
            rsort($uniquePayDates);

        }

        $arrayCombine = array_unique(array_merge($uniqueDepDates,$uniquePayDates));
        rsort($arrayCombine);

        if(!empty($arrayCombine)){
            $arrCurrency = ['1','2','3'];
            $arrSummary = array();
            foreach($arrayCombine as $key => $value){
                foreach($arrCurrency as $keyCur => $valueCur){
                    foreach($uniqueDepDates as $depKey => $depVal){
                        // DEPOSIT IN BANK
                        foreach($arrBankdepositSum as $bkey => $bValue){
                        if($valueCur == $bValue['fk_currency_id'] && $value == $bValue['deposit_date'] )
                            $arrSummary[$value][$valueCur]['deposit'] = $bValue['total'];
                        }
                        // PAYMENT BY CUSTOMER
                        foreach($arrPaymentSum as $pkey => $pValue){
                        if($valueCur == $pValue['fk_currency_id'] && $value == $pValue['payment_date'] )
                            $arrSummary[$value][$valueCur]['payment'] = $pValue['total'];
                        }

                    }
                }

            }

        }

        $content=  $this->renderPartial('summary_collection_print',['arrSummary'=>$arrSummary,
            'strStartDate'=>$strStartDate,
            'strEndDate'=>$strEndDate],true);

        $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' =>$content,
        'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Summary collection Reports',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
           // 'SetHeader' => ['Generated By: Solnet'],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
     ]);
        $logArray = array();
        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
        $logArray['module'] = 'Report';
        $logArray['action'] = 'Print';
        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has printed the Summary Collection Report' ;
        $logArray['created'] = date('Y-m-d H:i:s');
        Yii::$app->customcomponents->logActivity($logArray);
        return $pdf->render();


    }*/

    /*public function actionSummaryexcel()
    {

        $session = Yii::$app->session;
        $strStartDate=$strEndDate='';
        $depositIDRTotal=$depositSDDTotal=$depositUSDTotal=0;
        $paymentIDRTotal=$paymentSDDTotal=$paymentUSDTotal=0;
        $uniquePayDates=$uniqueDepDates=$arrayCombine=
        $arrBankdepositSumarray=$arrPaymentSum=$arrSummary=
        $arrCurrencyName=array();;

        $query = new Query;
        $arrCurrencyName= $query->select('*')//get all currency
                ->from('tblcurrency')
                ->all();


         if(isset($session['startdate']) &&
                isset($session['enddate'])){

            $strStartDate=$session['startdate'];
            $strEndDate=$session['enddate'];

            // get sum of amount according to currency and deposit date
            $arrBankdepositSum=$query->select('sum(amount) as total,fk_currency_id,deposit_date');
            $query->from('tblbankdeposit');

            if(!empty($strStartDate)&&!empty($strEndDate)){
            $dtStartdate=date("Y-m-d ",strtotime($strStartDate));
            $dtEnddate=date("Y-m-d ",strtotime($strEndDate));
            $query->where(['between','deposit_date',$dtStartdate,$dtEnddate]);
            }

            $query->groupBy(['deposit_date','fk_currency_id']);
            $arrBankdepositSum= $query->all();

            //get sum of paid amount according to currency and payment date
            $arrPaymentSum=$query->select('payment_date, sum(amount_paid) as total,fk_currency_id');
            $query->from('tblcustomerpayment');

             if(!empty($strStartDate)&&!empty($strEndDate)){
            $dtStartdate=date("Y-m-d ",strtotime($strStartDate));
            $dtEnddate=date("Y-m-d ",strtotime($strEndDate));
            $query->where(['between','payment_date',$dtStartdate,$dtEnddate]);
            }

            $query->groupBy(['payment_date','fk_currency_id']);
            $arrPaymentSum=$query->all();

         }
         else{

            $strStartDate = date('Y-m-01'); // hard-coded '01' for first day
            $strEndDate  = date('Y-m-t');//last day of current month

            $arrBankdepositSum=$query->select('sum(amount) as total,fk_currency_id,deposit_date')
                ->from('tblbankdeposit')
                ->groupBy(['deposit_date','fk_currency_id'])
                ->where(['between','deposit_date',$strStartDate,$strEndDate])
                ->all();

             $arrPaymentSum=$query->select('payment_date, sum(amount_paid) as total,fk_currency_id')
                ->from('tblcustomerpayment')
                ->where(['between','payment_date',$strStartDate,$strEndDate])
                ->groupBy(['payment_date','fk_currency_id'])
                ->all();

            }


        if(!empty($arrBankdepositSum)){
            $uniqueDepDates = array_unique(array_map(function ($i) { return $i['deposit_date']; }, $arrBankdepositSum));
             rsort($uniqueDepDates);

        }

        if(!empty($arrPaymentSum)){
            $uniquePayDates = array_unique(array_map(function ($i) { return $i['payment_date']; }, $arrPaymentSum));
            rsort($uniquePayDates);

        }

        $arrayCombine = array_unique(array_merge($uniqueDepDates,$uniquePayDates));
        rsort($arrayCombine);

        if(!empty($arrayCombine)){
            $arrCurrency = ['1','2','3'];
            $arrSummary = array();
            foreach($arrayCombine as $key => $value){
                foreach($arrCurrency as $keyCur => $valueCur){
                    foreach($uniqueDepDates as $depKey => $depVal){
                        // DEPOSIT IN BANK
                        foreach($arrBankdepositSum as $bkey => $bValue){
                        if($valueCur == $bValue['fk_currency_id'] && $value == $bValue['deposit_date'] )
                            $arrSummary[$value][$valueCur]['deposit'] = $bValue['total'];
                        }
                        // PAYMENT BY CUSTOMER
                        foreach($arrPaymentSum as $pkey => $pValue){
                        if($valueCur == $pValue['fk_currency_id'] && $value == $pValue['payment_date'] )
                            $arrSummary[$value][$valueCur]['payment'] = $pValue['total'];
                        }

                    }
                }

            }

        }

    $filename = 'summarycollection-'.Date('YmdGis').'.xls';
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=".$filename);
    echo '<table border=1px>
                      <caption><b>Summary Collection Report</b>
                      </caption>
                       <tr>Duration:'.$strStartDate.' to '.$strEndDate.'</tr>
                      <tr>
                        <th>Date</th>
                        <th colspan="2"> IDR</th>
                        <th colspan="2">SGD </th>
                        <th colspan="2">USD </th>
                      </tr>

                      <tr>
                        <td></td>
                        <td> Deposit </td>
                        <td> Payment</td>
                        <td> Deposit </td>
                        <td> Payment</td>
                        <td> Deposit </td>
                        <td> Payment</td>
                      </tr>';


                            foreach($arrSummary as $key => $date){
							 echo'<tr> ';
                           echo '<td>' . $key. '</td><td> ';
                           if(isset($arrSummary[$key][1]['deposit'])){
                                print_r($arrSummary[$key][1]['deposit']);
                                $depositIDRTotal+=$arrSummary[$key][1]['deposit'];
                            }else{
                                echo "--";
                            } echo'</td><td>';


                             if(isset($arrSummary[$key][1]['payment'])){
                                 print_r($arrSummary[$key][1]['payment']);
                                 $paymentIDRTotal+=$arrSummary[$key][1]['payment'];

                            }else{
                                echo "--";
                            } echo'</td><td>';

                            if(isset($arrSummary[$key][2]['deposit'])){
                                print_r($arrSummary[$key][2]['deposit']);
                                $depositSDDTotal+=$arrSummary[$key][2]['deposit'];

                            }else{
                                echo "--";
                            } echo'</td><td>';
                            if(isset($arrSummary[$key][2]['payment'])){
                                 print_r($arrSummary[$key][2]['payment']);
                                 $paymentSDDTotal+=$arrSummary[$key][2]['payment'];

                            }else{
                                echo "--";
                            } echo'</td><td>';
                            if(isset($arrSummary[$key][3]['deposit'])){
                                print_r($arrSummary[$key][3]['deposit']);
                                $depositUSDTotal+=$arrSummary[$key][3]['deposit'];

                            }else{
                                echo "--";
                            } echo'</td><td>';
                         if(isset($arrSummary[$key][3]['payment'])){
                                 print_r($arrSummary[$key][3]['payment']);
                                 $paymentUSDTotal+=$arrSummary[$key][3]['payment'];

                            }else{
                                echo "--";
                            }
						 echo '</tr>';

                       }

                       echo '<tr><td><b>Total Amount</b></td>
                       <td><b>'.$depositIDRTotal.'</b></td>
                       <td><b>'.$paymentIDRTotal.'</b></td>
                       <td><b>'.$depositSDDTotal.'</b></td>
                       <td><b>'.$paymentSDDTotal.'</b></td>
                       <td><b>'.$depositUSDTotal.'</b></td>
                       <td><b>'.$paymentUSDTotal.'</b></td></tr>';
                echo '</table>';
    }*/

	public function actionRevenue()
	{
		$searchModel = new CustomerinvoiceSearch();
	 	$queryParams = Yii::$app->request->queryParams;
		$postedArray = Yii::$app->request->get();
		$intCurrencyId = 1;
        
		/*************To fetch package from table************/
		$arrPackage 	= Package::find()->where(['is_deleted'=>'0'])->all();
		$packageListData	= ArrayHelper::map($arrPackage,'package_id','package_title');
        $arrState   = State::find()->where(['status'=>'active'])->all();
        $stateListData  = ArrayHelper::map($arrState,'state_id','state');
        
        $modelLinkPackage = new Linkcustomepackage();
        if (isset($postedArray['CustomerinvoiceSearch']['currency_id'])) {
            $resultRecurring = $modelLinkPackage->getrecurring($postedArray['CustomerinvoiceSearch']['currency_id']);
        }else{
            $resultRecurring = $modelLinkPackage->getrecurring(1);
        }

        $resultRecurringByState = $modelLinkPackage->getrecurringByState();
        $resultRecurringByStateTotal = $modelLinkPackage->getrecurringByStateTotal();
        if($resultRecurringByState)
        {
            $totalSum = array();
            foreach($resultRecurringByState as $key=>$value)
            {

                foreach($stateListData as $keyState=>$valState)
                {
                    /*if($keyState==$value['fk_state_id'])
                    {*/
                        $dataState[$value['fk_package_id']]['package_title'] = $value['package_title'];
                        $dataState[$value['fk_package_id']]['package_id'] = $value['fk_package_id'];
                        $dataState[$value['fk_package_id']]['states'][$value['fk_state_id']] = $value['Recurring'];
                        
                    /*}
                    else
                    {*/
                        if (!array_key_exists($keyState,$dataState[$value['fk_package_id']]['states']))
                        {
                            $dataState[$value['fk_package_id']]['states'][$keyState] = 0;
                        } 
                   //}
                    
                }
            }
        }
        /*echo "<pre>";
        print_r($dataState);die;*/
        $sumArray = array();

       
		/*************To fetch state from table************/
        
		if(!empty($postedArray))
		{

			if(!empty($postedArray["CustomerinvoiceSearch"]['start_date']) && !empty($postedArray["CustomerinvoiceSearch"]['end_date']))
			{

				$strStartDate	=	Yii::$app->formatter->asDate($postedArray["CustomerinvoiceSearch"]['start_date'], 'php:Y-m-d');
				$strEndDate		=	Yii::$app->formatter->asDate($postedArray["CustomerinvoiceSearch"]['end_date'], 'php:Y-m-d');
				/*-------added below 2 lines for passing query parameters--------*/
				 $queryParams["CustomerinvoiceSearch"]["start_date"]   = $strStartDate;
				 $queryParams["CustomerinvoiceSearch"]["end_date"] 	= $strEndDate;
				/*-------added below 2 lines for passing query parameters--------*/
			}
			if(!empty($postedArray['CustomerinvoiceSearch']['currency_id']) && !empty($postedArray['CustomerinvoiceSearch']['currency_id']))
			{
				$intCurrencyId	=	$postedArray['CustomerinvoiceSearch']['currency_id'];
                $queryParams["CustomerinvoiceSearch"]["currency_id"] = $intCurrencyId;
			}
            if(!empty($postedArray['CustomerinvoiceSearch']['package_id']))
            {
                $queryParams['CustomerinvoiceSearch']['package_id'] = $postedArray['CustomerinvoiceSearch']['package_id'];
            }
		}

	 	$dataProvider = $searchModel->searchRevenue($queryParams);
        if (isset($postedArray['CustomerinvoiceSearch']['currency_id'])) {
            
            $intTotalServiceChage = $searchModel->getTotalServicePrice($postedArray['CustomerinvoiceSearch']['currency_id']);
        }else{
            $intTotalServiceChage = $searchModel->getTotalServicePrice(1);
        }

        
	 	/*$dataProvider->query->where("tblcustomerinvoice.status = 'partial'");
		$dataProvider->query->orWhere("tblcustomerinvoice.status = 'unpaid'");
		$dataProvider->query->orWhere("tblcustomerinvoice.status = 'paid'");*/

		/*$dataProvider->query->andWhere("linkcustomepackage.fk_currency_id =".$intCurrencyId);
		if(!empty($strStartDate) && !empty($strEndDate)){
			$dataProvider->query->andWhere("DATE_FORMAT(tblcustomerinvoice.created_at,'%Y-%m-%d') between '".$strStartDate."' AND '".$strEndDate."'");
		}*/
		/*if(!empty($postedArray['CustomerinvoiceSearch']['package_id'])){
			$dataProvider->query->andWhere("linkcustomepackage.fk_package_id =".$postedArray['CustomerinvoiceSearch']['package_id']);
		}*/
		return $this->render('revenue',[
            'recurringState'        => $dataState,
            'searchModel'           => $searchModel,
            'dataProvider'          => $dataProvider,
            'stateList'             => $stateListData,
            'recurring'             => $resultRecurring,
            'packageList'           => $packageListData,
            'intTotalServiceChage'  => $intTotalServiceChage,
            'recurringStateTotal'   => $resultRecurringByStateTotal
        ]);
	}

    public function actionSalesrevenue()
    {
        
        $arrTotaldata = array();
        $report = array();
        $total = array();
        $intCurrencyId = 1;
        $searchModel = new CustomerinvoiceSearch();
        $queryParams = Yii::$app->request->queryParams;
        $postedArray = Yii::$app->request->post();
        if(!empty($postedArray))
        {
            if(isset($postedArray["CustomerinvoiceSearch"]['start_date']) && !empty($postedArray["CustomerinvoiceSearch"]['start_date']))
            {
                $year = $postedArray["CustomerinvoiceSearch"]['start_date'];
                $_SESSION['year'] = $year;
            }
            if(!empty($postedArray['CustomerinvoiceSearch']['currency_id']) && !empty($postedArray['CustomerinvoiceSearch']['currency_id']))
            {
                $currency  =   $postedArray['CustomerinvoiceSearch']['currency_id'];
                $queryParams["CustomerinvoiceSearch"]["currency_id"] = $currency;
                 $_SESSION['currency'] = $currency;
            }
        }
        else
        {
            $year = date('Y');
            $currency = 1;
            $_SESSION['year'] = $year;
            $_SESSION['currency'] = $currency;
        }
        $model = new Customerinvoice();
        $modelCustomer = new Customer();
        $arrSalesPerson = $modelCustomer->getUserName();
        $arrTotaldata = $model->getTotalData($year,$currency);
        if($arrTotaldata)
        {
            $totalAmount = 0;
            $totalJan = 0;
            $totalFeb = 0;
            $totalMar = 0;
            $totalApr = 0;
            $totalMay = 0;
            $totalJun = 0;
            $totalJul = 0;
            $totalAug = 0;
            $totalSep = 0;
            $totalOct = 0;
            $totalNov = 0;
            $totalDec = 0;
            $totalMonth = 0;
            foreach ($arrTotaldata as $key => $value) {
                
                $report[$value['fk_user_id']]['sales_person'] = $value['name'];
                
                if($value['month']=='Jan')
                {
                    
                    $report[$value['fk_user_id']]['Jan']['revenue'] = $value['c_revenue'];
                    $totalJan = $totalJan+$report[$value['fk_user_id']]['Jan']['revenue'];

                }
                if($value['month']=='Feb')
                {
                    
                    $report[$value['fk_user_id']]['Feb']['revenue'] = $value['c_revenue'];
                    $totalFeb = $totalFeb+$report[$value['fk_user_id']]['Feb']['revenue'];
                }
                if($value['month']=='Mar')
                {
                   
                    $report[$value['fk_user_id']]['Mar']['revenue'] = $value['c_revenue'];
                    $totalMar = $totalMar+$report[$value['fk_user_id']]['Mar']['revenue'];
                }
                if($value['month']=='Apr')
                {
                    
                    $report[$value['fk_user_id']]['Apr']['revenue'] = $value['c_revenue'];
                    $totalApr = $totalApr+$report[$value['fk_user_id']]['Apr']['revenue'];
                }
                if($value['month']=='May')
                {
                    
                    $report[$value['fk_user_id']]['May']['revenue'] = $value['c_revenue'];
                    $totalMay = $totalMay+$report[$value['fk_user_id']]['May']['revenue'];
                }
                if($value['month']=='Jun')
                {
                    
                    $report[$value['fk_user_id']]['Jun']['revenue'] = $value['c_revenue'];
                    $totalJun = $totalJun+$report[$value['fk_user_id']]['Jun']['revenue'];
                }
               if($value['month']=='Jul')
                {
                    
                    $report[$value['fk_user_id']]['Jul']['revenue'] = $value['c_revenue'];
                    $totalJul = $totalJul+$report[$value['fk_user_id']]['Jul']['revenue'];
                }
                if($value['month']=='Aug')
                {
                    
                    $report[$value['fk_user_id']]['Aug']['revenue'] = $value['c_revenue'];
                    $totalAug = $totalAug+$report[$value['fk_user_id']]['Aug']['revenue'];
                }
                if($value['month']=='Sept')
                {
                   
                    $report[$value['fk_user_id']]['Sept']['revenue'] = $value['c_revenue'];
                    $totalSep = $totalSep+$report[$value['fk_user_id']]['Sept']['revenue'];
                }
                if($value['month']=='Oct')
                {
                   
                    $report[$value['fk_user_id']]['Oct']['revenue'] = $value['c_revenue'];
                    $totalOct = $totalOct+$report[$value['fk_user_id']]['Oct']['revenue'];
                }
                if($value['month']=='Nov')
                {
                   
                    $report[$value['fk_user_id']]['Nov']['revenue'] = $value['c_revenue'];
                    $totalNov = $totalNov+$report[$value['fk_user_id']]['Nov']['revenue'];
                }
                if($value['month']=='Dec')
                {
                    
                    $report[$value['fk_user_id']]['Dec']['revenue'] = $value['c_revenue'];
                    $totalDec = $totalDec+$report[$value['fk_user_id']]['Dec']['revenue'];
                }

                if(!isset($report[$value['fk_user_id']]['Jan']['revenue']))
                {
                    $report[$value['fk_user_id']]['Jan']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Feb']['revenue']))
                {
                    $report[$value['fk_user_id']]['Feb']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Mar']['revenue']))
                {
                    $report[$value['fk_user_id']]['Mar']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Apr']['revenue']))
                {
                    $report[$value['fk_user_id']]['Apr']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['May']['revenue']))
                {
                    $report[$value['fk_user_id']]['May']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Jun']['revenue']))
                {
                    $report[$value['fk_user_id']]['Jun']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Jul']['revenue']))
                {
                    $report[$value['fk_user_id']]['Jul']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Aug']['revenue']))
                {
                    $report[$value['fk_user_id']]['Aug']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Sept']['revenue']))
                {
                    $report[$value['fk_user_id']]['Sept']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Oct']['revenue']))
                {
                    $report[$value['fk_user_id']]['Oct']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Nov']['revenue']))
                {
                    $report[$value['fk_user_id']]['Nov']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Dec']['revenue']))
                {
                    $report[$value['fk_user_id']]['Dec']['revenue'] = 0;
                }

                $totalAmount = $report[$value['fk_user_id']]['Jan']['revenue'] + $report[$value['fk_user_id']]['Feb']['revenue'] + $report[$value['fk_user_id']]['Mar']['revenue'] + $report[$value['fk_user_id']]['Apr']['revenue'] + $report[$value['fk_user_id']]['May']['revenue'] + $report[$value['fk_user_id']]['Jun']['revenue'] + $report[$value['fk_user_id']]['Jul']['revenue'] + $report[$value['fk_user_id']]['Aug']['revenue'] + $report[$value['fk_user_id']]['Sept']['revenue'] + $report[$value['fk_user_id']]['Oct']['revenue'] + $report[$value['fk_user_id']]['Nov']['revenue'] + $report[$value['fk_user_id']]['Dec']['revenue'];
                $report[$value['fk_user_id']]['totalAmount'] = $totalAmount;
                $totalMonth = $totalMonth + $totalAmount;
                $total['totalJan'] = $totalJan;
                $total['totalFeb'] = $totalFeb;
                $total['totalMar'] = $totalMar;
                $total['totalApr'] = $totalApr;
                $total['totalMay'] = $totalMay;
                $total['totalJun'] = $totalJun;
                $total['totalJul'] = $totalJul;
                $total['totalAug'] = $totalAug;
                $total['totalSep'] = $totalSep;
                $total['totalOct'] = $totalOct;
                $total['totalNov'] = $totalNov;
                $total['totalDec'] = $totalDec;
                
            }
            
        }
        
        //$dataProvider = $searchModel->searchSalesRevenue($queryParams);
        return $this->render('sales_revenue',[
            //'searchModel' => $searchModel,
            //'dataProvider' => $dataProvider,
            'salesPersons' => $arrSalesPerson,
            'reportData' => $report,
            'total' =>$total,
            'year' => $year,
            'currency'=>$currency
        ]);
    }

    public function actionSalesrevenueprint()
    {
        if(isset($_SESSION['year']))
        {
            $year = $_SESSION['year'];
        }
        if(isset($_SESSION['currency']))
        {
            $currency = $_SESSION['currency'];
        }
        $arrTotaldata = array();
        $report = array();
        $total = array();
        $intCurrencyId = 1;
        $searchModel = new CustomerinvoiceSearch();
        $queryParams = Yii::$app->request->queryParams;
        $postedArray = Yii::$app->request->post();
        
        $model = new Customerinvoice();
        $modelCustomer = new Customer();
        $arrSalesPerson = $modelCustomer->getUserName();
        $arrTotaldata = $model->getTotalData($year,$currency);

         if($arrTotaldata)
        {
            $totalAmount = 0;
            $totalJan = 0;
            $totalFeb = 0;
            $totalMar = 0;
            $totalApr = 0;
            $totalMay = 0;
            $totalJun = 0;
            $totalJul = 0;
            $totalAug = 0;
            $totalSep = 0;
            $totalOct = 0;
            $totalNov = 0;
            $totalDec = 0;
            $totalMonth = 0;
            foreach ($arrTotaldata as $key => $value) {
                
                $report[$value['fk_user_id']]['sales_person'] = $value['name'];
                
                if($value['month']=='Jan')
                {
                    $report[$value['fk_user_id']]['Jan']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Jan']['revenue'] = $value['c_revenue'];
                    $totalJan = $totalJan+$report[$value['fk_user_id']]['Jan']['revenue'];

                }
                if($value['month']=='Feb')
                {
                    $report[$value['fk_user_id']]['Feb']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Feb']['revenue'] = $value['c_revenue'];
                    $totalFeb = $totalFeb+$report[$value['fk_user_id']]['Feb']['revenue'];
                }
                if($value['month']=='Mar')
                {
                    $report[$value['fk_user_id']]['Mar']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Mar']['revenue'] = $value['c_revenue'];
                    $totalMar = $totalMar+$report[$value['fk_user_id']]['Mar']['revenue'];
                }
                if($value['month']=='Apr')
                {
                    $report[$value['fk_user_id']]['Apr']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Apr']['revenue'] = $value['c_revenue'];
                    $totalApr = $totalApr+$report[$value['fk_user_id']]['Apr']['revenue'];
                }
                if($value['month']=='May')
                {
                    $report[$value['fk_user_id']]['May']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['May']['revenue'] = $value['c_revenue'];
                    $totalMay = $totalMay+$report[$value['fk_user_id']]['May']['revenue'];
                }
                if($value['month']=='Jun')
                {
                    $report[$value['fk_user_id']]['Jun']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Jun']['revenue'] = $value['c_revenue'];
                    $totalJun = $totalJun+$report[$value['fk_user_id']]['Jun']['revenue'];
                }
               if($value['month']=='Jul')
                {
                    $report[$value['fk_user_id']]['Jul']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Jul']['revenue'] = $value['c_revenue'];
                    $totalJul = $totalJul+$report[$value['fk_user_id']]['Jul']['revenue'];
                }
                if($value['month']=='Aug')
                {
                    $report[$value['fk_user_id']]['Aug']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Aug']['revenue'] = $value['c_revenue'];
                    $totalAug = $totalAug+$report[$value['fk_user_id']]['Aug']['revenue'];
                }
                if($value['month']=='Sept')
                {
                    $report[$value['fk_user_id']]['Sept']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Sept']['revenue'] = $value['c_revenue'];
                    $totalSep = $totalSep+$report[$value['fk_user_id']]['Sept']['revenue'];
                }
                if($value['month']=='Oct')
                {
                    $report[$value['fk_user_id']]['Oct']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Oct']['revenue'] = $value['c_revenue'];
                    $totalOct = $totalOct+$report[$value['fk_user_id']]['Oct']['revenue'];
                }
                if($value['month']=='Nov')
                {
                    $report[$value['fk_user_id']]['Nov']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Nov']['revenue'] = $value['c_revenue'];
                    $totalNov = $totalNov+$report[$value['fk_user_id']]['Nov']['revenue'];
                }
                if($value['month']=='Dec')
                {
                    $report[$value['fk_user_id']]['Dec']['count'] = $value['c_count'];
                    $report[$value['fk_user_id']]['Dec']['revenue'] = $value['c_revenue'];
                    $totalDec = $totalDec+$report[$value['fk_user_id']]['Dec']['revenue'];
                }

                if(!isset($report[$value['fk_user_id']]['Jan']['revenue']))
                {
                    $report[$value['fk_user_id']]['Jan']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Feb']['revenue']))
                {
                    $report[$value['fk_user_id']]['Feb']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Mar']['revenue']))
                {
                    $report[$value['fk_user_id']]['Mar']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Apr']['revenue']))
                {
                    $report[$value['fk_user_id']]['Apr']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['May']['revenue']))
                {
                    $report[$value['fk_user_id']]['May']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Jun']['revenue']))
                {
                    $report[$value['fk_user_id']]['Jun']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Jul']['revenue']))
                {
                    $report[$value['fk_user_id']]['Jul']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Aug']['revenue']))
                {
                    $report[$value['fk_user_id']]['Aug']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Sept']['revenue']))
                {
                    $report[$value['fk_user_id']]['Sept']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Oct']['revenue']))
                {
                    $report[$value['fk_user_id']]['Oct']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Nov']['revenue']))
                {
                    $report[$value['fk_user_id']]['Nov']['revenue'] = 0;
                }
                if(!isset($report[$value['fk_user_id']]['Dec']['revenue']))
                {
                    $report[$value['fk_user_id']]['Dec']['revenue'] = 0;
                }

                $totalAmount = $report[$value['fk_user_id']]['Jan']['revenue'] + $report[$value['fk_user_id']]['Feb']['revenue'] + $report[$value['fk_user_id']]['Mar']['revenue'] + $report[$value['fk_user_id']]['Apr']['revenue'] + $report[$value['fk_user_id']]['May']['revenue'] + $report[$value['fk_user_id']]['Jun']['revenue'] + $report[$value['fk_user_id']]['Jul']['revenue'] + $report[$value['fk_user_id']]['Aug']['revenue'] + $report[$value['fk_user_id']]['Sept']['revenue'] + $report[$value['fk_user_id']]['Oct']['revenue'] + $report[$value['fk_user_id']]['Nov']['revenue'] + $report[$value['fk_user_id']]['Dec']['revenue'];
                $report[$value['fk_user_id']]['totalAmount'] = $totalAmount;
                $totalMonth = $totalMonth + $totalAmount;
                $total['totalJan'] = $totalJan;
                $total['totalFeb'] = $totalFeb;
                $total['totalMar'] = $totalMar;
                $total['totalApr'] = $totalApr;
                $total['totalMay'] = $totalMay;
                $total['totalJun'] = $totalJun;
                $total['totalJul'] = $totalJul;
                $total['totalAug'] = $totalAug;
                $total['totalSep'] = $totalSep;
                $total['totalOct'] = $totalOct;
                $total['totalNov'] = $totalNov;
                $total['totalDec'] = $totalDec;
                
            }
            
        }
        $content=  $this->renderPartial('sales_revenue_print',['reportData'=>$report,'total'=>$total],true);
        $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' =>$content,
        'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Summary collection Reports',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
           // 'SetHeader' => ['Generated By: Solnet'],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
     ]);
        return $pdf->render();
        
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
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
