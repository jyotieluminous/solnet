<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : PackageController.php
# Created on : 10th June 2017 by Swati Jadhav.
# Update on  : 10th June 2017 by Swati Jadhav.
# Purpose : Manage Package.
############################################################################################
*/

namespace app\controllers;

use Yii;
use app\models\Package;
use app\models\PackageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use yii\filters\AccessControl;
use app\models\Linkcustomepackage;
/**
 * PackageController implements the CRUD actions for Package model.
 */
class PackageController extends Controller
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
     * Lists all Package models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PackageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Package model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model=Package::findOne(['Package_id'=>$id,'is_deleted'=>'0']);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Package model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
         $flagShowStatus=1;
        $model = new Package();
        $model->status = 'active';
        $model->is_deleted = '0';
        $model->created_at=date('Y-m-d h:i:s');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Package';
                $logArray['action'] ='create';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has created the package "'.Yii::$app->request->post('Package')['package_title'].'"';
                $logArray['created'] = date('Y-m-d H:i:s');

                Yii::$app->customcomponents->logActivity($logArray);


            Yii::$app->session->setFlash('success', PACKAGE_CREATE_SUCCESSFULL);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,'flagShowStatus'=>$flagShowStatus
            ]);
        }
    }

    /**
     * Updates an existing Package model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $customerModel=array();
        $flagShowStatus=1;
        $model=Package::findOne(['package_id'=>$id,'is_deleted'=>'0']);
        if(!empty($model)){
            $customePackageModel= linkcustomepackage::find()->where(['fk_package_id'=>$id])->all();
                if(count($customePackageModel)>0){
                     $flagShowStatus=0;
                }
            $model->updated_at=date('Y-m-d h:i:s');
            if ($model->load(Yii::$app->request->post()) && $model->save()) {

                    $logArray = array();
                    $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                    $logArray['module'] = 'Manage Package';
                    $logArray['action'] = 'update';
                    $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated a package "'.$model->package_title.'"';
                    $logArray['created'] = date('Y-m-d H:i:s');
                    Yii::$app->customcomponents->logActivity($logArray);

                Yii::$app->session->setFlash('success', PACKAGE_UPDATE_SUCCESSFULL);
                return $this->redirect(['view', 'id' => $model->package_id]);
            } 
            else {
               return $this->render('update', [
                'model' => $model,'flagShowStatus'=>$flagShowStatus
            ]);
           }
             
        } else {

            throw new NotFoundHttpException('The requested page does not exist.');
        }
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

            $customePackageModel= linkcustomepackage::find()->where(['fk_package_id'=>$id])->all();
            if(count($customePackageModel)>0){
            $strError=STATE_STATUS_RESTRICT; 
                Yii::$app->session->setFlash('errorStatus', $strError);
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
                    $modelPackage=Package::findOne($id);
                    $logArray = array();
                    $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                    $logArray['module'] = 'Manage Package';
                    $logArray['action'] =$model->status;
                    $logArray['message'] = 
                    ' "'.yii::$app->user->identity->name.'" has updated the package "'.$modelPackage->package_title.'" status to "'.$model->status.'"';
                    $logArray['created'] = date('Y-m-d H:i:s');

                    Yii::$app->customcomponents->logActivity($logArray);
                    return true;
                }
            }
        }
    }

    /**
     * Deletes an existing Package model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
     public function actionDelete($id)
    {
        $model=Package::findOne(['package_id'=>$id,'is_deleted'=>'0']);
        if(!empty($model)){
            //check if this package is assign to any customer. if yes then can not delete the package
            $customePackageModel= linkcustomepackage::find()->where(['fk_package_id'=>$id])->all();
            if(count($customePackageModel)>0){
            $strError=$model->package_title .' '.BANK_DELETE_RESTRICT; 
                Yii::$app->session->setFlash('deleteMessage', $strError);
                 return $this->redirect(['index']);

            }
            else{//delete the package
            $model->is_deleted = '1';
            if($model->save()){
                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Package';
                $logArray['action'] = 'delete';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted  "'.$model->package_title.'" ';
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);
            Yii::$app->session->setFlash('success', PACKAGE_DELETE_SUCCESSFULL);    
            return $this->redirect(['index']);
            }
          }
       }else{
         
              throw new NotFoundHttpException('The requested page does not exist.');
        
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
        $arrError=array();
        if(!empty($ids))
        {
            foreach($ids as $key=>$id){
              
               $customePackageModel= linkcustomepackage::find()->where(['fk_package_id'=>$id])->all();

               if(count($customePackageModel)>0){
            
                 $packageModel= Package::findOne($id);

                 $arrError[]=$packageModel->package_title; 
                
              }
              else{
                  
                 $db = Yii::$app->db;
                 $db->createCommand("UPDATE tblpackage SET is_deleted='1' WHERE package_id=".$id)->execute();
                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Package';
                $logArray['action'] = 'delete';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted the package(s)';
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);

                }

                }
                
              }
              if($arrError){
                    $strError=implode(", ", $arrError);
                     
                    Yii::$app->session->setFlash('errorMessage',
                      $strError.' '.BANK_DELETE_RESTRICT);

                    return $this->redirect(['index']);
                }
            else{
                    return 'success';
                }
        }

    /**
     * Finds the Package model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Package the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Package::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
