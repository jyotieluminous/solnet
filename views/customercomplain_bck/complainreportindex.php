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

$resetLink = 'customercomplain/complainreport';
?>
<div class="package-index">
    <p>
       <?php echo Html::a('Reset Filters',[$resetLink],['class' => 'btn btn-success']) ?>
    </p>
	<div class="alert-success alert fade in" id="success" style="display:none"> </div>
	<div class="alert-success alert fade in" id="success_status" style="display:none"> </div>  
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
				]);

				?>
			</div>
			<div class="col-md-3"><?php echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);?></div>
		</div>
	</div>
<?php ActiveForm::end();?>
<hr>
<!-- end date range filter -->
<?php 
$tempfloatTotalMaintenceTime = $intTotalMaintenceTime = 0;
$to_time = $from_time = $intRecordCount = $intTotalHours = $intMTTRHrs = $intTotalCaseDays = 0;

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

	$intTotalHours = floor($intTotalMaintence / 60).':'.($intTotalMaintence -   floor($intTotalMaintence / 60) * 60);

	## total MTTR
	$intMTTRHrs = $intTotalHours/$intRecordCount;

	$intTotalCaseDays = $intRecordCount/365;
}

//echo $floatTotalMaintenceTime;die;
?>
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
<div class="box box-default">
    <div class="box-body">
        <div class="tbllanguage-form">
            <div class="customercomplain-index table-responsive">
                <?php Pjax::begin(['id'=>'customercomplain-grid']); ?>
                    <?php echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'id'=>'grid',
                        'showFooter'=>true,
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
								'value'		=> $strDate,
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
									$intMinutes = round(abs($to_time - $from_time) / 60,2)." minute";
									return $intMinutes;
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