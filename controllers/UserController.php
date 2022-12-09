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
# Created on : 16th June 2017 by Suraj Malve.
# Update on  : 16th June 2017 by Suraj Malve.
# Purpose : Manage Customer.
############################################################################################
*/

namespace app\controllers;

use Yii;
use app\models\Tblusers;
use app\models\TblusersSearch;
use app\models\AuthAssignment;
use app\models\Roles;
use app\models\State;
use app\models\TbluserStates;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\db\Expression;
use app\models\LoginForm;
/**
 * UserController implements the CRUD actions for Tblusers model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
   /*public function behaviors()
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
    }*/
	
	public function behaviors()
    {
		$behaviors['access'] = [
			'class' => AccessControl::className(),
                        'only' => ['create', 'update','index','view','delete','togglestatus','deletemultiple','changepassword','password'],
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
     * Lists all Tblusers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TblusersSearch();
		$queryParams = Yii::$app->request->queryParams;
		 $queryParams["TblusersSearch"]["is_deleted"]   = '0';
		/*************To fetch state from table************/
		$arrCountry 	= Roles::find()->where(['status'=>'active'])->all();
		$roleListData	= ArrayHelper::map($arrCountry,'role_id','role');
		/*************To fetch state from table************/
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'roleList'=>$roleListData
        ]);
    }

    /**
     * Displays a single Tblusers model.
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
     * Creates a new Tblusers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tblusers();
        $modelUserStates = new TbluserStates();
		$model->scenario = 'create';
		$connection = \Yii::$app->db;
		/*************To fetch roles from table************/
		$arrCountry 	= Roles::find()->where(['status'=>'active'])->all();
		$roleListData	= ArrayHelper::map($arrCountry,'role_id','role');
		/*************To fetch state from table************/
		$arrState 	= State::find()->where(['status'=>'active'])->all();
		$stateListData	= ArrayHelper::map($arrState,'state_id','state');

		$arrPostData = Yii::$app->request->post();

		if(!empty($arrPostData))
		{
			
			$strEmail 		= $arrPostData['Tblusers']['email'];
			$strPassWord	= $arrPostData['Tblusers']['password'];
		}
		$model->created_at = date('Y-m-d h:i:s');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
        	$arrStates 		   = $model->user_states;
        	
			$intLastInsertedId = $model->user_id;
			if($arrStates && $intLastInsertedId)
			{
				foreach($arrStates as $key=>$value)
				{
					$arrStatesAdd [] = [
						'fk_user_id' => $intLastInsertedId,
						'fk_state_id' => $value,
					];
				}
				$connection->createCommand()->batchInsert('tbluser_states', ['fk_user_id','fk_state_id'], $arrStatesAdd)->execute();

				$objAuthAssignment = new AuthAssignment();
				$objAuthAssignment->user_id=$intLastInsertedId;
				$objAuthAssignment->created_at=date('Ymdhis');
				$objRole = Roles::find()->where(['role_id'=>$model->fk_role_id])->one();
				if(!empty($objRole))
				{
					$strRoleName = $objRole->role;
				}
				$objAuthAssignment->item_name=$strRoleName;
				$imageUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/web/images/solnet.png';
				
				if($objAuthAssignment->save()){
					$strLoginUrl = Yii::$app->urlManager->createAbsoluteUrl('site/login/');
					$strSubject = 'New System User';
					$result	=	Yii::$app->mailer->compose('create_system_user', ['name'=>$model->name,'role'=>$model->roles->role,'email' => $strEmail,'password'=>$strPassWord,'loginUrl'=>$strLoginUrl,'image'=>$imageUrl])
								->setFrom('admin@solnet.com')
								->setTo($strEmail)
								->setSubject($strSubject)
								->send();
							
							if($result)
							{
								$emailLog = array();
								$emailLog['email_to'] = $strEmail;
								$emailLog['subject'] = $strSubject;
								$emailLog['is_user'] = 'Yes';
								$emailLog['sent_to_id'] = $model->user_id;
								$emailLog['sent_by'] = 'User';
								$emailLog['sent_by_user_id'] = yii::$app->user->identity->user_id;
								$emailLog['sent_date'] = date('Y-m-d H:i:s');
								Yii::$app->customcomponents->emailLogActivity($emailLog);

								/************Log Activity*********/
								$logArray = array();
								$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
								$logArray['module'] = 'Create User';
								$logArray['action'] = 'create';
								$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has created a system user "'.$model->name.'"';
								$logArray['created'] = date('Y-m-d H:i:s');
								Yii::$app->customcomponents->logActivity($logArray);
								/************Log Activity*********/
								return $this->redirect(['view', 'id' => $model->user_id]);
							}
				}

			}

			
        } else {
            return $this->render('create', [
                'model' => $model,
				'roleList'=>$roleListData,
				'statesList'=>$stateListData
            ]);
        }
    }

    /**
     * Updates an existing Tblusers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $connection = \Yii::$app->db;
        $modelUserStates = new TbluserStates();

        $getUserStates = TbluserStates::find()->joinWith(['states'])->select(['fk_state_id','tblstate.state'])->where(['fk_user_id'=>$id])->asArray()->all();

        $arrStateData	= ArrayHelper::map($getUserStates,'fk_state_id','fk_state_id');

		$model->scenario = 'update';
		/*************To fetch state from table************/
		$arrCountry 	= Roles::find()->where(['status'=>'active'])->all();
		$roleListData	= ArrayHelper::map($arrCountry,'role_id','role');
		/*************To fetch state from table************/
		$arrState 	= State::find()->where(['status'=>'active'])->all();
		$stateListData	= ArrayHelper::map($arrState,'state_id','state');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

        	$arrStates 		   = $model->user_states;
			$resultDeleteQuery 	   = $connection->createCommand()->delete('tbluser_states', 'fk_user_id = '.$id)->execute();
			if(!empty($arrStates))
			{
				foreach($arrStates as $key=>$value)
				{
					$arrStatesAdd [] = [
						'fk_user_id' => $id,
						'fk_state_id' => $value,
					];
				}
				$connection->createCommand()->batchInsert('tbluser_states', ['fk_user_id','fk_state_id'], $arrStatesAdd)->execute();
			}
			
			$objAuthAssignment = AuthAssignment::find()->where(['user_id'=>$id])->one();
			$objRole = Roles::find()->where(['role_id'=>$model->fk_role_id])->one();
			if(!empty($objRole))
			{
				$strRoleName = $objRole->role;
			}
			$objAuthAssignment->item_name=$strRoleName;
			if($objAuthAssignment->save()){
				/************Log Activity*********/
				$logArray = array();
				$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
				$logArray['module'] = 'Update User';
				$logArray['action'] = 'update';
				$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated a system user "'.$model->name.'"';
				$logArray['created'] = date('Y-m-d H:i:s');
				Yii::$app->customcomponents->logActivity($logArray);
				/************Log Activity*********/
            	return $this->redirect(['view', 'id' => $model->user_id]);
			}
            
        } else {
			
            return $this->render('update', [
                'model' => $model,
				'roleList'=>$roleListData,
				'statesList'=>$stateListData,
				'arrStateData'=>$arrStateData
            ]);
        }
    }
	/*
	* Function for delete multiple users
	*/
	public function actionDeletemultiple()
	{
		$ids = yii::$app->request->post('ids');
		if (($key = array_search(Yii::$app->user->identity->user_id, $ids)) !== false) {
    		unset($ids[$key]);
		}
		
		if(!empty($ids))
		{
			if(Tblusers::updateAll(['is_deleted' => 1],['user_id'=>$ids]))
			//if(Tblusers::deleteAll(['user_id'=>$ids]))
			{
				if(AuthAssignment::deleteAll(['user_id'=>$ids]))
				{
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Delete Multiple Users';
					$logArray['action'] = 'delete';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted multiple system users';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
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
		else
		{
			return 'failed';
		}

	}
	/**
	* Function to toggle status of single record
	*/
	public function actionTogglestatus()
	{
		$id	=	Yii::$app->request->post('id');
		$status	=	Yii::$app->request->post('page_status');
		$model = $this->findModel($id);
		if(!empty($model) && !empty($status) && !empty($id))
		{
			if($status=='active'){
			$model->status='inactive';
			}else if($status=='inactive'){
				$model->status='active';
			}
			$model->updated_at = new Expression('NOW()');
			if($model->save())
			{
				/************Log Activity*********/
				$logArray = array();
				$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
				$logArray['module'] = 'Updated User Status';
				$logArray['action'] = $model->status;
				$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated a system user "'.$model->name.'"\'s status to "'.$model->status.'"';
				$logArray['created'] = date('Y-m-d H:i:s');
				Yii::$app->customcomponents->logActivity($logArray);
				/************Log Activity*********/
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
     * Deletes an existing Tblusers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$session = Yii::$app->session;
        $model =  $this->findModel($id);//->delete();
		$model->is_deleted='1';
		if($model->save()){
			$modelAuthAssisgn	= AuthAssignment::find()->where(['user_id'=>$id])->one();
			$modelAuthAssisgn->delete();
			$session->setFlash('success',USER_DELETE_SUCCESSFULL);
			/************Log Activity*********/
			$logArray = array();
			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
			$logArray['module'] = 'Delete User';
			$logArray['action'] = 'delete';
			$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted system user "'.$model->name.'"';
			$logArray['created'] = date('Y-m-d H:i:s');
			Yii::$app->customcomponents->logActivity($logArray);
			/************Log Activity*********/
			return $this->redirect(['index']);
		}else{
			echo '<pre>';
			print_r($model->getErrors());die;
		}
        return $this->redirect(['index']);
    }

    public function actionPassword()
    {
         $model = new LoginForm;
         $model->scenario = 'change_password';
         return $this->render('password_new',['model' => $model]);
    }
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
        	return $this->render('password_new',['model' => $model]);
            /*Yii::$app->getSession()->setFlash('error', 'Password changed failed');  
            return $this->redirect(['user/changepassword']);*/
        }
   }


    /**
     * Finds the Tblusers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tblusers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tblusers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
