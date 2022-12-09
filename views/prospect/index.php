<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\User;
use app\models\Currency;
use app\models\Package;
use app\models\Speed;
use yii\helpers\ArrayHelper;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProspectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Prospects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prospect-index">

   <!--  <h1><?= Html::encode($this->title) ?></h1> -->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Add Prospect', ['create'], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Reset Filters', ['index'], ['class' => 'btn btn-success']) ?>
        <?php echo Html::a('&nbsp;Delete Selected','javascript:void(0)',['class' => 'btn btn-danger','onClick'=>'javascript:deleteAll()']) ?>
    </p>
<div class="alert-success alert fade in" id="success" style="display:none"> </div>
<div class="alert-success alert fade in" id="success_status" style="display:none"> </div>

<?php if ( Yii::$app->session->hasFlash('deleteMessage')):?>
    <div class="alert alert-danger"><?php echo Yii::$app->session->getFlash('deleteMessage');?></div>
<?php endif;?>

 <?php Pjax::begin(['id'=>'prospect-grid']); ?>
<div class="box box-default">
  <div class="box-body">
    <div class="horizontal-scroll">
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'id'=>'grid',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['class' => 'yii\grid\CheckboxColumn'],

                //'prospect_id',

                [
                'attribute'=>'customer_name',
                'label' => 'Prospect Customer',
                'value' =>function ($data) {
                        return $data->customer_name;},
                ],


                [
                'attribute'=>'person_incharge',
                'value' =>function ($data) {
                    if(!empty($data->person_incharge))
                    {
                        return $data->person_incharge;
                    }
                    else{
                        return '--';
                    }
                  }
                ],
                'address:ntext',

                [
                'attribute'=>'mobile_no',
                'value' =>function ($data) {
                    if(!empty($data->mobile_no))
                    {
                        return $data->mobile_no;
                    }
                    else{
                        return '--';
                    }
                  }
                ],

                'email:email',

                [
                'attribute'=>'current_isp',
                'value' =>function ($data) {
                    if(!empty($data->current_isp))
                    {
                        return $data->current_isp;
                    }
                    else{
                        return '--';
                    }
                  }
                ],
                [
                    'attribute'=>'current_contract_end_date',
                    'value'=>function ($data) {
                        if(!empty($data->current_contract_end_date))
                        {
                            return date("d-m-Y",  strtotime($data->current_contract_end_date));
                        }
                        else{
                            return '--';
                        }
                      },
                    'filter' => DatePicker::widget([
                                    'name' => 'ProspectSearch[current_contract_end_date]',
                                    //'value'=>$strDate,
                                    'template' => '{addon}{input}',
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'yyyy-mm-dd'
                                        ]
                                ]),
                ],


                [
                'attribute'=>'current_package',
                'value'=>function ($data) {
                    if(!empty($data->current_package))
                    {
                        return $data->current_package;
                    }
                    else{
                        return '--';
                    }
                  }
                ],

                [
                'attribute'=>'current_currency',
                'value'=>function ($data) {
                    if(!empty($data->current_currency))
                    {
                        return $data->currency->currency;
                    }
                    else{
                        return '--';
                    }
                  },
                'filter' => Html::activeDropDownList($searchModel, 'current_currency', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' =>'']),
                ],
                [
                'attribute'=>'current_isp_bill',
                'value'=>function ($data) {
                    if(!empty($data->current_isp_bill))
                    {
                        return number_format($data->current_isp_bill,2);
                    }
                    else{
                        return '--';
                    }
                  }
                ],

                [
                'attribute'=>'fk_package_id',
                'label'=>'Package Title',
                'value'=>'package.package_title',
                /* 'value'=>function ($data) {
                    if(!empty($data->fk_package_id))
                    {
                        return $data->package->package_title;
                    }
                    else{
                        return '--';
                    }
                  },*/
                'filter' => Html::activeDropDownList($searchModel, 'fk_package_id', ArrayHelper::map(Package::find()->where(['is_deleted'=>'0'])->asArray()->all(), 'package_id', 'package_title'),['class'=>'form-control','prompt' =>'']),
                ],

                [
                'attribute'=>'fk_speed_id',
                'label'=>'Speed Type',
                //'value'=>'speed.speed_type',
                'value'=>function ($data) {
                    if(!empty($data->fk_speed_id))
                    {
                        return $data->speed->speed_type;
                    }
                    else{
                        return '--';
                    }
                  },
                'filter' => Html::activeDropDownList($searchModel, 'fk_speed_id', ArrayHelper::map(Speed::find()->asArray()->all(), 'speed_id', 'speed_type'),['class'=>'form-control','prompt' =>'']),
                ],

                [
                'attribute'=>'package_speed',
                'value'=>function ($data) {
                    if(!empty($data->package_speed))
                    {
                        return $data->package_speed;
                    }
                    else{
                        return '--';
                    }
                  },
                ],
                [
                'attribute'=>'fk_currency_id',
                //'value'=>'currency.currency',
                'value'=>function ($data) {
                    if(!empty($data->fk_currency_id))
                    {
                        return $data->currency->currency;
                    }
                    else{
                        return '--';
                    }
                  },
                'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' =>'']),
                ],

                [
                'attribute'=>'price_quote',
                'value'=>function ($data) {
                    if(!empty($data->price_quote))
                    {
                        return number_format($data->price_quote,2);
                    }
                    else{
                        return '--';
                    }
                  },
                ],



                [
                'attribute'=>'estimate_sign_up_date',
                'value'=>function ($data) {
                    if(!empty($data->estimate_sign_up_date))
                    {
                        return date("d-m-Y",  strtotime($data->estimate_sign_up_date));
                    }
                    else{
                        return '--';
                    }
                  },
                'filter' => DatePicker::widget([
                                    'name' => 'ProspectSearch[estimate_sign_up_date]',
                                    //'value'=>$strDate,
                                    'template' => '{addon}{input}',
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'yyyy-mm-dd'
                                        ]
                                ]),
                ],

                [
                    'attribute'=>'quotation_date',
                    'value'=>function ($data) {
                        if(!empty($data->quotation_date))
                        {
                            return date("d-m-Y",  strtotime($data->quotation_date));
                        }
                        else{
                            return '--';
                        }
                      },
                    'filter' => DatePicker::widget([
                                    'name' => 'ProspectSearch[quotation_date]',
                                    //'value'=>$strDate,
                                    'template' => '{addon}{input}',
                                        'clientOptions' => [
                                            'autoclose' => true,
                                            'format' => 'yyyy-mm-dd'
                                        ]
                                ]),
                ],

               [
                'attribute'=>'fk_user_id',
                'label'=>'Sales Person',
                'value'=>'user.name',
                'filter' => Html::activeDropDownList($searchModel, 'fk_user_id', ArrayHelper::map(User::find()->where(['is_deleted'=>'0'])->asArray()->all(), 'user_id', 'name'),['class'=>'form-control','prompt' =>'']),
                ],

                [
                 'attribute'=>'success_rate',
                 'filter'=>['50'=>'50','70'=>'70','90'=>'90'],
                ],

                [
                 'attribute'=>'is_deal_closed',
                 'filter'=>[''=>'All','yes'=>'Yes','no'=>'no'],
                 'format'=>'raw',
                 'value' => function($data){
                   {
                       $redirect_url = Yii::$app->request->baseUrl.'/prospect/toggledeal';
                       return Html::a(ucfirst($data->is_deal_closed), 'javascript:void(0);',
                        ['onclick'=>"javascript:changeDeal(
                            '".$data->is_deal_closed."',
                            '".$data->prospect_id."',
                            '".$redirect_url."',
                            'prospect-grid',
                            'Deal status changed successfully.')"]);
                    }

                    }
                ],


                //
                // 'is_deleted',
                // 'created_at',
                // 'updated_at',

               [
                'header'=>'Action',
                'options'=>['width'=>100],
                'template'=>'{view} {update} {delete} {link}',
                'buttons' => [


                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('app', 'delete'),
                                'data-method'=>'POST',
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this prospect?'),
                    ]);
                 },

                'link' => function ($url,$model,$key) {
                        return Html::a('<i class="fa fa-file-pdf-o"></i>',['/prospect/pdf','id'=>$model->prospect_id],['target'=>'_blank','data-pjax'=>'0']);
                 },
                ],
                'class' => 'yii\grid\ActionColumn'
                ]
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
            if(confirm("Are you sure you want to delete the selected prospect?")){
                $.ajax({
                    type     : 'POST',
                    data     : { ids : ids },
                    cache    : false,
                    url      : "<?php echo yii::$app->request->baseUrl.'/prospect/deletemultiple' ?>",
                    success  : function(response) {
                                                    $.pjax.reload({container:'#prospect-grid',async:false});

                                                    /*if(response == 'failure'){
                                                        $("#success").css("display", "block");
                                                        $('#success').html("Prospect(s) deleted successfully");

                                                    }*/
                                                /*$( "#success" ).fadeOut(5000);
                                                $.pjax.reload({container: '#prospect-grid',async:false});*/

                        }

                    });
                }
            }else{
                alert("Please select the records to delete");
            }
        }
    </script>
