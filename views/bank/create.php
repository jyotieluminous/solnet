<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Bank */

$this->title = 'Add Bank';
$this->params['breadcrumbs'][] = ['label' => 'Banks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?php echo $this->render('_form', [
        'model' => $model,'flagShowStatus'=>$flagShowStatus
    ]) ?>

</div>
