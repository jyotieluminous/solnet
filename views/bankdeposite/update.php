<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Bankdeposit */

$this->title = ' Update Deposit Details'; 
$this->params['breadcrumbs'][] = ['label' => 'Bankdeposits', 'url' => ['index']];
/*$this->params['breadcrumbs'][] = ['label' => $model->bank_deposit_id, 'url' => ['view', 'id' => $model->bank_deposit_id]];*/
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bankdeposit-update">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,'data'=>$data,'cust_name'=>$cust_name
        
    ]) ?>

</div>
