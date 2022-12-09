<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customerpayment */

$this->title = $model->payment_id;
$this->params['breadcrumbs'][] = ['label' => 'Customerpayments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customerpayment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->payment_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->payment_id], [
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
            'payment_id',
            'fk_customer_id',
            'fk_invoice_id',
            'discount',
            'deduct_tax',
            'bank_admin',
            'payment_method',
            'cheque_no',
            'amount_paid',
            'payment_date',
            'reciept_no',
            'comment:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
