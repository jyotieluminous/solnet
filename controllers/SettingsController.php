<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : SettingsController.php
# Created on : 4th July 2017 by Suraj Malve.
# Update on  : 4th July 2017 by Swati Jadhav.
# Purpose : View and Update Genneral settings
############################################################################################
*/
namespace app\controllers;

use Yii;
use app\models\Settings;
use app\models\SettingsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SettingsController implements the CRUD actions for Settings model.
 */
class SettingsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Settings models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SettingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Settings model.
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
     * Creates a new Settings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Settings();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->settings_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Settings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->settings_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Settings model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /*
    * Diplays general settings 
    * displays Setting model 
    */
     public function actionGeneralsettings()
    {
        $arrSettings = Settings::find()->where(['status'=>'active','is_deleted'=>'0'])->all();

        return $this->render('settings_view',['arrSettings'=>$arrSettings]);
     }
    

     /*
     * Updates general settings with proper messages
     * redirect to same page 
     */
    public function actionUpdatesettings()
    {
       $strError='';
        if (Yii::$app->request->post()) {
            
              foreach (Yii::$app->request->post('value') as $key=>$value) {
                    if(empty($value) &&  $value==''){

                         echo $strError.=Yii::$app->request->post('label')[$key].' is required <br>';
                     }
                 } 
                foreach (Yii::$app->request->post('value') 
                    as $key=>$value){

                     if(empty($strError)){
                         Yii::$app->db->createCommand()
                            ->update('settings', 
                                ['value'=>$value,'updated_at'=>date('Y-m-d h:i:s')], 
                                ['settings_id'=>Yii::$app->request->post('id')[$key]])
                            ->execute();
                     }
                     else{
                         Yii::$app->session->setFlash('errorMessage', $strError);

                        return $this->redirect(['generalsettings']);
                     } 
                }
                $logArray = array();
                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                $logArray['module'] = 'General Settings';
                $logArray['action'] = 'update';
                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has update the setting(s)' ;
                $logArray['created'] = date('Y-m-d H:i:s');
                Yii::$app->customcomponents->logActivity($logArray);
                 Yii::$app->session->setFlash('success', SETTING_UPDATE_SUCCESSFULL);
            return $this->redirect(['generalsettings']); 
            }
        else {
                Yii::$app->session->setFlash('danger', SETTING_UPDATE_FAIL);
                return $this->redirect(['generalsettings']);
            }

    }

    /**
     * Finds the Settings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Settings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Settings::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
