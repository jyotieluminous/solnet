<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Customerpayment */

$this->title = 'Create Customerpayment';
$this->params['breadcrumbs'][] = ['label' => 'Customerpayments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customerpayment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
