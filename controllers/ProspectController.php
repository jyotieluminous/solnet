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
# Created on : 20th June 2017 by Suraj Malve.
# Update on  : 20th June 2017 by Swati Jadhav.
# Purpose : Manage Sales Activity(Prospect).
############################################################################################
*/

namespace app\controllers;

use Yii;
use app\models\Prospect;
use app\models\ProspectSearch;
use app\models\State;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use yii\db\Command;
use yii\db\Query;
use yii\helpers\ArrayHelper;
/**
 * ProspectController implements the CRUD actions for Prospect model.
 */
class ProspectController extends Controller
{
    /**
     * @inheritdoc
     */
    /*public function behaviors()
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
                        'only' => ['index','create', 'update','delete',
                        'view','deletemultiple'],
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
     * Lists all Prospect models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProspectSearch();
        $queryParams = Yii::$app->request->queryParams;
        if(Yii::$app->user->identity->fk_role_id=='3'){
        $queryParams['ProspectSearch']['fk_user_id']=Yii::$app->user->identity->user_id;
        }
        $dataProvider = $searchModel->search($queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Prospect model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model=Prospect::findOne(['prospect_id'=>$id,'is_deleted'=>'0']);
        return $this->render('view', [
            'model' =>$model ,
        ]);
    }

    /**
     * Creates a new Prospect model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new Prospect();
        if(empty(Yii::$app->request->post('Prospect')['current_isp_bill'])){
            $model->current_isp_bill=0;
        }
        if(empty(Yii::$app->request->post('Prospect')['price_quote'])){
            $model->price_quote=0;
        }
        $model->is_deal_closed='no';

        $model->fk_user_id = yii::$app->user->identity->user_id;
        $model->is_deleted = '0';
        $model->created_at = date('Y-m-d h:i:s');
        $arrState   = State::find()->where(['status'=>'active'])->all();
        $stateListData  = ArrayHelper::map($arrState,'state_id','state');
        
        if ($model->load(Yii::$app->request->post())) {

		  if(!empty($_POST['Prospect']['current_contract_end_date'])){
			$model->current_contract_end_date=date("Y-m-d",  strtotime($_POST['Prospect']['current_contract_end_date']));
			}
		  if(!empty($_POST['Prospect']['estimate_sign_up_date'])){
			$model->estimate_sign_up_date=date("Y-m-d",  strtotime($_POST['Prospect']['estimate_sign_up_date']));
		  }
		  if(!empty($_POST['Prospect']['quotation_date'])){
           $model->quotation_date=date("Y-m-d",  strtotime($_POST['Prospect']['quotation_date'])); 
		  }
              if (Yii::$app->request->post('addmore') ==='add') {
                   
                    if( $model->save()){  
                    $logArray = array();
                    $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                    $logArray['module'] = 'Manage Prospect';
                    $logArray['action'] ='create';
                    $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has created the prospect for "'.Yii::$app->request->post('Prospect')['customer_name'].'"';
                    $logArray['created'] = date('Y-m-d H:i:s');

                    Yii::$app->customcomponents->logActivity($logArray); 
                     Yii::$app->session->setFlash('success', PROSOPECT_CREATE_SUCCESSFULL);
                     return $this->redirect(['prospect/create']);
                    }
                }
            else {//if ((Yii::$app->request->post(''))== 'save_add') {
             
                if( $model->save()){  
                    $logArray = array();
                    $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                    $logArray['module'] = 'Manage Prospect';
                    $logArray['action'] ='create';
                    $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has created the prospect for "'.Yii::$app->request->post('Prospect')['customer_name'].'"';
                    $logArray['created'] = date('Y-m-d H:i:s');

                    Yii::$app->customcomponents->logActivity($logArray);
                 Yii::$app->session->setFlash('success', PROSOPECT_CREATE_SUCCESSFULL); 
                 return $this->redirect(['prospect/index']);
    
                }
            }

        }

     else{
            return $this->render('create', [
                    'model' => $model,
                    'statesList'=>$stateListData
                ]);
            }
        
    }




      /**
    * Function to toggle status of single record
    */
    public function actionToggledeal()
    {
        $id =   Yii::$app->request->post('id');
        $status =   Yii::$app->request->post('dealStatus');
        $model = $this->findModel($id);
        if(!empty($model) && !empty($status) && !empty($id))
        {
            if($status=='yes'){
            $model->is_deal_closed='no';
            }else if($status=='no'){
                $model->is_deal_closed='yes';
            }
            $model->updated_at = new Expression('NOW()');

            if($model->save())
            {

                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Prospect';
                
                $logArray['action'] ='update';
               
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the deal status of"'.$model->customer_name.'" to "'.$model->is_deal_closed.'"';
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



    /**
     * Updates an existing Prospect model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model=Prospect::find()->where(['prospect_id'=>$id,'is_deleted'=>'0'])->one();
        if(!empty($model)){
          $model->updated_at = date('Y-m-d h:i:s');
           $arrState   = State::find()->where(['status'=>'active'])->all();
           $stateListData  = ArrayHelper::map($arrState,'state_id','state');
           
             if ($model->load(Yii::$app->request->post())){
                $model->estimate_sign_up_date=date("Y-m-d",  strtotime($_POST['Prospect']['estimate_sign_up_date']));
                $model->quotation_date=date("Y-m-d",  strtotime($_POST['Prospect']['quotation_date'])); 
             if( $model->save()) {
                
                    $logArray = array();
                    $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                    $logArray['module'] = 'Manage Prospect';
                    $logArray['action'] = 'update';
                    $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated prospect of "'.$model->customer_name.'"';
                    $logArray['created'] = date('Y-m-d H:i:s');
                    Yii::$app->customcomponents->logActivity($logArray);

                Yii::$app->session->setFlash('success', PROSOPECT_UPDATE_SUCCESSFULL);
                return $this->redirect(['view', 'id' => $model->prospect_id]);
                } 
             }
        else {
            return $this->render('update', [
                'model' => $model,'statesList'=>$stateListData
            ]);
        }
    }else{
        return $this->render('update', [
                'model' => $model,'statesList'=>$stateListData
            ]);
    }

 }

    /**
     * Deletes an existing Prospect model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
       // $model = $this->findModel($id);
        $model=Prospect::findOne(['prospect_id'=>$id,'is_deleted'=>'0']);
        if(!empty($model)){
            $model->is_deleted = '1';
            if($model->save()){
                    $logArray = array();
                    $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                    $logArray['module'] = 'Manage Prospect';
                    $logArray['action'] = 'delete';
                    $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted the prospect of "'.$model->customer_name.'" ';
                    $logArray['created'] = date('Y-m-d H:i:s');
                    Yii::$app->customcomponents->logActivity($logArray);
                Yii::$app->session->setFlash('success', PROSOPECT_DELETE_SUCCESSFULL);
                return $this->redirect(['index']);
            }
        }else{
              throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Deletes multiple prospects.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletemultiple()
    {
        $ids = yii::$app->request->post('ids');
       
        if(!empty($ids))
        {

            if(Prospect::updateAll(['is_deleted'=>'1'],['prospect_id'=>$ids]))
            {
                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Prospect';
                $logArray['action'] = 'delete';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted prospect(s)';
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);
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
     * Generate a pdf for single prospect details .
     * @param integer $id
     * @return mixed
     */
    public function actionPdf($id) {
        $model=Prospect::find()->where(['prospect_id'=>$id,'is_deleted'=>'0'])->one();
        if(!empty($model)){
            $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'content' => $this->renderPartial('view', [
                'model' => $model,
            ]),
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'options' => [
                'title' => 'Prospect Details',
                'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
            ],
            'methods' => [
                //'SetHeader' => ['Generated By: Solnet'],
                'SetFooter' => ['|Page {PAGENO}|'],
            ]
        ]);
            
            return $pdf->render();
        }else{
            Yii::$app->session->setFlash('notFoundMessage', 'Sorry.Record not found!');
                return $this->redirect(['index']);
        }
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
     * Finds the Prospect model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Prospect the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Prospect::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
