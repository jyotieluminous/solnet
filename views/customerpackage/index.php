<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LinkcustomepackageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Linkcustomepackages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="linkcustomepackage-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Linkcustomepackage', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cust_pck_id',
            'fk_customer_id',
            'fk_package_id',
            'fk_speed_id',
            'fk_currency_id',
            // 'package_speed',
            // 'package_price',
            // 'other_service_fee',
            // 'installation_fee',
            // 'payment_type',
            // 'payment_term',
            // 'bulk_pay_start',
            // 'bulk_pay_end',
            // 'installation_address:ntext',
            // 'order_received_date',
            // 'activation_date',
            // 'contract_start_date',
            // 'contract_end_date',
            // 'invoice_start_date',
            // 'is_solnet_bank',
            // 'bank_id',
            // 'bank_name',
            // 'virtual_acc_no',
            // 'account_name',
            // 'contract_number',
            // 'is_disconnected',
            // 'disconnection_date',
            // 'reactivation_date',
            // 'is_current_package',
            // 'contract_status',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
