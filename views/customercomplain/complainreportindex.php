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
use kartik\export\ExportMenu;
use yii\grid\ActionColumn;
$this->title = 'Manage Complain Report';
$this->params['breadcrumbs'][] = $this->title;
if(isset($_GET['TblcustomercomplainsSearch']['complain_date']))
{
	$strDate = $_GET['TblcustomercomplainsSearch']['complain_date'];
}
else{
	$strDate = "";
}

if(isset($_GET['TblcustomercomplainsSearch']['job_completed_date']))
{
	$strJobCompletedDate = $_GET['TblcustomercomplainsSearch']['job_completed_date'];
}
else{
	$strJobCompletedDate = "";
}



$resetLink = 'customercomplain/complainreport';
 
$tempfloatTotalMaintenceTime = $intTotalMaintenceTime = 0;
$to_time = $from_time = $intRecordCount = $intTotalHours = $intMTTRHrs = $intTotalCaseDays = 0;

$model = $dataProvider->getModels();

//Get total package price on the page
if(!empty($dataProvider->getModels())) 
{
	$model = $dataProvider->getModels();
	
	$intRecordCount = count($model);

	foreach($dataProvider->getModels() as $key=>$value)
	{
		$to_time   += strtotime($value->complain_date);
		$from_time += strtotime($value->job_completed_date); 
		$tempfloatTotalMaintenceTime = round(abs($to_time - $from_time) / 60,2);
	}

	## total maintence hrs
	$intTotalMaintence = $tempfloatTotalMaintenceTime/$intRecordCount;

	//$intTotalHours = floor($intTotalMaintence / 60).':'.($intTotalMaintence -   floor($intTotalMaintence / 60) * 60);
	$hours = floor($intTotalMaintence / 3600);
  	$minutes = floor(($intTotalMaintence / 60) % 60);
  	$seconds = $intTotalMaintence % 60;

	$intTotalHours = $minutes.':'.$seconds;

	## total MTTR
	$intMTTRHrs = $intTotalHours/$intRecordCount;

	$intTotalCaseDays = $intRecordCount/365;
}

//echo $floatTotalMaintenceTime;die;
?>

