<?php

use yii\helpers\Html;

if($complain_type == 'Broadband') { 
	$link = 'broadbandindex';
} else if($complain_type == 'Dedicated'){
	$link = 'dedicatedindex';
} else if($complain_type == 'Local Loop'){
	$link = 'localloopindex';
}

$this->title = 'Create '.$complain_type.' Complain';
$this->params['breadcrumbs'][] = ['label' => $complain_type.' Complains', 'url' => [$link]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create">

    <?= $this->render('_form', [
        'model' => $model,
        'data' => $data,
        'complain_type'=>$complain_type
    ]) ?>

</div>