<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OutstandingRemarks */

$this->title = 'Create Outstanding Remarks';
$this->params['breadcrumbs'][] = ['label' => 'Outstanding Remarks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outstanding-remarks-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
