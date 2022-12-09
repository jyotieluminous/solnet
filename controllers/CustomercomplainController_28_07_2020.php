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
# Created on : 17th April 2020 by Ashvini Chavan-Hyalij.
# Update on  : 24th April 2020 by Ashvini Chavan-Hyalij.
# Purpose : Manage Complain.
############################################################################################
*/


namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\Tblcustomercomplains;
use app\models\TblcustomercomplainsSearch;
use app\models\Linkcustomepackage;
use app\models\Nocstaff;
use app\models\Tblcustomercomplaindocs;
use app\models\Bank;
//use app\models\PackageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
//use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\db\Expression;
use yii\web\UploadedFile;
/* EQUIPEMENT TASK */
use app\models\CustomerEquipments;
use app\models\Equipments;
use app\models\EquipmentsMacs;
use app\models\EquipmentsReturnActivity;
/* END EQUIPEMENT TASK */

//use yii\db\Expression;
/**
 * CustomerController implements the CRUD actions for complain model.
 */
class CustomercomplainController extends Controller
{
    /**
     * @inheritdoc
     */

	public function behaviors()
    {
		$behaviors['access'] = [
			'class' => AccessControl::className(),
                        'only' => ['broadbandindex', 'broadbandcreate','broadbandupdate','broadbandview','broadbanddelete','dedicatedindex','dedicatedcreate','dedicatedupdate','dedicatedview','dedicateddelete','localloopindex','localloopcreate','localloopupdate','localloopview','localloopdelete','returnequipmentbroadband','returnmacequipmentbroadband'], //'broadbanddeletemultiple', 'dedicateddeletemultiple', ,'localloopdeletemultiple'

			'rules' => [
                        [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rules, $action)
                        {
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
	## BROADBOND ACTIONS
    /**
     * Lists all open broadband complains.
     * @return mixed
     */
    public function actionBroadbandindex()
    {       
        $complainType = 'Broadband';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';

        /*$queryParams = Yii::$app->request->queryParams;*/
        
        ## DATE RANGE FILTER 
        $postedArray = Yii::$app->request->get();
		$returnParams = $this->getQueryParams($complainType,$postedArray);
		$queryParams = $returnParams['query_params'];
		$frmStartDate = $returnParams['frm_start_date'];
		$frmEndDate = $returnParams['frm_end_date'];		
        
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->pagination->pageSize=20;
		
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
			'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }

    /**
     * Creates a new Broadband Customer Complain model.
     * If creation is successful, the browser will be redirected to the 'broadbandindex' page.
     * @return mixed
     */
    public function actionBroadbandcreate()
    {
        $complainType = 'Broadband';
        $model 	= new Tblcustomercomplains();
        $model->scenario = 'create';          
        $arrData = $this->getCustomersByType($complainType);        
        if (Yii::$app->request->isPost) {
        	$arrPostData = Yii::$app->request->post();        	
        	if($this->setCustomerComplain($complainType, $model, $arrPostData)){
				Yii::$app->session->setFlash('success', $complainType." complain created successfully.");
            	return $this->redirect(['broadbandindex']);
			}
		} else {
			return $this->render('create', [
                'model' => $model,
                'data'=>$arrData,
                'complain_type'=>$complainType                
            ]);
		}        
    }
   	
   	public function actionBroadbandupdate($id)
    {
    	$complainType = 'Broadband';
    	$model = $this->findModel($id);
        $model->scenario = 'create';
    	$arrData = $this->getCustomersByType($complainType);
    	$customerData = array();
    	if(!empty($model)){
    		$customerId = $model->fk_customer_id;
    		$customerData = $this->getCustomer($customerId, $complainType);    		
    		$model->updated_at=date('Y-m-d h:i:s');
    		if (Yii::$app->request->isPost) {
	        	$arrPostData = Yii::$app->request->post();
	        	if($this->setCustomerComplain($complainType, $model, $arrPostData, 'update')){
					Yii::$app->session->setFlash('success', $complainType." complain updated successfully.");
					return $this->redirect(['broadbandindex']);
	            	
				}
			} else {
				return $this->render('update', [
	                'model' => $model,
	                'data'=>$arrData,
	                'cust_data'=> $customerData,
	                'complain_type'=>$complainType             
	            ]);
			} 
		} else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }    	
	}
	/**
     * Display a single broadband complain details .
     * @param integer $id
     * @return mixed
     */
    public function actionBroadbandview($id)
    {		
    	$complainType = 'Broadband';
      	$model = $this->getComplainDetails($id);
    	return $this->render('view',['model'=>$model, 'complain_type'=>$complainType]);
    }
   
    public function actionBroadbandrespond($id)
    {       
        $complainType = 'Broadband';
        $model = $this->findModel($id);
        $model->scenario = 'offlinerespond';       
        return $this->renderAjax('respondcomplain',['model'=>$model, 'id'=>$id, 'complain_type'=>$complainType]);
    }

    /**
    * submit activation date and make customer active and reomove from pending installation
    */
    public function actionSubmitbrespond($id)
    {
        $complainType = 'Broadband';
        $model = $this->findModel($id);
        //$model->scenario = 'offlinerespond';
        if (Yii::$app->request->isPost) {
            $arrPostData = Yii::$app->request->post();           
            if($this->setComplainRespond($complainType, $model, $arrPostData)){
                Yii::$app->session->setFlash('success', $complainType." data updated successfully.");
                return $this->redirect(['broadbandindex']);
            }
        }
    }
    public function actionBroadbandjoballocation($id)
    {       
        $complainType = 'Broadband';
        $model = $this->findModel($id);
        $arrData = $this->getStaff();     
        $model->scenario = 'onsitejoballocation';       
        return $this->renderAjax('joballocation',['model'=>$model, 'id'=>$id, 'complain_type'=>$complainType, 'arr_data'=>$arrData]);
    }
    public function actionBbjoballocationrespond($id)
    { 
        $complainType = 'Broadband';
        $model = $this->findModel($id);
        $model->scenario = 'joballocationrespond';        
        $getUplodedDocs   =   Tblcustomercomplaindocs::find()->where(['fk_complain_id'=>$id])->all();
       
        $imageModel =   new Tblcustomercomplaindocs();      
        return $this->renderAjax('joballocationrespond',['model'=>$model, 'imageModel'=>$imageModel, 'getUplodedDocs'=>$getUplodedDocs, 'id'=>$id, 'complain_type'=>$complainType]);
    }

    public function actionSubmitbjrespond($id)
    {
        $complainType = 'Broadband';
        $model = $this->findModel($id);        
       
        $imageModel =   new Tblcustomercomplaindocs();
        if (Yii::$app->request->isPost) {            
            $filesArray = UploadedFile::getInstances($imageModel, 'filepath');
            if(!empty($filesArray)){
               $this->saveComplainDocs($id, $filesArray); 
            }            
           
            $arrPostData = Yii::$app->request->post();   
            //echo '<pre>';print_r($arrPostData);die;        
            if($this->setComplainRespond($complainType, $model, $arrPostData)){
                Yii::$app->session->setFlash('success', $complainType." data updated successfully.");
                return $this->redirect(['broadbandjoballocationindex']);
            }
        }
    }    
    /**
     * Deletes an existing complain model.
     * If deletion is successful, the browser will be redirected to the 'broadbandindex' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBroadbanddelete($id)
    {
    	if($this->deleteCustomerComplain($id)){
			Yii::$app->session->setFlash('success', 'The complain has been deleted successfully');    
            return $this->redirect(['broadbandindex']);
		} else { 
			throw new NotFoundHttpException('Opps! something went wrong.');
		}    	
	}
	/**
     * Deletes multiple complains.
     * If deletion is successful, the browser will be redirected to the 'broadbandindex' page.
     * @param integer $id
     * @return mixed
     */
   	public function actionBroadbanddeletemultiple()
    {
		$ids = yii::$app->request->post('ids');
		if($this->deleteMultCustomerComplain($ids)){
			return 'success';
		} else {
			Yii::$app->session->setFlash('errorMessage','Opps! something went wrong.');
			return $this->redirect(['broadbandindex']);
		}
		
	}
   
    /**
     * Lists all open broadband onsite complains job allocations.
     * @return mixed
     */
    public function actionBroadbandjoballocationindex()
    {       
        $complainType = 'Broadband';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';

        /*$queryParams = Yii::$app->request->queryParams;*/
        
        ## DATE RANGE FILTER 
        $postedArray = Yii::$app->request->get();
        $returnParams = $this->getQueryParams($complainType,$postedArray, true);
        $queryParams = $returnParams['query_params'];
        $frmStartDate = $returnParams['frm_start_date'];
        $frmEndDate = $returnParams['frm_end_date'];        
        
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->pagination->pageSize=20;
        
        // echo "<xmp>";
        // print_r($dataProvider);
        // echo "</xmp>";
        // die;
        return $this->render('joballocationindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
            'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }

    public function actionBroadbandjview($id)
    {       
        $complainType = 'Broadband';        
        $model = $this->getComplainDetails($id, true);
        
        return $this->render('joballocationview',['model'=>$model, 'complain_type'=>$complainType]);
    }


   

	## END BROADBOND ACTIONS

	## DEDICATED ACTIONS
	/**
     * Lists all open dedicated complains.
     * @return mixed
     */
	public function actionDedicatedindex()
    {       
        $complainType = 'Dedicated';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';       
        //$queryParams = Yii::$app->request->queryParams;
        
		/*## LIST ONLY BROADBAND AND OPENED TICKETS
        $queryParams['TblcustomercomplainsSearch']['complain_type']=$complainType;
        $queryParams['TblcustomercomplainsSearch']['ticket_status']='open';*/
        ## DATE RANGE FILTER 
        $postedArray = Yii::$app->request->get();
		$returnParams = $this->getQueryParams($complainType,$postedArray);
		$queryParams = $returnParams['query_params'];
		$frmStartDate = $returnParams['frm_start_date'];
		$frmEndDate = $returnParams['frm_end_date'];
		
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->pagination->pageSize=20;
		
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
			'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }
     /**
     * Creates a new dedicated Customer Complain model.
     * If creation is successful, the browser will be redirected to the 'dedicatedindex' page.
     * @return mixed
     */
   	public function actionDedicatedcreate()
    {        
        $complainType = 'Dedicated';
        $model 	= new Tblcustomercomplains();
        $model->scenario = 'create';        
        $arrData = $this->getCustomersByType($complainType);
        
        if (Yii::$app->request->isPost) {
        	$arrPostData = Yii::$app->request->post();
        	if($this->setCustomerComplain($complainType, $model, $arrPostData)){
				Yii::$app->session->setFlash('success', $complainType." complain created successfully.");
            	return $this->redirect(['dedicatedindex']);
			}
		} else {
			return $this->render('create', [
                'model' => $model,
                'data'=>$arrData,
                'complain_type'=>$complainType              
            ]);
		}
    }
    
    public function actionDedicatedupdate($id)
    {
    	$complainType = 'Dedicated';
    	$model = $this->findModel($id);
        $model->scenario = 'create';
    	$arrData = $this->getCustomersByType($complainType);
    	$customerData = array();
    	if(!empty($model)){
    		$customerId = $model->fk_customer_id;
    		$customerData = $this->getCustomer($customerId, $complainType);    		
    		$model->updated_at=date('Y-m-d h:i:s');
    		if (Yii::$app->request->isPost) {
	        	$arrPostData = Yii::$app->request->post();
	        	if($this->setCustomerComplain($complainType, $model, $arrPostData, 'update')){
					Yii::$app->session->setFlash('success', $complainType." complain updated successfully.");
					return $this->redirect(['dedicatedindex']);
	            	
				}
			} else {
				return $this->render('update', [
	                'model' => $model,
	                'data'=>$arrData,
	                'cust_data'=> $customerData,
	                'complain_type'=>$complainType             
	            ]);
			} 
		} else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }    	
	}
	/**
     * Display a single dedicated complain details .
     * @param integer $id
     * @return mixed
     */
	public function actionDedicatedview($id)
    {		
    	$complainType = 'Dedicated';
      	$model = $this->getComplainDetails($id);
    	return $this->render('view',['model'=>$model, 'complain_type'=>$complainType]);
    }
    
    public function actionDedicatedrespond($id)
    {       
        $complainType = 'Dedicated';
        $model = $this->findModel($id);
        $model->scenario = 'offlinerespond';       
        return $this->renderAjax('respondcomplain',['model'=>$model, 'id'=>$id, 'complain_type'=>$complainType]);
    }

    /**
    * submit activation date and make customer active and reomove from pending installation
    */
    public function actionSubmitdrespond($id)
    {
        $complainType = 'Dedicated';
        $model = $this->findModel($id);
        $model->scenario = 'offlinerespond';
        if (Yii::$app->request->isPost) {
            $arrPostData = Yii::$app->request->post();
            if($this->setComplainRespond($complainType, $model, $arrPostData)){
                Yii::$app->session->setFlash('success', $complainType." data updated successfully.");
                return $this->redirect(['dedicatedindex']);
            }
        }
    }
    public function actionDedicatedjoballocation($id)
    {       
        $complainType = 'Dedicated';
        $model = $this->findModel($id);
        $arrData = $this->getStaff();     
        $model->scenario = 'onsitejoballocation';       
        return $this->renderAjax('joballocation',['model'=>$model, 'id'=>$id, 'complain_type'=>$complainType, 'arr_data'=>$arrData]);
    }
    public function actionDjoballocationrespond($id)
    {        
        $complainType = 'Dedicated';
        $model = $this->findModel($id);
        $model->scenario = 'joballocationrespond';        
        $getUplodedDocs   =   Tblcustomercomplaindocs::find()->where(['fk_complain_id'=>$id])->all();        
        $imageModel =   new Tblcustomercomplaindocs();      
        return $this->renderAjax('joballocationrespond',['model'=>$model, 'imageModel'=>$imageModel, 'getUplodedDocs'=>$getUplodedDocs, 'id'=>$id, 'complain_type'=>$complainType]);
    }

    public function actionSubmitdjrespond($id)
    {
        $complainType = 'Dedicated';
        $model = $this->findModel($id);        
       
        $imageModel =   new Tblcustomercomplaindocs();
        if (Yii::$app->request->isPost) {            
            $filesArray = UploadedFile::getInstances($imageModel, 'filepath');            
            if(!empty($filesArray)){
               $this->saveComplainDocs($id, $filesArray); 
            }            
           
            $arrPostData = Yii::$app->request->post();           
            if($this->setComplainRespond($complainType, $model, $arrPostData)){
                Yii::$app->session->setFlash('success', $complainType." data updated successfully.");
                return $this->redirect(['dedicatedjoballocationindex']);
            }
        }
    }
    /**
     * Deletes an existing complain model.
     * If deletion is successful, the browser will be redirected to the 'dedicatedindex' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDedicateddelete($id)
    {
    	if($this->deleteCustomerComplain($id)){
			Yii::$app->session->setFlash('success', 'The complain has been deleted successfully');    
            return $this->redirect(['dedicatedindex']);
		} else { 
			throw new NotFoundHttpException('Opps something went wrong.');
		}    	
	}
	/**
     * Deletes multiple complains.
     * If deletion is successful, the browser will be redirected to the 'dedicatedindex' page.
     * @param integer $id
     * @return mixed
     */
   	public function actionDedicateddeletemultiple()
    {
		$ids = yii::$app->request->post('ids');
		if($this->deleteMultCustomerComplain($ids)){
			return 'success';
		} else {
			Yii::$app->session->setFlash('errorMessage','Opps! something went wrong.');
			return $this->redirect(['dedicatedindex']);
		}
		
	}
    /**
     * Lists all open broadband onsite complains job allocations.
     * @return mixed
     */
    public function actionDedicatedjoballocationindex()
    {       
        $complainType = 'Dedicated';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';

        $queryParams = Yii::$app->request->queryParams;
        
        ## DATE RANGE FILTER 
        $postedArray = Yii::$app->request->get();
        $returnParams = $this->getQueryParams($complainType,$postedArray, true);
        $queryParams  = $returnParams['query_params'];
        $frmStartDate = $returnParams['frm_start_date'];
        $frmEndDate = $returnParams['frm_end_date'];        
        
        $dataProvider = $searchModel->search($queryParams);
        

        $dataProvider->pagination->pageSize=20;
        
        return $this->render('joballocationindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
            'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }

    public function actionDedicatedjview($id)
    {       
        $complainType = 'Dedicated';       
        $model = $this->getComplainDetails($id, true);
        
        return $this->render('joballocationview',['model'=>$model, 'complain_type'=>$complainType]);
    }

    
    
    ## END DEDICATED ACTIONS
    ## LOCAL LOOP ACTIONS
    /**
     * Lists all open broadband complains.
     * @return mixed
     */
    public function actionLocalloopindex()
    {       
        $complainType = 'Local Loop';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';       
        /*$queryParams = Yii::$app->request->queryParams;        
		## LIST ONLY BROADBAND AND OPENED TICKETS
        $queryParams['TblcustomercomplainsSearch']['complain_type']=$complainType;
        $queryParams['TblcustomercomplainsSearch']['ticket_status']='open';*/
        ## QUERY PARAMS
        $postedArray = Yii::$app->request->get();
		$returnParams = $this->getQueryParams($complainType,$postedArray);
		$queryParams = $returnParams['query_params'];
		$frmStartDate = $returnParams['frm_start_date'];
		$frmEndDate = $returnParams['frm_end_date'];
		
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->pagination->pageSize=20;
		
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
			'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }

    /**
     * Creates a new Broadband Customer Complain model.
     * If creation is successful, the browser will be redirected to the 'broadbandindex' page.
     * @return mixed
     */
    public function actionLocalloopcreate()
    {
        $complainType = 'Local Loop';
        $model 	= new Tblcustomercomplains();
        $model->scenario = 'create';         
        $arrData = $this->getCustomersByType($complainType);        
        if (Yii::$app->request->isPost) {
        	$arrPostData = Yii::$app->request->post();        	
        	if($this->setCustomerComplain($complainType, $model, $arrPostData)){
				Yii::$app->session->setFlash('success', $complainType." complain created successfully.");
            	return $this->redirect(['localloopindex']);
			}
		} else {
			return $this->render('create', [
                'model' => $model,
                'data'=>$arrData,
                'complain_type'=>$complainType                
            ]);
		}        
    }
   	
   	public function actionLocalloopupdate($id)
    {
    	$complainType = 'Local Loop';
    	$model = $this->findModel($id);
        $model->scenario = 'create';
    	$arrData = $this->getCustomersByType($complainType);
    	$customerData = array();
    	if(!empty($model)){
    		$customerId = $model->fk_customer_id;
    		$customerData = $this->getCustomer($customerId, $complainType);    		
    		$model->updated_at=date('Y-m-d h:i:s');
    		if (Yii::$app->request->isPost) {
	        	$arrPostData = Yii::$app->request->post();
	        	if($this->setCustomerComplain($complainType, $model, $arrPostData, 'update')){
					Yii::$app->session->setFlash('success', $complainType." complain updated successfully.");
					return $this->redirect(['localloopindex']);
	            	
				}
			} else {
				return $this->render('update', [
	                'model' => $model,
	                'data'=>$arrData,
	                'cust_data'=> $customerData,
	                'complain_type'=>$complainType             
	            ]);
			} 
		} else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }    	
	}
	/**
     * Display a single broadband complain details .
     * @param integer $id
     * @return mixed
     */
    public function actionLocalloopview($id)
    {		
    	$complainType = 'Local Loop';
      	$model = $this->getComplainDetails($id);
    	return $this->render('view',['model'=>$model, 'complain_type'=>$complainType]);
    }
    public function actionLocallooprespond($id)
    {       
        $complainType = 'Local Loop';
        $model = $this->findModel($id);
        $model->scenario = 'offlinerespond';       
        return $this->renderAjax('respondcomplain',['model'=>$model, 'id'=>$id, 'complain_type'=>$complainType]);
    }

    /**
    * submit activation date and make customer active and reomove from pending installation
    */
    public function actionSubmitlrespond($id)
    {
        $complainType = 'Local Loop';
        $model = $this->findModel($id);
        $model->scenario = 'offlinerespond';
        if (Yii::$app->request->isPost) {
            $arrPostData = Yii::$app->request->post();
            if($this->setComplainRespond($complainType, $model, $arrPostData)){
                Yii::$app->session->setFlash('success', $complainType." data updated successfully.");
                return $this->redirect(['localloopindex']);
            }
        }
    }
    public function actionLocalloopjoballocation($id)
    {       
        $complainType = 'Local Loop';
        $model = $this->findModel($id);
        $arrData = $this->getStaff();     
        $model->scenario = 'onsitejoballocation';       
        return $this->renderAjax('joballocation',['model'=>$model, 'id'=>$id, 'complain_type'=>$complainType, 'arr_data'=>$arrData]);
    }
    public function actionLljoballocationrespond($id)
    {        
        $complainType = 'Local Loop';
        $model = $this->findModel($id);
        $model->scenario = 'joballocationrespond';        
        $getUplodedDocs   =   Tblcustomercomplaindocs::find()->where(['fk_complain_id'=>$id])->all();        
        $imageModel =   new Tblcustomercomplaindocs();      
        return $this->renderAjax('joballocationrespond',['model'=>$model, 'imageModel'=>$imageModel, 'getUplodedDocs'=>$getUplodedDocs, 'id'=>$id, 'complain_type'=>$complainType]);
    }
    public function actionSubmitljrespond($id)
    {
        $complainType = 'Local Loop';
        $model = $this->findModel($id);        
        
        $imageModel =   new Tblcustomercomplaindocs();
        if (Yii::$app->request->isPost) {            
            $filesArray = UploadedFile::getInstances($imageModel, 'filepath');           
            if(!empty($filesArray)){
               $this->saveComplainDocs($id, $filesArray); 
            }            
            
            $arrPostData = Yii::$app->request->post();           
            if($this->setComplainRespond($complainType, $model, $arrPostData)){
                Yii::$app->session->setFlash('success', $complainType." data updated successfully.");
                return $this->redirect(['localloopjoballocationindex']);
            }
        }
    }    
    public function actionLocalloopdelete($id)
    {
    	if($this->deleteCustomerComplain($id)){
			Yii::$app->session->setFlash('success', 'The complain has been deleted successfully');    
            return $this->redirect(['localloopindex']);
		} else { 
			throw new NotFoundHttpException('Opps something went wrong.');
		}    	
	}
	/**
     * Deletes multiple complains.
     * If deletion is successful, the browser will be redirected to the 'dedicatedindex' page.
     * @param integer $id
     * @return mixed
     */
   	public function actionLocalloopdeletemultiple()
    {
		$ids = yii::$app->request->post('ids');
		if($this->deleteMultCustomerComplain($ids)){
			return 'success';
		} else {
			Yii::$app->session->setFlash('errorMessage','Opps! something went wrong.');
			return $this->redirect(['localloopindex']);
		}		
	}
    /**
     * Lists all open broadband onsite complains job allocations.
     * @return mixed
     */
    public function actionLocalloopjoballocationindex()
    {       
        $complainType = 'Local Loop';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';

        /*$queryParams = Yii::$app->request->queryParams;*/
        
        ## DATE RANGE FILTER 
        $postedArray = Yii::$app->request->get();
        $returnParams = $this->getQueryParams($complainType,$postedArray, true);
        $queryParams = $returnParams['query_params'];
        $frmStartDate = $returnParams['frm_start_date'];
        $frmEndDate = $returnParams['frm_end_date'];        
        
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->pagination->pageSize=20;
        
        return $this->render('joballocationindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
            'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }
    
    public function actionLocalloopjview($id)
    {       
        $complainType = 'Local Loop';       
        $model = $this->getComplainDetails($id, true);
        
        return $this->render('joballocationview',['model'=>$model, 'complain_type'=>$complainType]);
    }
	## END LOCAL LOOP ACTIONS
	
    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tblcustomercomplains::findOne(['complain_id'=>$id,'is_deleted'=>'0'])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    ## FUNCTION TO GET COMPLAIN DETAILS BY ID
    protected function getComplainDetails($id, $docFlag = false)
    {
        if($docFlag){
            $model= Tblcustomercomplains::find()->joinWith(['customer','customer.linkcustomerpackage.package','complaindocs'])->where(['complain_id'=>$id])->one();
        } else {
            $model= Tblcustomercomplains::find()->joinWith(['customer','customer.linkcustomerpackage.package'])->where(['complain_id'=>$id])->one();
        }        
        
        return $model; 
    }
    
    ## GET ALL CUSTOMERS OF COMPLAIN TYPE
    protected function getCustomersByType($complainType)
    {
        $arrSolnetId    = Customer::find()->select(['customer_id','solnet_customer_id','name'])->where(['tblcustomer.status'=>'active','is_deleted'=>'0', 'customer_type'=>$complainType])->asArray()->all();   
        $arrData = array();
        if(!empty($arrSolnetId)){
			foreach($arrSolnetId as $key=>$val){
				$arrData[$val['customer_id']] = $val['name']." (".$val['solnet_customer_id'].")";
			}
		}
		return $arrData;
    }
    
    ## CREATE AND UPDATE FUNCTION
    protected function setCustomerComplain($complainType, $model, $postData, $action='create')
    {      
        $incrementValue = '';
        if(isset($postData['custom_data']) && $postData['custom_data'] != '')
        	list($stateId, $incrementValue) = explode("||", $postData['custom_data']);
        if($action == 'create')
        {
        	$lable = 'created';
        	$model->ticket_status = 'open';
        	$model->complain_type = $complainType;
        	$model->fk_user_id = yii::$app->user->identity->user_id;
        	$model->is_deleted = '0';
        	$model->created_at = date('Y-m-d h:i:s');
        	     
		} else if($action == 'update'){
			$lable = 'updated';
			$model->updated_at=date('Y-m-d h:i:s');					
		}
		
        if($model->load($postData)){
    		if($model->save(false)){
    			## INCREMENT THE ID
    			if($incrementValue > 0 && $stateId > 0) //$action == 'create' &&
        		{
        			$arrComplain = Yii::$app->customcomponents->updateComplainIncrementValue($stateId,$incrementValue, $complainType);
				}
    			$logArray = array();
    			$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
        		$logArray['module'] = 'Manage Complain';        		
        		$logArray['action'] = $action;
        		$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has '.$lable.' the "'.$postData['Tblcustomercomplains']['ticket_number'].'"';
        		$logArray['created'] = date('Y-m-d H:i:s');        		
        		Yii::$app->customcomponents->logActivity($logArray);
        		return true;
			}	
		}
		return false;
        
    }
    
    ## COMMON DELETE FUNCTION CODE
    protected function deleteCustomerComplain($id)
    {
    	$model = $this->findModel($id);
    	if(!empty($model)){
    		## IN FUTURE CHECK IF TICKET IS ASSIGNED TO SALES PERSOPN
    		## IF YES DO NOT DELETE
    		$model->is_deleted = '1';
    		if($model->save()){
    			$logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Complain';        		
        		$logArray['action'] = 'delete';        		
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted  "'.$model->ticket_number.'" ';
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);
                return true;
			}
		}
		return false;
	}
	
	## COMMON MULTIPLE DELETE FUNCTION CODE
    protected function deleteMultCustomerComplain($ids)
    {    	
    	if(!empty($ids))
		{
    		foreach($ids as $key=>$id){
    			$db = Yii::$app->db;
				$db->createCommand("UPDATE tbl_customer_complains SET is_deleted='1' WHERE complain_id=".$id)->execute();
				$logArray = array();
	            $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
	            $logArray['module'] = 'Manage Complain';
	            $logArray['action'] = 'delete';
	            $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has deleted the complain(s)';
	            $logArray['created'] = date('Y-m-d H:i:s');
	            Yii::$app->customcomponents->logActivity($logArray);
			}
			return true;
		}
		return false;
	}	
	## COMMON FUNCTION TO GET QUERY PARAMS
	protected function getQueryParams($complainType,$postedArray=array(), $jobAllocation=false)
    {
    	$return = array();
    	$frmStartDate = $frmEndDate = '';
    	$queryParams = Yii::$app->request->queryParams;    	
        

        if(Yii::$app->user->identity->fk_role_id=='23')
        {
            $queryParams['TblcustomercomplainsSearch']['support_site']='onsite';
        }
        // if(Yii::$app->user->identity->fk_role_id=='27'){
        //     $queryParams['TblcustomercomplainsSearch']['support_site']='onsite';
        // }
        // 26 role id NOC ADMIN, 27 FIELD ADMIN
    	## LIST ONLY BROADBAND AND OPENED TICKETS
        $queryParams['TblcustomercomplainsSearch']['complain_type']=$complainType;
        //$params['TblcustomercomplainsSearch']['support_site']

        //$queryParams['TblcustomercomplainsSearch']['ticket_status']= 'open';
        
        ## GET CURRENT WEEK START DATE AND END DATE
        $monday = strtotime("last monday");
		$monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;
		$sunday = strtotime(date("Y-m-d",$monday)." +6 days");
		$this_week_sd = date("Y-m-d",$monday);
		$this_week_ed = date("Y-m-d",$sunday);
		
		$queryParams["TblcustomercomplainsSearch"]["start_date"]   = $this_week_sd;
		$queryParams["TblcustomercomplainsSearch"]["end_date"] 	= $this_week_ed;
        
        if(!empty($postedArray))
		{
			if(!empty($postedArray['start_date']) && !empty($postedArray['end_date']))
			{
				$strStartDate	=	 Yii::$app->formatter->asDate($postedArray['start_date'], 'php:Y-m-d');
				$strEndDate		=	Yii::$app->formatter->asDate($postedArray['end_date'], 'php:Y-m-d');
				$queryParams["TblcustomercomplainsSearch"]["start_date"]   = $strStartDate;
				$queryParams["TblcustomercomplainsSearch"]["end_date"] 	= $strEndDate;				
			}
		}
        if($jobAllocation){
            //$queryParams["TblcustomercomplainsSearch"]["support_site"]   = 'onsite';
            $queryParams["TblcustomercomplainsSearch"]["is_job_allocated"]  = 'yes';
            
        }
		$frmStartDate = date('d-m-Y',strtotime($queryParams["TblcustomercomplainsSearch"]["start_date"]));
		$frmEndDate = date('d-m-Y',strtotime($queryParams["TblcustomercomplainsSearch"]["end_date"]));;
				
		$return['query_params'] = $queryParams;
		$return['frm_start_date'] = $frmStartDate;
		$return['frm_end_date'] = $frmEndDate;
		return $return;
	}
	
    protected function getNocQueryParams($complainType,$postedArray=array(), $jobAllocation=false)
    {
        $return = array();
        $frmStartDate = $frmEndDate = '';
        $queryParams = Yii::$app->request->queryParams;     
        

        if(Yii::$app->user->identity->fk_role_id=='23')
        {
            $queryParams['TblcustomercomplainsSearch']['support_site']='onsite';
        }

        // if(Yii::$app->user->identity->fk_role_id=='27'){
        //     $queryParams['TblcustomercomplainsSearch']['support_site']='onsite';
        // }
        // 26 role id NOC ADMIN, 27 FIELD ADMIN
        ## LIST ONLY BROADBAND AND OPENED TICKETS
        $queryParams['TblcustomercomplainsSearch']['complain_type']=$complainType;
        
        $queryParams['TblcustomercomplainsSearch']['ticket_status'] = 'open';
        
        ## GET CURRENT WEEK START DATE AND END DATE
        $monday = strtotime("first day of this month");
        $monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;
        $sunday = strtotime(date("Y-m-d",$monday)." +6 days");
        $this_week_sd = date("Y-m-d",$monday);
        $this_week_ed = date("Y-m-d");
        
        $queryParams["TblcustomercomplainsSearch"]["start_date"]   = $this_week_sd;
        $queryParams["TblcustomercomplainsSearch"]["end_date"]  = $this_week_ed;
        
        if(!empty($postedArray))
        {
            if(!empty($postedArray['start_date']) && !empty($postedArray['end_date']))
            {
                $strStartDate   =    Yii::$app->formatter->asDate($postedArray['start_date'], 'php:Y-m-d');
                $strEndDate     =   Yii::$app->formatter->asDate($postedArray['end_date'], 'php:Y-m-d');
                $queryParams["TblcustomercomplainsSearch"]["start_date"]   = $strStartDate;
                $queryParams["TblcustomercomplainsSearch"]["end_date"]  = $strEndDate;              
            }
        }
        if($jobAllocation){
            //$queryParams["TblcustomercomplainsSearch"]["support_site"]   = 'onsite';
            $queryParams["TblcustomercomplainsSearch"]["is_job_allocated"]  = 'yes';
            
        }
        $frmStartDate = date('d-m-Y',strtotime($queryParams["TblcustomercomplainsSearch"]["start_date"]));
        $frmEndDate = date('d-m-Y',strtotime($queryParams["TblcustomercomplainsSearch"]["end_date"]));;
                
        $return['query_params'] = $queryParams;
        $return['frm_start_date'] = $frmStartDate;
        $return['frm_end_date'] = $frmEndDate;
        return $return;
    }

    protected function getReportQueryParams($complainType,$postedArray=array(), $jobAllocation=false)
    {
        $return = array();
        $frmStartDate = $frmEndDate = '';
        $queryParams = Yii::$app->request->queryParams;     
        

        if(Yii::$app->user->identity->fk_role_id=='23')
        {
            //$queryParams['TblcustomercomplainsSearch']['support_site']='onsite';
        }
        
        // if(Yii::$app->user->identity->fk_role_id=='27'){
        //     $queryParams['TblcustomercomplainsSearch']['support_site']='onsite';
        // }
        // 26 role id NOC ADMIN, 27 FIELD ADMIN
        ## LIST ONLY BROADBAND AND OPENED TICKETS
        //$queryParams['TblcustomercomplainsSearch']['complain_type'] = $complainType;
        
        $queryParams['TblcustomercomplainsSearch']['ticket_status'] = 'open';
        
        ## GET CURRENT WEEK START DATE AND END DATE
        $this_week_sd = date('01-m-Y');
        $this_week_ed = date('30-m-Y');
        
        $queryParams["TblcustomercomplainsSearch"]["start_date"]   = $this_week_sd;
        $queryParams["TblcustomercomplainsSearch"]["end_date"]  = $this_week_ed;
        
        if(!empty($postedArray))
        {
            if(!empty($postedArray['start_date']) && !empty($postedArray['end_date']))
            {
                $strStartDate   =  Yii::$app->formatter->asDate($postedArray['start_date'], 'php:Y-m-d');
                $strEndDate     =  Yii::$app->formatter->asDate($postedArray['end_date'], 'php:Y-m-d');
                $queryParams["TblcustomercomplainsSearch"]["start_date"]   = $strStartDate;
                $queryParams["TblcustomercomplainsSearch"]["end_date"]  = $strEndDate;              
            }
        }
        if($jobAllocation){
            //$queryParams["TblcustomercomplainsSearch"]["support_site"]   = 'onsite';
            $queryParams["TblcustomercomplainsSearch"]["is_job_allocated"]  = 'yes';
            
        }
        
        $frmStartDate = date('d-m-Y',strtotime($queryParams["TblcustomercomplainsSearch"]["start_date"]));
        $frmEndDate = date('d-m-Y',strtotime($queryParams["TblcustomercomplainsSearch"]["end_date"]));;
                
        $return['query_params'] = $queryParams;
        $return['frm_start_date'] = $frmStartDate;
        $return['frm_end_date'] = $frmEndDate;

        //echo '<pre>';print_r($return);die;
        return $return;
    }

	## FUNCTION TO GET CUSTOMER DETAILS BY ID
    protected function getCustomer($id, $complain_type)
    {
    	$custmerDetails = array();
		$customerData = Customer::find()->where(['customer_id'=>$id])->one();		
		$modelLinkCustPackage	= Linkcustomepackage::find()->where(['fk_customer_id'=>$id,'is_current_package'=>'yes'])->one();
		
		if(!empty($customerData)){			
			if($customerData->user_type=='home') {
				$custmerDetails['email'] = $customerData->email_address;
			} elseif($customerData->user_type=='corporate'){
				$custmerDetails['email'] = $customerData->email_it;
			}			
			$custmerDetails['mobile_no'] = $customerData->mobile_no;
			$custmerDetails['phone_number'] = '-';
			if($customerData->phone_number != '')
				$custmerDetails['phone_number'] = $customerData->phone_number;			
			$custmerDetails['state_id'] = $customerData->fk_state_id;
			
		}
		$custmerDetails['package_title'] = '-';
		$custmerDetails['speed'] = '-';
		
		if(!empty($modelLinkCustPackage)){
			$custmerDetails['package_title'] = $modelLinkCustPackage->package->package_title;
			$custmerDetails['speed'] = $modelLinkCustPackage->package_speed.' '.$modelLinkCustPackage->speed->speed_type;
			$custmerDetails['billing_address'] = $modelLinkCustPackage->installation_address;		
		}
		
		## GET TICKETNUMBER. PASS CUSTOMER'S STATE ID
		$custmerDetails['ticket_no'] = '';
		$custmerDetails['increment_value'] = '';
		$intStateId = $customerData->fk_state_id;
        $arrTicketNo =  Yii::$app->customcomponents->GetCoplainTicketNo($intStateId, $complain_type);
        if(!empty($arrTicketNo))
		{			
			$custmerDetails['ticket_no'] = $arrTicketNo['current_ticket_no'];
			$custmerDetails['increment_value'] = $arrTicketNo['increment_value'];
			
		}
		/*echo "<xmp>";
		print_r($custmerDetails);
		echo "</xmp>";
		die;*/
		return $custmerDetails;
	}
    ## UPDATE COMPLAIN RESPOND FUNCTION
    protected function setComplainRespond($complainType, $model, $postData)
    { 
        if($model->load($postData)){
            if($model->save(false)){
                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'Manage Complain';                
                $logArray['action'] = "Complain Respond: ".$complainType;
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has responded to the "'.$model->ticket_number;
                $logArray['created'] = date('Y-m-d H:i:s');             
                Yii::$app->customcomponents->logActivity($logArray);
                return true;
             }
        }
        return false;
    }
    public function actionGetcustomerdetails($id, $complain_type)
	{		
		$custmerDetails = $this->getCustomer($id, $complain_type);	
		$obj = json_encode($custmerDetails);
		return $obj;
	}
    ## GET ALL ENGINEERS
    protected function getStaff()
    {
        $arrSolnetId    = Nocstaff::find()->select(['staff_id','staff_name'])->where(['noc_staff.status'=>'Active'])->asArray()->all();   
        $arrData = array();
        if(!empty($arrSolnetId)){
            foreach($arrSolnetId as $key=>$val){
                $arrData[$val['staff_id']] = $val['staff_name'];
            }
        }
        return $arrData;
    }

     ## UPDATE COMPLAIN RESPOND FUNCTION
    protected function saveComplainDocs($id, $filesArray)
    { 
        $connection =    \Yii::$app->db;        
        $uploadPath = Yii::$app->basePath.'/web/uploads/complain_docs/'.$id.'/';
        if(!(file_exists($uploadPath))){
            $dirCreated = mkdir($uploadPath);            
        }
        if(!empty($filesArray)){
            foreach ($filesArray as $key=>$file){
                $strTimeStamp = time();
                $file->saveAs($uploadPath . $file->baseName.$strTimeStamp. '.' . $file->extension);
                $filepath=$file->baseName.$strTimeStamp.'.'.$file->extension;
                $connection->createCommand()->insert('tbl_customer_complain_docs', ['fk_complain_id' =>$id ,'filepath'=>$filepath,'created_at'=>date('Y-m-d h:i:s')])->execute();
            }
        }
        //return false;
    }

    public function actionRemove($id, $filepath,$complain_id)
    {
        $connection = \Yii::$app->db;        
        $docMmodel   =   Tblcustomercomplaindocs::find()->where(['doc_id'=>$id])->one();
        $uploadPath = Yii::$app->basePath.'/web/uploads/complain_docs/'.$complain_id.'/';
        //$uploadPath = '/web/uploads/complain_docs/';
        if(!empty($id) && !empty($filepath) && !empty($complain_id))
        {
            $delete = $connection->createCommand()->delete("tbl_customer_complain_docs", "filepath = '".$filepath."' and fk_complain_id =".$complain_id)->execute();
            if($delete)
            {
                @unlink($uploadPath.$filepath);
                return true;
            }
        }       
        return false;
    }


    ## Akshay Added functions

    ## Add EQUIPEMENT functions Start
    public function actionAddequipment($id)
    {
        $session    = Yii::$app->session;
        $model = new CustomerEquipments;
        if(Yii::$app->request->post())
        {   
            $arrPostData = Yii::$app->request->post();
            
            $connection = Yii::$app->getDb();
            foreach ($arrPostData['CustomerEquipments']['fk_equipments_id'] as $key => $arrFkEquIdValue)
            {
                if(isset($arrFkEquIdValue[0]) && $arrFkEquIdValue[0] != '')
                {
                    $intEquMentId = $arrFkEquIdValue[0];
                    if(isset($arrPostData['quntity'][$key])){
                        $intQuntity   = $arrPostData['quntity'][$key];
                    }
                    if(isset($arrPostData['inv_type'][$key])){
                        $intEquType   = $arrPostData['inv_type'][$key];
                    }

                    //echo '<pre>';print_r($intEquMentId);echo '<pre>';die;

                    //Update  fin_equipments table quntity
                    $command = $connection->createCommand('UPDATE `fin_equipments` SET quantity_out = quantity_out + '.$intQuntity.', quantity_in_hand = quantity_in_hand - '.$intQuntity.' WHERE equipment_id ='.$intEquMentId);
                    //$command->execute();

                    //echo '<pre> >>';print_r($command->execute());echo '<pre>';die;

                    //insert equipments to tbl_customer_equipments.
                    $connection->createCommand()->insert('tbl_customer_equipments',
                    [   
                        'fk_equipments_id'  => $intEquMentId,
                        'fk_customer_id'    => $id,
                        'fk_comp_id'        => $id,
                        'quantity'          => $intQuntity,
                        'euipment_type'     => $intEquType,
                        'added_by'          => yii::$app->user->identity->user_id,
                        'status'            => 'active',
                    ])
                    ->execute();

                    $intLastInsertedId = $connection->getLastInsertID();
                    
                    //Update mac address stuatus
                    if(isset($arrPostData['mac_address'][$key])){
                        foreach ($arrPostData['mac_address'][$key] as $arrMacKey => $arrMacValue) {
                            $command = $connection->createCommand('UPDATE `fin_equipments_macs` SET `assigned_status` = "Assigned", `assigned_by` = '.yii::$app->user->identity->user_id.' , `assigned_id` = '.$intLastInsertedId.' , `assigned_to` = "coustomer",`fk_comp_id` = '.$id.' WHERE equipments_mac_id ='.$arrMacValue);
                            $command->execute();
                        }
                    }
                }
                else
                {
                    $session->setFlash('error_msg_eqip',"Please Enter a values");
                    return $this->redirect(Yii::$app->request->referrer);
                }

            }
            $session->setFlash('success_msg_eqip',"Equipments Added Successfully");
            //return $this->redirect(Yii::$app->request->referrer);
            return $this->redirect(['/customercomplain/addequipmenttocustomercomplain/'.$id]);
        }
        $arrEquipData = Equipments::find()->all();
        $arrEquipList = ArrayHelper::map($arrEquipData,'equipment_id','model_type');
        
        return  $this->render('add_equipment',
        [
            'id'            => $id,
            'model'         => $model,
            'arrEquipList'  => $arrEquipList
        ]);
    }
    
    public function actionGetbrandname($intId,$id)
    {
        $hmtl = '';
        if(!empty($id)){
            $arrResultEqu = Equipments::find()->where(['equipment_id'=>$id])->all();
            if(!empty($arrResultEqu)) {
                foreach($arrResultEqu as $value){
                    if($value->type == 'Normal'){
                        $hmtl .="<input type='hidden' name='inv_type[]' value='normal'>
                                    <div class='col-md-2'>
                                        <label>Brand Name</label><input type='text' value='".$value->brand_name."' name='brandName[".$intId."][]' class='form-control' readonly >
                                    </div>
                                    <div class='col-md-2'>
                                        <label>Quantity In Hand </label>
                                        <input type='text' name='inHandQuntity[]' class='form-control' value='".$value->quantity_in_hand."' readonly> 
                                    </div>
                                    <div class='col-md-2'>
                                        <label>Quantity</label>
                                        <input type='text' value='' name='quntity[]' class='form-control' onkeypress='javascript:return isNumber(event)' required> 
                                    </div>";
                    }else{
                        $arrResult = EquipmentsMacs::find()->where(['fk_equipment_id' => $value->equipment_id ,'assigned_status' =>'Not Assigned'])->all();

                        /*$arrResult = EquipmentsMacs::find()->where(['fk_equipment_id'=>$value->equipment_id])->all();*/
                        $hmtl .=  "<input type='hidden' name='inv_type[]' value='mac'>
                                    <div class='col-md-2'>
                                    <label>Brand Name</label>
                                    <input type='text' value='".$value->brand_name."' name='brandName[".$intId."][]' class='form-control' readonly >
                                    </div>
                                    <div class='col-md-2'>
                                        <label>Quantity In Hand </label>
                                        <input type='text' name='inHandQuntity[]' class='form-control' value='".$value->quantity_in_hand."' readonly> 
                                    </div>
                                    <div class='col-md-2'>
                                        <label>Quantity</label>
                                        <input type='text' name='quntity[]' class='form-control' onkeypress='javascript:return isNumber(event)' required>
                                    </div>
                                    <div class='col-md-2'>
                                        <label>Mac Address</label>
                                        <select name='mac_address[".$intId."][]' class='form-control multipleSelect' multiple required>";
                                        foreach ($arrResult as $arrValue) {
                                          $hmtl .=  "<option value='".$arrValue->equipments_mac_id."'>".$arrValue->serial_number."</option>";
                                        }
                        $hmtl .=  "</select>
                                    </div>";
                    }
                    return $hmtl;
                }
            }
             else{
                  echo "-";
             }
        }
    }

    public function actionGetequipment($id)
    {
        $intNoOfEqu = '';
        $model = new CustomerEquipments;
        if(Yii::$app->request->post())
        {   
            $arrPostData = Yii::$app->request->post();
            $intNoOfEqu  = $arrPostData['nofoequipment'];
        }
        $arrEquipData = Equipments::find()->all();
        $arrEquipList = ArrayHelper::map($arrEquipData,'equipment_id','model_type');
        $arrResultEqupment = CustomerEquipments::find()->joinWith(['equmentData','equmentMacData'])->where(['fk_customer_id'=>$id])->andWhere(['!=', 'quantity_in_hand', '0'])->all();

        return $this->render('add_equipment_complain',
        [
            'id'                 => $id,
            'model'              => $model,
            'arrEquipList'       => $arrEquipList,
            'arrResultEqupment'  => $arrResultEqupment,
            'intNoOfEqu'         => $intNoOfEqu
        ]);
    }
    
    public function actionDeleteequipment($id)
    {
        $session                 = Yii::$app->session;
        $modelEquipments         = new Equipments;
        $modelEquipmentsMacs     = new EquipmentsMacs;

        $modelCustomerEquipments = CustomerEquipments::findOne(['id' => $id]);
        
        $connection = Yii::$app->getDb();

        //echo '<pre>';print_r($modelCustomerEquipments);echo '</pre>';die;

        $command = $connection->createCommand('UPDATE fin_equipments SET quantity_out = quantity_out - '.$modelCustomerEquipments->quantity.', quantity_in_hand = quantity_in_hand + '.$modelCustomerEquipments->quantity.' WHERE equipment_id ='.$modelCustomerEquipments->fk_equipments_id);
        $command->execute();

        $modelCustomerEquipments->delete();

        $command = $connection->createCommand('UPDATE fin_equipments_macs SET assigned_status = "Not Assigned", assigned_by = 0 ,assigned_id = 0 ,assigned_to = "NULL" WHERE assigned_id ='.$id);
        $command->execute();
        $session->setFlash('success_msg',"Equipments Deleted Successfully");
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAddequipmenttocustomercomplain($id)
    {
        $complainType = 'Broadband';
        $model = $this->findModel($id);
        $model->scenario = 'add equipment';
        $arrResultEqupment = CustomerEquipments::find()->joinWith(['equmentData','equmentMacData'])->where(['fk_customer_id'=>$id])->all();

        //echo '<pre>';print_r($arrResultEqupment);die;

        return $this->render('add_equipment_complain', [
            'model' => $model,           
            'arrResultEqupment' => $arrResultEqupment,           
            'id' => $id           
        ]); 
    }

    // Return Normal Equipment
    public function actionReturnequipmentbroadband($id)
    {
        $arrResultEqupment = CustomerEquipments::find()->joinWith(['equmentData'])->where(['id'=>$id])->one();

        if(!empty($arrResultEqupment))
        {
            $arrPostData = Yii::$app->request->post();  
        
            if(isset($arrPostData['status']))
            {
                // echo '<pre>';print_r($arrResultEqupment);echo '<br/>';
                // echo '<pre>';print_r($arrPostData);die;

                $connection = Yii::$app->getDb();

                ## tbl_customer_equipments table
                $query = Yii::$app->db->createCommand()->update('tbl_customer_equipments', ['fk_comp_id' => $id ,'status' => $arrPostData['status'],'return_reasone' => $arrPostData['return_reasone'] ], ['id' => $id])->execute();

                if($arrPostData['status'] != 'active')
                {
                    ## fin_equipments
                    $command1 = $connection->createCommand('UPDATE `fin_equipments` SET quantity_out = quantity_out - '.$arrResultEqupment->quantity.', quantity_in_hand = quantity_in_hand + '.$arrResultEqupment->quantity.' WHERE equipment_id ='.$arrResultEqupment->fk_equipments_id);

                    $command1->execute();
                }

                if($arrPostData['status'] == 'returned')
                {
                    ## tbl_customer_equipments table
                    $command2 = $connection->createCommand('UPDATE tbl_customer_equipments SET return_equipment_quntity = return_equipment_quntity + 1, `return_status`="returned" WHERE fk_equipments_id ='.$arrResultEqupment->id);

                }
                elseif ($arrPostData['status'] == 'broken')
                {
                    ## tbl_customer_equipments table
                    $command2 = $connection->createCommand('UPDATE tbl_customer_equipments SET broken_equipment_quntity = broken_equipment_quntity + 1, `return_status`= "returned"  WHERE fk_equipments_id ='.$arrResultEqupment->id);
                }
                $command2->execute();

                $connection->createCommand()->insert('tbl_equipments_return_activity',
                    [   
                        'equipments_type'        => 'normal',
                        'fk_equipments_id'      => $arrResultEqupment->fk_equipments_id,
                        'fk_equip_mac_id'       => 0,
                        'fk_comp_id'            => $arrPostData['fk_complain_id'],
                        'replace_euipment_id'   => $arrResultEqupment->fk_equipments_id,
                        'replace_status'        => $arrPostData['status'],
                        'replace_by'            => yii::$app->user->identity->user_id,
                    ])
                ->execute();

                return $this->redirect(Yii::$app->request->referrer);
            }

            return $this->renderAjax('return_equipment_brodband',['model'=>$arrResultEqupment, 'id'=>$id,'type' => 'normal']);
        }
        else
        {
            Yii::$app->session->setFlash('errorMessage','Opps! something went wrong.');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    // Return MAC Equipment
    public function actionReturnmacequipmentbroadband($id)
    {
        $macEquipmentsModel = EquipmentsMacs::findOne($id);

        if(!empty($macEquipmentsModel))
        {
            $arrPostData = Yii::$app->request->post();  
            
            //echo '<pre>';print_r($arrPostData);die;

            if(isset($arrPostData['status']))
            {
                // echo '<pre>';print_r($macEquipmentsModel);echo '<br/>';
                // echo '<pre>';print_r($arrPostData);die;

                $connection = Yii::$app->getDb();
                
                if($arrPostData['status'] != 'active')
                {
                    ## Update  fin_equipments table quntity
                    $command1 = $connection->createCommand('UPDATE `fin_equipments` SET quantity_out = quantity_out - 1, quantity_in_hand = quantity_in_hand + 1 WHERE equipment_id ='.$macEquipmentsModel->fk_equipment_id);

                    $command1->execute();
                }

                if($arrPostData['status'] == 'returned' && $arrPostData['status'] != 'active')
                {
                    ## tbl_customer_equipments table
                    $command2 = $connection->createCommand('UPDATE `tbl_customer_equipments` SET return_equipment_quntity = return_equipment_quntity + 1, `return_status` = "returned" ,`status`= "returned" ,`return_reasone`= "'.$arrPostData['return_mac_reasone'].'",`fk_comp_id` ='.$id.' WHERE fk_equipments_id ='.$macEquipmentsModel->fk_equipment_id);

                }
                elseif ($arrPostData['status'] == 'broken' && $arrPostData['status'] != 'active')
                {
                    ## tbl_customer_equipments table
                    $command2 = $connection->createCommand('UPDATE `tbl_customer_equipments` SET broken_equipment_quntity = broken_equipment_quntity + 1, `return_status` = "returned" ,`status` = "broken",`return_reasone` = "'.$arrPostData['return_mac_reasone'].'",`fk_comp_id` = '.$id.' WHERE fk_equipments_id ='.$macEquipmentsModel->fk_equipment_id);
                }

                $command2->execute();
                
                ## Add activity in tbl_equipments_return_activity table 
                $connection->createCommand()->insert('tbl_equipments_return_activity',
                    [   
                        'equipments_type'        => 'mac',
                        'fk_equipments_id'      => $macEquipmentsModel->fk_equipment_id,
                        'fk_equip_mac_id'       => $macEquipmentsModel->equipments_mac_id,
                        'fk_comp_id'            => $arrPostData['fk_complain_id'],
                        'replace_euipment_id'   => $macEquipmentsModel->equipments_mac_id,
                        'replace_status'        => $arrPostData['status'],
                        'replace_by'            => yii::$app->user->identity->user_id,
                    ])
                ->execute();

                ## fin_equipments_macs
                $macEquipmentsModel->status             = $arrPostData['status'];
                $macEquipmentsModel->return_mac_reasone = $arrPostData['return_mac_reasone'];
                $macEquipmentsModel->save(false);

                return $this->redirect(Yii::$app->request->referrer);
            }
            return $this->renderAjax('return_mac_equipment_brodband',['model'=>$macEquipmentsModel, 'id'=>$id,'type' => 'mac']);
        }
        else
        {
            Yii::$app->session->setFlash('errorMessage','Opps! something went wrong.');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
    ## Add EQUIPEMENT functions End

    ## NOC Functions
    // NOC broad band listing
    public function actionNocbroadbandjoballocationindex()
    {       
        $complainType = 'Broadband';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';

        /*$queryParams = Yii::$app->request->queryParams;*/
        
        ## DATE RANGE FILTER 
        $postedArray = Yii::$app->request->get();
        $returnParams = $this->getNocQueryParams($complainType,$postedArray, true);
        $queryParams = $returnParams['query_params'];
        $frmStartDate = $returnParams['frm_start_date'];
        $frmEndDate = $returnParams['frm_end_date'];        
        
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->pagination->pageSize=20;
        
        // echo "<xmp>";
        // print_r($dataProvider);
        // echo "</xmp>";
        // die;
        return $this->render('nocjoballocatioindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
            'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }

    public function actionNocbroadbandjview($id)
    {       
        $complainType = 'Broadband';        
        $model = $this->getComplainDetails($id, true);
        
        return $this->render('nocjoballocationview',['model'=>$model, 'complain_type'=>$complainType]);
    }

    public function actionNocbroadbandjoballocationajax($id)
    {     
        $complainType = 'Broadband';
        $model = $this->findModel($id);
        $arrData = $this->getStaff();     
        $model->scenario = 'onsitejoballocation';       
        return $this->renderAjax('statusjoballocationajax',['model'=>$model, 'id'=>$id, 'complain_type'=>$complainType, 'arr_data'=>$arrData]);
    }

    public function actionNocsubmitbrespond($id)
    {
        $complainType = 'Broadband';
        $model = $this->findModel($id);
        //$model->scenario = 'offlinerespond';
        if (Yii::$app->request->isPost) {
            $arrPostData = Yii::$app->request->post();           
            if($this->setComplainRespond($complainType, $model, $arrPostData)){
                Yii::$app->session->setFlash('success', $complainType." data updated successfully.");
                return $this->redirect(['nocbroadbandjoballocationindex']);
            }
        }
    }

    // NOC dedicated listing
    public function actionNocdedicatedjoballocationindex()
    {     
        $complainType = 'Dedicated';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';

        $queryParams = Yii::$app->request->queryParams;
        
        ## DATE RANGE FILTER 
        $postedArray = Yii::$app->request->get();
        $returnParams = $this->getNocQueryParams($complainType,$postedArray, true);
        $queryParams  = $returnParams['query_params'];
        $frmStartDate = $returnParams['frm_start_date'];
        $frmEndDate = $returnParams['frm_end_date'];        
        
        $dataProvider = $searchModel->search($queryParams);
        

        $dataProvider->pagination->pageSize=20;
        
        return $this->render('nocjoballocatioindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
            'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }

    public function actionNocdedicatedjview($id)
    {       
        //die('Nocdedicatedjview');
        $complainType = 'Dedicated';       
        $model = $this->getComplainDetails($id, true);
        
        $arrResultEqupment = CustomerEquipments::find()->joinWith(['equmentData','equmentMacData'])->where(['fk_comp_id'=>$id])->all();

        $arrReturnEqupmentResult = EquipmentsReturnActivity::find()->joinWith(['equmentData','equmentMacData'])->where(['fk_comp_id'=>$id])->all();

        //echo '<pre>';print_r($arrReturnEqupmentResult);echo '</pre>';die;

        return $this->render('nocjoballocationview',['model'=>$model, 'complain_type' => $complainType,'arrResultEqupment' => $arrResultEqupment,'arrReturnEqupmentResult' => $arrReturnEqupmentResult]);
    }
    
    public function actionNocdedicatedjoballocationajax($id)
    {     
        $complainType = 'Dedicated';
        $model = $this->findModel($id);
        $arrData = $this->getStaff();     
        $model->scenario = 'onsitejoballocation';       
        return $this->renderAjax('statusjoballocationajax',['model'=>$model, 'id'=>$id, 'complain_type'=>$complainType, 'arr_data'=>$arrData]);
    }

    public function actionNocsubmitdrespond($id)
    {
        $complainType = 'Dedicated';
        $model = $this->findModel($id);
        //$model->scenario = 'offlinerespond';
        if (Yii::$app->request->isPost) {
            $arrPostData = Yii::$app->request->post();           
            if($this->setComplainRespond($complainType, $model, $arrPostData)){
                Yii::$app->session->setFlash('success', $complainType." data updated successfully.");
                return $this->redirect(['nocdedicatedjoballocationindex']);
            }
        }
    }

    // NOC local loop listing
    public function actionNoclocalloopjoballocationindex()
    {       
        $complainType = 'Local Loop';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';

        /*$queryParams = Yii::$app->request->queryParams;*/
        
        ## DATE RANGE FILTER 
        $postedArray = Yii::$app->request->get();
        $returnParams = $this->getNocQueryParams($complainType,$postedArray, true);
        $queryParams = $returnParams['query_params'];
        $frmStartDate = $returnParams['frm_start_date'];
        $frmEndDate = $returnParams['frm_end_date'];        
        
        $dataProvider = $searchModel->search($queryParams);
        $dataProvider->pagination->pageSize=20;
        
        return $this->render('nocjoballocatioindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
            'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }
    
    public function actionNoclocalloopjview($id)
    {       
        $complainType = 'Local Loop';       
        $model = $this->getComplainDetails($id, true);
        
        return $this->render('nocjoballocationview',['model'=>$model, 'complain_type'=>$complainType]);
    }

    public function actionNoclocalloopjoballocationajax($id)
    {     
        $complainType = 'Local Loop';
        $model = $this->findModel($id);
        $arrData = $this->getStaff();     
        $model->scenario = 'onsitejoballocation';       
        return $this->renderAjax('statusjoballocationajax',['model'=>$model, 'id'=>$id, 'complain_type'=>$complainType, 'arr_data'=>$arrData]);
    }

    public function actionNocsubmitlrespond($id)
    {
        $complainType = 'Local Loop';
        $model = $this->findModel($id);
        if (Yii::$app->request->isPost) {
            $arrPostData = Yii::$app->request->post();           
            if($this->setComplainRespond($complainType, $model, $arrPostData)){
                Yii::$app->session->setFlash('success', $complainType." data updated successfully.");
                return $this->redirect(['noclocalloopjoballocationindex']);
            }
        }
    }

    // Report 
    public function actionComplainreport()
    { 
        $complainType = '';
        $searchModel = new TblcustomercomplainsSearch();
        $searchModel->is_deleted= '0';

        /*$queryParams = Yii::$app->request->queryParams;*/
        
        ## DATE RANGE FILTER 
        $postedArray = Yii::$app->request->get();
        $returnParams = $this->getReportQueryParams($complainType,$postedArray, true);
        $queryParams = $returnParams['query_params'];
        $frmStartDate = $returnParams['frm_start_date'];
        $frmEndDate = $returnParams['frm_end_date'];        
        
        $dataProvider = $searchModel->reportsearch($queryParams);
        $dataProvider->pagination->pageSize=20;
        
        return $this->render('complainreportindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'strStartDate' => $frmStartDate,
            'strEndDate'  => $frmEndDate,
            'complain_type'=>$complainType
        ]);
    }
}