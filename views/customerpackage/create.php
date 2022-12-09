<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Linkcustomepackage */

$this->title = 'Create Linkcustomepackage';
$this->params['breadcrumbs'][] = ['label' => 'Linkcustomepackages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="linkcustomepackage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
