<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\OutstandingRemarkssearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Daily JOB Report';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="box box-default">
    <div class="box-body">
        <div class="tbllanguage-form">
            <div class="customerinvoice-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Reset Filters', ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
             'attribute'=>'created_date',
              //'options'=>['style'=>'width:30%;'],
              'filter' =>  DatePicker::widget([
                'model' => $searchModel,
                'attribute'=>'created_date',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]),
            'value'=>function($model)
            {
                $date = date('Y-m-d',strtotime($model->created_date));
                return $date;
            },  
            'format' => 'raw',
            ],
            [
                'attribute'=>'name',
                'value'=> 'customers.name',
            ],
            [
                'attribute'=>'invoice_number',
                'value'=> 'invoices.invoice_number',
            ],
            
            [
                'attribute'=>'package_title',
                'value'=>'invoices.linkcustomepackage.package.package_title'
            ],
            [
                //'label'=>'Package Speed',
                'attribute'=>'package_speed',
                'value'=>function($data){
                    return $data->invoices->linkcustomepackage->package_speed;
                }
            ],
            [
                'attribute'=>'mobile_no',
                'value'=>'customers.mobile_no',
            ],
            'remark1:ntext',
            'remark2:ntext',
            [
                'attribute'=>'user_name',
                'value'=>'user.name',
                'label'=>'Admin Name'
            ],
            // 'fk_user_id',
            // 'created_date',
            [
                            'header'=>'Action',
                            //'options'=>['width'=>'140%'],
                            'class' => 'yii\grid\ActionColumn',
                            'template'=>'{delete} ',
                            'buttons'=>[
                                
                                'delete' => function ($url,$model,$key) {
                                           //echo '<pre>'.print_r($model);die;
                                         return Html::a('<i class="fa fa-trash"></i>', ['/remarks/delete','id'=>$model->outstanding_remark_id], ['title' => 'Delete','data-confirm'=>'Are you sure you want to delete this record??']);
                                },
                            ]
                        ],
        ],
    ]); ?>
</div>
</div>
</div>
</div>
