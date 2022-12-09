<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Linkcustomepackage */

$this->title = $model->cust_pck_id;
$this->params['breadcrumbs'][] = ['label' => 'Linkcustomepackages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="linkcustomepackage-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cust_pck_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cust_pck_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'cust_pck_id',
            'fk_customer_id',
            'fk_package_id',
            'fk_speed_id',
            'fk_currency_id',
            'package_speed',
            'package_price',
            'other_service_fee',
            'installation_fee',
            'payment_type',
            'payment_term',
            'bulk_pay_start',
            'bulk_pay_end',
            'installation_address:ntext',
            'order_received_date',
            'activation_date',
            'contract_start_date',
            'contract_end_date',
            'invoice_start_date',
            'is_solnet_bank',
            'bank_id',
            'bank_name',
            'virtual_acc_no',
            'account_name',
            'contract_number',
            'is_disconnected',
            'disconnection_date',
            'reactivation_date',
            'is_current_package',
            'contract_status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
