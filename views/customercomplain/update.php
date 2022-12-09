<?php

use yii\helpers\Html;

if(!empty($model)){
if($complain_type == 'Broadband') { 
	$link = 'broadbandindex';
	$viewLink = 'broadbandview';
} else if($complain_type == 'Dedicated'){
	$link = 'dedicatedindex';
	$viewLink = 'dedicatedview';
} else if($complain_type == 'Local Loop'){
	$link = 'localloopindex';
	$viewLink = 'localloopview';
}
$this->title = 'Update Complain: ' . $model->ticket_number;
$this->params['breadcrumbs'][] = ['label' => $complain_type.' Complains', 'url' => [$link]];
$this->params['breadcrumbs'][] = ['label' => $model->ticket_number, 'url' => [$viewLink, 'id' => $model->complain_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="package-update">
   
    <?= $this->render('_form', [
        'model' => $model,'data' => $data, 'cust_data'=>$cust_data, 'complain_type'=>$complain_type
    ]); 
}else{
   throw new NotFoundHttpException('The requested page does not exist.');
} ?>
</div>