<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : CustomerpackageController.php
# Created on : 20th June 2017 by Suraj Malve.
# Update on  : 20th June 2017 by Suraj Malve.
# Purpose : Manage Customer's package.
############################################################################################
*/

namespace app\controllers;

use Yii;
use app\models\Linkcustomepackage;
use app\models\LinkcustomepackageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use yii\filters\AccessControl;
use app\models\Customer;
use app\models\TblCustomerContractFiles;
use yii\web\UploadedFile;

/**
 * CustomerpackageController implements the CRUD actions for Linkcustomepackage model.
 */
class CustomerpackageController extends Controller
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
                        'only' => ['disconnectreport','disconnectview','print','customercontract','printcontract',
                        'contractview','editcontract','submitcontract'],
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
     * Lists all Linkcustomepackage models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LinkcustomepackageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Linkcustomepackage model.
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
     * Creates a new Linkcustomepackage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Linkcustomepackage();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cust_pck_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Linkcustomepackage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cust_pck_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


     /**
     * Lists all diconnection reports.
     * @return mixed
     */
    public function actionDisconnectreport()
    {
        $searchModel = new LinkcustomepackageSearch();
        $modelCustomer = new Customer();
        $arrSalesPerson = array();
        $arrSalesPerson = $modelCustomer->getUserName();
        $queryParams = Yii::$app->request->queryParams;
       
          if(isset($_REQUEST['start_date']) && 
                isset($_REQUEST['end_date']) ){
           $strStartDate = $_REQUEST['start_date'];
           $strEndDate  = $_REQUEST['end_date'];
           $queryParams['LinkcustomepackageSearch']['start_date']   = $strStartDate;
           $queryParams['LinkcustomepackageSearch']['end_date']  = $strEndDate;
        
        }
       
        // if(Yii::$app->user->identity->fk_role_id=='3')
             // {
                // $queryParams['LinkcustomepackageSearch']['CustomerSearch.fk_user_id']=Yii::$app->user->identity->user_id;
             // }
        $dataProvider = $searchModel->searchDisconnectRecord($queryParams);


        return $this->render('disconnect_report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user'=>$arrSalesPerson
        ]);
    }

    public function actionUpdatecomment($id)
    {
        /*******To fetch data from Linkcustome package*****/
        $model = Linkcustomepackage::find()->where(['cust_pck_id'=>$id])->one();
        if ($model->load(Yii::$app->request->post()) ) {
            $arrPostData   = Yii::$app->request->post();
            //echo '<pre>';print_r();die;
            $model->reason_for_disconnection = $arrPostData['Linkcustomepackage']['reason_for_disconnection'];
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
                return $this->redirect(['customerpackage/disconnectreport']);
            }
        }
        return  $this->renderAjax('updatecomment',
        [
            'model' => $model,
            'id'    =>  $id
        ]);
    }
    
     /**
     * Generate a pdf to print single diconnection report details .
     * @param integer $id
     * @return mixed
     */
    public function actionPrint($id) {
        $model =  $this->findModel($id);
        
            $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'content' => $this->renderPartial('disconnect_view', [
                'model' => $this->findModel($id),
            ]),
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'options' => [
                'title' => 'Disconnection Details',
                'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
            ],
            'methods' => [
                //'SetHeader' => ['Generated By: Solnet'],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
            $logArray = array();
            $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
            $logArray['module'] = 'Disconnection Report';
            $logArray['action'] = 'update';
            $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has printed the disconnection report of "'. $model->customer->name.'"' ;
            $logArray['created'] = date('Y-m-d H:i:s');
            Yii::$app->customcomponents->logActivity($logArray);
            
            return $pdf->render();
          
    }


     /**
     * Displays a single disconnection report.
     * @param integer $id
     * @return mixed
     */
    public function actionDisconnectview($id)
    {
        return $this->render('disconnect_view', [
            'model' => $this->findModel($id),
        ]);
    }


    
    /*  /**
    * list contract report of all custoemr
    */
     public function actionCustomercontract()
    {
        $searchModel = new LinkcustomepackageSearch();
        $modelCustomer = new Customer();
        $arrSalesPerson = array();
        $arrSalesPerson = $modelCustomer->getUserName();
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['LinkcustomepackageSearch']['is_deleted']=0;

        if(isset($_REQUEST['start_date']) && 
                isset($_REQUEST['end_date']) &&
                 isset($_REQUEST['fromDate'])){
            $strStartDate = $_REQUEST['start_date'];
            $strEndDate  = $_REQUEST['end_date'];
            $strFromDate  = $_REQUEST['from_date'];
 
            $queryParams['LinkcustomepackageSearch']['start_date']   = $strStartDate;
            $queryParams['LinkcustomepackageSearch']['end_date']  = $strEndDate;
            $queryParams['LinkcustomepackageSearch']['from_date']  = $strFromDate;
        }
		
		$dataProvider = $searchModel->searchContractRecord($queryParams);
	
        return $this->render('customer_contract', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user'=>$arrSalesPerson
        ]);
    }


     /**
     * Generate a pdf to print single customer contract report details .
     * @param integer $id
     * @return mixed
     */
    public function actionPrintcontract($id) {
        $model =  $this->findModel($id);
        $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
        'content' => $this->renderPartial('contract_view', [
            'model' => $this->findModel($id),
        ]),
        'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        'options' => [
            'title' => 'Customer Contract Details',
            'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
        ],
        'methods' => [
            //'SetHeader' => ['Generated By: Solnet '],
            'SetFooter' => ['|Page {PAGENO}|'],
        ]
    ]);
        $logArray = array();
        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
        $logArray['module'] = 'Disconnection Report';
        $logArray['action'] = 'update';
        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has printed the disconnection report of "'. $model->customer->name.'"' ;
        $logArray['created'] = date('Y-m-d H:i:s');
        Yii::$app->customcomponents->logActivity($logArray);
        
        return $pdf->render();
    }

     /**
     * Displays a single customer contract report.
     * @param integer $id
     * @return mixed
     */
    public function actionContractview($id)
    {
        $session = Yii::$app->session;
        $model   = $this->findModel($id);
        $customerContractFilesModel = new TblCustomerContractFiles();

        $arrCustomerData = $customerContractFilesModel::find()->where(['customer_id'=>$model->fk_customer_id])->orderBy(['customer_contract_id' => SORT_DESC])->all();
        
        if(Yii::$app->request->post()){
            //echo '<pre>';print_r($_POST);echo '<br/>';
            //echo '<pre>count >>>>';print_r(count($_FILES));echo '<br/>';
            $custModel   = new TblCustomerContractFiles();
            $arrPostData = Yii::$app->request->post();
            
            $filesArrayReceipt = UploadedFile::getInstances($customerContractFilesModel, 'file_name');

            echo '<pre>';print_r($filesArrayReceipt);echo '</pre>';die;
            
            $intTimeStamp    = date('Ymdhis').rand(10,100000);
            $supported_image = array('pdf','jpg','png','jpeg','xls','csv','doc');
                if ($filesArrayReceipt) {
                    $intI = 0;
                    foreach ($filesArrayReceipt as $file)
                    {
                        if($file->error != '1')
                        {
                            if (in_array($file->extension, $supported_image))
                            {
                                $file->saveAs('uploads/contract_files/' . $file->baseName .'_'.$intTimeStamp .'.'. $file->extension);
                                $strfilepName = $file->baseName .'_'.$intTimeStamp .'.'. $file->extension;   
                                
                                $connection = Yii::$app->getDb();    
                                
                                $connection->createCommand()->insert('tbl_customer_contract_files',
                                [
                                    'customer_id'   => $arrPostData['intCustomerId'],
                                    'file_name'     => $strfilepName,
                                    'description'   => $arrPostData['description'][$intI],
                                    'created_at'    => date('Y-m-d h:i:s'),
                                ])->execute();
                                $intI++;
                                $session->setFlash('success_msg','Image Uploaded successfully');
                            }
                            else
                            {
                                $session->setFlash('error_msg','Your Only Allowed JPG and PNG Images');

                            }
                        }
                        else
                        {
                            $session->setFlash('error_msg','File too large. File must be less than 1 MB.');
                        }
                    }
                   
                }
            return $this->redirect(Yii::$app->request->referrer);
        }
        
        return $this->render('contract_view', [
            'model'                      => $model,
            'arrCustomerData'            => $arrCustomerData,
            'customerContractFilesModel' => $customerContractFilesModel,
        ]);

        // return $this->render('contract_view', [
        //     'model' => $this->findModel($id),
        // ]);
    }
        
    public function actionDeletedoc($id)
    {
        if($id!="")
        {
            $customerContractFilesModel = new TblCustomerContractFiles();
            
            $arrCustomerFilesDetails = $customerContractFilesModel::find()->where(['customer_contract_id' => $id])->one();
            //echo '<pre>';echo '<br/>';print_r($arrCustomerFilesDetails);die;
            if($arrCustomerFilesDetails)
            {
                if($arrCustomerFilesDetails->file_name){
                    $strImgName = $arrCustomerFilesDetails->file_name;
                    unlink(getcwd().'/uploads/contract_files/'.$strImgName);
                }
                
                $deleteCustomerFiles = $arrCustomerFilesDetails->delete();

                if($deleteCustomerFiles){
                    $session    = Yii::$app->session;
                    $session->setFlash('success_msg','Image deleted successfully');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }
    }
    
    /**
    * popup modal of specific customer contract report details to edit his contract
    */
     public function actionEditcontract($id)
    {    
        /*******To fetch  customer from table*****/
          $model = Linkcustomepackage::findOne($id);
 
         return  $this->renderAjax('edit_contract',['model'=>$model,
            'id'=>$id]);
    }


    /**
    * submit contract status and contract number and  update contract
    */
     public function actionSubmitcontract($id)
    {

         $model=Linkcustomepackage::findOne($id);;
         /******To update  contract number and contract status date*****/
         //print_r($_POST);die;
         $arrPost=Yii::$app->request->post('Linkcustomepackage');
         $strContractNumber = $arrPost['contract_number'];
         $strContractStatus = $arrPost['contract_status'];
         $strCotractStartDate = $arrPost['contract_start_date']; 
         $strCotractEndDate   = $arrPost['contract_end_date']; 
            
                if(!empty($strContractNumber) ||
                    !empty($strContractStatus) || !empty($strCotractStartDate) ||!empty($strCotractEndDate) ){
                
                    $query=Yii::$app->db->createCommand()
                            ->update('linkcustomepackage', [
                                'contract_number' =>$strContractNumber,
                                'contract_status' =>$strContractStatus,
                                'contract_start_date'=>$strCotractStartDate,
                                'contract_end_date'=>$strCotractEndDate,
                                'updated_at'=>date('Y-m-d h:i:s') ], ['cust_pck_id' =>$id])
                            ->execute();
                    if($query){
                        $logArray = array();
                        $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                        $logArray['module'] = 'Contract Report';
                        $logArray['action'] = 'update';
                        $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the contract
                         of "'.$model->customer->name.'" ';
                        $logArray['created'] = date('Y-m-d H:i:s');
                        Yii::$app->customcomponents->logActivity($logArray);
                    
                    Yii::$app->session->setFlash('success', CONTRACT_REPORT_EDIT_SUCCESSFUL);
                    return $this->redirect(['customercontract']);
                }
              }
        
        else {
            Yii::$app->session->setFlash('danger', CONTRACT_REPORT_EDIT_FAIL);
            
            return $this->redirect(['customercontract']);
            }
    }

    /**
     * Deletes an existing Linkcustomepackage model.
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
     * Finds the Linkcustomepackage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Linkcustomepackage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Linkcustomepackage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
