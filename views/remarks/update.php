<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OutstandingRemarks */

$this->title = 'Update Outstanding Remarks: ' . $model->outstanding_remark_id;
$this->params['breadcrumbs'][] = ['label' => 'Outstanding Remarks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->outstanding_remark_id, 'url' => ['view', 'id' => $model->outstanding_remark_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="outstanding-remarks-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
