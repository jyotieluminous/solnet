<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DateRangePicker;
//use app\models\Package;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'Manage '.$complain_type.' Job Allocations';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['TblcustomercomplainsSearch']['complain_date']))
{
	$strDate = $_GET['TblcustomercomplainsSearch']['complain_date'];
}
else{
	$strDate = "";
}
?>
<div class="package-index">
    <p>
        <?php 
	        if($complain_type == 'Broadband')
	        {        
	        	$resetLink = 'customercomplain/broadbandjoballocationindex';
	        }
	        else if($complain_type == 'Dedicated')
	        {
	        	$resetLink = 'customercomplain/nocdedicatedjoballocationindex';
			}
			else if($complain_type == 'Local Loop')
			{
	        	$resetLink = 'customercomplain/localloopjoballocationindex';
			}
        ?>	
        <?php echo Html::a('Reset Filters',[$resetLink],['class' => 'btn btn-success']) ?>
    </p>
	<div class="alert-success alert fade in" id="success" style="display:none"> </div>
	<div class="alert-success alert fade in" id="success_status" style="display:none"> </div>  
	 <?php if(Yii::$app->session->hasFlash('deleteMessage')){ ?>
		        <div class="alert-danger alert fade in">
		            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
		                <?php echo Yii::$app->session->getFlash('deleteMessage'); ?>
		        </div>
	 <?php } ?>
	 <?php if(Yii::$app->session->hasFlash('errorMessage')){ ?>
	      
	            <div class="alert-danger alert fade in">
	                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	                    <?php echo Yii::$app->session->getFlash('errorMessage'); ?>
	            </div>
	 <?php } ?>
	 <?php if(Yii::$app->session->hasFlash('errorStatus')){ ?>
	      
	            <div class="alert-danger alert fade in">
	                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	                    <?php echo Yii::$app->session->getFlash('errorStatus'); ?>
	            </div>
	 <?php } ?>
<!-- date range filter -->
	<?php $form = ActiveForm::begin([
				'action' => [$resetLink],
				'method' =>'GET',
				'options' => [
                	'class' => 'form-horizontal form-bordered'
                 ],
                'fieldConfig' => [
                	'template' => '{label}<div class="col-sm-6">{input}</div>{error}',
                    'labelOptions' => ['class' => 'col-sm-2 control-label']
                 ]
             ]); 
		?>
<div class="row">
	<div class="col-md-12">
	<div class="col-md-3 text-right"><label>Select Daterange :-</label></div>
	<div class="col-md-6">
	<?php echo DateRangePicker::widget([
			'name' => 'start_date',
			'value' => $strStartDate,
			'nameTo' => 'end_date',
			'valueTo' => $strEndDate,
			'clientOptions' => 	[
									'autoclose' => true,
									'format' => 'dd-mm-yyyy'
								]
	]);?>
	</div>
	<div class="col-md-3"><?php echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);?></div>
	</div>
