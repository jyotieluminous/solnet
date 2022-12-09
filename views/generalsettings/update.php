<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Generalsettings */

$this->title = 'Update Generalsettings: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Generalsettings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->settings_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="generalsettings-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
