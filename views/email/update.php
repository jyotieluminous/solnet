<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EmailLogs */

$this->title = 'Update Email Logs: ' . $model->email_log_id;
$this->params['breadcrumbs'][] = ['label' => 'Email Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->email_log_id, 'url' => ['view', 'id' => $model->email_log_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="email-logs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
