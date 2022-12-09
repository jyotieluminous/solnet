<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\OutstandingRemarks */

$this->title = $model->outstanding_remark_id;
$this->params['breadcrumbs'][] = ['label' => 'Outstanding Remarks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outstanding-remarks-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->outstanding_remark_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->outstanding_remark_id], [
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
            'outstanding_remark_id',
            'fk_customer_id',
            'fk_invoice_id',
            'remark1:ntext',
            'remark2:ntext',
            'fk_user_id',
            'created_date',
        ],
    ]) ?>

</div>
