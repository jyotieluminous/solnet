<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\models\User;
use app\models\Log;
use yii\widgets\Pjax;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Activity Logs';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['start_date']) && isset($_GET['end_date']))
{
 $strStartDate = $_GET['start_date'];
 $strEndDate = $_GET['end_date'];
}
else{
    $strStartDate = '';
    $strEndDate='';
}

?>
<div class="log-index">

   <!--  <h1><?= Html::encode($this->title) ?></h1> -->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
         <?php echo Html::a('Reset filters',['index'],['class' => 'btn btn-success']) ?>

         <?php echo Html::a('Delete Selected','javascript:void(0)',['class' => 'btn btn-danger','onClick'=>'javascript:deleteAll()']) ?>
    </p>
    


     <div class="alert-success alert fade in" id="success" style="display:none"> </div>

<?php $form = ActiveForm::begin(['method' => 'get']); ?>
    <div class="row">
        <div class="col-md-7">
        <?php echo DateRangePicker::widget([
        'name' => 'start_date',
        'value' => $strStartDate,
        'nameTo' => 'end_date',
        'valueTo' => $strEndDate,
        'clientOptions' => [
                    'autoclose'=>true,
                    'format' => 'dd-mm-yyyy'
                ]
         ]);?>
         </div>
         <div class="col-md-3">
             <?php echo Html::submitButton('Search',['class' => 'btn btn-success']) ?>
         </div>
    </div>  
<?php  ActiveForm::end(); ?>
<br>
<?php Pjax::begin(['id'=>'log-grid']); ?>
<div class="box box-default">
   <div class="box-body">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'grid',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
             ['class' => 'yii\grid\CheckboxColumn'],

            //'id',
            'module',
            [
                'attribute'=>'fk_user_id', 
                'label'=>'Sales Person',
                'value'=>'user.name',
                'filter' => Html::activeDropDownList($searchModel, 'fk_user_id', ArrayHelper::map(User::find()->asArray()->all(), 'user_id', 'name'),['class'=>'form-control','prompt' =>'']),
                ],
            //'action',
            [
                'attribute'=>'action',
                'value'=>function($data){
                        return ucfirst($data->action);
                },
                'filter' => [''=>'All','view'=>'View','create'=>'Create','update'=>'Update','delete'=>'Delete',
                'admin'=>'Admin','active'=>'Active','inactive'=>'Inactive']
                         
            ],
            'message:ntext',
            // 'is_deleted',
            //'created', 
            
            [
                'attribute'=>'created',
                'value'=> function($data){
                       return date("d-m-Y h:i:s ",  strtotime($data->created));
                },
                'filter' => DatePicker::widget([
                            'name' => 'LogSearch[created]',
                            //'value'=>$strDate,
                            'template' => '{addon}{input}',
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd-mm-yyyy'
                                ]
                        ])
                 
            ],
            
            ['class' => 'yii\grid\ActionColumn',    
             'header'=>'action',
             'template'=>'{delete}',
             'buttons' => [

                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('app', 'delete'),
                                'data-method'=>'POST',
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this activity?'),
                    ]);
                 },
              ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
  </div>
</div>
<script type="text/javascript">

        function deleteAll(){ 

            var ids = $('#grid').yiiGridView('getSelectedRows');
        
            if(ids.length >  0){
            if(confirm("Are you sure you want to delete the selected activity?")){
                $.ajax({
                    type     : 'POST',
                    data     : { ids : ids },
                    cache    : false,
                    url      : "<?php echo yii::$app->request->baseUrl.'/log/deletemultiple' ?>",
                    success  : function(response) {
                                                    $.pjax.reload({container:'#log-grid',async:false});

                                                    if(response == 'success'){
                                                        $("#success").css("display", "block");
                                                        $('#success').html("Activity(s) deleted successfully");

                                                    }
                                                $( "#success" ).fadeOut(5000);
                                                $.pjax.reload({container: '#log-grid',async:false});

                        }

                    });
                }
            }else{
                alert("Please select the records to delete");
            }
        }
    </script>