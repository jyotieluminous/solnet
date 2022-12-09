<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : BankController.php
# Created on : 12th June 2017 by Swati Jadhav.
# Update on  : 12th June 2017 by Swati Jadhav.
# Purpose : Manage Bank Details.
############################################################################################
*/

namespace app\controllers;

use Yii;
use app\models\Bank;
use app\models\Bankdeposit;
use app\models\BankSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use yii\web\Application;
use yii\filters\AccessControl;
/**
 * BankController implements the CRUD actions for Bank model.
 */
class BankController extends Controller
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
                        'only' => ['index','create', 'update','delete','view','deletemultiple'],
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
     * Lists all Bank models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BankSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bank model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $model=Bank::findOne(['Bank_id'=>$id,'is_deleted'=>'0']);
        Yii::$app->session->setFlash('notFoundMessage','Sorry. Record not found!');
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Bank model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $flagShowStatus=1;
        $model = new Bank();
        $model->status='active';
        $model->is_deleted='0';
        $model->created_at=date('Y-m-d h:i:s');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Bank';
                $logArray['action'] = 'create';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has created a bank "'.Yii::$app->request->post('Bank')['bank_name'].'" ';
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);
               
             Yii::$app->session->setFlash('success', BANK_CREATE_SUCCESSFULL);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,'flagShowStatus'=>$flagShowStatus
            ]);
        }
    }

    /**
     * Updates an existing Bank model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $depositModel = array();
        $flagShowStatus = 1;
        $model = Bank::findOne(['Bank_id'=>$id,'is_deleted'=>'0']);
       if(!empty($model)){
            $depositModel = Bankdeposit::find()->where(['fk_bank_id'=>$id,'is_deleted'=>'0'])->all();
            if(count($depositModel)>0){
                    $flagShowStatus=0;
            }
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    $logArray = array();
                    $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                    $logArray['module']     = 'Manage Bank';
                    $logArray['action']     = 'update';
                    $logArray['message']    = ' "'.yii::$app->user->identity->name.'" has updated a bank "'.$model->bank_name.'"';
                    $logArray['created']    = date('Y-m-d H:i:s');
                    Yii::$app->customcomponents->logActivity($logArray);
                Yii::$app->session->setFlash('success', BANK_UPDATE_SUCCESSFULL);
                return $this->redirect(['view', 'id' => $model->bank_id]);
            } 
            return $this->render('update', [
                'model'         => $model,
                'flagShowStatus'=> $flagShowStatus
            ]);
        }
            else {
                Yii::$app->session->setFlash('notFoundMessage','Sorry. Record not found!');
                return $this->render('update', [
                    'model' => $model,'flagShowStatus'=>$flagShowStatus
                ]);
            }
       
    }

    /**
     * Deletes an existing Bank model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
   
        public function actionDelete($id)
    {
    	$model=Bank::findOne(['Bank_id'=>$id,'is_deleted'=>'0']);
        if(!empty($model)){
            //check if this bank assign to any customer. if yes then can not delete the bank
            $depositModel= Bankdeposit::find()->where(['fk_bank_id'=>$id,'is_deleted'=>'0'])->all();
            if(count($depositModel)>0){
            $strError=$model->bank_name .' '.BANK_DELETE_RESTRICT; 
                Yii::$app->session->setFlash('error', $strError);
                 return $this->redirect(['index']);

            }
            else{//delete the bank
            $model->is_deleted = '1';
            if($model->save()){
                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Bank';
                $logArray['action'] = 'delete';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted bank "'.$model->bank_name.'" ';
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);
                return $this->redirect(['index']);
            }
        }
      }else{
              throw new NotFoundHttpException('The requested page does not exist.');
        }
      

        
    } 
    
    /**
     * Deletes multiple Bank.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletemultiple()
    {
        $ids = yii::$app->request->post('ids');
        $arrError=array();
        if(!empty($ids))
        {
            foreach($ids as $key=>$id){
              
               $depositModel= Bankdeposit::find()->where(['fk_bank_id'=>$id,'is_deleted'=>'0'])->all();

                   if(count($depositModel)>0){
                
                     $bankModel= Bank::findOne($id);
                     $arrError[]=$bankModel->bank_name; 
                    
                  }
                  else{
                      
                    $db = Yii::$app->db;
                    $db->createCommand("UPDATE tblbank SET is_deleted='1' WHERE bank_id=".$id)->execute();
                    $logArray = array();
                    $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                    $logArray['module'] = 'Manage Bank';
                    $logArray['action'] = 'delete';
                    $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted the bank(s)';
                    $logArray['created'] = date('Y-m-d H:i:s');
                    Yii::$app->customcomponents->logActivity($logArray);

                    }

              }
                
              }
              if($arrError){
                
                    $strError=implode(", ", $arrError);
                     
                    Yii::$app->session->setFlash('deleteMessage',
                      $strError.' '.BANK_DELETE_RESTRICT);

                    return $this->redirect(['index']);
                }
             else{
                    return 'success';
                }
    }


     /**
    * Function to toggle status of  record
    */
     public function actionTogglestatus()
    {
        $id =   Yii::$app->request->post('id');
        $status =   Yii::$app->request->post('page_status');
        $model = $this->findModel($id);
        if(!empty($model) && !empty($status) && !empty($id))
        {

            $depositModel= Bankdeposit::find()->where(['fk_bank_id'=>$id,'is_deleted'=>'0'])->all();
            if(count($depositModel)>0){
            $strError=STATE_STATUS_RESTRICT; 
                Yii::$app->session->setFlash('error', $strError);
                 return $this->redirect(['index']);

            }
            else{
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
                    $logArray['module'] = 'Manage Bank';
                    $logArray['action'] =$model->status;          
                    $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the bank "'.$model->bank_name.'" status to "'.$model->status.'"';
                    $logArray['created'] = date('Y-m-d H:i:s');

                    Yii::$app->customcomponents->logActivity($logArray);
                    return true;
                }
            }
        }
    }


    /**
     * get bank name
     * 
     * @return bank name
     */

    public function actionGetbankname()
    {

         $model=new Bank();
          if(Yii::$app->request->post())
        {

            $intBankId=Yii::$app->request->post('id');
            $arrBankName=$model->getBankName($intBankId);
            echo json_encode($arrBankName['bank_name']);
            
        }

        
    }



    /**
     * Finds the Bank model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bank the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bank::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
