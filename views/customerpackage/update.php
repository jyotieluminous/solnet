<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Linkcustomepackage */

$this->title = 'Update Linkcustomepackage: ' . $model->cust_pck_id;
$this->params['breadcrumbs'][] = ['label' => 'Linkcustomepackages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cust_pck_id, 'url' => ['view', 'id' => $model->cust_pck_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="linkcustomepackage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
