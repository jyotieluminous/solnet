<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Package */

$this->title = 'Add Package';
$this->params['breadcrumbs'][] = ['label' => 'Packages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="package-create">

<!--     <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,'flagShowStatus'=>$flagShowStatus
    ]) ?>

</div>
