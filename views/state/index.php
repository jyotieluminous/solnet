<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Country;
use app\models\State;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage State';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Add State', ['create'], ['class' => 'btn btn-primary']) ?>

        <?php echo Html::a('Reset Filters',['index'], ['class' => 'btn btn-success']) ?>

        <?php echo Html::a('&nbsp;Delete Selected','javascript:void(0)',['class' => 'btn btn-danger','onClick'=>'javascript:deleteAll()']) ?>
    </p>
      <div class="alert-success alert fade in" id="success" style="display:none"> </div>
     <div class="alert-success alert fade in" id="success_status" style="display:none"> </div>

 <?php if(Yii::$app->session->hasFlash('deleteMessage')) : ?>
            <div class="alert-danger alert fade in">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?php echo Yii::$app->session->getFlash('deleteMessage'); ?>
            </div>
 <?php endif;?> 

<?php Pjax::begin(['id'=>'state-grid']); ?>
<div class="box box-default">
        <div class="box-body">
            <div class="tbllanguage-form">
                <div class="state-index">
                    <?= GridView::widget([
                        
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'id'=>'grid',
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            ['class' => 'yii\grid\CheckboxColumn'],

                            //'state_id',
                            [
                                'attribute'=>'fk_country_id', //fetch country name instead of country id
                                'label'=>'Country',
                                'value'=>'country.country',
                                'filter' => Html::activeDropDownList($searchModel, 'fk_country_id', ArrayHelper::map(Country::find()->asArray()->all(), 'country_id', 'country'),['class'=>'form-control','prompt' => '']),
                            ],
                            'state',
                            'state_prefix',
                           [  
                             'attribute'=>'vat',          
                            'label' => 'VAT',
                            'value' =>function ($data) {
                                    return $data->vat.' %' ;},
                           ],
                            
                           [
                            'attribute'=>'status',
                             'label'=>'Status',
                             'filter'=>[''=>'All','active'=>'Active','inactive'=>'Inactive'],
                             'format'=>'raw',
                             'value' => function($data){
                              
                                {
                                  if($data->status=='active')
                                  {
                                    $url = Url::to('@web/images/active.png');
                                    $strAltText =  'Active';
                                  }else{
                                    $url = Url::to('@web/images/inactive.png'); 
                                    $strAltText =  'Inctive';
                                  }
                                   $redirect_url = Yii::$app->request->baseUrl.'/state/togglestatus';
                                   return Html::a('<img src='.$url.' class="switch-button" />', 'javascript:void(0);', ['title' => $strAltText,'onclick'=>"javascript:changeStatus('".$data->status."','".$data->state_id."','".$redirect_url."','state-grid','State status changed successfully.')"]); 
                                }
                             }
                          ],
                            // 'created_at',
                            // 'updated_at',

                            [
                            'header'=>'Action',
                            'class' => 'yii\grid\ActionColumn',
                             'buttons' => [
                            
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                            'title' => Yii::t('app', 'delete'),
                                            'data-method'=>'POST',
                                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this state?'),
                                ]);
                            }

                          ],
                            ],
                        ],
                    ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
     </div>
 </div>
           
<script type="text/javascript">
        function deleteAll(){ 
            var ids = $('#grid').yiiGridView('getSelectedRows');
            if(ids.length >  0){
            if(confirm("Are you sure you want to delete the selected state?")){
                $.ajax({
                    type     : 'POST',
                    data     : { ids : ids },
                    cache    : false,
                    url      : "<?php echo yii::$app->request->baseUrl.'/state/deletemultiple' ?>",
                    success  : function(response) {
                                                    $.pjax.reload({container:'#state-grid',async:false});

                                                    if(response == 'success'){
                                                        $("#success").css("display", "block");
                                                        $('#success').html("state(s) deleted successfully");

                                                    }
                                                $( "#success" ).fadeOut(5000);
                                                $.pjax.reload({container: '#state-grid',async:false});

                        }

                    });
                }
            }else{
                alert("Please select the records to delete");
            }
        }
    </script>