</div>
<?php ActiveForm::end();?>
<hr>
<!-- end date range filter -->
<div class="box box-default">
    <div class="box-body">
        <div class="tbllanguage-form">
            <div class="customercomplain-index table-responsive">
                <?php Pjax::begin(['id'=>'customercomplain-grid']); ?>
                    <?php echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'id'=>'grid',
                        'columns' => 
                        [
                            ['class' => 'yii\grid\SerialColumn'],
                            ['class' => 'yii\grid\CheckboxColumn'],
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
								'attribute'=>'installation_address',
								'value'=>'customer.linkcustomerpackage.installation_address'
							],
								'ticket_number',								
							[
								'attribute'=>'complain_date',
								'value'=>function($model)
				                {					                   
				                    return date_format(date_create($model->complain_date),'d-m-Y h:i A');
				                },
				                'filter' => DatePicker::widget([
								'name' 		=> 'TblcustomercomplainsSearch[complain_date]',
								'value'		=> $strDate,
								'template' 	=> '{addon}{input}',
								'clientOptions' => [
									'autoclose' => true,
									'format' 	=> 'yyyy-mm-dd'
								]
								]),
				                'label'=>'Complain Date Time'
							],
							[
								'attribute'=>'package_title',
								'value'=>'customer.linkcustomerpackage.package.package_title'
							],								
							[
								'attribute'=>'package_speed',								
								'value'=>function($model)
				                {
				                    return $model->customer->linkcustomerpackage->package_speed.' '.$model->customer->linkcustomerpackage->speed->speed_type;
				                },
							],
							
							'caller_name',
				            [
								'attribute'=>'mobile_no',
								'value'=> 'customer.mobile_no'									
							],
					        [
					            'label' => 'Problem',
					            'attribute' => 'issue',						            
					        ],                                  
                            [
					            'label' => 'Actual problem',
					            'attribute'=>'actual_problem',
					            'value' => function($data){
									if($data->actual_problem)
									{
										return $data->actual_problem;
									}
									else
									{
										return '-';
									}
							 	}						            
					        ],                                
							'proposed_solution',
							[
								'label' => 'Job Allocation',
								'attribute'=>'support_site',
								'filter'=>array(''=>'All','onsite'=>'Onsite','offsite'=>'Offsite'),
								'value' => function($data){
									if($data->support_site == 'onsite'){
										return 'Onsite';
									} else if($data->support_site == 'offsite'){
										return 'Offsite';
									} else {
										return '-';
									}
							 	}
							],
				            [
								'attribute'=>'job_completed_date',
								'value'=>function($model)
				                {					                   
				                    return date_format(date_create($model->job_completed_date),'d-m-Y h:i A');
				                },
				                'filter' => DatePicker::widget([
								'name' 		=> 'TblcustomercomplainsSearch[job_completed_date]',
								'value'		=> $strDate,
								'template' 	=> '{addon}{input}',
								'clientOptions' => [
									'autoclose' => true,
									'format' 	=> 'yyyy-mm-dd'
								]
								]),
				                'label'=>'Job Completed Date'
							],
							[
				                'attribute'=>'engineer_name',
				                'value'=>function($model)
				                {
				                    if(isset($model->engineer->staff_name)){
										return $model->engineer->staff_name;
									} else {
										return '-';
									}
				                    
				                },
				                'label'=>'Engineer Name'
				            ],
							[	
								'attribute'=>'ticket_status',
								'filter'=>array('open'=>'Open','closed'=>'Closed'),
								'value' => function($data){
									if($data->ticket_status == 'open')
									{
										return 'Open';
									}
									else if($data->ticket_status == 'closed')
									{
										return 'Closed';
									}
									else
									{
										return '-';
									}
							 	}
							],					        
                            [
                                'header'=>'Action',
                                'class' => 'yii\grid\ActionColumn',
                                'options'=>['width'=>100],
								'template'=>'{view} {activate} {eqipments}', //{link}
                                'buttons' => [
                               	'view' => function ($url,$model) {
	                       			if($model->complain_type == 'Broadband')
	                       				return Html::a('<i class="fa fa-eye"></i>',['customercomplain/nocbroadbandjview','id'=>$model->complain_id]);
	                       			else if($model->complain_type == 'Dedicated')
	                       				return Html::a('<i class="fa fa-eye"></i>',['customercomplain/nocdedicatedjview','id'=>$model->complain_id]);
	                       			else if($model->complain_type == 'Local Loop')
	                       				return Html::a('<i class="fa fa-eye"></i>',['customercomplain/noclocalloopjview','id'=>$model->complain_id]);	
                				},

	                			'activate' => function ($url, $model)
	                			{
						            if($model->complain_type == 'Broadband'){
										$resLink = '/customercomplain/broadbandrespond';
                                		$jobAlcnLink = '/customercomplain/nocbroadbandjoballocationajax';
									}                                    		
	                                else if($model->complain_type == 'Dedicated'){
										$resLink = '/customercomplain/dedicatedrespond';
										$jobAlcnLink = '/customercomplain/nocdedicatedjoballocationajax';
									}
	                                else if($model->complain_type == 'Local Loop'){
										$resLink = '/customercomplain/locallooprespond';
										$jobAlcnLink = '/customercomplain/noclocalloopjoballocationajax';
									}		                                	
						            
						            if($model->support_site == 'offsite'){
										$url = Url::to([$resLink, 'id' => $model->complain_id]);
						            	return Html::a(' <span class=" 	glyphicon glyphicon-exclamation-sign" title = "Respond" ></span> ', 'javascript:void(0)', ['class' => 'list', 'value' => $url]);
									} else {
										$url = Url::to([$jobAlcnLink, 'id' => $model->complain_id]);
						            	return Html::a(' <span class=" 	glyphicon glyphicon-exclamation-sign" title = "Job Allocation" ></span> ', 'javascript:void(0)', ['class' => 'list', 'value' => $url]);							            
									}
						        },
                          	],
                        ],
                        ],
                    ]); ?>
                <?php Pjax::end(); ?>
                <?php
					Modal::begin([
						    'id'     => "modal",
						    'header' => '<h3 class="text-center">Respond Complain</h3>',
						]);
						echo "<div id='modalContent'></div>";
						Modal::end();
						
						$this->registerJs(
						    "$(document).on('ready pjax:success', function() {
						            $('.list').click(function(e){
						               e.preventDefault(); //for prevent default behavior of <a> tag.
						               $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));			   
						           });
						        });
						    ");
						    		
				?>


            </div>
        </div>
    </div>
</div>