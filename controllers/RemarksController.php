<?php

namespace app\controllers;

use Yii;
use app\models\OutstandingRemarks;
use app\models\OutstandingRemarkssearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * RemarksController implements the CRUD actions for OutstandingRemarks model.
 */
class RemarksController extends Controller
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
     * Lists all OutstandingRemarks models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OutstandingRemarkssearch();
        $model = new OutstandingRemarks();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		//echo "<pre>"; $dataProvider; die;
        return $this->render('index', [
            'model'=>$model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OutstandingRemarks model.
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
     * Creates a new OutstandingRemarks model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OutstandingRemarks();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->outstanding_remark_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing OutstandingRemarks model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->outstanding_remark_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing OutstandingRemarks model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', 'Remark deleted successfully');
        return $this->redirect(['index']);
    }

    /**
     * Finds the OutstandingRemarks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OutstandingRemarks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OutstandingRemarks::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
