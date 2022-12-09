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
use app\models\Generalsettings;
use app\models\GeneralsettingsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Dbbackup;


/**
 * GeneralsettingsController implements the CRUD actions for Generalsettings model.
 */
class GeneralsettingsController extends Controller
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
                        'only' => ['index','create', 'update','delete','view','generalsetting','updatesettings'],
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
     * Lists all Generalsettings models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GeneralsettingsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Generalsettings model.
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
     * Creates a new Generalsettings model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Generalsettings();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->settings_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Generalsettings model.
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
     * Deletes an existing Generalsettings model.
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
        $arrSettings = Generalsettings::find()->where(['status'=>'active','is_deleted'=>'0'])->all();

        return $this->render('general_settings_view',['arrSettings'=>$arrSettings]);
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
                         $isUpdate=Yii::$app->db->createCommand()
                            ->update('settings', 
                                ['value'=>$value,'updated_at'=>date('Y-m-d h:i:s')], 
                                ['settings_id'=>Yii::$app->request->post('id')[$key]])
                            ->execute();
                            if($isUpdate){
                                $logArray = array();
                                $logArray['fk_user_id'] = yii::$app->user->identity->user_id;
                                $logArray['module'] = 'General settings';
                                $logArray['action'] ='update';
                               
                                
                                $logArray['message'] = ' "'.yii::$app->user->identity->name.'" has updated the general settings';
                                $logArray['created'] = date('Y-m-d H:i:s');

                                Yii::$app->customcomponents->logActivity($logArray);
                            }

                     }
                     else{
                         Yii::$app->session->setFlash('errorMessage', $strError);

                        return $this->redirect(['generalsettings']);
                     } 
                }
                 Yii::$app->session->setFlash('success', SETTING_UPDATE_SUCCESSFULL);
            return $this->redirect(['generalsettings']); 
            }
        else {
                Yii::$app->session->setFlash('danger', SETTING_UPDATE_FAIL);
                return $this->redirect(['generalsettings']);
            }

    }

    /**
     * Finds the Generalsettings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Generalsettings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Generalsettings::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDbbackup()
    {
        $searchModel = new Dbbackup();
        $queryParams = Yii::$app->request->queryParams;
     
            if(isset($_REQUEST['filename']) ||isset($_REQUEST['datetime']) ){
                $strFilename = $_REQUEST['filename'];
                $strDatetime  = $_REQUEST['datetime'];
                $queryParams['Dbbackup']['filename']   = $strFilename;
                $queryParams['Dbbackup']['datetime']  = $strDatetime;
            }
       
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('dbbackup', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

        return $this->render('dbbackup',['dataProvider'=>$dataProvider]);
    }
     public function actionBackup()
    {
        ini_set('MAX_EXECUTION_TIME', '-1');
        set_time_limit('-1'); // 
       ini_set('memory_limit','2048M');

        $connection = \Yii::$app->db;
        preg_match("/dbname=([^;]*)/", $connection->dsn, $dbname);
        preg_match("/mysql:host=([^;]*)/", $connection->dsn, $host);
        $host = $host[1];
        $username = $connection->username;
        $password = $connection->password;
        $database_name = $dbname[1];
        $conn = mysqli_connect($host, $username, $password, $database_name);
        $conn->set_charset("utf8");
        $tables = array();
        $sql = "SHOW TABLES";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        $sqlScript = "";
        foreach ($tables as $table) {
            $query = "SHOW CREATE TABLE $table";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_row($result);
            $sqlScript .= "\n\n" . $row[1] . ";\n\n";
            $query = "SELECT * FROM $table";
            $result = mysqli_query($conn, $query);
            $columnCount = mysqli_num_fields($result);
            for ($i = 0; $i < $columnCount; $i ++) {
                while ($row = mysqli_fetch_row($result)) {
                    $sqlScript .= "INSERT INTO $table VALUES(";
                    for ($j = 0; $j < $columnCount; $j ++) {
                        $row[$j] = $row[$j];
                        if (isset($row[$j])) {
                            $sqlScript .= '"' . $row[$j] . '"';
                        } else {
                            $sqlScript .= '""';
                        }
                        if ($j < ($columnCount - 1)) {
                            $sqlScript .= ',';
                        }
                    }
                    $sqlScript .= ");\n";
                }
            }
            $sqlScript .= "\n"; 
        }

        if(!empty($sqlScript))
        {
            $backup_file_name = $database_name . '_backup_'.date("Y-m-d-h-i-s").'.sql';
            $fileHandler = fopen('../web/database/'.$backup_file_name, 'w+');
            $number_of_lines = fwrite($fileHandler, $sqlScript);
            fclose($fileHandler);
            $data = exec('rm ' . $backup_file_name); 
            
            $model = new Dbbackup();
            $model->scenario = 'create';
            $model->filename=$backup_file_name;
            $model->datetime=date("Y-m-d h:i:s");
            if($model->save())
                    { 
                        Yii::$app->session->setFlash('success', 'Database Create successfully.');
                        return $this->redirect(['generalsettings/dbbackup']);
                         
                    }
                    else{
                       
                        Yii::$app->session->setFlash('success','Error while creating the database backup');
                         return $this->redirect(['generalsettings/dbbackup']);
                    }

            return $this->redirect(['generalsettings/dbbackup']);
        }
    }
    public function actionRestore($id)
    {   
        ini_set('MAX_EXECUTION_TIME', '-1');
        set_time_limit('-1'); // 
        $arrDbback = Dbbackup::find()->where(['id'=>$id])->all();
        //print_r($arrDbback);

        /*$connection = mysqli_connect('localhost','root','','crm11');
        $filename = '../web/database/crm_new_backup_2021-02-15-09-11-05.sql';
        $handle = fopen($filename,"r+");
        $contents = fread($handle,filesize($filename));
        $sql = explode(';',$contents);
        foreach($sql as $query){
          $result = mysqli_query($connection,$query);
          if($result){
              echo '<tr><td><br></td></tr>';
              echo '<tr><td>'.$query.' <b>SUCCESS</b></td></tr>';
              echo '<tr><td><br></td></tr>';
          }
        }
        fclose($handle);
        echo 'Successfully imported';*/
       // echo phpinfo();
        $mysqlDatabaseName ='crm11';
        $mysqlUserName ='root';
        $mysqlPassword ='';
        $mysqlHostName ='localhost';
        $mysqlImportFilename = 'D:\wamp64\www\solnet_crm\web\database\crm_new_backup_2021-02-15-09-11-05.sql';

        //Please do not change the following points
        //Import of the database and output of the status
        echo $command='mysql -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' < ' .$mysqlImportFilename;
        exec($command,$worked);
        switch($worked){
        case 0:
        echo 'The data from the file <b>' .$mysqlImportFilename .'</b> were successfully imported into the database <b>' .$mysqlDatabaseName .'</b>';
        break;
        case 1:
        echo 'An error occurred during the import. Please check if the file is in the same folder as this script. Also check the following data again:<br/><br/><table><tr><td>MySQL Database Name:</td><td><b>' .$mysqlDatabaseName .'</b></td></tr><tr><td>MySQL User Name:</td><td><b>' .$mysqlUserName .'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>' .$mysqlHostName .'</b></td></tr><tr><td>MySQL Import Dateiname:</td><td><b>' .$mysqlImportFilename .'</b></td></tr></table>';
        break;
        }

    }

    public function actionDownload($id)
     {
        $arrDbback = Dbbackup::find()->where(['id'=>$id])->all();
        if(count($arrDbback) > 0 )
        {
            $filename = $arrDbback[0]['filename'];
            $f=$filename;   
            $filenamePath = '../web/database/'.$filename;
            $filetype=filetype($filenamePath);
            $filename=basename($filenamePath);
            header ("Content-Type: ".$filetype);
            header ("Content-Length: ".filesize($filenamePath));
            header ("Content-Disposition: attachment; filename=".$filename);
            readfile($filenamePath);
        }

     }
}