<div class="package-index">
    <p>
       <?php echo Html::a('Reset Filters',[$resetLink],['class' => 'btn btn-success']) ?>
       <?php echo Html::button('Import to CSV',['class' => 'btn btn-success','id'=>'importCSV']) ?>
    </p>
    
	<div class="alert-success alert fade in" id="success" style="display:none"> </div>
	<div class="alert-success alert fade in" id="success_status" style="display:none"> </div>  
	<!-- date range filter -->
	<?php Pjax::begin(['id'=>'customercomplain-grid']); ?>
	<?php
		$gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'value'=>function($model)
	            {
	                return '-';
	            },
	            'label'=> "MTTR :".number_format($intMTTRHrs,2)." hours   
	            Angka Gangguan / total dwon time :".$intTotalHours." hours  
	            Penanganan Dalam 1 hari   / Case handle in 1 day :".number_format($intTotalCaseDays,2)." case a day"
	        ],
			[
                'attribute'=>'customer_id',                
                'value'=>function($model)
                {
                    return $model->customer->solnet_customer_id;
                },
                'label'=>"Customer ID"
            ],
			[
                'attribute'=>'name',
                'value'=>function($model)
                {
                    return $model->customer->name;
                },
                'label'=>'Customer Name'
            ],	
            [
                'attribute'=>'state',
                'value'=>function($model)
                {
                    return $model->customer->state->state;
                },
                'label'=>'State'
            ],            [
				'label' => 'Complain Type',
				'attribute'=>'complain_type',
				'filter'=>array(''=>'All','Broadband'=>'Broadband','Dedicated'=>'Dedicated','Local Loop'=>'Local Loop'),
				'value' => function($data){
					if(!empty($data->complain_type))
					{
						return $data->complain_type;
					}
					else
					{
						return '-';
					}
			 	}
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
            [
				'attribute'=>'installation_address',
				'value'=>'customer.linkcustomerpackage.installation_address'
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
	        [
	            'label' => 'Permanent solution',
	            'attribute'=>'permanent_solution',
	            'value' => function($data){
					if($data->permanent_solution)
					{
						return $data->permanent_solution;
					}
					else
					{
						return '-';
					}
			 	}						            
	        ], 								
			[
				'attribute'=>'complain_date',
				'value'=>function($model)
                {					                   
                    return date_format(date_create($model->complain_date),'d-m-Y  h:i A');
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
                'label'=>'Date and Time Received Complain'
			],    
			[
				'attribute'=>'job_completed_date',
				'value'=>function($model)
                {					                   
                    return date_format(date_create($model->job_completed_date),'d-m-Y h:i A');
                },
                'filter' => DatePicker::widget([
				'name' 		=> 'TblcustomercomplainsSearch[job_completed_date]',
				'value'		=> $strJobCompletedDate,
				'template' 	=> '{addon}{input}',
				'clientOptions' => [
					'autoclose' => true,
					'format' 	=> 'yyyy-mm-dd'
				]
				]),
                'label'=>'Date and Time Job Completed',
                'footer' =>'Total'
			],
			[
				'attribute'=>'job_completed_date',
				'value'=>function($model)
                {					                   
                    $to_time   = strtotime($model->complain_date);
					$from_time = strtotime($model->job_completed_date); 
					$intMinutes = round(abs($to_time - $from_time) / 60,2);
					return $intMinutes;
                },
                'label'=>'Total Down time (min)',
                'footer' => $intTotalHours.'h'
			],
		];

		// Renders a export dropdown menu
		/*echo ExportMenu::widget([
		    'dataProvider' => $dataProvider,
		    'columns' => $gridColumns,
            'filterModel' => $searchModel,
			'filename'=>'complainreport_'.date('Ymdhis')
		]);*/
	?>

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
						'clientOptions' => [
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

<div class="box box-default">
    <div class="box-body">
        <div class="tbllanguage-form">
            <div class="customercomplain-index table-responsive">
                
                <div class="row">
				 	<div class="col-md-11">
				 		<label>MTTR : <?php echo number_format($intMTTRHrs,2)." hours"; ?></label>
				 	</div>
				 	<!-- <div class="col-md-11">
				 		<label>Network Availability : <?php echo ''; ?></label>
				 	</div> -->
				 	<div class="col-md-11">
				 		<label>Angka Gangguan / total dwon time : <?php echo $intTotalHours.' hours'; ?></label>
				 	</div>
				 	<div class="col-md-11">
				 		<label>Penanganan Dalam 1 hari   / Case handle in 1 day : <?php echo number_format($intTotalCaseDays,2).' case a day'; ?></label>
				 	</div>
				 </div>
                    <?php 

                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'id'=>'grid',
                        'showFooter'=>true,
                        'columns' => 
                        [
                            ['class' => 'yii\grid\SerialColumn'],
                            /*[
                            	
								'class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function($model) {
					                return ['value' => $model->customer->customer_id , 'class' => 'checkbox-row'];
					            },
                        	],*/
                        	[
								'class' => 'yii\grid\CheckboxColumn',
								'checkboxOptions' =>
								function($model) {
									return ['value' => $model->complain_id, 'class' => 'checkbox-row', 'id' => 'checkbox'];
								}
							],
                        	/*['class' => 'yii\grid\CheckboxColumn',
			                    'contentOptions' => ['class' => 'text-center'],
			                    'checkboxOptions' => function($model, $key, $index) {
			                        $url = \yii\helpers\Url::to(['customercomplain/complainreport/' . $model->customer->customer_id]);
			                        return ['onclick' => 'js:followUp("' . $url . '")', 'checked' => $model->customer->customer_id ? true : false, 'value' =>$model->customer->customer_id];
			                    }
			                ],*/
                        	
							[
				                'attribute'=>'custid',
				                'value'=>function($model)
				                {
				                    return $model->customer->solnet_customer_id;
				                },
				                'label'=>'Customer ID'
				            ],	
				            [
				                'attribute'=>'name',
				                'value'=>function($model)
				                {
				                    return $model->customer->name;
				                },
				                'label'=>'Customer Name'
				            ],
				            [
				                'attribute'=>'state',
				                'value'=>function($model)
				                {
				                    return $model->customer->state->state;
				                },
				                'label'=>'State'
				            ],
				            [
								'label' => 'Customer Type',
								'attribute'=>'complain_type',
								'filter'=>array(''=>'All','Broadband'=>'Broadband','Dedicated'=>'Dedicated','Local Loop'=>'Local Loop'),
								'value' => function($data){
									if(!empty($data->complain_type))
									{
										return $data->complain_type;
									}
									else
									{
										return '-';
									}
							 	}
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
				            [
								'attribute'=>'installation_address',
								'value'=>'customer.linkcustomerpackage.installation_address'
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
					        [
					            'label' => 'Permanent solution',
					            'attribute'=>'permanent_solution',
					            'value' => function($data){
									if($data->permanent_solution)
									{
										return $data->permanent_solution;
									}
									else
									{
										return '-';
									}
							 	}						            
					        ], 	
							[
					            'label' => 'Job Allocation',
					            'attribute'=>'support_site',
					            'filter'=>array(''=>'All','offsite'=>'OFFSITE','onsite'=>'ONSITE'),
					            'value' => function($data){
									if($data->support_site)
									{
										return strtoupper($data->support_site);
									}
									else
									{
										return '-';
									}
							 	}						            
					        ], 							
							[
								'attribute'=>'complain_date',
								'value'=>function($model)
				                {					                   
				                    return date_format(date_create($model->complain_date),'d-m-Y  h:i A');
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
				                'label'=>'Date and Time Received Complain'
							],    
							[
								'attribute'=>'job_completed_date',
								'value'=>function($model)
				                {					                   
				                    if($model->job_completed_date)
				                    	return date_format(date_create($model->job_completed_date),'d-m-Y h:i A');
				                    else
				                    	return "-";
				                },
				                'filter' => DatePicker::widget([
								'name' 		=> 'TblcustomercomplainsSearch[job_completed_date]',
								'value'		=> $strJobCompletedDate,
								'template' 	=> '{addon}{input}',
								'clientOptions' => [
									'autoclose' => true,
									'format' 	=> 'yyyy-mm-dd'
								]
								]),
				                'label'=>'Date and Time Job Completed',
				                'footer' =>'Total'
							],
							[
								'attribute'=>'job_completed_date',
								'value'=>function($model)
				                {					                   
				                    if($model->job_completed_date){
					                    $to_time   = strtotime($model->complain_date);
										$from_time = strtotime($model->job_completed_date); 
										$intMinutes = round(abs($to_time - $from_time) / 60,2)." minute";
										return $intMinutes;
								    }
								    else
				                    	return "-";
				                },
				                'label'=>'Total Down time (min)',
				                'footer' => '<b>'.$intTotalHours.'h</b> '
							],

                        ],
                    ]); ?>
                <?php Pjax::end(); ?>
                <?php
					Modal::begin([
					    'id'     => "modal",
					    'header' => '<h3 class="text-center">Respond Job Allocation</h3>',
					]);
					echo "<div id='modalContent'></div>";
					Modal::end();
					$this->registerJs("
					 ");						
				?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).on('ready pjax:success', function() {
	$('.list').click(function(e){
       e.preventDefault(); //for prevent default behavior of <a> tag.
       $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));			   
    });
    $(".form-control").on("change",function(){
    	setTimeout(function(){
		   window.location.reload(1);
		}, 1000);
    });
    $('#importCSV').on('click',function(){
    	var data = $('form').serialize();
    	var fileType = 'CSV';
    	var ids = [];

    	var custid = "<?php if(isset($_GET['TblcustomercomplainsSearch']['custid'])){ echo $_GET['TblcustomercomplainsSearch']['custid'];};?>";
    	var name = "<?php if(isset($_GET['TblcustomercomplainsSearch']['name'])){ echo $_GET['TblcustomercomplainsSearch']['name'];};?>";
		var complain_type = "<?php if(isset($_GET['TblcustomercomplainsSearch']['complain_type'])){ echo $_GET['TblcustomercomplainsSearch']['complain_type'];};?>";
		var package_title = "<?php if(isset($_GET['TblcustomercomplainsSearch']['package_title'])){ echo $_GET['TblcustomercomplainsSearch']['package_title'];};?>";
		var installation_address = "<?php if(isset($_GET['TblcustomercomplainsSearch']['installation_address'])){ echo $_GET['TblcustomercomplainsSearch']['installation_address'];};?>";
		var issue = "<?php if(isset($_GET['TblcustomercomplainsSearch']['issue'])){ echo $_GET['TblcustomercomplainsSearch']['issue'];};?>";
		var actual_problem = "<?php if(isset($_GET['TblcustomercomplainsSearch']['actual_problem'])){ echo $_GET['TblcustomercomplainsSearch']['actual_problem'];};?>";
		var complain_date = "<?php if(isset($_GET['TblcustomercomplainsSearch']['complain_date'])){ echo $_GET['TblcustomercomplainsSearch']['complain_date'];};?>";
		var job_completed_date = "<?php if(isset($_GET['TblcustomercomplainsSearch']['job_completed_date'])){ echo $_GET['TblcustomercomplainsSearch']['job_completed_date'];};?>";   	
    	$.each($(".checkbox-row:checked"), function(){
           ids.push($(this).val());
        });
    	 $.ajax({
    	 	 url: '<?php echo yii::$app->request->baseUrl;  ?>/customercomplain/complainreportexportcsv',
		    dataType: 'html',
		    type: 'POST',
		    data: {
		    	ids:ids,
		    	data:data,
		    	name:name,
		    	complain_type:complain_type,
		    	package_title:package_title,
		    	installation_address:installation_address,
		    	issue:issue,
		    	actual_problem:actual_problem,
		    	complain_date:complain_date,
		    	job_completed_date:job_completed_date,
		    	custid:custid
		    },
		    beforeSend: function() {
		    },
		    success: function(data) {
		    	//console.log(data);
		    window.location.href = '<?php echo yii::$app->request->baseUrl;  ?>/customercomplain/complainreportdownloadcsv?fileName='+data; 
		    }
		});						    	
    });						           
});
</script>
