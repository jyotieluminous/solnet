<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : RoleController.php
# Created on : 20th June 2017 by Suraj Malve.
# Update on  : 20th June 2017 by Suraj Malve.
# Purpose : Manage role to assign access.
############################################################################################
*/

namespace app\controllers;

use Yii;
use app\models\AuthItem;
use app\models\AuthAssignment;
use app\models\AuthItemChild;
use app\models\AuthController;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Expression;
use yii\web\Response;
use yii\widgets\ActiveForm;


/**
 * BrandsController implements the CRUD actions for Brands model.
 */
class RoleController extends Controller
{

	/*public function beforeAction($action)
	{
		$exception = Yii::$app->errorHandler->exception;
		
		if(Yii::$app->user->identity->fk_role_id!='1')
		{

			$name='Forbidden';
			$message='Forbidden';
			return Yii::$app->runAction('site/error', ['name'=>$name,'message'=>$message]);
			//return $this->render('/site/error',['name'=>$name,'message'=>$message]);
		}
		else
		{
			return true;
		}
	}*/
   public function behaviors()
    {
		$behaviors['access'] = [
			'class' => AccessControl::className(),
                        'only' => ['add', 'update','index','view','delete'],
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
     * Lists all Brands models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItem();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination->pageSize=5;

	return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single Brands model.
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
     * Creates a new Brands model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new AuthItem();
		$session = Yii::$app->session;
		$authModel = new AuthController();
		$arrModules = $authModel -> getModules();
		if ($model->load(Yii::$app->request->post())) {
			
		 if (Yii::$app->request->isAjax) {// ajax call performed to check category name uniqueness
				Yii::$app->response->format = Response::FORMAT_JSON;
    			return ActiveForm::validate($model);
		 }else{
			$arrPost		=	Yii::$app->request->post();
			$parent_nm = $arrPost['AuthItem']['name'];
			$model -> name = $parent_nm;
			$model->type= 1;
			if($model -> save()){
				if($arrPost['AuthItem']['child']){
					foreach($arrPost['AuthItem']['child'] as $val ){
						if($val){
							foreach($val as $module){
								$chkItem = $this->checkIfItemExist($module);
								if(!($chkItem)) {
									$modelItem = new AuthItem();
									$modelItem->name = $module;
									$modelItem->type = 2;
									$modelItem->description = $module;	
									$modelItem->status = 'Active';	
									if($modelItem->save()){
										$session->setFlash('success', ROLE_ADD_SUCCESS);
									}
								}
								$modelItemChild = new AuthItemChild();
								$modelItemChild->parent = $parent_nm;
								$modelItemChild->child = $module;
								if($modelItemChild->save()){
									$logArray = array();
									$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
									$logArray['module'] = 'Add roles';
									$logArray['action'] = 'create';
									$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has created new role';
									$logArray['created'] = date('Y-m-d H:i:s');
									Yii::$app->customcomponents->logActivity($logArray);
									$session->setFlash('success', ROLE_ADD_SUCCESS);
								}
								//print_r($modelItemChild->getErrors());exit;
							}
						}					
					}
				}
				
				$session->setFlash('success', ROLE_ADD_SUCCESS);
			}
			else {
				$session->setFlash('error', ROLE_ADD_ERR);
			}
			
			return $this->redirect('index');
        } 
		}
		return $this->render('create', [
                'model' => $model,
				'authModel' => $authModel,
				'arrModules' => $arrModules,
            ]);
    }

    /**
     * Updates an existing Brands model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$authModel = new AuthController();
		$arrModules = $authModel -> getModules();
		
		$session = Yii::$app->session;
		//if($model->name!=='Super Admin'){
        if ($model->load(Yii::$app->request->post())) {
			
			if (Yii::$app->request->isAjax) {// ajax call performed to check category name uniqueness
				Yii::$app->response->format = Response::FORMAT_JSON;
    			return ActiveForm::validate($model);
		 }else{
			$arrPost   =	Yii::$app->request->post();
			$parent_nm = $arrPost['AuthItem']['name'];
			
                        $model -> name = $parent_nm;
			$model -> save();
                        
			AuthItemChild::deleteAll(['parent' => $parent_nm]);
			
			if($arrPost['AuthItem']['child']){
				foreach($arrPost['AuthItem']['child'] as $val ){
					if($val){
						foreach($val as $module){
							$chkItem = $this->checkIfItemExist($module);
							
							if(!($chkItem)) {
								$modelItem = new AuthItem();
								$modelItem->name = $module;
								$modelItem->type = 2;
								$modelItem->description = $module;	
								$modelItem->status = 'Active';	
								$modelItem->save();
							}
							
							$modelItemChild = new AuthItemChild();
							$modelItemChild->parent = $parent_nm;
							$modelItemChild->child = $module;
							$modelItemChild->save();
							//print_r($modelItemChild->getErrors());exit;
						}
					}	
					}
				}
			}
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Update roles';
			$logArray['action'] = 'update';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the role "'.$id.'"';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			Yii::$app->session->setFlash('success',ROLE_UPDATE_SUCCESS);         
			return $this->redirect('index');
        } 
		
		//}
		return $this->render('update', [
                    'model' => $model,
                    'authModel' => $authModel,
                    'arrModules' => $arrModules,
            ]);
    }

    /**
     * Deletes an existing Brands model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//        $role_name = Yii::$app->request->post('id');
        $role_name = $id;
		$session = Yii::$app->session;
		$model = $this->findModel($role_name);

		//$model->status = 'Deleted';
		
		if($model->name!=='Super Admin'){
			AuthItemChild::deleteAll(['parent' => $role_name]);
			if($model->delete()){
				$session->setFlash('success', ROLE_DELTED_MSG);
			}
			else {
				
				$session->setFlash('error', ROLE_DELTED_ERR);
			}
			return $this->redirect(['index']);
		}
		else{
			$session->setFlash('error', CANNOT_DELETE_ERR);
		}
    }

    /**
     * Finds the Brands model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Brands the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	protected function checkIfItemExist($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            return false;
        }
    }
	
	protected function findChildModel($id)
    {
        if (($model = AuthItemChild::findAll($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }	
}
