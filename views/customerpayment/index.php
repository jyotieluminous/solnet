<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerpaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customer Support Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default">
    <div class="box-body">
        <div class="tbllanguage-form">
<div class="customerpayment-index">


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'attribute'=>'name',
                'value'=>function($model)
                {
                    return $model->customer->name;
                },
                'label'=>'Customer Name'
            ],
            [
                'attribute'=>'solnet_customer_id',
                'value'=>function($model)
                {
                    return $model->customer->solnet_customer_id;
                },
                'label'=>'Customer ID'
            ],
            [
                'attribute'=>'invoice_number',
                'value'=>function($model)
                {
                    return $model->invoice->invoice_number;
                }
            ],
            [
                'attribute'=>'amount_paid',
                'value'=>function($model)
                {
                    return number_format($model->amount_paid,2);
                }
            ],
            [
                'attribute'=>'cs_user_id',
                'value'=>function($model)
                {
                    $name = User::find()->where(['user_id'=>$model->cs_user_id])->one();
                    return $name->name;
                },
                'label'=>'User Name'
            ]
            
            // 'bank_admin',
            // 'payment_method',
            // 'cheque_no',
         //'amount_paid',
            // 'payment_date',
            // 'reciept_no',
            // 'comment:ntext',
            // 'created_at',
            // 'updated_at',

           // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
</div>
</div>
</div>