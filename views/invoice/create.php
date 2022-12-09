<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Customerinvoice */

$this->title = 'Create Customerinvoice';
$this->params['breadcrumbs'][] = ['label' => 'Customerinvoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customerinvoice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
