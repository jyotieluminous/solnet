<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Customerpayment */

$this->title = 'Update Customerpayment: ' . $model->payment_id;
$this->params['breadcrumbs'][] = ['label' => 'Customerpayments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->payment_id, 'url' => ['view', 'id' => $model->payment_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="customerpayment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
