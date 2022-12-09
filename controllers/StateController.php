<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : StateController.php
# Created on : 8th June 2017 by Swati Jadhav.
# Update on  : 8th June 2017 by Swati Jadhav.
# Purpose : Manage State.
############################################################################################
*/


namespace app\controllers;

use Yii;
use app\models\State;
use app\models\StateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Expression;
use app\models\Customer;
use yii\web\Response;
use yii\widgets\ActiveForm;
/**
 * StateController implements the CRUD actions for State model.
 */
class StateController extends Controller
{
    /**
     * @inheritdoc
     */
   public function behaviors()
    {
		$behaviors['access'] = [
			'class' => AccessControl::className(),
                        'only' => ['create', 'update','index','view','delete'],
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
     * Lists all State models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model= new State();
        $searchModel = new StateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            
        ]);
    }

    /**
     * Displays a single State model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model=State::findOne(['state_id'=>$id,'status'=>'active']);
        Yii::$app->session->setFlash('notFoundMessage','Sorry. Record not found!');
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new State model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {   
        $flagShowStatus=1;
        $model = new State();
        $model->status = 'active';
        $model->created_at=date('Y-m-d h:i:s');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->state_prefix = strtoupper($model->state_prefix);
            
            $model->save();
            $logArray = array();
            $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
            $logArray['module'] = 'Manage State';
            $logArray['action'] = 'create';
            $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has created the state "'.Yii::$app->request->post('State')['state'].'" ';
            $logArray['created'] = date('Y-m-d H:i:s');
            Yii::$app->customcomponents->logActivity($logArray);

            Yii::$app->session->setFlash('success', STATE_CREATE_SUCCESSFULL);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,'flagShowStatus'=>$flagShowStatus
            ]);
        }
        
    }

    /**
     * Updates an existing State model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $customerModel=array();
        $flagShowStatus=1;
        $model=State::findOne(['state_id'=>$id,'status'=>'active']);
        if(!empty($model)){
            $customerModel= Customer::find()->where(['fk_state_id'=>$id,'is_deleted'=>'0'])->all();
            if(count($customerModel)>0){
                    $flagShowStatus=0;

            }

            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            $model->updated_at=date('Y-m-d h:i:s');
            if ($model->load(Yii::$app->request->post())) {
                $model->state_prefix = strtoupper($model->state_prefix);
            
                $model->save();

                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage State';
                $logArray['action'] = 'update';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the state "'.$model->state.'" ';
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);


                Yii::$app->session->setFlash('success', STATE_UPDATE_SUCCESSFULL);
                return $this->redirect(['view', 'id' => $model->state_id]);
            } 
        }else {
                Yii::$app->session->setFlash('notFoundMessage','Sorry. Record not found!');
                return $this->render('update', [
                    'model' => $model,'flagShowStatus'=>$flagShowStatus
                ]);
             
       }
       return $this->render('update', [
                    'model' => $model,'flagShowStatus'=>$flagShowStatus]);
    }

     

    /**
    * Function to toggle status of single record
    */
    public function actionTogglestatus()
    {
        $id =   Yii::$app->request->post('id');
        $status =   Yii::$app->request->post('page_status');
        $model = $this->findModel($id);
        if(!empty($model) && !empty($status) && !empty($id))
        {

            $customerModel= Customer::find()->where(['fk_state_id'=>$id,'is_deleted'=>'0'])->all();
            if(count($customerModel)>0){
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
                    $logArray['module'] = 'Manage State';
                    $logArray['action'] =$model->status;
                    $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the state "'.$model->state.'" status to "'.$model->status.'"';
                    $logArray['created'] = date('Y-m-d H:i:s');

                    Yii::$app->customcomponents->logActivity($logArray);
                    return true;
                }
            }
        }
    }

    /**
     * Deletes an existing State model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

     public function actionDelete($id)
    {
       
        $model=State::findOne(['state_id'=>$id,'status'=>'active']);
        if(!empty($model)){
            $customerModel= Customer::find()->where(['fk_state_id'=>$id,'is_deleted'=>'0'])->all();
            if(count($customerModel)>0){
            $strError=$model->state .' '.STATE_DELETE_RESTRICT; 
                Yii::$app->session->setFlash('error', $strError);
                 return $this->redirect(['index']);

            }
            else{

                $model->status = 'deleted';
               
               if( $model->save()){

                    $logArray = array();
                    $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                    $logArray['module'] = 'Manage State';
                    $logArray['action'] = 'delete';
                    $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted bank ';
                    $logArray['created'] = date('Y-m-d H:i:s');
                    Yii::$app->customcomponents->logActivity($logArray);
                    Yii::$app->session->setFlash('success', "State deleted successfully");
                    return $this->redirect(['index']);
                }

              }
        }else{
            throw new NotFoundHttpException('The requested page does not exist.');
       }
    }

     /**
     * Deletes multiple states.
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
              
               $customerModel= Customer::find()->where(['fk_state_id'=>$id,'is_deleted'=>'0'])->all();

               if(count($customerModel)>0){
            
                 $stateModel= State::findOne($id);

                 $arrError[]=$stateModel->state; 
                
              }
              else{
                  
                 $db = Yii::$app->db;
                 $db->createCommand("UPDATE tblstate SET status='deleted' WHERE state_id=".$id)->execute();
                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage State';
                $logArray['action'] = 'delete';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted the state(s)';
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

	
	public function actionGet($id){
		$options = Yii::$app->customcomponents->getState($id);
		return $options;
	}
    /**
     * Finds the State model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return State the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = State::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
