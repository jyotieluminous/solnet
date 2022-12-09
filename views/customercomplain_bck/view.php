<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\NotFoundHttpException;

if(!empty($model)){
if($complain_type == 'Broadband') { 
	$link = 'broadbandindex';
	$updateLink = 'broadbandupdate';
	$backLink = 'customercomplain/broadbandindex';
	$deleteLink = 'broadbanddelete';
} else if($complain_type == 'Dedicated'){
	$link = 'dedicatedindex';
	$updateLink = 'dedicatedupdate';
	$backLink = 'customercomplain/dedicatedindex';
	$deleteLink = 'dedicateddelete';
} else if($complain_type == 'Local Loop'){
	$link = 'localloopindex';
	$updateLink = 'localloopupdate';
	$backLink = 'customercomplain/localloopindex';
	$deleteLink = 'localloopdelete';
}

$this->title = $model->ticket_number;
$this->params['breadcrumbs'][] = ['label' => $complain_type.' Complains', 'url' => [$link]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="package-view">

    <p>
        <?php echo Html::a('Update', [$updateLink, 'id' => $model->complain_id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', [$deleteLink, 'id' => $model->complain_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this complain?',
                'method' => 'post',
            ],
        ]) ?>
         <?php echo Html::a('Back',[$backLink],['class' => 'btn btn-default']) ?>
    </p>
<div class="box box-default">
    <div class="box-body">
        <?php echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
	                'attribute'=>'name',
	                'value'=>function($model)
	                {
	                    return $model->customer->name;
	                },
	                'label'=>'Customer Name'
	            ],
	            [
	                'attribute'=>'custid',
	                'value'=>function($model)
	                {
	                    return $model->customer->solnet_customer_id;
	                },
	                'label'=>'Customer ID'
	            ],
	            [	                
	                'label'=>'Email Address',
	                'value'=>function($model)
	                {
	                    if($model->customer->user_type=='home') {
	                    	return $model->customer->email_address;
						} elseif($model->customer->user_type=='corporate') {
							return $model->customer->email_it;
						} else {
							return '-';
						}
	                    
	                },	                
	            ],
	            [
					'attribute'=>'mobile_no',
					'value'=>function($model)
	                {
	                    return $model->customer->mobile_no;
	                },													
				],
				[
					'label' => 'Phone Number',
					'value'=>function($model)
	                {
	                    if($model->customer->phone_number!='') {
	                    	return $model->customer->phone_number;
						} else {
							return '-';
						}
	                },													
				],
	            [
					'attribute'=>'installation_address',
					'value'=>function($model)
	                {
	                    return $model->customer->linkcustomerpackage->installation_address;
	                },					
				],
				[
					'label' => 'Noc Incharge',
					'attribute'=>'noc_incharge',									
					'value' => function($model){
						return $model->user->name;
				 	}
				],
				[
					'attribute'=>'package_title',
					'value'=>function($model)
	                {
	                    return $model->customer->linkcustomerpackage->package->package_title;
	                },					
				],
				[
					'attribute'=>'package_speed',
					'value'=>function($model)
	                {
	                    return $model->customer->linkcustomerpackage->package_speed." ".$model->customer->linkcustomerpackage->speed->speed_type;
	                },					
				],
                'ticket_number',
                [
                'label' => 'Complain Date Time',
                'value' =>  date('d-m-Y H:i A',strtotime($model->complain_date)),
                ],				
				'caller_name',
	            [
		            'label' => 'Alternative Email',
		            'attribute' => 'alternative_email',
		            'value'=>function($model)
	                {
	                    if($model->alternative_email!='') {
	                    	return $model->alternative_email;
						} else {
							return '-';
						}
	                },						            
		        ],                                
                [
		            'label' => 'Alternative Phone No. 1',
		            'attribute' => 'phone_no_1',						            
		        ],
		        [
					'label' => 'Alternative Phone No. 2',
					'attribute'=>'phone_no_2',
					'value'=>function($model)
	                {
	                  if($model->phone_no_2!='') {
	                    	return $model->phone_no_2;
						} else {
							return '-';
						}	                    
	                },	                				
				],
				[
		            'label' => 'Problem',
		            'attribute' => 'issue',						            
		        ],                              
                [
		            'label' => 'Link Status',
		            'value' => ucfirst($model->link_status),						            
		        ],
		        'proposed_solution',
		        [
		            'label' => 'Job Allocation',
		            'value' => ucfirst($model->support_site),						            
		        ],	        
                [
		            'label' => 'Ticket Status',
		            'value' => ucfirst($model->ticket_status),						            
		        ],
                
            ],
        ]) ?>
    </div>
</div>

</div>
<?php }else{
   throw new NotFoundHttpException('The requested page does not exist.');
   }?>
