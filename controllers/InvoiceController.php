<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : InvoiceController.php
# Created on : 27th June 2017 by Suraj Malve.
# Update on  : 14th July 2017 by Swati Jadhav.
# Purpose : Manage Invoices.
############################################################################################
*/

namespace app\controllers;

use Yii;
use app\models\Customerinvoice;
use app\models\Customer;
use app\models\ServiceInvoice;
use app\models\CustomerinvoiceSearch;

use app\models\CustomerpaymentSearch;
use app\models\Customerpayment;
use app\models\Currency;
use app\models\CustomerService;
use app\models\Bankdeposit;
use app\models\OutstandingRemarks;
use app\models\OutstandingRemarkssearch;
use app\models\State;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use mPDF;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\models\Package;
use app\models\User;
use yii\web\Response;
use app\models\Generalsettings;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use yii\web\UploadedFile;



/**
 * InvoiceController implements the CRUD actions for Customerinvoice model.
 */
class InvoiceController extends Controller
{
    /**
     * @inheritdoc
     */
   /* public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }*/

	public function behaviors()
    {
		$behaviors['access'] = [
			'class' => AccessControl::className(),
                        'only' => ['create', 'update','index','view','delete','outstanding','setsession','sendmail','pdf','multiplepdf','pay','deletepaymenthistory','remark'],
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
     * Lists all Customerinvoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerinvoiceSearch();
        $modelCustomer = new Customer();
        $arrSalesPerson = array();
		$objCurrency = Currency::find()->orderBy('currency')->asArray()->all();
		//$objSalesPerson = Customer::find()->joinWith('user')->select(['fk_user_id','tblusers.name'])->distinct()->all();
		$arrSalesPerson = $modelCustomer->getUserName();
		
		$queryParams = Yii::$app->request->getQueryParams();
		$postedArray = Yii::$app->request->get();
		$strStartDate	=	'';
		$strEndDate		=	'';
		if(!empty($postedArray))
		{
			if(!empty($postedArray['start_date']) && !empty($postedArray['end_date']))
			{
				$strStartDate	=	 Yii::$app->formatter->asDate($postedArray['start_date'], 'php:Y-m-d');
				$strEndDate		=	Yii::$app->formatter->asDate($postedArray['end_date'], 'php:Y-m-d');
				/*-------added below 2 lines for passing query parameters--------*/
				 $queryParams["CustomerinvoiceSearch"]["start_date"]   = $strStartDate;
				 $queryParams["CustomerinvoiceSearch"]["end_date"] 	= $strEndDate;
				/*-------added below 2 lines for passing query parameters--------*/
			}
		}
		//$queryParams["CustomerinvoiceSearch"]["status"]   = ['partial','unpaid'];
        $dataProvider = $searchModel->search($queryParams);
		$dataProvider->pagination->pageSize=100;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'strStartDate' => $strStartDate,
			'strEndDate'  => $strEndDate,
			'currency'=>$objCurrency,
			'user'=>$arrSalesPerson
        ]);
    }


	/**
     * Lists all Outstanding Customer invoice models.
     *
     */
    public function actionOutstanding()
    {
        $searchModel = new CustomerinvoiceSearch();
		$modelCustomer = new Customer();
		$arrSalesPerson = array();
		$objCurrency = Currency::find()->orderBy('currency')->asArray()->all();
		$queryParams = Yii::$app->request->getQueryParams();
		$postedArray = Yii::$app->request->get();
		$strStartDate	=	'';
		$strEndDate		=	'';
		//$queryParams["CustomerinvoiceSearch"]["status"]   = ['partial','unpaid'];
		//$queryParams["CustomerinvoiceSearch"]["status"]   = '';
		$arrSalesPerson = $modelCustomer->getUserName();
        $dataProvider = $searchModel->search($queryParams);
		//$dataProvider->query->where("tblcustomerinvoice.status = 'partial'");
		//$dataProvider->query->orWhere("tblcustomerinvoice.status = 'unpaid'");
		if(!empty($postedArray))
		{
			if(!empty($postedArray['start_date']) && !empty($postedArray['end_date']))
			{
				$strStartDate	=	$postedArray['start_date'];
				$strEndDate		=	$postedArray['end_date'];
				/*-------added below 2 lines for passing query parameters--------*/
				 //$queryParams["CustomerinvoiceSearch"]["start_date"]   = $strStartDate;
				 //$queryParams["CustomerinvoiceSearch"]["end_date"] 	= $strEndDate;
				/*-------added below 2 lines for passing query parameters--------*/
				$dataProvider->query->andWhere(['between',"DATE_FORMAT(invoice_date,'%Y-%m-%d')",$strStartDate,$strEndDate]);
			}
		}


        return $this->render('outstanding', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'strStartDate' => $strStartDate,
			'strEndDate'  => $strEndDate,
			'currency'=>$objCurrency,
			'user'=>$arrSalesPerson
        ]);
    }

    public function actionRemark($id)
    {
    	$searchModel = new CustomerinvoiceSearch();
    	$model = Customerinvoice::find()->joinWith(['customer','linkcustomepackage','customer.user','customer.state','linkcustomepackage.package'])->where(['customer_invoice_id'=>$id])->one();
    	$modelRemark = new OutstandingRemarks();
    	$searchModelRemark = new OutstandingRemarkssearch();
		$queryParams = Yii::$app->request->getQueryParams();
		//$queryParams["CustomerpaymentSearch"]["tblcustomerpayment.fk_customer_id"]   = $model->fk_customer_id;
		$queryParams["OutstandingRemarkssearch"]["fk_invoice_id"]   = $model->customer_invoice_id;
		$queryParams["OutstandingRemarkssearch"]["fk_customer_id"]   = $model->fk_customer_id;
		$dataProvider = $searchModelRemark->search($queryParams);
		
    	if ($modelRemark->load(Yii::$app->request->post())) 
    	{
    		$arrPostData = Yii::$app->request->post();
    		$modelRemark->fk_customer_id = $model->fk_customer_id;
    		$modelRemark->fk_invoice_id = $model->customer_invoice_id;
    		$modelRemark->fk_user_id = Yii::$app->user->identity->user_id;
    		$modelRemark->created_date = date('Y-m-d H:i:s');
    		if($modelRemark->save())
    		{
    			Yii::$app->getSession()->setFlash('success', 'Remarks updated successfully');
    			return $this->redirect(['invoice/remark','id'=>$id]);
    		}
    		else
    		{
    			Yii::$app->getSession()->setFlash('error', 'Failed to update Remarks');
    			return $this->redirect(['invoice/remark','id'=>$id]);
    		}
    	}
    	return $this->render('remark', [
            'model' => $model,
            'modelRemark' => $modelRemark,
            'dataProvider'=>$dataProvider
        ]);
    }
    public function actionRemarklist()
    {
    	$searchModel = new CustomerinvoiceSearch();
    	$model = new OutstandingRemarks();
    	
    	$queryParams = Yii::$app->request->getQueryParams();
    	$dataProvider = $searchModel->searchRemark($queryParams);
    	return $this->render('remark_list', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider'=>$dataProvider
        ]);
    }
	/**
	* Function to set session value for print pdf header
	*/

	 public function actionSetsession() {
	  $headerFlag = $_POST['header_flag'];
	  if($headerFlag=="true") {
	   Yii::$app->session['print_header'] = '1';
	  } else if($headerFlag=="false") {
	   Yii::$app->session['print_header'] = '0';
	  }
	  exit;
	 }
	 
	 
	/**
	* Function to set session value for signature pdf 
	*/

	 public function actionSetsignature() {
	  $headerFlag = $_POST['header_flag'];
	  
	  if($headerFlag=="true") {
	   Yii::$app->session['signature'] = '1';
	  } else if($headerFlag=="false") {
	   Yii::$app->session['signature'] = '0';
	  }
	   exit;
	}


	 

	/**
     * Generate a pdf.
     * @param integer $id
     *
     
	public function actionPdf($id,$state) {
		$signatureEmailId = "accountbali@solnet.net.id";
		$headerAddress="";
		$headerTelephone = "";
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

		//ini_set('memory_limit', '512M'); 
		$flgSign  = 0;
		$session = Yii::$app->session;
		$intPrintValue = $session->get('print_header');
		$flgSign = $session->get('signature');
	

		$inModel = $this->findModel($id);
		
	
		
		$pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' => $this->renderPartial('view', [
            'model' => $inModel,
			'header'=>$intPrintValue,
			'sign' => $flgSign,
			'signatureEmail'=>$signatureEmailId,
			'headerAddress'=>$headerAddress,
			'headerTelephone'=>$headerTelephone
        ]),


		'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Customer Invoice',
            'subject' => 'Solnet Invoice',

        ],
        'methods' => [
            //'SetHeader' => ['Generated By: Solnet '],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
    ]);

		echo $pdf->render(); // call the mpdf api output as needed

	}*/
	
	
	
	/**
     * Generate a pdf.
     * @param integer $id
     *
     */
	public function actionPdf($id,$state) {
		$signatureEmailId = "accountbali@solnet.net.id";
		$headerAddress="";
		$headerTelephone = "";
		$model = $this->findModel($id);
		$invoicetype = $model->invoice_type;
		$serviceModel = array();
		$arrServiceDetails = array();
		
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
			
			$content =$this->renderPartial('view_service', [
            'model' => $this->findModel($id),
			'header'=>$intPrintValue,
			'sign'=>$flgSign, 
			'signatureEmail'=>$signatureEmailId,
			'headerAddress'=>$headerAddress,
			'headerTelephone'=>$headerTelephone,
			'serviceModel' => $serviceModel,
			'serviceDetail' => $arrServiceDetails
        ]);
      	
			$pdf = new Pdf([
			'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
			'content' => $this->renderPartial('view_service', [
				'model' => $this->findModel($id),
				'header'=>$intPrintValue,
				'sign' => $flgSign,
				'signatureEmail'=>$signatureEmailId,
				'headerAddress'=>$headerAddress,
				'headerTelephone'=>$headerTelephone,
				'serviceModel' => $serviceModel,
				'serviceDetail' => $arrServiceDetails
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
				'model' => $this->findModel($id),
				'header'=>$intPrintValue,
				'sign'=>$flgSign, 
				'signatureEmail'=>$signatureEmailId,
				'headerAddress'=>$headerAddress,
				'headerTelephone'=>$headerTelephone,
				'serviceDetail' => $customService
			]);
      	
			$pdf = new Pdf([
				'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
				'content' => $this->renderPartial('view_custom', [
					'model' => $this->findModel($id),
					'header'=>$intPrintValue,
					'sign' => $flgSign,
					'signatureEmail'=>$signatureEmailId,
					'headerAddress'=>$headerAddress,
					'headerTelephone'=>$headerTelephone,
					'serviceDetail' => $customService
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
				'model' => $this->findModel($id),
				'header'=>$intPrintValue,
				'sign'=>$flgSign, 
				'signatureEmail'=>$signatureEmailId,
				'headerAddress'=>$headerAddress,
				'headerTelephone'=>$headerTelephone
			]);
			
			$pdf = new Pdf([
				'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
				'content' => $this->renderPartial('view', [
					'model' => $this->findModel($id),
					'header'=>$intPrintValue,
					'sign' => $flgSign,
					'signatureEmail'=>$signatureEmailId,
					'headerAddress'=>$headerAddress,
					'headerTelephone'=>$headerTelephone
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

	
public function actionSendmail($id) {

        ini_set('memory_limit', '512M'); 
	  
	    $flgSign  = 0;
		$session = Yii::$app->session;
		$intPrintValue = $session->get('print_header');
		$flgSign = $session->get('signature');
	  $path=Yii::getAlias('@webroot')."/uploads/user_invoice/";
		$invModel = $this->findModel($id);
		$invoicetype = $invModel->invoice_type;
		
		$state = $invModel->customer->fk_state_id;
		$signatureEmailId = 'account@solnet.net.id';
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
		
		if($invoicetype == 'service'){
				$serviceModel =  CustomerService::find()->where(['fk_customer_id'=>$invModel->fk_customer_id])->one();
				if(!empty($serviceModel)){
					$arrServiceDetails = $serviceModel->service;
				}	
					$content =$this->renderPartial('view_service', [
					'model' => $this->findModel($id),
					'header'=>$intPrintValue,
					'sign'=>$flgSign, 
					//'signatureEmail'=>$signatureEmailId,
					//'headerAddress'=>$headerAddress,
					//'headerTelephone'=>$headerTelephone,
					'serviceModel' => $serviceModel,
					'serviceDetail' => $arrServiceDetails
				]);
		
			  $strTimeStamp = date('Ymdhis');

			  $mpdf=new mPDF();
			  $mpdf->WriteHTML($this->renderPartial('view_service', [
						 'model' => $this->findModel($id),
					'header'=>$intPrintValue,
					'sign'=>$flgSign, 
					//'signatureEmail'=>$signatureEmailId,
					//'headerAddress'=>$headerAddress,
					//'headerTelephone'=>$headerTelephone,
					'serviceModel' => $serviceModel,
					'serviceDetail' => $arrServiceDetails
			  ]));
			  $result = $mpdf->Output($path.'customer_invoice_'.$strTimeStamp.'.pdf', 'F');
		
      	
			
		}elseif($invoicetype == 'custom_service'){
			$customService =   ServiceInvoice::find()->where(['fk_customer_id'=>$invModel->fk_customer_id,'fk_invoice_id' => $id])->all();
			
			$content =$this->renderPartial('view_custom', [
				'model' => $this->findModel($id),
				'header'=>$intPrintValue,
				'sign'=>$flgSign, 
				//'signatureEmail'=>$signatureEmailId,
				//'headerAddress'=>$headerAddress,
				//'headerTelephone'=>$headerTelephone,
				'serviceDetail' => $customService
				]);
				 $strTimeStamp = date('Ymdhis');

				  $mpdf=new mPDF();
				  $mpdf->WriteHTML($this->renderPartial('view_custom', [
					'model' => $this->findModel($id),
					'header'=>$intPrintValue,
					'sign'=>$flgSign, 
					//'signatureEmail'=>$signatureEmailId,
					//'headerAddress'=>$headerAddress,
					//'headerTelephone'=>$headerTelephone,
					'serviceDetail' => $customService
				  ]));
				  $result = $mpdf->Output($path.'customer_invoice_'.$strTimeStamp.'.pdf', 'F');
				
			
		}else{
				 $content =$this->renderPartial('view', [
					'model' => $this->findModel($id),
					'header'=>$intPrintValue,
					'sign' => $flgSign
				]);
				$strTimeStamp = date('Ymdhis');

				  $mpdf=new mPDF();
				  $mpdf->WriteHTML($this->renderPartial('view', [
							'model' => $this->findModel($id),
							'header'=>$intPrintValue,
							'sign' => $flgSign
				  ]));
				  $result = $mpdf->Output($path.'customer_invoice_'.$strTimeStamp.'.pdf', 'F');
					
		}
			
		if(file_exists($path.'customer_invoice_'.$strTimeStamp.'.pdf'))
		{

				$model = $this->findModel($id);
				$intId=Yii::$app->user->identity->user_id;
			    if($model->customer->user_type=='home')
				{
					$to = $model->customer->email_address;
				}elseif($model->customer->user_type=='corporate'){
					$to = $model->customer->email_finance;
				}else{
					$to = 'eluminous_15@eluminoustechnologies.com';
				}

				$subject = 'Solnet Internet Billing ';

				$imageUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/web/images/solnet.png';
				$params = '';
				try{
				
				$resultMail = Yii::$app->mailer->compose('invoice_user',['params' => $params,'imageFileName' => $imageUrl,'model'=>$model])
						->setFrom($signatureEmailId)
						->setTo($to)
						->attach($path.'customer_invoice_'.$strTimeStamp.'.pdf')
						->setSubject($subject)
						->send();

						if($resultMail){

							$emailLog = array();
                            $emailLog['email_to'] = $to;
                            $emailLog['subject'] = 'Solnet :'.$subject;
                            $emailLog['is_customer'] = 'Yes';
                            $emailLog['sent_to_id'] = $model->fk_customer_id;
                            $emailLog['sent_by'] = 'User';
                            $emailLog['sent_by_user_id'] = $intId;
                            $emailLog['sent_date'] = date('Y-m-d H:i:s');
                            Yii::$app->customcomponents->emailLogActivity($emailLog);

							$session = Yii::$app->session;
							$session->setFlash('success',INVOICE_SENT_SUCCESSFULL);
							return $this->redirect(['invoice/index']);
						}
				 } catch (Swift_TransportException $e) {
					 return Yii::$app->getResponse()->redirect(['site/login']);
				 }
					exit;
		}



	 }


	/*public function actionSendmail($id) {

        ini_set('memory_limit', '512M'); 
		$fromMail = 'account@solnet.net.id';
	    $flgSign  = 0;
		$session = Yii::$app->session;
		$intPrintValue = $session->get('print_header');
		$flgSign = $session->get('signature');
	  $path=Yii::getAlias('@webroot')."/uploads/user_invoice/";
	  $content =$this->renderPartial('view', [
		  'model' => $this->findModel($id),
		  'header'=>$intPrintValue,
		  'sign' => $flgSign
	  ]);
	  $strTimeStamp = date('Ymdhis');

	  $mpdf=new mPDF();
	  $mpdf->WriteHTML($this->renderPartial('view', [
				'model' => $this->findModel($id),
	   			'header'=>$intPrintValue,
	   			'sign' => $flgSign
	  ]));
	  $result = $mpdf->Output($path.'customer_invoice_'.$strTimeStamp.'.pdf', 'F');


		if(file_exists($path.'customer_invoice_'.$strTimeStamp.'.pdf'))
		{
				
				$model = $this->findModel($id);
				$intId=Yii::$app->user->identity->user_id;
			    if($model->customer->user_type=='home')
				{
					$to = $model->customer->email_address;
				}elseif($model->customer->user_type=='corporate'){
					$to = $model->customer->email_finance;
				}else{
					$to = 'eluminous_15@eluminoustechnologies.com';
				}
				//$to =  'eluminous_se32@eluminoustechnologies.com';
				$subject = 'Invoice';

				$imageUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/web/images/solnet.png';
				$params = '';
				try{
				$fromMail = $model->customer->state->signature_email_id;
				$resultMail = Yii::$app->mailer->compose('invoice_user',['params' => $params,'imageFileName' => $imageUrl,'model'=>$model])
						->setFrom($fromMail)
						->setTo($to)
						->attach($path.'customer_invoice_'.$strTimeStamp.'.pdf')
						->setSubject('Solnet :'.$subject)
						->send();

						if($resultMail){

							$emailLog = array();
                            $emailLog['email_to'] = $to;
                            $emailLog['subject'] = 'Solnet :'.$subject;
                            $emailLog['is_customer'] = 'Yes';
                            $emailLog['sent_to_id'] = $model->fk_customer_id;
                            $emailLog['sent_by'] = 'User';
                            $emailLog['sent_by_user_id'] = $intId;
                            $emailLog['sent_date'] = date('Y-m-d H:i:s');
                            Yii::$app->customcomponents->emailLogActivity($emailLog);

							$session = Yii::$app->session;
							$session->setFlash('success',INVOICE_SENT_SUCCESSFULL);
							return $this->redirect(['invoice/index']);
						}
				 } catch (Swift_TransportException $e) {
					 return Yii::$app->getResponse()->redirect(['site/login']);
				 }
					exit;
		}



	 }*/

	/**
     * Generate a multiple pdf.
     * @param integer $id
     *
     */
	public function actionMultipledf() {

		/*$headerAddress="";
		$headerTelephone = "";
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
		}*/

	    ini_set('memory_limit', '512M'); 
		$session = Yii::$app->session;
		$intPrintValue = $session->get('print_header');
		$sign = $session->get('signature');
		$ids = yii::$app->request->post('ids');
		$content = '';
		
		$ids =explode(',',$ids);
		$invoiceIds = implode("','",$ids);

		 $connection = \Yii::$app->db;
        
        $command = $connection->createCommand('SELECT signature_email_id,header_address,header_telephones,tblcustomerinvoice.*
        	from tblcustomerinvoice 
        	left join tblcustomer on tblcustomer.customer_id = tblcustomerinvoice.fk_customer_id
        	left join tblstate on tblstate.state_id = tblcustomer.fk_state_id
        	where customer_invoice_id IN (\''.$invoiceIds.'\')
        	')->queryAll();
		/*echo "<pre>";
		print_r($command);die;*/
		if(!empty($ids))
		{
			$model = new Customerinvoice();
			foreach($ids as $val)
			{
				$content .= $this->renderPartial('view', [
            	'model' => $model->getStateData($val),
				//'model'=>$this->findModel($val),
				'header'=>$intPrintValue,
				'sign'=>$sign,
				//'signatureEmail'=>$signatureEmailId,
				/*'headerAddress'=>$headerAddress,
				'headerTelephone'=>$headerTelephone*/
        		]);

			}
			$pdf = new Pdf([
							'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
							
							'content' => $content ,
							'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
							'cssInline' => '@media print{
								.page-break{display: block;page-break-after: always;}
							}',
							'options' => [
								'title' => 'Customer Details',
								'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
							],
							'methods' => [
								//'SetHeader' => ['Generated By: Solnet'],
								'SetFooter' => ['|Page {PAGENO}|'],
							]
    		]);
			
			return $pdf->render();

		}
	}

    /**
     * Displays a single Customerinvoice model.
     * @param integer $id
     * @return mixed
     */
   /* public function actionView($id)
    {

        return $this->render('view', [
            'model' => $this->findModel($id),

        ]);
    }*/

    /**
     * Creates a new Customerinvoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customerinvoice();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->customer_invoice_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Customerinvoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$intTotal = 0;
		$floatVat = 0;
        $model = $this->findModel($id);
		if(!empty($model->vat)){
			$floatVat = round($model->vat);
		}
		$model->scenario = 'invoice-update';
		$arrPostData = Yii::$app->request->post();
		if(!empty($arrPostData))
		{

			$floatInstallationFee   = $arrPostData['Customerinvoice']['installation_fee'];
			$floatLastDueAmount     = $arrPostData['Customerinvoice']['last_due_amount'];
			$floatOtherServiceFee   = $arrPostData['Customerinvoice']['other_service_fee'];
			$strCommentForOtherServiceFee = $arrPostData['Customerinvoice']['comment_for_other_service_fee'];
			$floatCurrentInvoiceamt = $arrPostData['Customerinvoice']['current_invoice_amount'];
			$floatVat				= $arrPostData['Customerinvoice']['vat'];
			$intTotal 				= $floatInstallationFee + $floatOtherServiceFee + $floatCurrentInvoiceamt + $floatLastDueAmount;
			$floatTotalAmount = $floatVat + $intTotal;
			$model->vat = $floatVat;
			$model->current_invoice_amount = $floatCurrentInvoiceamt;
			$model->total_invoice_amount = $floatTotalAmount;
			$model->pending_amount = $floatTotalAmount;
			$model->other_service_fee = $floatOtherServiceFee;
			$model->comment_for_other_service_fee = $strCommentForOtherServiceFee;
		}
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			/************Log Activity*********/
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Update Invoice';
			$logArray['action'] = 'update';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated a invoice of "'.$model->customer->name.'" customer.';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			/************Log Activity*********/
            //return $this->redirect(['view', 'id' => $model->customer_invoice_id]);
			return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


	 /**
     * Cancel an existing Customer's invoice model.
     * If cancellation is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     *
     */
  //   public function actionCancel($id,$status)
  //   {
  //       $model = $this->findModel($id);
		// if($status=='unpaid'){
		// 	$model->status='cancelled';
		// }elseif($status=='cancelled'){
		// 	$model->status='unpaid';
		// }

  //       if($model->save())
		// {
		// 	$session = Yii::$app->session;
		// 	$session->setFlash('success',INVOICE_STATUS_SUCCESSFULL);
		// 	/************Log Activity*********/
		// 	$logArray = array();
		// 	$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
		// 	$logArray['module'] = 'Cancel Invoice';
		// 	$logArray['action'] = 'update';
		// 	$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has cancelled a invoice of "'.$model->customer->name.'" customer.';
		// 	$logArray['created'] = date('Y-m-d H:i:s');
		// 	Yii::$app->customcomponents->logActivity($logArray);
		// 	/************Log Activity*********/
		// 	return $this->redirect(['invoice/index']);
		// }else{
		// 	echo '<pre>';
		// 	print_r($model->getErrors());
		// 	die;
		// }
  //   }

	public function actionCancel($id,$status)
    {
    	if(Yii::$app->request->post())
    	{
	        //$model = $this->findModel($id);
	        $model = Customerinvoice::find()->where(['customer_invoice_id'=>$id])->one();
			
			if($status=='unpaid'){
				$model->status='cancelled';
			}elseif($status=='cancelled'){
				$model->status='unpaid';
			}
			$model->scenario = 'updatecomment';
    		if ($model->load(Yii::$app->request->post()) ) {
		        if($model->save())
				{
					$session = Yii::$app->session;
					$session->setFlash('success',INVOICE_STATUS_SUCCESSFULL);
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Cancel Invoice';
					$logArray['action'] = 'update';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has cancelled a invoice of "'.$model->customer->name.'" customer.';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					//return $this->redirect(['invoice/index']);
					return $this->redirect(Yii::$app->request->referrer);
				}
			}
		}
		$model = Customerinvoice::find()->where(['customer_invoice_id'=>$id])->one();
		//echo '<pre>';print_r($model);echo '<pre>';die;
        return  $this->renderAjax('updatecommentwithstatus',
       	[
       		'model'	=> $model,
	       	'id'	=> $id
	    ]);
    }


    public function actionReminderlist()
    {
    	/********To fetch all currency from Currency model*******/
    	$objCurrency = Currency::find()->orderBy('currency')->asArray()->all();

    	$searchModel = new CustomerinvoiceSearch();
		$queryParams = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->searchReminder($queryParams);

        return $this->render('reminder_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currency'=>$objCurrency
        ]);

    }

	/**
     * reminder moodal to send reminder to single customer.
     * @param integer $id
     * @return mixed
     */
    public function actionRemindermodal($id)
    {


    	 $date = strtotime("+7 day");
       	 $afterDays=date('Y-m-d', $date);
         $todaysDate=date('Y-m-d');

        /*************To fetch data with above condition from Customerinvoice **********/
    	 $model = Customerinvoice::find()->from(Customerinvoice::tableName().' t')
        ->joinWith(['customer as c'])
         ->andWhere(['t.fk_customer_id'=>$id])
         ->one();


	        return $this->renderAjax('reminder_modal', [
	            'model' => $model,'id'=>$id
	        ]);
    }

    /**
     * send reminder to single customers multiple invoices.
     * @param integer $id
     * @return mixed
     */
    public function actionSendreminder($id)
    {
ini_set('memory_limit', '512M'); 
    	$invoiceModel= new Customerinvoice();

       if(Yii::$app->request->isAjax && $invoiceModel->load($_POST)) {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($invoiceModel);
        }

    	 $strOtherEmail=  yii::$app->request->post('Customerinvoice')['other_email'];
    	 if(!empty($strOtherEmail)){
    	 	$arrAllEmail = explode(",", $strOtherEmail);
    	 }

    	 if(!empty(yii::$app->request->post('Customerinvoice')['email_address'])){
    	 	$arrAllEmail[]=yii::$app->request->post('Customerinvoice')['email_address'];
    	 }

    	 if(!empty(yii::$app->request->post('Customerinvoice')['email_it'])){
    	 	$arrAllEmail[]=yii::$app->request->post('Customerinvoice')['email_it'];
    	 }

    	  if(!empty(yii::$app->request->post('Customerinvoice')['email_finance'])){
    	 	$arrAllEmail[]=yii::$app->request->post('Customerinvoice')['email_finance'];
    	 }

       	 $date = strtotime("+7 day");
       	 $afterDays=date('Y-m-d', $date);
         $todaysDate=date('Y-m-d');

         /*************To fetch data with above condition from Customerinvoice **********/
        $model = Customerinvoice::find()->from(Customerinvoice::tableName().' t')
        ->joinWith(['customer as c','linkcustomepackage as l'])
        ->andWhere(['t.fk_customer_id'=>$id])
        ->andWhere(['between','due_date',$todaysDate.'%', $afterDays.'%'])
        ->andWhere(['c.status'=>'active'])
        ->andWhere(['is_disconnected'=>'no'])
        ->andWhere(['t.status'=>'unpaid'])
        ->orWhere(['t.status'=>'partial'])
        ->andWhere(['t.fk_customer_id'=>$id])
        ->orderBy(['due_date'=>SORT_DESC])
        ->all();


         /*************To fetch SUM of pending_amount from Customerinvoice **********/
        $sumModel = Customerinvoice::find()->from(Customerinvoice::tableName().' t')
        ->joinWith(['customer as c','linkcustomepackage.currency'])
        ->select('sum(pending_amount) as total,currency')
        ->andWhere(['t.fk_customer_id'=>$id])
        ->andWhere(['between','due_date',$todaysDate.'%', $afterDays.'%'])
        ->andWhere(['c.status'=>'active'])
        ->andWhere(['is_disconnected'=>'no'])
        ->andWhere(['t.status'=>'unpaid'])
        ->orWhere(['t.status'=>'partial'])
        ->andWhere(['t.fk_customer_id'=>$id])
        ->orderBy(['due_date'=>SORT_DESC])
        ->one();


         /*************To fetch customer details from Customer model **********/
         $customerModel= Customer::findOne($id);

    	if(!empty($model)){

		$subject = ' Reminder';
        $imageUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/web/images/solnet.png';

        /**** GET ADMIN EMAIL FORM GENERALSETTINGS MODEL****/
        $sentFrom= Generalsettings::find()->where(['name'=>'ADMIN_EMAIL'])->one();
    			 if(!empty($sentFrom->value)){
    			 	$strFromEmail=$sentFrom->value;
    			 }
    			 	else{
    			 	$strFromEmail='account@solnet.net.id';
    			 	}


		$params = '';

         /*********send mail to valid receipients*********/
		try{
			$resultMail = Yii::$app->mailer->compose('reminder_template',['params' => $params,'imageUrl' => $imageUrl,'model'=>$model,'sumModel'=>$sumModel,'strCustomerName'=>$customerModel->name])
				->setFrom($strFromEmail)
				->setTo($arrAllEmail)
				->setSubject('Solnet :'.$subject)
				->send();
				if($resultMail){
					$isSent='';
					foreach($model as $key=>$model){
					$isSent= Yii::$app->db->createCommand()
                        ->update('tblcustomerinvoice',
                            ['is_remider_mail_sent'=>'yes','reminder_email'=>$strOtherEmail],
                            ['customer_invoice_id'=>$model->customer_invoice_id])
                        ->execute();
                    }

                 $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Reminder';
                $logArray['action'] = 'update';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has sent reminder to "'.$customerModel->name.'" ';
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);



                      Yii::$app->getSession()->setFlash('succ_sent_reminder', 'Reminder sent to customer');

					  return $this->redirect(['invoice/reminderlist']);


				}
			 } catch (Swift_TransportException $e) {
				 return Yii::$app->getResponse()->redirect(['site/reminderlist']);
			 }
				exit;
		}
		else{
			Yii::$app->getSession()->setFlash('error_sent_reminder', 'Could not send reminder. Please Try again.');
			return $this->redirect(['invoice/reminderlist']);
		}

	}



	public function actionReminderprint($id){
	    
	    ini_set('memory_limit', '512M'); 

		 $date = strtotime("+7 day");
       	 $afterDays=date('Y-m-d', $date);
         $todaysDate=date('Y-m-d');

         /*************To fetch data with below conditions from tblcustomerinvoice table************/
		 $model = Customerinvoice::find()->from(Customerinvoice::tableName().' t')
        ->joinWith(['customer as c','linkcustomepackage as l'])
        ->andWhere(['c.status'=>'active'])
        ->andWhere(['t.customer_invoice_id'=>$id])
        ->one();

      	$pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' => $this->renderPartial('reminder_list_view', [
            'model' => $model,
			]),


		'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Customer Invoice',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy',

        ],
        'methods' => [
           // 'SetHeader' => ['Generated By: Solnet '],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
    ]);

		$logArray = array();
        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
        $logArray['module'] = 'Reminder';
        $logArray['action'] = 'update';
        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has printed the reminder invoice of  "'.$model->customer->name.'" ';
        $logArray['created'] = date('Y-m-d H:i:s');
        Yii::$app->customcomponents->logActivity($logArray);

        return $pdf->render();// call the mpdf api output as needed




	}

    /**
     * Deletes an existing Customerinvoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	/**
     * function to generate custome invoice
     *
     */
    public function actionGenerate($id='')
    {
        ini_set('memory_limit', '512M'); 
        $invoiceModel = new Customerinvoice();
		$invoiceModel->scenario = 'custom';
		$floatVat = 0;
 		$model = Customer::find()->where(['customer_id'=>$id])->one();
		if(empty($id)){
			$model = new Customer();
			$invoiceModel->send_invoice = 'no';
		}else{
			$intStateId = $model->fk_state_id;
			$getVat =  Yii::$app->customcomponents->GetVat($intStateId);
			$intFkCustPckId = $model->linkcustomerpackage->cust_pck_id;
		}

		/*************To fetch Solnet ID from table************/
		$arrSolnetId 	= Customer::find()->where(['status'=>'active','is_deleted'=>'0','is_invoice_activated'=>'yes'])->all();
		$SolnetIdListData	= ArrayHelper::map($arrSolnetId,'customer_id','solnet_customer_id');
		/*************To fetch Solnet ID from table************/

		$invoiceModel->fk_customer_id = $id;
		$invoiceModel->invoice_type = 'custom';
		$invoiceModel->invoice_date = date('Y-m-d h:i:s');
		$invoiceModel->po_wo_number = $model->po_wo_number;
		$arrPostData = Yii::$app->request->post();
		if(!empty($arrPostData) && $invoiceModel->load(Yii::$app->request->post())){


			$floatInstallationFee = $arrPostData['Customerinvoice']['installation_fee'];
			$floatOtherFee = $arrPostData['Customerinvoice']['other_service_fee'];
			$floatCurrentInvoiceAmt = $arrPostData['Customerinvoice']['current_invoice_amount'];
			$boolSendInvoice = $arrPostData['Customerinvoice']['send_invoice'];
			if(!empty($floatInstallationFee)){
				$floatCurrentInvoiceAmt += $floatInstallationFee ;
			}
			if(!empty($floatOtherFee)){
				$floatCurrentInvoiceAmt += $floatOtherFee ;
			}
			if(!empty($getVat)){
				$floatVat1 = ($floatCurrentInvoiceAmt*$getVat)/100;
				$floatVat = round($floatVat1);
			}
			$invoiceModel->total_invoice_amount = $floatVat + $floatCurrentInvoiceAmt;
			$invoiceModel->pending_amount = 	$floatVat + $floatCurrentInvoiceAmt;
			$invoiceModel->fk_cust_pckg_id = 	$intFkCustPckId;
			$invoiceModel->usage_period_to = 	Yii::$app->formatter->asDate($arrPostData['Customerinvoice']['usage_period_to'], 'php:Y-m-d H:i:s');
			$invoiceModel->usage_period_from =  Yii::$app->formatter->asDate($arrPostData['Customerinvoice']['usage_period_from'], 'php:Y-m-d H:i:s');
			$invoiceModel->due_date =  			Yii::$app->formatter->asDate($arrPostData['Customerinvoice']['due_date'], 'php:Y-m-d H:i:s');
			$arrInvoice = Yii::$app->customcomponents->GetInvoiceId($model->fk_state_id);

			if(!empty($arrInvoice))
			{
				$invoiceModel->invoice_number = $arrInvoice['current_invoice_id'];
			}
		}
		if(!empty($floatVat))
		{
			$invoiceModel->vat = $floatVat;
		}

		$invoiceModel->status = 'unpaid';
		$invoiceModel->created_at = date('Y-m-d h:i:s');
		if( $invoiceModel->save())
		{
			$intLastInsertedId = $invoiceModel->customer_invoice_id;
			$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
			if($boolSendInvoice=='yes'){
				$this->actionSendmail($intLastInsertedId);
			}
			$session = Yii::$app->session;
			$session->setFlash('success',INVOICE_CUSTOM_SUCCESSFULL);
			/************Log Activity*********/
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Generate Custom Invoice';
			$logArray['action'] = 'create';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has generated custom invoice for "'.$model->name.'".';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			/************Log Activity*********/
			return $this->redirect(['invoice/generate','id'=>$id]);
		}
		else{
			return $this->render('custom', [
					'model' => $model,
					'invoiceModel'=>$invoiceModel,
					'data'=>$SolnetIdListData
				]);
		}
    }

	public function actionPay($id)
	{
		$model = $this->findModel($id);
		$bank = new Bankdeposit();
		$arrStatus = array('unpaid','partial');
        $arrSolnetId    = Customerinvoice::find()->joinWith('customer')->select(['fk_customer_id','solnet_customer_id'])->where(['is_deleted'=>'0','is_invoice_activated'=>'yes'])->andWhere(['IN',['tblcustomerinvoice.status'],$arrStatus])->asArray()->all();
        
        $SolnetIdListData   = ArrayHelper::map($arrSolnetId,'fk_customer_id','solnet_customer_id');


		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
       return ActiveForm::validate($model);
      }
		$searchModel = new CustomerpaymentSearch();
		$queryParams = Yii::$app->request->getQueryParams();
		//$queryParams["CustomerpaymentSearch"]["tblcustomerpayment.fk_customer_id"]   = $model->fk_customer_id;
		$queryParams["CustomerpaymentSearch"]["fk_invoice_id"]   = $model->customer_invoice_id;
		$dataProvider = $searchModel->search($queryParams);
		$payModel = new Customerpayment();
		$intPendingAmt =  $model->pending_amount;
		$intCustId =  $model->fk_customer_id;
		$intCurrencyId = $model->linkcustomepackage->fk_currency_id;
		$intinvoiceId =  $model->customer_invoice_id;
		$strInvoiceNumber = $model->invoice_number;
		$model->scenario = 'pay';
		$arrPostData = Yii::$app->request->post();
		/*if(!empty($arrPostData)) {
			echo "<pre>";
			print_r($arrPostData);die;
		}*/
		/*************To fetch currency from table************/
		$arrCurrency 	= Currency::find()->where(['status'=>'active'])->all();
		$currencyListData	= ArrayHelper::map($arrCurrency,'currency_id','currency');
		/*************To fetch state from table************/

		//fetch cutomer payment data
		$arrPaymentResultData = $payModel::find()->joinWith('invoice')->where(['fk_invoice_id'=>$model->customer_invoice_id])->all();

		if($model->load(Yii::$app->request->post()))
		{

			if(!empty($arrPostData)) {
				if(isset($arrPostData['Customerinvoice']['check']) && $arrPostData['Customerinvoice']['check']==1)
				{
					$model->check = $arrPostData['Customerinvoice']['check'];
					if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

				       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
				       return ActiveForm::validate($bank);
				      }
					if(isset($arrPostData['Customerinvoice']['fk_customer_id']) && $arrPostData['Customerinvoice']['fk_customer_id']!="")
					{
						$bank->fk_customer_id = $arrPostData['Customerinvoice']['fk_customer_id'];
					}
					if(isset($arrPostData['Customerinvoice']['fk_invoice_id']) && $arrPostData['Customerinvoice']['fk_invoice_id']!="")
					{
						$bank->fk_invoice_id = $arrPostData['Customerinvoice']['fk_invoice_id'];
					}
					if(isset($arrPostData['Customerinvoice']['amount']) && $arrPostData['Customerinvoice']['amount']!="")
					{
						$bank->amount = $arrPostData['Customerinvoice']['amount'];
					}
					if(isset($arrPostData['Customerinvoice']['amount']) && $arrPostData['Customerinvoice']['amount']!="")
					{
						$bank->amount = $arrPostData['Customerinvoice']['amount'];
					}
					if(isset($arrPostData['Customerinvoice']['fk_currency_id']) && $arrPostData['Customerinvoice']['fk_currency_id']!="")
					{
						$bank->fk_currency_id = $arrPostData['Customerinvoice']['fk_currency_id'];
					}
					if(isset($arrPostData['Customerinvoice']['deposit_date']) && $arrPostData['Customerinvoice']['deposit_date']!="")
					{
						$bank->deposit_date = Yii::$app->formatter->asDate($_POST['Customerinvoice']['deposit_date'], 'php:Y-m-d H:i:s');
					}
					if(isset($arrPostData['Customerinvoice']['deposit_type']) && $arrPostData['Customerinvoice']['deposit_type']!="")
					{
						$bank->deposit_type = $arrPostData['Customerinvoice']['deposit_type'];
					}
					if(isset($arrPostData['Customerinvoice']['deposit_type']) && $arrPostData['Customerinvoice']['deposit_type']!="")
					{
						$bank->deposit_type = $arrPostData['Customerinvoice']['deposit_type'];
					}
					if(isset($arrPostData['Customerinvoice']['is_solnet_bank']) && $arrPostData['Customerinvoice']['is_solnet_bank']!="")
					{
						$bank->is_solnet_bank = $arrPostData['Customerinvoice']['is_solnet_bank'];
					}
					if(isset($arrPostData['Customerinvoice']['is_solnet_bank']) && $arrPostData['Customerinvoice']['is_solnet_bank']!="")
					{
						$bank->is_solnet_bank = $arrPostData['Customerinvoice']['is_solnet_bank'];
						if( $_POST['Customerinvoice']['is_solnet_bank']=='0'){
               
			                $bank->account_no=$_POST['Customerinvoice']['account_no']; 
			            }
			            else{

			                 $bank->fk_bank_id=$_POST['Customerinvoice']['fk_bank_id'];
			                 $bank->bank=$_POST['Customerinvoice']['bank_name']; 
			            }
					}
					if(isset($arrPostData['Customerinvoice']['description']) && $arrPostData['Customerinvoice']['description']!="")
					{
						$bank->description = $arrPostData['Customerinvoice']['description'];
					}
					$bank->fk_user_id=Yii::$app->user->identity->user_id;
            		$bank->created_at=date('Y-m-d h:i:s');
            		
				}
						
						$paymentModel = new Customerpayment();
						$intTotalPaid = $arrPostData['Customerinvoice']['discount'] + $arrPostData['Customerinvoice']['deduct_tax']+$arrPostData['Customerinvoice']['bank_amount']+ $arrPostData['Customerinvoice']['payment_amount'];
						$paymentModel->fk_customer_id = $intCustId;
						$paymentModel->fk_invoice_id = $intinvoiceId;
						$paymentModel->discount = $arrPostData['Customerinvoice']['discount'];
						$paymentModel->deduct_tax = $arrPostData['Customerinvoice']['deduct_tax'];
						$paymentModel->bank_admin = $arrPostData['Customerinvoice']['bank_amount'];
						$paymentModel->payment_method = $arrPostData['Customerpayment']['payment_method'];
						$paymentModel->cheque_no =  $arrPostData['Customerpayment']['cheque_no'];
						$paymentModel->amount_paid = $intTotalPaid;
						$paymentModel->reciept_no = $arrPostData['Customerpayment']['reciept_no'];
						$paymentModel->fk_currency_id = $intCurrencyId;
						$paymentModel->comment = $arrPostData['Customerpayment']['comment'];
						$paymentModel->created_at = date('Y-m-d h:i:s');
						$paymentModel->payment_date =date("Y-m-d",  strtotime($arrPostData['Customerpayment']['payment_date']));
						//save payment details
						if($paymentModel->save())
						{
							$intLastInsertID = $paymentModel->db->getLastInsertID();

							//Get invoice number
							$customerinvoiceModel = New Customerinvoice();
							$arrCustomerResult 	  = $customerinvoiceModel->findOne($intinvoiceId);
							$intInvoiceNo 		  = str_replace('/', '_', $arrCustomerResult->invoice_number);

							//reciept upload
							$filesArrayReceipt = UploadedFile::getInstance($payModel, 'receipt');
							
							if(!empty($filesArrayReceipt)){
								$filesArrayReceipt->saveAs('uploads/invoice/' . 'receipt_'.$intInvoiceNo . '_' . $intLastInsertID. '.' . $filesArrayReceipt->extension);
								$strRecieptName ='receipt_'.$intInvoiceNo . '_' . $intLastInsertID. '.' . $filesArrayReceipt->extension;
							}else{
								$strRecieptName = "";
							}

							//cheque upload
							$filesArrayCheque = UploadedFile::getInstance($payModel, 'cheque');
							if(!empty($filesArrayCheque)){
								$filesArrayCheque->saveAs('uploads/invoice/' . 'cheque_'.$intInvoiceNo. '_' . $intLastInsertID. '.' . $filesArrayCheque->extension);
								$strChequeName ='cheque_'.$intInvoiceNo . '_' . $intLastInsertID. '.' . $filesArrayCheque->extension;
							}else{
								$strChequeName = '';
							}

							$paymentModel 				= new Customerpayment();
							$arrPaymentModel 			= $paymentModel->findOne($intLastInsertID);
							$arrPaymentModel->receipt 	= $strRecieptName;
							$arrPaymentModel->cheque 	= $strChequeName;
							$arrPaymentModel->save();

							//save invoice details
							if($model->save())
							{
								$connection = Yii::$app->getDb();
								if(isset($arrPostData['Customerpayment']['po_wo_number']) && $arrPostData['Customerpayment']['po_wo_number']!="")
								{
									$command = $connection->createCommand(
									'UPDATE tblcustomerinvoice SET po_wo_number = '.$arrPostData['Customerpayment']['po_wo_number'].' WHERE customer_invoice_id ='.$id);
									$command->execute();
									
									$command = $connection->createCommand(
										'UPDATE tblcustomer SET po_wo_number = '.$arrPostData['Customerpayment']['po_wo_number'].' WHERE customer_id ='.$intCustId);
									$command->execute();
								}
							}

						if($model->check==1)
						{
						//save bank details
						if($bank->save())
						{
							$logArray = array();
	                        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
	                        $logArray['module'] = 'Pay invoice';
	                        $logArray['action'] = 'create';
	                        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deposited the amount ';
	                        $logArray['created'] = date('Y-m-d H:i:s');
	                        Yii::$app->customcomponents->logActivity($logArray);
						}
						else {
							$arrErrors = array();
							$message="";
							if($bank->getErrors())
							{
								foreach ($bank->getErrors() as $key => $value) 
								{
									$arrErrors[] = $value[0];
								}
								$message = implode("<br>",$arrErrors);
								$session = Yii::$app->session;
								$session->setFlash('error','Error: Payment done successfully but unable to deposite money. <br>'.$message.'<br>'.'Please fill the proper details for bank deposite form.');
								return $this->redirect(['index']);
								
									return $this->render('index', [
									'model' 			=> $model,
									'pay'				=> $payModel,
									'bank'				=> $bank,
									'dataProvider'		=> $dataProvider,
									'searchModel'		=> $searchModel,
									'currencyList'		=> $currencyListData,
									'data'				=> $SolnetIdListData,
									'arrPaymentResultData' 	=> $arrPaymentResultData
									]);
							}
						}
						}
						
							
							$session = Yii::$app->session;
							$session->setFlash('success_paid',INVOICE_PAID_SUCCESSFULL);
							/************Log Activity*********/
							$logArray = array();
							$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
							$logArray['module'] = 'Pay Invoice';
							$logArray['action'] = 'update';
							$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has paid the invoice of "'.$model->customer->name.'".';
							$logArray['created'] = date('Y-m-d H:i:s');
							Yii::$app->customcomponents->logActivity($logArray);
							/************Log Activity*********/
							return $this->redirect(['invoice/pay','id'=>$id]);
						}else{
							
							if($paymentModel->getErrors())
							{
								foreach ($paymentModel->getErrors() as $key => $value) 
								{
									$arrErrors[] = $value[0];
								}
									$message = implode("<br>",$arrErrors);
									$session = Yii::$app->session;
									$session->setFlash('error','Error <br>'.$message);
									if(isset($arrPostData['Customerinvoice']['check']) && $arrPostData['Customerinvoice']['check']==1)
									
									{
										return $this->render('pay', [
										'model' 		=> $model,
										'pay'			=> $paymentModel,
										'bank'			=> $bank,
										'dataProvider'	=> $dataProvider,
										'searchModel'	=> $searchModel,
										'currencyList'	=> $currencyListData,
										'data'			=> $SolnetIdListData,
										'arrPaymentResultData' 	=> $arrPaymentResultData
										]);
									}
									else
									{
										return $this->render('pay', [
										'model' => $model,
										'pay'=>$paymentModel,
										
										'dataProvider'=>$dataProvider,
										'searchModel'=>$searchModel,
										'currencyList'=>$currencyListData,
										'data'=>$SolnetIdListData,
										'arrPaymentResultData' 	=> $arrPaymentResultData
										]);
									}
							}
							
						}
					//}

				}


		}else{
			
			return $this->render('pay', [
					'model' => $model,
					'pay'=>$payModel,
					'bank'=>$bank,
					'dataProvider'=>$dataProvider,
					'searchModel'=>$searchModel,
					'currencyList'=>$currencyListData,
					'data'=>$SolnetIdListData,
					'arrPaymentResultData' 	=> $arrPaymentResultData
			]);
		}
	}


	public function actionUploadimg($id)
	{
		//echo '<pre>';echo '<br/>';print_r($_FILES);die;
		$session = Yii::$app->session;
		if($id){
			$payModel = new Customerpayment();
			$paymentModel = $payModel->findOne($id);
			
			$arrPostData  = Yii::$app->request->post();
			$intInvoiceId = $arrPostData['invoice_id'];

			$intInvoiceNo = str_replace('/', '_', $arrPostData['intinvoiceId']);
			if($intInvoiceNo != '')
			{
				//reciept upload
				$filesArrayReceipt = UploadedFile::getInstance($payModel, 'receipt');

				if($filesArrayReceipt){
					if($filesArrayReceipt->error != '1'){
						$intLastInsertID = $paymentModel->payment_id;
						if(!empty($filesArrayReceipt)){
							$filesArrayReceipt->saveAs('uploads/invoice/' . 'receipt_'.$intInvoiceNo . '_' . $intLastInsertID. '.' . $filesArrayReceipt->extension);
							$strRecieptName ='receipt_'.$intInvoiceNo . '_' . $intLastInsertID. '.' . $filesArrayReceipt->extension;
							$connection = Yii::$app->getDb();
							$command = $connection->createCommand('UPDATE tblcustomerpayment SET receipt = "'.$strRecieptName.'" WHERE payment_id ='.$id);
							$command->execute();
							$session->setFlash('success_add_msg','Receipt Uploaded successfully');
						}
						else{
							$strRecieptName = '';
						}
					}else{
						$session->setFlash('error_add_msg','Something is worng. Please again');
					}
				}

				//cheque upload
				$filesArrayCheque = UploadedFile::getInstance($payModel, 'cheque');
				if($filesArrayCheque){
					if($filesArrayCheque->error != '1'){
						$intLastInsertID = $paymentModel->payment_id;
						if(!empty($filesArrayCheque)){
							$filesArrayCheque->saveAs('uploads/invoice/' . 'cheque_'.$intInvoiceNo. '_' . $intLastInsertID. '.' . $filesArrayCheque->extension);
							$strChequeName ='cheque_'.$intInvoiceNo . '_' . $intLastInsertID. '.' . $filesArrayCheque->extension;

							$connection = Yii::$app->getDb();
							$command = $connection->createCommand('UPDATE tblcustomerpayment SET cheque = "'.$strChequeName.'" WHERE payment_id ='.$id);
							$command->execute();
							$session->setFlash('success_add_msg','Cheque Uploaded successfully');
						}
						else{
							$strChequeName = '';
						}
					}else{
						$session->setFlash('error_add_msg','Something is worng. Please again');
					}
				}
				return $this->redirect(['invoice/pay/'.$intInvoiceId]);
			}
		}
		return $this->redirect(['invoice/pay/'.$intInvoiceId]);
	}

	public function actionDeleteimg($id,$action)
	{
		if($id!="")
		{
			$arrPaymentDetails = Customerpayment::find()->where(['payment_id'=>$id])->one();
			//echo '<pre>';echo '<br/>';print_r($paymentDetails);die;
			if($arrPaymentDetails)
			{
				if($action == 'deletereceipt')
				{
					$imgReceipt = $arrPaymentDetails->receipt;
					if($imgReceipt){
						unlink(getcwd().'/uploads/invoice/'.$imgReceipt);
						$condition = 'receipt = ""';
						$deleteMsg = 'Receipt image deleted successfully';
					}
				}
				
				if($action == 'deletecheque')
				{
					$imgCheque 	= $arrPaymentDetails->cheque;
					if($imgCheque){
						unlink(getcwd().'/uploads/invoice/'.$imgCheque);
						$condition = 'cheque = ""';
						$deleteMsg = 'Cheque image deleted successfully';
					}
				}

				$connection = Yii::$app->getDb();
				$command 	= $connection->createCommand('UPDATE tblcustomerpayment SET '.$condition.' WHERE payment_id ='.$id);
				$command->execute();
				
				$session 	= Yii::$app->session;
				$session->setFlash('success_delete_msg',$deleteMsg);
				return $this->redirect(Yii::$app->request->referrer);
			}
		}
	}

	public function actionPaycustomersupport($id)
	{
		$model = $this->findModel($id);
		$bank = new Bankdeposit();
		$arrStatus = array('unpaid','partial');
        $arrSolnetId    = Customerinvoice::find()->joinWith('customer')->select(['fk_customer_id','solnet_customer_id'])->where(['is_deleted'=>'0','is_invoice_activated'=>'yes'])->andWhere(['IN',['tblcustomerinvoice.status'],$arrStatus])->asArray()->all();
        
        $SolnetIdListData   = ArrayHelper::map($arrSolnetId,'fk_customer_id','solnet_customer_id');


		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
       return ActiveForm::validate($model);
      }
		$searchModel = new CustomerpaymentSearch();
		$queryParams = Yii::$app->request->getQueryParams();
		//$queryParams["CustomerpaymentSearch"]["tblcustomerpayment.fk_customer_id"]   = $model->fk_customer_id;
		$queryParams["CustomerpaymentSearch"]["fk_invoice_id"]   = $model->customer_invoice_id;
		$dataProvider = $searchModel->search($queryParams);
		$payModel = new Customerpayment();
		$intPendingAmt =  $model->pending_amount;
		$intCustId =  $model->fk_customer_id;
		$intCurrencyId = $model->linkcustomepackage->fk_currency_id;
		$intinvoiceId =  $model->customer_invoice_id;
		$strInvoiceNumber = $model->invoice_number;
		$model->scenario = 'pay';
		$arrPostData = Yii::$app->request->post();
		
		/*************To fetch currency from table************/
		$arrCurrency 	= Currency::find()->where(['status'=>'active'])->all();
		$currencyListData	= ArrayHelper::map($arrCurrency,'currency_id','currency');
		/*************To fetch state from table************/

		if($model->load(Yii::$app->request->post()))
		{

			if(!empty($arrPostData)) {
				
				$paymentModel = new Customerpayment();
				$intTotalPaid = $arrPostData['Customerinvoice']['payment_amount'];
				$paymentModel->fk_customer_id = $intCustId;
				$paymentModel->fk_invoice_id = $intinvoiceId;
				$paymentModel->discount = 0;
				$paymentModel->deduct_tax = 0;
				$paymentModel->bank_admin = 0;
				
				$paymentModel->payment_method = $arrPostData['Customerpayment']['payment_method'];
				$paymentModel->cheque_no =  $arrPostData['Customerpayment']['cheque_no'];
				$paymentModel->amount_paid = $intTotalPaid;
				//$paymentModel->reciept_no = $arrPostData['Customerpayment']['reciept_no'];
				$paymentModel->fk_currency_id = $intCurrencyId;
				$paymentModel->comment = $arrPostData['Customerpayment']['comment'];
				$paymentModel->created_at = date('Y-m-d h:i:s');
				$paymentModel->payment_date =date("Y-m-d",  strtotime($arrPostData['Customerpayment']['payment_date']));
				$paymentModel->is_payment_by_cs = 'yes';
				$paymentModel->cs_user_id = Yii::$app->user->identity->user_id;
					//save payment details
				if($paymentModel->save())
				{
					$model->po_wo_number = 0;
					//save invoice details
					if($model->save())
					{
						$connection = Yii::$app->getDb();
						
						$command = $connection->createCommand(
								'UPDATE tblcustomer SET po_wo_number = "0" WHERE customer_id ='.$intCustId);
						$command->execute();
					}

					$session = Yii::$app->session;
					$session->setFlash('success_paid',INVOICE_PAID_SUCCESSFULL);
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Pay Invoice';
					$logArray['action'] = 'update';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has paid the invoice of "'.$model->customer->name.'".';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					return $this->redirect(['invoice/paycustomersupport','id'=>$id]);
				}
				else{
					
					if($paymentModel->getErrors())
					{
						foreach ($paymentModel->getErrors() as $key => $value) 
						{
							$arrErrors[] = $value[0];
						}
						$message = implode("<br>",$arrErrors);
						$session = Yii::$app->session;
						$session->setFlash('error','Error <br>'.$message);
						if(isset($arrPostData['Customerinvoice']['check']) && $arrPostData['Customerinvoice']['check']==1)
						
						{
							return $this->render('pay_customer_support', [
							'model' => $model,
							'pay'=>$paymentModel,
							'bank'=>$bank,
							'dataProvider'=>$dataProvider,
							'searchModel'=>$searchModel,
							'currencyList'=>$currencyListData,
							'data'=>$SolnetIdListData
							]);
						}
						else
						{
							return $this->render('pay_customer_support', [
							'model' => $model,
							'pay'=>$paymentModel,
							
							'dataProvider'=>$dataProvider,
							'searchModel'=>$searchModel,
							'currencyList'=>$currencyListData,
							'data'=>$SolnetIdListData
							]);
						}
					}
					
				}
					//}

				}


		}else{
			
			return $this->render('pay_customer_support', [
					'model' => $model,
					'pay'=>$payModel,
					
					'dataProvider'=>$dataProvider,
					'searchModel'=>$searchModel,
					'currencyList'=>$currencyListData,
					'data'=>$SolnetIdListData
			]);
		}
	}
	
	public function actionDeletepaymenthistory($id,$action)
	{
		if($id!="")
		{
			$paymentDetails = Customerpayment::find()->where(['payment_id'=>$id])->one();

			if($paymentDetails)
			{
				$paymentAmount = $paymentDetails->amount_paid;
				$customerInvoiceId=$paymentDetails->fk_invoice_id;
				$model = $this->findModel($customerInvoiceId);
				if($model)
				{
					$newPendingAmount = $model->pending_amount + $paymentAmount;
					$newPaidAmount = $model->paid_amount - $paymentAmount;

					if($newPendingAmount==$model->total_invoice_amount)
					{
						$model->status = 'unpaid';
					}
					elseif($newPendingAmount<$model->total_invoice_amount)
					{
						$model->status = 'partial';
					}

					$model->pending_amount = $newPendingAmount;
					$model->paid_amount = $newPaidAmount;
					
					$deletePayment = $paymentDetails->delete();
					if($deletePayment)
					{
						$model->save();
						$session = Yii::$app->session;

						$session->setFlash('success','Payment history deleted successfully.');
						if($action=='pay')
							return $this->redirect(['invoice/pay','id'=>$customerInvoiceId]);
						else
							return $this->redirect(['customersupport/paycustomersupport','id'=>$customerInvoiceId]);
					}
					else
					{
						$session = Yii::$app->session;
						$session->setFlash('error','Unable to delete Payment history.');
						if($action=='pay')
							return $this->redirect(['invoice/pay','id'=>$customerInvoiceId]);
						else
							return $this->redirect(['customersupport/paycustomersupport','id'=>$customerInvoiceId]);
						
					}
				}
			}
		}
	}
	public function actionPendingamount($id)
    {
    	/*******To fetch data from Customer invoice*****/
		//$model=Customerinvoice::find()->where(['fk_customer_id'=>$id,'status'=>'cf'])->all();

		$searchModel = new CustomerinvoiceSearch();
		$queryParams = Yii::$app->request->queryParams;
		$queryParams['CustomerinvoiceSearch']['fk_customer_id'] = $id;
		//$queryParams['CustomerinvoiceSearch']['status'] = 'cf';
		
		$dataProvider = $searchModel->searchPendinginvoices($queryParams);
		$dataProvider->query->where("tblcustomerinvoice.status = 'partial'");
		$dataProvider->query->orWhere("tblcustomerinvoice.status = 'unpaid'");
		$dataProvider->query->andWhere("tblcustomerinvoice.fk_customer_id =".$id);
       return  $this->renderAjax('pendinginvoices',
       	[
       		//'model'=>$model,
       		'searchModel'=>$searchModel,
	       	'dataProvider'=>$dataProvider,
	       	
	       	'id'=>$id]);
    }

    public function actionUpdatecomment($id)
    {
    	/*******To fetch data from Customer invoice*****/
		
    	$model = Customerinvoice::find()->where(['customer_invoice_id'=>$id])->one();
    	$model->scenario = 'updatecomment';
    	if ($model->load(Yii::$app->request->post()) ) {
    		if($model->save())
    		{
    			$logArray = array();
				$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
				$logArray['module'] = 'Update invoice comment';
				$logArray['action'] = 'update';
				$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the comment';
				$logArray['created'] = date('Y-m-d H:i:s');
				Yii::$app->customcomponents->logActivity($logArray);


    			Yii::$app->session->setFlash('success', 'Comment added successfully');
    			return $this->redirect(['invoice/index']);
    		}
    	}
        return  $this->renderAjax('updatecomment',
       	[
       		'model'=>$model,
	       	'id'=>$id]);
    }

	/*
	*	Function to delete paid invoice and recalculate the invoice and update customerinvoice
	*
	*/
	public function actionDeletepaidinvoice($id)
	{
		if(!empty($id))
		{
			$custPayInfo = Customerpayment::find()->where(['payment_id'=>$id])->one();

			if(!empty($custPayInfo))
			{
				$intDiscount  = $custPayInfo->discount;
				$intDeductTax =	$custPayInfo->deduct_tax;
				$intBankAmt   = $custPayInfo->bank_admin;
				$intPaidAmt   = $custPayInfo->amount_paid;
				$intInvoiceId = $custPayInfo->fk_invoice_id;

				$invoiceInfo = Customerinvoice::find()->where(['customer_invoice_id'=>$intInvoiceId])->one();
				if(!empty($invoiceInfo)){
					$intInvoiceDeductTax  = $invoiceInfo->deduct_tax;
					$intInvoiceDiscount   = $invoiceInfo->discount;
					$intInvoiceBankAmt 	  = $invoiceInfo->bank_amount;
					$intInvoicePendingAmt = $invoiceInfo->pending_amount;
					$intInvoicePaidAmt 	  = $invoiceInfo->paid_amount;
					$totalInvoiceAmount   = $invoiceInfo->total_invoice_amount;

					$intUpdatedDiscount  = $intInvoiceDiscount  - $intDiscount;
					$intUpdatedDeductTax = $intInvoiceDeductTax - $intDeductTax;
					$intUpdatedBankAmt   = $intInvoiceBankAmt   - $intBankAmt;
					$intUpdatedPaidAmt   = $intInvoicePaidAmt   - $intPaidAmt;
					$intUpdatedPendingAmt= $intInvoicePendingAmt + $intPaidAmt;

					$invoiceModel = Customerinvoice::find()->where(['customer_invoice_id'=>$intInvoiceId])->one();
					$invoiceModel->deduct_tax = $intUpdatedDeductTax;
					$invoiceModel->discount = $intUpdatedDiscount;
					$invoiceModel->bank_amount = $intUpdatedBankAmt;
					$invoiceModel->paid_amount = $intUpdatedPaidAmt;
					$invoiceModel->pending_amount = $intUpdatedPendingAmt;
					if($intUpdatedPendingAmt==0)
					{
						$invoiceModel->status = 'paid';
					}
					elseif($intUpdatedPendingAmt>0 && $intUpdatedPendingAmt < $totalInvoiceAmount)
					{
						$invoiceModel->status = 'partial';
					}elseif($intUpdatedPendingAmt == $totalInvoiceAmount){

						$invoiceModel->status = 'unpaid';
					}
					if($invoiceModel->save()) {
						$connection = Yii::$app->db;
						$resultDel = $connection	->createCommand()->delete('tblcustomerpayment', 'payment_id = '.$id)->execute();
						if($resultDel){
							$session = Yii::$app->session;
							$session->setFlash('success_delete',INVOICE_DELETE_SUCCESSFULL);
							return $this->redirect(['invoice/pay','id'=>$intInvoiceId]);
						}
					}
				}
			}
		}
	}

	public function actionSoa($id='')
	{
	     ini_set('memory_limit', '512M'); 
		$arrGetData = Yii::$app->request->get();
		if(!empty($id)){
			$model = $this->findModel($id);
			$modelCust = Customer::findOne(['customer_id' => $id]);
			
		}else{
			$model = new Customerinvoice();
			$modelCust = new Customer();
		}
		$model->scenario = 'soa';
		$searchModel = new CustomerinvoiceSearch();
		/*************To fetch Solnet ID from table************/
		$arrSolnetId 	= Customer::find()->where(['status'=>'active','is_deleted'=>'0','is_invoice_activated'=>'yes'])->all();
		$SolnetIdListData	= ArrayHelper::map($arrSolnetId,'customer_id','solnet_customer_id');
		/*************To fetch Solnet ID from table************/

		$objAllInvoices = Customerinvoice::find()->where(['fk_customer_id'=>$id,'status'=>['unpaid','partial']]);
		$dataProvider = new \yii\data\ActiveDataProvider([
		'query'=>$objAllInvoices,
		'pagination'=>[
						'pageSize'=>20
					]
		]);

		if(!empty($arrGetData) && !empty($arrGetData['Customerinvoice']['soa_type']))
		{

			$intCustId  = $arrGetData['id'];
			$strSoaType = $arrGetData['Customerinvoice']['soa_type'];

			if(!empty($intCustId))
			{

				/*$queryParams = Yii::$app->request->getQueryParams();
				$queryParams["CustomerinvoiceSearch"]["fk_customer_id"]   = $intCustId;
				$queryParams["CustomerinvoiceSearch"]["status"] 	= ['unpaid','partial'];
				$dataProvider = $searchModel->search($queryParams);*/
				if($strSoaType=='due'){
					$objAllInvoices = Customerinvoice::find()->where(['fk_customer_id'=>$intCustId,'status'=>['unpaid','partial']]);
					$dataProvider = new \yii\data\ActiveDataProvider([
					'query'=>$objAllInvoices,
					'pagination'=>[
									'pageSize'=>20
								]
					]);
				}else{

					$strSoaDate = Yii::$app->formatter->asDate($arrGetData['Customerinvoice']['soa_date'], 'php:Y-m-d');
					$objAllInvoices = Customerinvoice::find()->where(['fk_customer_id'=>$intCustId]);
					$objAllInvoices->andWhere(['>=','invoice_date',$strSoaDate]);
					//echo $objAllInvoices->createCommand()->getRawsql(); die;
					$dataProvider = new \yii\data\ActiveDataProvider([
					'query'=>$objAllInvoices,
					'pagination'=>[
									'pageSize'=>20
								]
					]);

				}
			}

		}else{

		}
		return $this->render('soa', [
					'data'=>$SolnetIdListData,
					'model'=>$model,
					'dataProvider'=>$dataProvider,
					'searchModel'=>$searchModel,
					'modelCust' =>$modelCust
			
			]);
	}

	public function actionSoaprint($id,$type,$date='')
	{
	     ini_set('memory_limit', '512M'); 
		$model = $this->findModel($id);
		$searchModel = new CustomerinvoiceSearch();
		$modelCust = Customer::findOne(['customer_id' => $id]);
		if(!empty($type) && !empty($id))
		{
				if($type=='due'){
					$objAllInvoices = Customerinvoice::find()->where(['fk_customer_id'=>$id,'status'=>['unpaid','partial']]);
					$dataProvider = new \yii\data\ActiveDataProvider([
					'query'=>$objAllInvoices,
					'pagination'=>[
									'pageSize'=>20
								]
					]);
				}else{
					//$strSoaDate = $arrGetData['Customerinvoice']['soa_date'];
					$strSoaDate = Yii::$app->formatter->asDate($date, 'php:Y-m-d');
					$objAllInvoices = Customerinvoice::find()->where(['fk_customer_id'=>$id]);
					$objAllInvoices->andWhere(['>=','invoice_date',$strSoaDate]);
					$dataProvider = new \yii\data\ActiveDataProvider([
					'query'=>$objAllInvoices,
					'pagination'=>[
									'pageSize'=>20
								]
					]);

				}
		}

		$pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' => $this->renderPartial('soa_print', [
            'model'=>$model,
			'dataProvider'=>$dataProvider,
			'searchModel'=>$searchModel,
			'modelCust' =>$modelCust
        ]),
		'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Customer Details',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
            //'SetHeader' => ['Generated By: Solnet'],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
    ]);

		return $pdf->render();


	}
	
	
	/**
     * function to generate custom service invoice
     *
     */
    public function actionService($id='')
    {
        ini_set('memory_limit', '512M'); 
        $invoiceModel = new Customerinvoice();
		$serviceModel = new ServiceInvoice();
		$invoiceModel->scenario = 'custom_service';
		$floatVat = 0;
		$floatCurrentInvoiceAmt = 0;
 		$model = Customer::find()->where(['customer_id'=>$id])->one();
		if(empty($id)){
			$model = new Customer();
			$invoiceModel->send_invoice = 'no';
		}else{
			$intStateId = $model->fk_state_id;
			$getVat =  Yii::$app->customcomponents->GetVat($intStateId);
			$intFkCustPckId = $model->linkcustomerpackage->cust_pck_id;
		}

		/*************To fetch Solnet ID from table************/
		$arrSolnetId 	= Customer::find()->where(['status'=>'active','is_deleted'=>'0','is_invoice_activated'=>'yes'])->all();
		$SolnetIdListData	= ArrayHelper::map($arrSolnetId,'customer_id','solnet_customer_id');
		/*************To fetch Solnet ID from table************/

		$invoiceModel->fk_customer_id = $id;
		$invoiceModel->invoice_type = 'custom_service';
		$invoiceModel->invoice_date = date('Y-m-d h:i:s');
		$invoiceModel->po_wo_number = $model->po_wo_number;
		$arrPostData = Yii::$app->request->post();
		if(!empty($arrPostData) ){
			if(!empty($arrPostData['description'])){
			foreach($arrPostData['description'] as $key => $value){
							$floatCurrentInvoiceAmt = $floatCurrentInvoiceAmt + ($arrPostData['price'][$key]* $arrPostData['quantity'][$key]);
				
						} 
			}
			$invoiceModel->current_invoice_amount = $floatCurrentInvoiceAmt;
			$floatInstallationFee = 0;
			$floatOtherFee = 0;
			
			$boolSendInvoice = $arrPostData['Customerinvoice']['send_invoice'];
			if(!empty($floatInstallationFee)){
				$floatCurrentInvoiceAmt += $floatInstallationFee ;
			}
			if(!empty($floatOtherFee)){
				$floatCurrentInvoiceAmt += $floatOtherFee ;
			}
			if(!empty($getVat)){
				$floatVat1 = ($floatCurrentInvoiceAmt*$getVat)/100;
				$floatVat = round($floatVat1);
			}
			$invoiceModel->total_invoice_amount = $floatVat + $floatCurrentInvoiceAmt;
			$invoiceModel->pending_amount = 	$floatVat + $floatCurrentInvoiceAmt;
			$invoiceModel->fk_cust_pckg_id = 	$intFkCustPckId;
			//$invoiceModel->usage_period_to = 	Yii::$app->formatter->asDate($arrPostData['Customerinvoice']['usage_period_to'], 'php:Y-m-d H:i:s');
			//$invoiceModel->usage_period_from =  Yii::$app->formatter->asDate($arrPostData['Customerinvoice']['usage_period_from'], 'php:Y-m-d H:i:s');
			$invoiceModel->due_date =  			Yii::$app->formatter->asDate($arrPostData['Customerinvoice']['due_date'], 'php:Y-m-d H:i:s');
			$arrInvoice = Yii::$app->customcomponents->GetInvoiceId($model->fk_state_id);

			if(!empty($arrInvoice))
			{
				$invoiceModel->invoice_number = $arrInvoice['current_invoice_id'];
			}
		}
		if(!empty($floatVat))
		{
			$invoiceModel->vat = $floatVat;
		}

		$invoiceModel->status = 'unpaid';
		$invoiceModel->created_at = date('Y-m-d h:i:s');
		if( $invoiceModel->save())
		{
			$intLastInsertedId = $invoiceModel->customer_invoice_id;
			$arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($model->fk_state_id,$arrInvoice['increment_value']);
			// Save Invoice details
			if(!empty($arrPostData['description'])){
			foreach($arrPostData['description'] as $key => $value){
				$serviceModel = new ServiceInvoice();
				$serviceModel->description = $value;
				$serviceModel->price = $arrPostData['price'][$key];
				$serviceModel->quantity =	$arrPostData['quantity'][$key];
				$serviceModel->fk_customer_id = $id;
				$serviceModel->fk_invoice_id = $intLastInsertedId;
				$serviceModel->created_on = date('Y-m-d H:i:s');
				$serviceModel->updated_on = date('Y-m-d H:i:s');
				$serviceModel->save();
						
				} 
			}
			
			if($boolSendInvoice=='yes'){
				//$this->actionSendmail($intLastInsertedId);  develop this functionality
			}
			$session = Yii::$app->session;
			$session->setFlash('success',INVOICE_CUSTOM_SUCCESSFULL);
			/************Log Activity*********/
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Generate Custom Invoice';
			$logArray['action'] = 'create';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has generated custom invoice for "'.$model->name.'".';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			/************Log Activity*********/
			return $this->redirect(['invoice/custom_service','id'=>$id]);
		}
		else{
			return $this->render('custom_service', [
					'model' => $model,
					'invoiceModel'=>$invoiceModel,
					'data'=>$SolnetIdListData,
					'serviceModel' => $serviceModel
				]);
		}
    }
	

    /**
     * Finds the Customerinvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customerinvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
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
}
