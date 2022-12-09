<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : BankdepositeController.php
# Created on : 15th June 2017 by Swati Jadhav.
# Update on  : 15th June 2017 by Swati Jadhav.
# Purpose : Manage Bank Deposit Details.
############################################################################################
*/
namespace app\controllers;

use Yii;
use app\models\Bankdeposit;
use app\models\BankdepositeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Bank;
use app\models\Customerinvoice;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;
use app\models\Customer;

/**
 * BankdepositeController implements the CRUD actions for Bankdeposit model.
 */
class BankdepositeController extends Controller
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
                        'only' => ['index','create', 'update','print','view','deletemultiple','incentive'],
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
     * Lists all Bankdeposit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BankdepositeSearch();
        $dataProvider = $searchModel->searchBankdeposite(Yii::$app->request->queryParams);
        $resultTotalPrice = $searchModel->getTotalPrice(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,            
            'totalPackagePrice'=>$resultTotalPrice,
        ]);
    }

    /**
     * Displays a single Bankdeposit model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Bankdeposit model.
     * If creation is successful, the browser will be redirected to the 'same' page and show saved records in griedview.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Bankdeposit();
        $model->scenario = 'create';
        $searchModel = new BankdepositeSearch();
        $arrStatus = array('unpaid','partial');
        $arrSolnetId    = Customerinvoice::find()->joinWith('customer')->select(['fk_customer_id','solnet_customer_id','name'])->where(['tblcustomer.status'=>'active','is_deleted'=>'0','is_invoice_activated'=>'yes'])->andWhere(['IN',['tblcustomerinvoice.status'],$arrStatus])->asArray()->all();
        
        $SolnetIdListData   = ArrayHelper::map($arrSolnetId,'fk_customer_id','solnet_customer_id');
        $SolnetCustnameListData   = ArrayHelper::map($arrSolnetId,'fk_customer_id','name');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if ($model->load(Yii::$app->request->post())) {
           

             if( $_POST['Bankdeposit']['is_solnet_bank']=='0'){
               
                $model->account_no=$_POST['Bankdeposit']['account_no']; 
            }
            else{

                 $model->fk_bank_id=$_POST['Bankdeposit']['fk_bank_id'];
                 $model->bank=$_POST['Bankdeposit']['bank_name']; 
            }
           if(!empty($_POST['Bankdeposit']['fk_customer_id']))
           {
                $model->fk_customer_id = $_POST['Bankdeposit']['fk_customer_id'];
           }
           elseif(!empty($_POST['Bankdeposit']['customer_name']))
           {
                $model->fk_customer_id = $_POST['Bankdeposit']['customer_name'];
           }
            $model->is_deleted='0';
            $model->fk_user_id=Yii::$app->user->identity->user_id;
            $model->created_at=date('Y-m-d h:i:s');
            $model->is_solnet_bank=$_POST['Bankdeposit']['is_solnet_bank']; 
           // $model->deposit_date=date("Y-m-d",  strtotime($_POST['Bankdeposit']['deposit_date']));
            $model->deposit_date = Yii::$app->formatter->asDate($_POST['Bankdeposit']['deposit_date'], 'php:Y-m-d H:i:s');

            if (Yii::$app->request->post('addmore') ==='add')
                {
                    
                    if($model->save())
                    { 
                        $logArray = array();
                        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                        $logArray['module'] = 'Manage Bank Deposit';
                        $logArray['action'] = 'create';
                        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deposited the amount ';
                        $logArray['created'] = date('Y-m-d H:i:s');
                        Yii::$app->customcomponents->logActivity($logArray);
                        Yii::$app->session->setFlash('success', BANK_DEPOSIT_CREATE_SUCCESSFULL);
                         return $this->redirect(['bankdeposite/create']);
                         
                    }
                    else{
                       
                        Yii::$app->session->setFlash('success', BANK_DEPOSIT_CREATE_FAIL);
                         return $this->redirect(['bankdeposite/create']);
                    }
                }
            else
                {

                  if($model->save())
                    {

                        $logArray = array();
                        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                        $logArray['module'] = 'Manage Bank Deposit';
                        $logArray['action'] = 'create';
                        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deposit amount ';
                        $logArray['created'] = date('Y-m-d H:i:s');
                        Yii::$app->customcomponents->logActivity($logArray);
                                                 
                        Yii::$app->session->setFlash('success', BANK_DEPOSIT_CREATE_SUCCESSFULL);
                         return $this->redirect(['bankdeposite/index']);
                         
                    }
                  else{
                    
                    Yii::$app->session->setFlash('success', BANK_DEPOSIT_CREATE_FAIL);
                     return $this->redirect(['bankdeposite/create']);
                    }
                    
                }
            
        }else{

         return $this->render('create', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'data'=>$SolnetIdListData,
                'cust_name'=>$SolnetCustnameListData
            ]);
        }
    }



    public function actionIncentive()
    {
        $searchModel    = new BankdepositeSearch();
        $modelCustomer  = new Customer();
        $arrSalesPerson = array();
        $arrSalesPerson = $modelCustomer->getUserName();
        $queryParams    = Yii::$app->request->queryParams;
        //echo "<pre>";
       // print_r($BankdepositeSearch);
        if(!empty($_GET['start_date']) && !empty($_GET['end_date']))
        {
            $strStartDate   = $_GET['start_date'];
            $strEndDate     = $_GET['end_date'];
            $startDate      = Yii::$app->formatter->asDate($strStartDate, 'php:Y-m-d');
            $endDate        = Yii::$app->formatter->asDate($strEndDate, 'php:Y-m-d');
            $queryParams["start_date"] = $startDate;
            $queryParams["end_date"]   = $endDate;

        }

        $dataProvider   = $searchModel->search($queryParams);

        //$dataProvider->pagination->pageSize = 100;
        
        return $this->render('incentive_listing', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'user'         => $arrSalesPerson
        ]);
    }

     public function actionIncentivecsv()
    {

        $searchModel    = new BankdepositeSearch();
        $modelCustomer  = new Customer();
        $arrSalesPerson = array();
        $arrSalesPerson = $modelCustomer->getUserName();
        $queryParams    = Yii::$app->request->post();//Yii::$app->request->queryParams;       
        $name =  $created_at = $deposit_date = $billing_address = $invoice_number = $start_date = $end_date = 
        $sales_person = $agent_name = $fiber_installed = '';
        if(isset($_POST['sales_person']))
        { 
            $sales_person =  $_POST['sales_person'];
        }
        else
        {
            $sales_person =  $queryParams['name'];
        }
        if(isset($_POST['agent_name']))
        { 
            $agent_name =  $_POST['agent_name'];
        }
        else
        {
            $agent_name =  $queryParams['agent_name'];
        }
        if(isset($_POST['fiber_installed']))
        { 
            $fiber_installed =  $_POST['fiber_installed'];
        }
        else
        {
            $fiber_installed =  $queryParams['fiber_installed'];
        }
       
        if(isset($_POST['name']))
        { 
            $name =  $_POST['name'];
        }
        else
        {
            $name =  $queryParams['name'];
        }

        if(isset($_POST['created_at']))
        { 
            $created_at =  $_POST['created_at'];
        }
        else
        {
          $created_at =  $queryParams['created_at'];
        }
        if(isset($_POST['deposit_date']))
        { 
            $deposit_date =  $_POST['deposit_date'];
        }
        else
        {
          $deposit_date =  $queryParams['deposit_date'];
        }
        if(isset($_POST['billing_address']))
        { 
            $billing_address =  $_POST['billing_address'];
        }
        else
        {
          $billing_address =  $queryParams['billing_address'];
        }
        if(isset($_POST['invoice_number']))
        { 
            $invoice_number =  $_POST['invoice_number'];
        }
        else
        {
          $invoice_number =  $queryParams['invoice_number'];
        }
        if(isset($_POST['start_date']))
        { 
            $start_date =  $_POST['start_date'];
        }
         else
        {
          $start_date =  $queryParams['start_date'];
        }
        if(isset($_POST['end_date']))
        { 
            $end_date =  $_POST['end_date'];
        }
         else
        {
          $end_date =  $queryParams['end_date'];
        }
        if(!empty($_GET['start_date']) && !empty($_GET['end_date']))
        {
            $strStartDate   = $_GET['start_date'];
            $strEndDate     = $_GET['end_date'];
            $startDate      = $strStartDate;
            $endDate        = $strEndDate;
            $queryParams["start_date"] = $startDate;
            $queryParams["end_date"]   = $endDate;

        }
        
        $floatIDRTotBalance = 0;
        $floatSGDTotBalance = 0;
        $floatUSDTotBalance = 0;
        $floatTotBalance    = 0;
        $floatIDRTtoBalance    = 0;
        $floatSGDTtoBalance    = 0;

        $dataProvider   = $searchModel->searchcsv($queryParams,$name,$created_at,$deposit_date,$billing_address,$invoice_number,$start_date,$end_date,$sales_person,$agent_name,$fiber_installed);
        $filename = 'Customer_contract'.date('Ymdhis').'.csv';
        $file = fopen($filename, 'w');
        fputcsv($file, array("Created At","Deposit Date","Solnet Customer Id","Name","Installation Address","Billing Address","Invoice Number","Package Title","Package Price","Installation Fee","Other Fee","Usage  Period  From","Usage  Period  To","Amount Deposit","Sales Person","Agent Name","Fiber Installed"));
                $data = array();
                $model = $dataProvider->getModels();
                foreach($dataProvider->getModels() as $key=>$value){
                        $arrResult = BankdepositeSearch::getLinkdata($value->customer->customer_id);
                           if(!empty($arrResult))
                           {
                              $customer_id =  $arrResult[0]['installation_address'];
                           }
                           else
                           {
                              $customer_id =  '-';
                           }
                        $arrResultPackageTitle = BankdepositeSearch::getLinkdata($value->customer->customer_id);
                           if(!empty($arrResult))
                           {
                               $package_title =  $arrResultPackageTitle[0]['package_title'];
                           }
                           else
                           {
                               $package_title =  '-';
                           }
                        $arrResultBankdepositeSearch = BankdepositeSearch::getLinkdata($value->customer->customer_id);
                           if(!empty($arrResultBankdepositeSearch))
                           {
                              $package_price = number_format($arrResultBankdepositeSearch[0]['package_price'],2);
                           }
                           else
                           {
                              $package_price =  '-';
                           }
                           
                        $arrResultgetUsagePeriod = BankdepositeSearch::getUsagePeriod($value->customerinvoice->customer_invoice_id);
                           if(!empty($arrResultgetUsagePeriod))
                           {
                               $installation_fee =  number_format($arrResultgetUsagePeriod[0]['installation_fee'],2);
                               $other_service_fee =  number_format($arrResultgetUsagePeriod[0]['other_service_fee'],2);
                               $usage_period_from = date('d-m-Y',strtotime($arrResultgetUsagePeriod['0']['usage_period_from']));
                               $usage_period_to = date('d-m-Y',strtotime($arrResultgetUsagePeriod['0']['usage_period_to']));

                           }
                           else
                           {
                               $installation_fee =  '-';
                               $other_service_fee =  '-';
                               $usage_period_from = '-';
                               $usage_period_to = '-'; 
                           }
                           if($value->customer->agent_name!=null || $value->customer->agent_name!="")
                               $agent_name =  $value->customer->agent_name;
                           else
                               $agent_name =  "-";

                           if($value->customer->fiber_installed == null){
                               $fiber_installed = '-';
                           }else{
                               $fiber_installed = $value->customer->fiber_installed;
                           }

                       $data[] = [
                        date_format(date_create($value->created_at),'d-m-Y  h:i A'),
                        date_format(date_create($value->deposit_date),'d-m-Y h:i A'),
                        $value->customer->solnet_customer_id,
                        $value->customer->name,
                        $customer_id,
                        $value->customer->billing_address,
                        $value->customerinvoice->invoice_number,
                        $package_title,
                        $package_price,
                        $installation_fee,
                        $other_service_fee,
                        $usage_period_from,
                        $usage_period_to,
                        number_format($value->amount,2),
                        $value->customer->user->name,                        
                        $agent_name,
                        $fiber_installed,
                    ] ;

                    if($model[$key]->fk_currency_id == 1)
                       {   //For IDR Currency
                           $floatIDRTotBalance  += $value->amount;
                       }
                       elseif($model[$key]->fk_currency_id == 2)
                       {  //For SGD Currency
                            $floatSGDTotBalance  += $value->amount;
                       }
                       elseif($model[$key]->fk_currency_id == 3)
                       {   //For USD Currency
                            $floatUSDTotBalance  += $value->amount;
                       }
                       $floatTotBalance = 'IDR '.number_format($floatIDRTotBalance,2);
                       $floatIDRTtoBalance = 'SGD '.number_format($floatSGDTotBalance,2);
                       $floatSGDTtoBalance = 'USD '.number_format($floatUSDTotBalance,2); 
                }
         foreach ($data as $row)
        {
            fputcsv($file, $row);
        }

        fputcsv($file, array("","","","","","","","","","","","","",$floatTotBalance)); 
        fputcsv($file, array("","","","","","","","","","","","","",$floatIDRTtoBalance)); 
        fputcsv($file, array("","","","","","","","","","","","","",$floatSGDTtoBalance)); 
        // Close the file
        fclose($file);
       return $filename;
    }
    public function actionIncentivecsvdownload(){

        /*header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename=../web/".$_GET['fileName']); 
        header("Pragma: no-cache");*/
        $f=$_GET['fileName'];   
        $file = ("../web/$f");
        $filetype=filetype($file);
        $filename=basename($file);
        header ("Content-Type: ".$filetype);
        header ("Content-Length: ".filesize($file));
        header ("Content-Disposition: attachment; filename=".$filename);
        readfile($file);
    }

    
    public function actionGetinvoiceid()
    {
       $invoiceData = array();
       if(isset($_POST) && !empty($_POST))
       {
         if(isset($_POST['id']) && $_POST['id']!="")
         {
             $intCustId = $_POST['id'];
             $model = new Customerinvoice();
             $invoiceData = $model->getInvoiceId($intCustId);
             echo $invoiceData;
         }
         if(isset($_POST['invId']) && $_POST['invId']!="")
         {
            $intInvId = $_POST['invId'];
            $model = new Customerinvoice();
            $invoiceAmount = $model->getAmount($intInvId);
            echo $invoiceAmount;
         }
       }
    }

    /**
     * Updates an existing Bankdeposit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
        }

        $arrStatus = array('unpaid','partial');
        $arrSolnetId    = Customerinvoice::find()->joinWith('customer')->select(['fk_customer_id','solnet_customer_id','name'])->where(['tblcustomer.status'=>'active','is_deleted'=>'0','is_invoice_activated'=>'yes'])->andWhere(['IN',['tblcustomerinvoice.status'],$arrStatus])->asArray()->all();
        
        $SolnetIdListData   = ArrayHelper::map($arrSolnetId,'fk_customer_id','solnet_customer_id');
        $SolnetCustnameListData   = ArrayHelper::map($arrSolnetId,'fk_customer_id','name');

        if ($model->load(Yii::$app->request->post())) {

			$model->deposit_date = Yii::$app->formatter->asDate($_POST['Bankdeposit']['deposit_date'], 'php:Y-m-d H:i:s');
            if($model->save()){
				
			
				$logArray = array();
				$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
				$logArray['module'] = 'Manage Bank Deposit';
				$logArray['action'] = 'Update';
				$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated deposit ';
				$logArray['created'] = date('Y-m-d H:i:s');
				Yii::$app->customcomponents->logActivity($logArray);
				Yii::$app->session->setFlash('success',BANK_DEPOSIT_UPDATE_SUCCESSFULL);
				return $this->redirect(['view', 'id' => $model->bank_deposit_id]);
				
			}
		  
         
        } else {
              
            return $this->render('update', [
                'model' => $model,'data'=>$SolnetIdListData,'cust_name'=>$SolnetCustnameListData]);
                 
        }
    }

    /**
     * Deletes an existing Bankdeposit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
       
        $model = $this->findModel($id);
        $delete = $model->delete();

       if( $model->delete()){

            $logArray = array();
            $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
            $logArray['module'] = 'Manage Bank Deposit';
            $logArray['action'] = 'delete';
            $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted deposit ';
            $logArray['created'] = date('Y-m-d H:i:s');
            Yii::$app->customcomponents->logActivity($logArray);
            Yii::$app->session->setFlash('success', BANK_DEPOSIT_DELETE_SUCCESSFULL);
            return $this->redirect(['index']);
        }
        else{
            Yii::$app->session->setFlash('success', BANK_DEPOSIT_DELETE_FAIL);
            return $this->redirect(['index']);
        }
    }


    /**
     * Deletes multiple packages.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletemultiple()
    {
        $ids = yii::$app->request->post('ids');

        if(!empty($ids))
        {
            if(Bankdeposit::updateAll(['is_deleted'=>'1'],['bank_deposit_id'=>$ids]))
            {
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



    /**
     * Generate a pdf to print single bank deposite details .
     * @param integer $id
     * @return mixed
     */
    public function actionPrint($id) {
        $model =  $this->findModel($id);
        $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' => $this->renderPartial('view', [
            'model' => $this->findModel($id),
        ]),
        'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Bankdeposite Details',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
            //'SetHeader' => ['Generated By: Solnet '],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
    ]);
        $logArray = array();
        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
        $logArray['module'] = 'Mange Bank Deposite';
        $logArray['action'] = 'update';
        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has printed the bank deposit' ;
        $logArray['created'] = date('Y-m-d H:i:s');
        Yii::$app->customcomponents->logActivity($logArray);
        
        return $pdf->render();
    }

    /**
     * Finds the Bankdeposit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bankdeposit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bankdeposit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
