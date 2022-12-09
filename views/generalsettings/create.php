<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Generalsettings */

$this->title = 'Create Generalsettings';
$this->params['breadcrumbs'][] = ['label' => 'Generalsettings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="generalsettings-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
