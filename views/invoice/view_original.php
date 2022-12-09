<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customerinvoice */

$this->title = $model->customer_invoice_id;
$this->params['breadcrumbs'][] = ['label' => 'Customerinvoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customerinvoice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->customer_invoice_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->customer_invoice_id], [
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
            'customer_invoice_id',
            'invoice_number',
            'fk_customer_id',
            'invoice_type',
            'invoice_date',
            'usage_period_from',
            'usage_period_to',
            'due_date',
            'fk_cust_pckg_id',
            'last_due_invoice_id',
            'last_due_amount',
            'last_invoice_date',
            'current_invoice_amount',
            'installation_fee',
            'vat',
            'other_service_fee',
            'total_invoice_amount',
            'deduct_tax',
            'discount',
            'bank_amount',
            'paid_amount',
            'pending_amount',
            'payment_method',
            'next_invoice_date',
            'next_usage_date_from',
            'comments:ntext',
            'status',
            'is_mail_sent',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
