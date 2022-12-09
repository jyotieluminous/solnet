<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Currency;
use app\models\Bank;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BankSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Bank Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-index">

   
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Add Bank Details', ['create'], ['class' => 'btn btn-primary']) ?>

        <?php echo Html::a('Reset Filters',['index'], ['class' => 'btn btn-success']) ?>

        <?php echo Html::a('Delete Selected','javascript:void(0)',['class' => 'btn btn-danger','onClick'=>'javascript:deleteAll()']) ?>
    </p>
    <div class="alert-success alert fade in" id="success" style="display:none"> </div>
    <div class="alert-success alert fade in" id="success_status" style="display:none"> </div>

 <?php if(Yii::$app->session->hasFlash('deleteMessage')) : ?>
            <div class="alert-danger alert fade in">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?php echo Yii::$app->session->getFlash('deleteMessage'); ?>
            </div>
 <?php endif;?>
    <div class="box box-default">
        <div class="box-body">
            <div class="tbllanguage-form">
                <div class="package-index">
                <?php Pjax::begin(['id'=>'bank-grid']); ?>
                    <?php echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                         'id'=>'grid',
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            ['class' => 'yii\grid\CheckboxColumn'],

                            //'bank_id',
                            'bank_name',
                            'account_no',
                            'account_name',
                            
                            [
                                'attribute'=>'fk_currency_id', //fetch currency instead of fk_currency_id
                                'label'=>'Currency',
                                'value'=>'currency.currency',
                                'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' =>'']),
                            ],
                            // 'bank_branch',
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
                                   $redirect_url = Yii::$app->request->baseUrl.'/bank/togglestatus';
                                   return Html::a('<img src='.$url.' class="switch-button" />', 'javascript:void(0);', ['title' => $strAltText,'onclick'=>"javascript:changeStatus('".$data->status."','".$data->bank_id."','".$redirect_url."','bank-grid','Bank status changed successfully.')"]); 
                                }
                            }
                             
                          ],
                            // 'is_deleted',
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
                                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this bank?'),
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
            if(confirm("Are you sure you want to delete the selected package?")){
                $.ajax({
                    type     : 'POST',
                    data     : { ids : ids },
                    cache    : false,
                    url      : "<?php echo yii::$app->request->baseUrl.'/bank/deletemultiple' ?>",
                    success  : function(response) {
                                                    $.pjax.reload({container:'#bank-grid',async:false});

                                                    if(response == 'success'){
                                                        $("#success").css("display", "block");
                                                        $('#success').html("Bank(s) deleted successfully");

                                                    }
                                                $( "#success" ).fadeOut(5000);
                                                $.pjax.reload({container: '#bank-grid',async:false});

                        }

                    });
                }
            }else{
                alert("Please select the records to delete");
            }
        }
    </script>

