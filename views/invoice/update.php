<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Customerinvoice */

$this->title = 'Update Customer Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Manage invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="customerinvoice-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
