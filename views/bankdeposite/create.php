<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Bankdeposit */

$this->title = 'Mange Bank deposit';
$this->params['breadcrumbs'][] = ['label' => 'Bankdeposits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bankdeposit-create">

    <!-- <h3>Add new deposite into bank</h3> -->

    <?php echo $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'data' => $data,
            'cust_name'=>$cust_name
    ]) ?>

</div>
