<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : SiteController.php
# Created on : 2nd June 2017 by Swati Jadhav.
# Update on  : 2nd June 2016 by Swati Jadhav.
# Purpose : Manage Admin login 
############################################################################################
*/
namespace app\controllers;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use app\models\Customer;
use app\models\Customerinvoice;
use app\models\Generalsettings;
use app\models\ContactForm;
use app\models\TbluserStates;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\LoginTemp;
use app\models\CustomerService;
use app\models\Linkcustomepackage;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
	 public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
				'only' => ['logout','login','index', 'error'],
                'rules' => [
                    [
                        'actions' => ['login', 'error','password','changepassword','forgetpassword'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    
    

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $modelLoginTemp = new LoginTemp();
        $stateListData = array();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            
        	$intId=Yii::$app->user->identity->user_id;

            //add entry in Login temp table
            $modelLoginTemp->fk_user_id = $intId;
            $modelLoginTemp->login_time = date('Y-m-d H:i:s');
            $modelLoginTemp->last_activity_time = date('Y-m-d H:i:s');
            $modelLoginTemp->save();
        	$getUserStates = TbluserStates::find()->joinWith(['states'])->select(['fk_state_id','tblstate.state'])->where(['fk_user_id'=>$intId])->asArray()->all();

        	$stateListData	= ArrayHelper::map($getUserStates,'fk_state_id','state');
        	if($stateListData)
        	{
        		$sessionData = Yii::$app->session;
        		$sessionData->set('userStates',$stateListData);
        		//\Yii::$app->session->set('user.states',$stateListData);
        	}
            $logArray = array();
            $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
            $logArray['module'] = 'User logged in';
            $logArray['action'] = 'login';
            $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has logged in';
            $logArray['created'] = date('Y-m-d H:i:s');
            Yii::$app->customcomponents->logActivity($logArray);
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $intId=Yii::$app->user->identity->user_id;

        Yii::$app->user->logout();
        
                $model = LoginTemp::find()->where(['fk_user_id'=>$intId])->one();
                
        return $this->redirect(['site/login']);
    }

    /**
     * render change password form.
     */
    /*public function actionPassword()
    {
         $model = new LoginForm;
         $model->scenario = 'change_password';
         return $this->render('password',['model' => $model]);
    }*/

    public function actionOnlineusers()
    {
        $arrData = array();
        $model = new LoginTemp();
        $getOnlineUsers = $model->getOnlineUsers();
        if($getOnlineUsers)
        {
            $count=count($getOnlineUsers);
        }
        else
        {
            $count = 0;
        }
        $arrData['count'] = $count;
        $arrData['view'] = $this->renderPartial('/layouts/online_users.php');
        if($arrData)
        {
            echo json_encode($arrData);
        }
    }


    /**
     *  change password action.
     */
    public function actionChangepassword()
    {   
       $model = new LoginForm();
       $model->scenario = 'change_password';
       
       if(Yii::$app->request->isAjax && $model->load($_POST)) {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($model);
        }
       
        if ($model->load(Yii::$app->request->post())) {

           $intId=Yii::$app->user->identity->user_id;

           $hashPassword = Yii::$app->getSecurity()->generatePasswordHash($model->new_password);
           //make hash of new password and save it
            $query=Yii::$app->db->createCommand()->update('tblusers', ['password' => $hashPassword,'updated_at'=>date('Y-m-d h:i:s')], ['user_id'=>$intId])->execute();

            if($query){
            Yii::$app->getSession()->setFlash('success', 'Password changed successfully');                   
            return $this->redirect(['site/index']);
           }
         }
        else{
            Yii::$app->getSession()->setFlash('error', 'Password changed failed');  
            return $this->redirect(['site/changepassword']);
        }
   }

   /**
   * Forgot password action
   * send password reset link to user
   */

    public function actionForgetpassword()
    {
          $strFromEmail='';
          $model = new LoginForm();
          if ($model->load(Yii::$app->request->post())){
          $strEmail=Yii::$app->request->post('LoginForm')['email'];
			$arrExistEmail= User::find()->where(['email'=>$strEmail,'status'=>'active','is_deleted'=>'0' ])->one();

			if(count($arrExistEmail)==1){	

			 $intRandomNo = rand(000000,999999); 
			 $intUserId= $arrExistEmail->user_id;
             $strUserName=$arrExistEmail->name;

			 $strEncryptedNo= base64_encode($intRandomNo);
			 $strEncryptedId= base64_encode($intUserId);

			 $query=Yii::$app->db->createCommand()->update('tblusers', ['password_reset_key' => $strEncryptedNo], 
			 	['user_id'=>$intUserId])->execute();
			if($query){
				
				$subject = 'Reset your password';
                $imageUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/web/images/solnet.png';
				
				$strEmailLink='<a href='.Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/site/resetpassword/'.$strEncryptedId.'/'.$strEncryptedNo.'>Click to reset password</a>';
                
    			 $link= Url::to(['site/resetpassword' ,'id'=>$strEncryptedId, 'key'=>$strEncryptedNo]);
    			 /**** GET ADMIN EMAIL FORM GENERALSETTINGS MODEL****/
    			 $sentFrom= Generalsettings::find()->where(['name'=>'ADMIN_EMAIL'])->one();
    			 if(!empty($sentFrom->value)){
    			 	$strFromEmail=$sentFrom->value;
    			 }
    			 else{
    			 	$strFromEmail='eluminous_se42@eluminoustechnologies.com';
    			 	}
    			
				
				$params = '';
                $to=$strEmail;
				try{
				$resultMail = Yii::$app->mailer->compose('password_reset_link',['params' => $params,'imageFileName' => $imageUrl,'model'=>$model,
                    'link'=>$strEmailLink,'strUserName'=>$strUserName])
						->setFrom($strFromEmail)
						->setTo($to)
						->setSubject('Solnet :'.$subject)
						//->setHtmlBody($strEmailLink)
						->send();
						if($resultMail){
						    $emailLog = array();
                            $emailLog['email_to'] = $to;
                            $emailLog['subject'] = 'Solnet :'.$subject;
                            $emailLog['is_user'] = 'Yes';
                            $emailLog['sent_to_id'] = $intUserId;
                            $emailLog['sent_by'] = 'System';
                            $emailLog['sent_date'] = date('Y-m-d H:i:s');
                            Yii::$app->customcomponents->emailLogActivity($emailLog);



							Yii::$app->getSession()->setFlash('succ_sent_link', 'Password reset link sent to your email');   
							return $this->redirect(['site/forgetpassword']);
						}
				 } catch (Swift_TransportException $e) {
					 return Yii::$app->getResponse()->redirect(['site/login']);
				 }	
					exit;
			 

			}
          }
		
		else{
			Yii::$app->getSession()->setFlash('error_sent_link', 'This email is not exists');   
				return $this->redirect(['site/forgetpassword']);
			}
	  	}


       	$this->layout = 'main-login';
	    return $this->render('forget_password',['model' => $model]);
    }


    /**
    *check for valid password reset link of valid user 
    */
    
     public function actionResetpassword($id,$key)
    {
    		 $intDecodeId=base64_decode($id);
    		 $model = new LoginForm();
    		
			 $arrExistUser= User::find()->where(['user_id'=>$intDecodeId,'status'=>'active','password_reset_key'=>$key ])->one();
			
			if(count($arrExistUser)==1){
				   	$this->layout = 'main-login';
				 return $this->render('reset_password',['model' => $model,'userId'=>$intDecodeId]);
			}
			else{
				
				 echo 'Invalid Link';
			}

    }

     /**
     * save new password 
     * redirect to login
     */
    public function actionNewpassword($id)
    {   
    	
       $model = new LoginForm();
       $model->scenario = 'change_password';
       
       if(Yii::$app->request->isAjax && $model->load($_POST)) {
            Yii::$app->response->format = 'json';
            return \yii\widgets\ActiveForm::validate($model);
        }
       
        if ($model->load(Yii::$app->request->post())) {

           
           $hashPassword = Yii::$app->getSecurity()->generatePasswordHash($model->new_password); //make hash of new password and save it
          
            $query=Yii::$app->db->createCommand()->update('tblusers', ['password' => $hashPassword,'password_reset_key' =>'',
                'updated_at'=>date('Y-m-d h:i:s')], ['user_id'=>$id])->execute();
            
            if($query){
            Yii::$app->getSession()->setFlash('succ_reset_password', ' Your password has been changed successfully');                   
            return $this->redirect(['site/login']);
           }
         }
        else{
            Yii::$app->getSession()->setFlash('err_reset_password', ' Password not changed.Please try again.');  
            return $this->redirect(['site/resetpassword']);
        }
   }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
	
	
	
	public function actionMonthlycron()
    {
        if(date('Y-m-d') != date('Y-m-01')){
            echo "here"; exit;
        }
         // ini_set('max_execution_time', 0);
        $arrCustId = array();
        $connection = Yii::$app->db;
        $qryGetCustomer = Customer::find()->joinWith('linkcustomerpackage')->where(['status'=>'active','is_invoice_activated'=>'yes','linkcustomepackage.payment_type'=>['term','advance'],'is_disconnected'=>'no'])->andWhere(['<=','linkcustomepackage.invoice_start_date',date("Y-m-d")])->andWhere(['invoice_generated' => '0'])->all();
        
        $strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
        $strNextMonthLastDate = date('Y-m-d', strtotime(date('Y-m-t').' +1 MONTH'));  // To get Next month last day
                    
    
        //echo $query->createCommand()->getRawSql();
        
        if(!empty($qryGetCustomer))
        {
            /***To get id's of customers***/
            foreach($qryGetCustomer as  $key=>$val)
            {
                //echo "<pre>"; print_r($val->linkcustomerpackage->other_service_fee); exit;
                $strPackagePrice = $val->linkcustomerpackage->package_price;
                $strFkCustPckgId = $val->linkcustomerpackage->cust_pck_id;
                $strCustomerID   = $val->customer_id;
                $strInvoiceDate  = date('Y-m-d h:i:s');
                $intStateId      = $val->fk_state_id;
                $newInvoiceModel = new Customerinvoice();
                $newInvoiceModel->po_wo_number = $val->po_wo_number; // Added to add the po/wo/number 
                
                /*****If invoice start date is today's date****/
                if(date('Y-m-d',strtotime($val->linkcustomerpackage->invoice_start_date))==date('Y-m-d'))
                {
                
                    $qryGetCustInvc = Customerinvoice::find()->where(['fk_customer_id'=>$val->customer_id,'next_invoice_date'=>date('Y-m-d')])->orderBy('customer_invoice_id DESC')->one();
                    if(!empty($qryGetCustInvc))   // If customer has any previous invoice
                    {
                        if($val->linkcustomerpackage->payment_type=='term')
                        {
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = date('Y-m-d');
                            $newInvoiceModel->usage_period_to       = date('Y-m-t');
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                                
                            }
                        }elseif($val->linkcustomerpackage->payment_type=='advance'){  // FOR ADVANCE
                        
                            $advanceNextInvoiceDate =  date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH')); 
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to       = $strNextMonthLastDate;
                            $newInvoiceModel->due_date              =  $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                $updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                            }
                        }
                    }else{
                        
                        // Consider it as a first invoice and generate
                        
                        if($val->linkcustomerpackage->payment_type=='term')
                        {
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = date('Y-m-d');
                            $newInvoiceModel->usage_period_to       = date('Y-m-t');
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                            }
                        }elseif($val->linkcustomerpackage->payment_type=='advance'){  // FOR ADVANCE
                        
                            $advanceNextInvoiceDate =  date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
                            
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to       = $strNextMonthLastDate;
                            $newInvoiceModel->due_date              =  $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                                
                            }
                        }
                        
                        
                    }
                }
                // IF INVOICE START DATE IS NOT TODAY's DATE
                else
                {
                    $floatTotalDueAmt = 0;
                    $intInvoiceCount  = 0;
                    $arrDueInvoiceId  = array();
                    /*$qryGetUnpaidCustInvc = Customerinvoice::find()->where(['status'=>['partial','unpaid'],'fk_customer_id'=>$val->customer_id])->all();
                    
                    if(!empty($qryGetUnpaidCustInvc))
                    {
                        
                        $arrDueInvoiceId  = array();
                        foreach($qryGetUnpaidCustInvc as $custinvckey=>$custinvcval)
                        {
                            $intInvoiceCount++;
                            $floatTotalDueAmt +=    $custinvcval->pending_amount;
                            $arrDueInvoiceId[]   =  $custinvcval->customer_invoice_id;
                        }
                    }*/
                    
                    
                    $qryGetCustInvc = Customerinvoice::find()->where(['fk_customer_id'=>$val->customer_id,'next_invoice_date'=>date('Y-m-d')])->orderBy('customer_invoice_id DESC')->one();
                    
                        if(!empty($qryGetCustInvc))  // CUSTOMER WITH NEXT INVOICE DATE AS TODAY's DATE
                        {
                            $strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
                            $strNextUsageDateFrom=strtotime($qryGetCustInvc->next_usage_date_from);
                            $lastdayNextUsageDateFrom = date('Y-m-t',$strNextUsageDateFrom);
                            if($val->linkcustomerpackage->payment_type=='advance')
                            {
                                //$strDueDate = date('Y-m-t');
                                $strDueDate = date('Y-m-t');
                            }elseif($val->linkcustomerpackage->payment_type=='term')
                            {
                                $intTermDays = $val->linkcustomerpackage->payment_term;
                                $strDueDate =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            }
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $qryGetCustInvc->next_usage_date_from;
                            $newInvoiceModel->usage_period_to       = $lastdayNextUsageDateFrom;
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            $newInvoiceModel->last_due_amount       = $floatTotalDueAmt;
							
							// Added by shruti shah on 26/11/2018 to calculate recurring 'service fees'
							$floatTotalAmt = 0;
							if(isset($val->linkcustomerpackage->other_service_fee)){
								$newInvoiceModel->last_due_amount = $val->linkcustomerpackage->other_service_fee;
								$floatTotalAmt += $val->linkcustomerpackage->other_service_fee;
							}
							// Script to add service fees ends here
							
                            if(!empty($arrDueInvoiceId)){
                                $newInvoiceModel->last_due_invoice_id   = implode(',',$arrDueInvoiceId);
                                
                            }
                            $floatTotalAmt += $strPackagePrice;
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                //$floatCalVat1   = ($strPackagePrice*$floatVat)/100;
                                $floatCalVat1   = ($floatTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $floatTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $floatTotalAmt + $floatTotalDueAmt;
                            $newInvoiceModel->pending_amount       = $floatTotalAmt + $floatTotalDueAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $strNextUsageDate =  date('Y-m-d',strtotime($lastdayNextUsageDateFrom.'+1 days'));
                            $newInvoiceModel->next_usage_date_from = $strNextUsageDate;
                            
                            if($newInvoiceModel->save())
                            {
                                if(!empty($intInvoiceCount)){
                                    
                                    foreach($arrDueInvoiceId as $invoicekey => $invoiceval)
                                    {
                                        $command = $connection->createCommand("UPDATE tblcustomerinvoice SET status='cf' WHERE   customer_invoice_id=".$invoiceval."");
                                        $updateInvoiceStatus = $command->execute();
                                    }
                                    
                                }
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                                
                            }else{
                                echo '<pre>';
                                print_r($newInvoiceModel->getErrors());
                                die;
                            }
                            
                        }else {  // CUSTOMER DOES NOT HAVE ANY PREVIOUS INVOICE WHOSE NEXT INVOICE DATE IS TODAY's DATE[1st invoice for existing customer]
                        
                        if($val->linkcustomerpackage->payment_type=='term')
                        {
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = date('Y-m-d');
                            $newInvoiceModel->usage_period_to       = date('Y-m-t');
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                            }
                        }elseif($val->linkcustomerpackage->payment_type=='advance'){  // FOR ADVANCE
                        
                            $advanceNextInvoiceDate =  date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
                            
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to       = $strNextMonthLastDate;
                            $newInvoiceModel->due_date              =  $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                                
                            }
                        }
                            
                
                            
                        }
                    
                }
            }
            /***To get id's of customers***/
            
            
        }
        
    }
	
	
	/**
	* Cron function to generate monthly invoice backup function
	* 31st Jan 2019
	*
	*/
	public function actionMonthlycronBackup()
    {
        if(date('Y-m-d') != date('Y-m-01')){
            echo "here"; exit;
        }
          
        $arrCustId = array();
        $connection = Yii::$app->db;
        $qryGetCustomer = Customer::find()->joinWith('linkcustomerpackage')->where(['status'=>'active','is_invoice_activated'=>'yes','linkcustomepackage.payment_type'=>['term','advance'],'is_disconnected'=>'no'])->andWhere(['<=','linkcustomepackage.invoice_start_date',date("Y-m-d")])->all();
        
        $strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
        $strNextMonthLastDate = date('Y-m-d', strtotime(date('Y-m-t').' +1 MONTH'));  // To get Next month last day
                    
    
        //echo $query->createCommand()->getRawSql();
        
        if(!empty($qryGetCustomer))
        {
            /***To get id's of customers***/
            foreach($qryGetCustomer as  $key=>$val)
            {
                //echo "<pre>"; print_r($val->linkcustomerpackage->other_service_fee); exit;
                $strPackagePrice = $val->linkcustomerpackage->package_price;
                $strFkCustPckgId = $val->linkcustomerpackage->cust_pck_id;
                $strCustomerID   = $val->customer_id;
                $strInvoiceDate  = date('Y-m-d h:i:s');
                $intStateId      = $val->fk_state_id;
                $newInvoiceModel = new Customerinvoice();
                $newInvoiceModel->po_wo_number = $val->po_wo_number; // Added to add the po/wo/number 
                
                /*****If invoice start date is today's date****/
                if(date('Y-m-d',strtotime($val->linkcustomerpackage->invoice_start_date))==date('Y-m-d'))
                {
                
                    $qryGetCustInvc = Customerinvoice::find()->where(['fk_customer_id'=>$val->customer_id,'next_invoice_date'=>date('Y-m-d')])->orderBy('customer_invoice_id DESC')->one();
                    if(!empty($qryGetCustInvc))   // If customer has any previous invoice
                    {
                        if($val->linkcustomerpackage->payment_type=='term')
                        {
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = date('Y-m-d');
                            $newInvoiceModel->usage_period_to       = date('Y-m-t');
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                
                            }
                        }elseif($val->linkcustomerpackage->payment_type=='advance'){  // FOR ADVANCE
                        
                            $advanceNextInvoiceDate =  date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH')); 
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to       = $strNextMonthLastDate;
                            $newInvoiceModel->due_date              =  $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                
                            }
                        }
                    }else{
                        
                        // Consider it as a first invoice and generate
                        
                        if($val->linkcustomerpackage->payment_type=='term')
                        {
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = date('Y-m-d');
                            $newInvoiceModel->usage_period_to       = date('Y-m-t');
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                            }
                        }elseif($val->linkcustomerpackage->payment_type=='advance'){  // FOR ADVANCE
                        
                            $advanceNextInvoiceDate =  date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
                            
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to       = $strNextMonthLastDate;
                            $newInvoiceModel->due_date              =  $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                
                            }
                        }
                        
                        
                    }
                }
                // IF INVOICE START DATE IS NOT TODAY's DATE
                else
                {
                    $floatTotalDueAmt = 0;
                    $intInvoiceCount  = 0;
                    $arrDueInvoiceId  = array();
                    /*$qryGetUnpaidCustInvc = Customerinvoice::find()->where(['status'=>['partial','unpaid'],'fk_customer_id'=>$val->customer_id])->all();
                    
                    if(!empty($qryGetUnpaidCustInvc))
                    {
                        
                        $arrDueInvoiceId  = array();
                        foreach($qryGetUnpaidCustInvc as $custinvckey=>$custinvcval)
                        {
                            $intInvoiceCount++;
                            $floatTotalDueAmt +=    $custinvcval->pending_amount;
                            $arrDueInvoiceId[]   =  $custinvcval->customer_invoice_id;
                        }
                    }*/
                    
                    
                    $qryGetCustInvc = Customerinvoice::find()->where(['fk_customer_id'=>$val->customer_id,'next_invoice_date'=>date('Y-m-d')])->orderBy('customer_invoice_id DESC')->one();
                    
                        if(!empty($qryGetCustInvc))  // CUSTOMER WITH NEXT INVOICE DATE AS TODAY's DATE
                        {
                            $strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
                            $strNextUsageDateFrom=strtotime($qryGetCustInvc->next_usage_date_from);
                            $lastdayNextUsageDateFrom = date('Y-m-t',$strNextUsageDateFrom);
                            if($val->linkcustomerpackage->payment_type=='advance')
                            {
                                //$strDueDate = date('Y-m-t');
                                $strDueDate = date('Y-m-t');
                            }elseif($val->linkcustomerpackage->payment_type=='term')
                            {
                                $intTermDays = $val->linkcustomerpackage->payment_term;
                                $strDueDate =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            }
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $qryGetCustInvc->next_usage_date_from;
                            $newInvoiceModel->usage_period_to       = $lastdayNextUsageDateFrom;
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            $newInvoiceModel->last_due_amount       = $floatTotalDueAmt;
							// Added by shruti shah on 26/11/2018 to calculate recurring 'service fees'
							 $floatTotalAmt = 0;
							if(isset($val->linkcustomerpackage->other_service_fee)){
								$newInvoiceModel->last_due_amount = $val->linkcustomerpackage->other_service_fee;
								$floatTotalAmt += $val->linkcustomerpackage->other_service_fee;
							}
							// Script to add service fees ends here
                            if(!empty($arrDueInvoiceId)){
                                $newInvoiceModel->last_due_invoice_id   = implode(',',$arrDueInvoiceId);
                                
                            }
                            $floatTotalAmt  +=  $strPackagePrice;
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                //$floatCalVat1   = ($strPackagePrice*$floatVat)/100; 
                                $floatCalVat1   = ($floatTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $floatTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $floatTotalAmt + $floatTotalDueAmt;
                            $newInvoiceModel->pending_amount       = $floatTotalAmt + $floatTotalDueAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $strNextUsageDate =  date('Y-m-d',strtotime($lastdayNextUsageDateFrom.'+1 days'));
                            $newInvoiceModel->next_usage_date_from = $strNextUsageDate;
                            
                            if($newInvoiceModel->save())
                            {
                                if(!empty($intInvoiceCount)){
                                    
                                    foreach($arrDueInvoiceId as $invoicekey => $invoiceval)
                                    {
                                        $command = $connection->createCommand("UPDATE tblcustomerinvoice SET status='cf' WHERE   customer_invoice_id=".$invoiceval."");
                                        $updateInvoiceStatus = $command->execute();
                                    }
                                    
                                }
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                
                            }else{
                                echo '<pre>';
                                print_r($newInvoiceModel->getErrors());
                                die;
                            }
                            
                        }else {  // CUSTOMER DOES NOT HAVE ANY PREVIOUS INVOICE WHOSE NEXT INVOICE DATE IS TODAY's DATE[1st invoice for existing customer]
                        
                        if($val->linkcustomerpackage->payment_type=='term')
                        {
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = date('Y-m-d');
                            $newInvoiceModel->usage_period_to       = date('Y-m-t');
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                            }
                        }elseif($val->linkcustomerpackage->payment_type=='advance'){  // FOR ADVANCE
                        
                            $advanceNextInvoiceDate =  date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
                            
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to       = $strNextMonthLastDate;
                            $newInvoiceModel->due_date              =  $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                
                            }
                        }
                            
                
                            
                        }
                    
                }
            }
            /***To get id's of customers***/
            
            
        }
        
    }
	
	
	/**
	* Cron function to generate monthly invoice
	*
	*/
	public function actionDailycron()
	{
	   if(date('Y-m-d') == date('Y-m-01')){
			exit;
		}
		$arrCustId = array();
		$connection = Yii::$app->db;
		$qryGetCustomer = Customer::find()->joinWith('linkcustomerpackage')->where(['status'=>'active','is_invoice_activated'=>'yes','linkcustomepackage.payment_type'=>['term','advance'],'is_disconnected'=>'no','is_deleted'=>'0'])->andWhere(['=','linkcustomepackage.invoice_start_date',date("Y-m-d")])->all();
		
		$strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
		$strNextMonthLastDate = date('Y-m-d', strtotime(date('Y-m-t').' +1 MONTH'));  // To get Next month last day
		if(!empty($qryGetCustomer)){
			foreach($qryGetCustomer as $key=>$val)	
			{
				$intCustomreId =  $val->customer_id;
				Yii::$app->customcomponents->GenerateInvoice($intCustomreId,'activate');
			}
		}
	}
	
    public function actionState()
    {
        if(isset($_POST))
        {

            $state = $_POST['state_id'];
            if($state=='all')
            {
                $sessionData = Yii::$app->session;
                $sessionData->set('user_state_id','all');
            }
            else
            {
                $sessionData = Yii::$app->session;
                $sessionData->set('user_state_id',$state);
            }
            $logArray = array();
            $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
            $logArray['module'] = 'Update State from State filter';
            $logArray['action'] = 'update';
            $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the state from state filter';
            $logArray['created'] = date('Y-m-d H:i:s');
            Yii::$app->customcomponents->logActivity($logArray);
            Yii::$app->session->setFlash('success', 'State changed successfully');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }	
	
    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionError($name='',$message='')
	 {
		  
		 return $this->render('error', ['name'=>$name,'message'=>$message]);


	 }
	 
	public function actionMonthlyservicecron()
    {
        if(date('Y-m-d') != date('Y-m-01')){
            echo "here"; exit;
        }
         // ini_set('max_execution_time', 0);
        $arrCustId = array();
        $connection = Yii::$app->db;
        $qryGetCustomer = Customer::find()->joinWith('linkcustomerpackage')->where(['status'=>'active','is_invoice_activated'=>'yes','linkcustomepackage.payment_type'=>['term','advance'],'is_disconnected'=>'no'])->andWhere(['<=','linkcustomepackage.invoice_start_date',date("Y-m-d")])->andWhere(['service_invoice_generated' => '0'])->all();
        
        $strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
        $strNextMonthLastDate = date('Y-m-d', strtotime(date('Y-m-t').' +1 MONTH'));  // To get Next month last day
                    
    
        //echo $query->createCommand()->getRawSql();
        
        if(!empty($qryGetCustomer))
        {
            /***To get id's of customers***/
            foreach($qryGetCustomer as  $key=>$val)
            {
                //echo "<pre>"; print_r($val->linkcustomerpackage->other_service_fee); exit;
                $strPackagePrice = $val->linkcustomerpackage->package_price;
                $strFkCustPckgId = $val->linkcustomerpackage->cust_pck_id;
                $strCustomerID   = $val->customer_id;
                $strInvoiceDate  = date('Y-m-d h:i:s');
                $intStateId      = $val->fk_state_id;
                $newInvoiceModel = new Customerinvoice();
                $newInvoiceModel->po_wo_number = $val->po_wo_number; // Added to add the po/wo/number 
                
                /*****If invoice start date is today's date****/
                if(date('Y-m-d',strtotime($val->linkcustomerpackage->invoice_start_date))==date('Y-m-d'))
                {
                
                    $qryGetCustInvc = Customerinvoice::find()->where(['fk_customer_id'=>$val->customer_id,'next_invoice_date'=>date('Y-m-d')])->andWhere(['invoice_type' => 'service'])->orderBy('customer_invoice_id DESC')->one();
                    if(!empty($qryGetCustInvc))   // If customer has any previous invoice
                    {
                        if($val->linkcustomerpackage->payment_type=='term')
                        {
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = date('Y-m-d');
                            $newInvoiceModel->usage_period_to       = date('Y-m-t');
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                                
                            }
                        }elseif($val->linkcustomerpackage->payment_type=='advance'){  // FOR ADVANCE
                        
                            $advanceNextInvoiceDate =  date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH')); 
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to       = $strNextMonthLastDate;
                            $newInvoiceModel->due_date              =  $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                $updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                            }
                        }
                    }else{
                        
                        // Consider it as a first invoice and generate
                        
                        if($val->linkcustomerpackage->payment_type=='term')
                        {
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = date('Y-m-d');
                            $newInvoiceModel->usage_period_to       = date('Y-m-t');
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                            }
                        }elseif($val->linkcustomerpackage->payment_type=='advance'){  // FOR ADVANCE
                        
                            $advanceNextInvoiceDate =  date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
                            
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to       = $strNextMonthLastDate;
                            $newInvoiceModel->due_date              =  $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                                
                            }
                        }
                        
                        
                    }
                }
                // IF INVOICE START DATE IS NOT TODAY's DATE
                else
                {
                    $floatTotalDueAmt = 0;
                    $intInvoiceCount  = 0;
                    $arrDueInvoiceId  = array();
                    /*$qryGetUnpaidCustInvc = Customerinvoice::find()->where(['status'=>['partial','unpaid'],'fk_customer_id'=>$val->customer_id])->all();
                    
                    if(!empty($qryGetUnpaidCustInvc))
                    {
                        
                        $arrDueInvoiceId  = array();
                        foreach($qryGetUnpaidCustInvc as $custinvckey=>$custinvcval)
                        {
                            $intInvoiceCount++;
                            $floatTotalDueAmt +=    $custinvcval->pending_amount;
                            $arrDueInvoiceId[]   =  $custinvcval->customer_invoice_id;
                        }
                    }*/
                    
                    
                    $qryGetCustInvc = Customerinvoice::find()->where(['fk_customer_id'=>$val->customer_id,'next_invoice_date'=>date('Y-m-d')])->orderBy('customer_invoice_id DESC')->one();
                    
                        if(!empty($qryGetCustInvc))  // CUSTOMER WITH NEXT INVOICE DATE AS TODAY's DATE
                        {
                            $strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
                            $strNextUsageDateFrom=strtotime($qryGetCustInvc->next_usage_date_from);
                            $lastdayNextUsageDateFrom = date('Y-m-t',$strNextUsageDateFrom);
                            if($val->linkcustomerpackage->payment_type=='advance')
                            {
                                //$strDueDate = date('Y-m-t');
                                $strDueDate = date('Y-m-t');
                            }elseif($val->linkcustomerpackage->payment_type=='term')
                            {
                                $intTermDays = $val->linkcustomerpackage->payment_term;
                                $strDueDate =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            }
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $qryGetCustInvc->next_usage_date_from;
                            $newInvoiceModel->usage_period_to       = $lastdayNextUsageDateFrom;
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            $newInvoiceModel->last_due_amount       = $floatTotalDueAmt;
							
							// Added by shruti shah on 26/11/2018 to calculate recurring 'service fees'
							$floatTotalAmt = 0;
							if(isset($val->linkcustomerpackage->other_service_fee)){
								$newInvoiceModel->last_due_amount = $val->linkcustomerpackage->other_service_fee;
								$floatTotalAmt += $val->linkcustomerpackage->other_service_fee;
							}
							// Script to add service fees ends here
							
                            if(!empty($arrDueInvoiceId)){
                                $newInvoiceModel->last_due_invoice_id   = implode(',',$arrDueInvoiceId);
                                
                            }
                            $floatTotalAmt += $strPackagePrice;
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                //$floatCalVat1   = ($strPackagePrice*$floatVat)/100;
                                $floatCalVat1   = ($floatTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $floatTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $floatTotalAmt + $floatTotalDueAmt;
                            $newInvoiceModel->pending_amount       = $floatTotalAmt + $floatTotalDueAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $strNextUsageDate =  date('Y-m-d',strtotime($lastdayNextUsageDateFrom.'+1 days'));
                            $newInvoiceModel->next_usage_date_from = $strNextUsageDate;
                            
                            if($newInvoiceModel->save())
                            {
                                if(!empty($intInvoiceCount)){
                                    
                                    foreach($arrDueInvoiceId as $invoicekey => $invoiceval)
                                    {
                                        $command = $connection->createCommand("UPDATE tblcustomerinvoice SET status='cf' WHERE   customer_invoice_id=".$invoiceval."");
                                        $updateInvoiceStatus = $command->execute();
                                    }
                                    
                                }
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                                
                            }else{
                                echo '<pre>';
                                print_r($newInvoiceModel->getErrors());
                                die;
                            }
                            
                        }else {  // CUSTOMER DOES NOT HAVE ANY PREVIOUS INVOICE WHOSE NEXT INVOICE DATE IS TODAY's DATE[1st invoice for existing customer]
                        
                        if($val->linkcustomerpackage->payment_type=='term')
                        {
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = date('Y-m-d');
                            $newInvoiceModel->usage_period_to       = date('Y-m-t');
                            $newInvoiceModel->due_date              = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                            }
                        }elseif($val->linkcustomerpackage->payment_type=='advance'){  // FOR ADVANCE
                        
                            $advanceNextInvoiceDate =  date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
                            
                            
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->linkcustomerpackage->payment_term;
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number        = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id        = $strCustomerID;
                            $newInvoiceModel->invoice_type          = 'normal';
                            $newInvoiceModel->invoice_date          = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from     = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to       = $strNextMonthLastDate;
                            $newInvoiceModel->due_date              =  $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id       = $strFkCustPckgId;
                            $newInvoiceModel->current_invoice_amount= $strPackagePrice;
                            //$floatInstallationFee = $qryGetCustInvc->installation_fee;
                            //$floatOtherFee = $qryGetCustInvc->other_service_fee;
                            $intTotalAmt   = $strPackagePrice;
                            if(!empty($val->linkcustomerpackage->installation_fee))
                            {
                                $newInvoiceModel->installation_fee = $val->linkcustomerpackage->installation_fee;
                                $intTotalAmt += $val->linkcustomerpackage->installation_fee;
                            }
                            if(!empty($val->linkcustomerpackage->other_service_fee))
                            {
                                $newInvoiceModel->other_service_fee = $val->linkcustomerpackage->other_service_fee;
                                $intTotalAmt += $val->linkcustomerpackage->other_service_fee;
                            }
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
								$updateInvoiceStatus = Yii::$app->customcomponents->updateInvoiceStatus($strCustomerID);
                                
                            }
                        }
                            
                
                            
                        }
                    
                }
            }
            /***To get id's of customers***/
            
            
        }
        
    }
	
	public function actionTestcron(){
		exit;
		Yii::$app->mailer->compose()
			->setFrom('eluminous_se32@eluminoustechnologies.com')
			->setTo('eluminous_se32@eluminoustechnologies.com')
			->setSubject('Batam cron testing')
			->setTextBody('Test mail')
			->setHtmlBody('<b>Test mail</b>')
			->send();
	}
	
	
	public function actionMonthlyservicecronnew()
    {
        // if(date('Y-m-d') != date('Y-m-01')){
        //     echo "here"; exit;
        // }

        ini_set('max_execution_time', 0);
        
        $arrCustId  = array();
        $connection = Yii::$app->db;
        
        //$qryGetCustomer = Customer::find()->joinWith('servicecustomer')->where(['status'=>'active','is_invoice_activated'=>'yes'])->andWhere(['invoice_generated' => '0'])->all();

        $qryGetCustomer = CustomerService::find()->joinWith('customer')->all();
        //echo "<pre>"; print_r($qryGetCustomer); exit;
        $strNextMonthFirstDate = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
        $strNextMonthLastDate  = date('Y-m-d', strtotime(date('Y-m-t').' +1 MONTH'));  // To get Next month last day
        $strServiceTotal = 0;
        if(!empty($qryGetCustomer))
        {
            /***To get id's of customers***/
            foreach($qryGetCustomer as  $key=>$val)
            {
                $modelLinkCustPackage  = Linkcustomepackage::find()->where(['fk_customer_id'=>$val->fk_customer_id,'is_current_package'=>'yes'])->one();
				$strServiceTotal  = Yii::$app->customcomponents->getServicePrice($val->customer_service_id); 
                $strFkCustPckgId  = $modelLinkCustPackage->cust_pck_id;
                $strPackagePrice  = '0';
                $strCustomerID    = $val->customer->customer_id;
                $strInvoiceDate   = date('Y-m-d h:i:s');
                $intStateId       = $val->customer->fk_state_id;
                $newInvoiceModel  = new Customerinvoice();
                $newInvoiceModel->po_wo_number = $val->customer->po_wo_number; // Added to add the po/wo/number 
                
                /*****If invoice start date is today's date****/
                // echo date('Y-m-d',strtotime($val->s_invoice_start_date));echo '<br/>';
                // echo date('Y-m-d');echo '<br/>';
                
                if(date('Y-m-d',strtotime($val->s_invoice_start_date)) == date('Y-m-d'))
                {
                    $qryGetCustInvc = Customerinvoice::find()->where(['fk_customer_id'=>$val->fk_customer_id,'next_invoice_date'=>date('Y-m-d'),'invoice_type'=>'service'])->orderBy('customer_invoice_id DESC')->one();
                    if(!empty($qryGetCustInvc))   // If customer has any previous invoice
                    {
                        //die('if');
                        if($val->payment_type == 'term')
                        {
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->term_period;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number         = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id         = $strCustomerID;
                            $newInvoiceModel->invoice_type           = 'service';
                            $newInvoiceModel->invoice_date           = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from      = date('Y-m-d');
                            $newInvoiceModel->usage_period_to        = date('Y-m-t');
                            $newInvoiceModel->due_date               = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id        = $strFkCustPckgId;
                            //$strServiceTotal                    = Yii::$app->customcomponents->getServicePrice($val->customer_service_id);
                            $newInvoiceModel->current_invoice_amount = $strServiceTotal;
                            
                            $intTotalAmt   = $strServiceTotal;
                            
                            $newInvoiceModel->installation_fee = 0;
                            
                            $floatVat = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1         = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat          = round($floatCalVat1);
                                $newInvoiceModel->vat = $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
							
							
                            
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                $updateInvoiceStatus = Yii::$app->customcomponents->updateServiceInvoiceStatus($strCustomerID);
                            }
                        }
                        elseif($val->payment_type == 'advance')  // FOR ADVANCE
                        {
                            $advanceNextInvoiceDate = date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            //echo "<pre>"; print_r($arrInvoice); exit;

                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number         = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id         = $strCustomerID;
                            $newInvoiceModel->invoice_type           = 'service';
                            $newInvoiceModel->invoice_date           = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from      = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to        = $strNextMonthLastDate;
                            $newInvoiceModel->due_date               = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id        = $strFkCustPckgId;
                            $strServiceTotal                         = Yii::$app->customcomponents->getServicePrice($val->customer_service_id);
                            $newInvoiceModel->current_invoice_amount = $strServiceTotal;

                            $intTotalAmt   = $strServiceTotal;
                            $newInvoiceModel->installation_fee = 0;
                            $newInvoiceModel->other_service_fee = 0;

                            $floatVat = Yii::$app->customcomponents->GetVat($intStateId);
                            
                            if(!empty($floatVat)){
                                $floatCalVat1         = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat          = round($floatCalVat1);
                                $newInvoiceModel->vat = $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }

                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                $updateInvoiceStatus = Yii::$app->customcomponents->updateServiceInvoiceStatus($strCustomerID);
                            }
                        }
                    }
                    else
                    {
                        //die('else');
                        if($val->payment_type == 'term')
                        {
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->term_period;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number         = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id         = $strCustomerID;
                            $newInvoiceModel->invoice_type           = 'service';
                            $newInvoiceModel->invoice_date           = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from      = date('Y-m-d');
                            $newInvoiceModel->usage_period_to        = date('Y-m-t');
                            $newInvoiceModel->due_date               = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id        = $strFkCustPckgId;
                            $strServiceTotal                         = Yii::$app->customcomponents->getServicePrice($val->customer_service_id);
                            $newInvoiceModel->current_invoice_amount = $strServiceTotal;
                            
                            $intTotalAmt   = $strServiceTotal;
                            //$intTotalAmt   = $strPackagePrice;
                            $newInvoiceModel->installation_fee = 0;
                            
                            $floatVat  = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat= $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
							echo "<pre>"; print_r($newInvoiceModel); die;
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                $updateInvoiceStatus = Yii::$app->customcomponents->updateServiceInvoiceStatus($strCustomerID);
                            }
                        }
                        elseif($val->payment_type == 'advance')  // FOR ADVANCE
                        { 
                            $advanceNextInvoiceDate = date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            //echo "<pre>"; print_r($arrInvoice); exit;

                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number         = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id         = $strCustomerID;
                            $newInvoiceModel->invoice_type           = 'service';
                            $newInvoiceModel->invoice_date           = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from      = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to        = $strNextMonthLastDate;
                            $newInvoiceModel->due_date               = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id        = $strFkCustPckgId;
                            $strServiceTotal                         = Yii::$app->customcomponents->getServicePrice($val->customer_service_id);
                            $newInvoiceModel->current_invoice_amount = $strServiceTotal;


                            $intTotalAmt   = $strServiceTotal;
                            $newInvoiceModel->installation_fee = 0;
                            $newInvoiceModel->other_service_fee = 0;

                            $floatVat = Yii::$app->customcomponents->GetVat($intStateId);
                            
                            if(!empty($floatVat)){
                                $floatCalVat1         = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat          = round($floatCalVat1);
                                $newInvoiceModel->vat = $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }

                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                $updateInvoiceStatus = Yii::$app->customcomponents->updateServiceInvoiceStatus($strCustomerID);
                            }
                        }
                    }
                }
                else
                {
                    //die('else');
                    $floatTotalDueAmt = 0;
                    $intInvoiceCount  = 0;
                    $arrDueInvoiceId  = array();

                    $qryGetCustInvc = Customerinvoice::find()->where(['fk_customer_id'=>$val->fk_customer_id,'next_invoice_date'=>date('Y-m-d'),'invoice_type'=>'service'])->orderBy('customer_invoice_id DESC')->one();
                    
                    if (!empty($qryGetCustInvc))  // CUSTOMER WITH NEXT INVOICE DATE AS TODAY's DATE
                    {
						
                        $strNextMonthFirstDate    = date('Y-m-d', strtotime(date('Y-m-01').' +1 MONTH'));  // To get Next month 1st day
                        $strNextUsageDateFrom     = strtotime($qryGetCustInvc->next_usage_date_from);
                        $lastdayNextUsageDateFrom = date('Y-m-t',$strNextUsageDateFrom);
                        
                        if($val->payment_type == 'advance')
                        {
                            $strDueDate = date('Y-m-t');
                        }
                        elseif($val->payment_type == 'term')
                        {
                            $intTermDays = $val->term_period;
                            $strDueDate =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                        }
                        $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                        if(!empty($arrInvoice))
                        {
                            $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                        }
                        $newInvoiceModel->invoice_number         = $strInvoiceNumber;
                        $newInvoiceModel->fk_customer_id         = $strCustomerID;
                        $newInvoiceModel->invoice_type           = 'service';
                        $newInvoiceModel->invoice_date           = $strInvoiceDate;
                        $newInvoiceModel->usage_period_from      = $qryGetCustInvc->next_usage_date_from;
                        $newInvoiceModel->usage_period_to        = $lastdayNextUsageDateFrom;
                        $newInvoiceModel->due_date               = $strDueDate;
                        $newInvoiceModel->fk_cust_pckg_id        = $strFkCustPckgId;
                        $newInvoiceModel->current_invoice_amount = Yii::$app->customcomponents->getServicePrice($val->customer_service_id);
                        $newInvoiceModel->last_due_amount        = $floatTotalDueAmt;
                        
                       
                        $floatTotalAmt = 0;
                        $newInvoiceModel->last_due_amount = 0;

                        if(!empty($arrDueInvoiceId)){
                            $newInvoiceModel->last_due_invoice_id   = implode(',',$arrDueInvoiceId);
                        }
                        $floatTotalAmt += $strPackagePrice;
                        $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                        
                        if(!empty($floatVat)){
                            $floatCalVat1   = ($floatTotalAmt*$floatVat)/100;
                            $floatCalVat    = round($floatCalVat1);
                            $newInvoiceModel->vat = $floatCalVat;
                            $floatTotalAmt  += $floatCalVat;
                        }
                        $newInvoiceModel->total_invoice_amount = $floatTotalAmt + $floatTotalDueAmt;
                        $newInvoiceModel->pending_amount       = $floatTotalAmt + $floatTotalDueAmt;
                        $newInvoiceModel->status               = 'unpaid';
                        $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                        $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                        $strNextUsageDate                      = date('Y-m-d',strtotime($lastdayNextUsageDateFrom.'+1 days'));
                        $newInvoiceModel->next_usage_date_from = $strNextUsageDate;
                        
                        if($newInvoiceModel->save())
                        {
                            if(!empty($intInvoiceCount))
                            {
                                foreach($arrDueInvoiceId as $invoicekey => $invoiceval)
                                {
                                    $command = $connection->createCommand("UPDATE tblcustomerinvoice SET status='cf' WHERE   customer_invoice_id=".$invoiceval."");
                                    $updateInvoiceStatus = $command->execute();
                                }
                            }
                            $arrInvoice          = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                            $updateInvoiceStatus = Yii::$app->customcomponents->updateServiceInvoiceStatus($strCustomerID);
                            
                        }else{
                            echo '<pre>';
                            print_r($newInvoiceModel->getErrors());
                            die;
                        }
                    }
                    else
                    {
						
                        if($val->payment_type == 'term')
                        {
                           
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $intTermDays = $val->term_period;
                            $strDueDate  =  date('Y-m-d',strtotime('+'.$intTermDays.'days'));
                            $newInvoiceModel->invoice_number         = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id         = $strCustomerID;
                            $newInvoiceModel->invoice_type           = 'service';
                            $newInvoiceModel->invoice_date           = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from      = date('Y-m-d');
                            $newInvoiceModel->usage_period_to        = date('Y-m-t');
                            $newInvoiceModel->due_date               = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id        = $strFkCustPckgId;
                            $strServiceTotal                         = Yii::$app->customcomponents->getServicePrice($val->customer_service_id);
                            $newInvoiceModel->current_invoice_amount = $strServiceTotal;
                            
                            $intTotalAmt   = $strServiceTotal;
                            
                            $newInvoiceModel->installation_fee = 0;
                            
                            $floatVat       = Yii::$app->customcomponents->GetVat($intStateId);
                            if(!empty($floatVat)){
                                $floatCalVat1   = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat    = round($floatCalVat1);
                                $newInvoiceModel->vat = $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }
                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $strNextMonthFirstDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
							
                            if($newInvoiceModel->save())
                            {
                                
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                $updateInvoiceStatus = Yii::$app->customcomponents->updateServiceInvoiceStatus($strCustomerID);
                                
                            }
                        }
                        elseif ($val->payment_type == 'advance')
                        {
							
							
                            $advanceNextInvoiceDate = date('Y-m-d', strtotime(date('Y-m-01').' +2 MONTH'));
                            $arrInvoice = Yii::$app->customcomponents->GetInvoiceId($intStateId);
                           

                            if(!empty($arrInvoice))
                            {
                                $strInvoiceNumber = $arrInvoice['current_invoice_id'];
                            }
                            $strDueDate  =  date('Y-m-t');
                            $newInvoiceModel->invoice_number         = $strInvoiceNumber;
                            $newInvoiceModel->fk_customer_id         = $strCustomerID;
                            $newInvoiceModel->invoice_type           = 'service';
                            $newInvoiceModel->invoice_date           = $strInvoiceDate;
                            $newInvoiceModel->usage_period_from      = $strNextMonthFirstDate;
                            $newInvoiceModel->usage_period_to        = $strNextMonthLastDate;
                            $newInvoiceModel->due_date               = $strDueDate;
                            $newInvoiceModel->fk_cust_pckg_id        = $strFkCustPckgId;
                            $strServiceTotal =  Yii::$app->customcomponents->getServicePrice($val->customer_service_id);
                            
                            $newInvoiceModel->current_invoice_amount = $strServiceTotal;
                            
                            $intTotalAmt   = $strServiceTotal;
                            $newInvoiceModel->installation_fee = 0;
                            $newInvoiceModel->other_service_fee = 0;
                            

                            $floatVat = Yii::$app->customcomponents->GetVat($intStateId);
                            

                            if(!empty($floatVat)){
                                $floatCalVat1         = ($intTotalAmt*$floatVat)/100;
                                $floatCalVat          = round($floatCalVat1);
                                $newInvoiceModel->vat = $floatCalVat;
                                $intTotalAmt  += $floatCalVat;
                            }

                            $newInvoiceModel->total_invoice_amount = $intTotalAmt;
                            $newInvoiceModel->pending_amount       = $intTotalAmt;
                            $newInvoiceModel->status               = 'unpaid';
                            $newInvoiceModel->next_invoice_date    = $strNextMonthFirstDate;
                            $newInvoiceModel->next_usage_date_from = $advanceNextInvoiceDate;
                            $newInvoiceModel->created_at           = date('Y-m-d h:i:s');
                            if($newInvoiceModel->save())
                            {
                                $arrInvoice = Yii::$app->customcomponents->updateInvoiceIncrementValue($intStateId,$arrInvoice['increment_value']);
                                $updateInvoiceStatus = Yii::$app->customcomponents->updateServiceInvoiceStatus($strCustomerID);
                            }
                        }
                    }
                }
            }
        }
    }
	
	public function actionMicrotik(){
		
		$postfields = [
			  'secret' => 'somerandomsecret',
			  'ipAddress' => '192.168.1.115'
			];

			$curl = curl_init();

			// Everything here can stay the same, except for the CURLOPT_URL, suspend vs unsuspend.  Or if the server IP changes.  Ideally, we will set a DNS name for this.
			curl_setopt_array($curl, [
			  CURLOPT_URL => "http://103.11.141.15/api/subscriber/suspend",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => http_build_query($postfields),
			  CURLOPT_HTTPHEADER => [
				"Content-Type: application/x-www-form-urlencoded"
			  ],
			]);

			// This executes the POST request, captures an error if there was one and echos.
			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  echo "cURL Error #:" . $err;
			} else {
			  echo $response;
			}
		
	}
	 
	 
	 
	 
}
