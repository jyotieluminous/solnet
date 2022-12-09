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

$this->title = 'Manage '.$complain_type.' Complains';
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
        <?php if($complain_type == 'Broadband') {        	
        	$addLink = 'broadbandcreate';
        	$resetLink = 'customercomplain/broadbandindex';
        	$deleteLink = yii::$app->request->baseUrl.'/customercomplain/broadbanddeletemultiple';
        } else if($complain_type == 'Dedicated'){
			$addLink = 'dedicatedcreate';
        	$resetLink = 'customercomplain/dedicatedindex';
        	$deleteLink = yii::$app->request->baseUrl.'/customercomplain/dedicateddeletemultiple';
		} else if($complain_type == 'Local Loop'){
			$addLink = 'localloopcreate';
        	$resetLink = 'customercomplain/localloopindex';
        	$deleteLink = yii::$app->request->baseUrl.'/customercomplain/localloopdeletemultiple';
		}
        ?>			 
		
        <?php echo Html::a('Add Complain', [$addLink], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Reset Filters',[$resetLink],['class' => 'btn btn-success']) ?>
        
        <?php echo Html::a('&nbsp;Delete Selected','javascript:void(0)',['class' => 'btn btn-danger','onClick'=>'javascript:deleteAll()']) ?>
        
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
<!-- end date range filter -->
<div class="box box-default">
        <div class="box-body">
            <div class="tbllanguage-form">
                <div class="customercomplain-index table-responsive ">
                    <?php Pjax::begin(['id'=>'customercomplain-grid']); ?>
                        <?php echo GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'id'=>'grid',
                            'columns' => [
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
								/*[
								'attribute'=>'package_speed',
								'value'=>'customer.linkcustomerpackage.package_speed'
								],
								[
								'attribute'=>'speed_type',
								'value'=>'customer.linkcustomerpackage.speed.speed_type'
								],*/
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
						            'label' => 'Alternative Phone',
						            'attribute' => 'phone_no_1',						            
						        ],
						        [
						            'label' => 'Problem',
						            'attribute' => 'issue',						            
						        ],                              
                                
                                [
									'attribute'=>'link_status',
									'filter'=>array(''=>'All','up'=>'Up','down'=>'Down','unstable'=>'Unstable'),
									'value' => function($data){
										if($data->link_status == 'up'){
											return 'Up';
										} else if($data->link_status == 'down'){
											return 'Down';
										} else if($data->link_status == 'unstable'){
											return 'Unstable';
										} else {
											return '-';
										}
								 	}
								],                                
								'proposed_solution',
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
									'filter'=>array('all'=>'All','onsite'=>'Onsite','offsite'=>'Offsite'),
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
									'attribute'=>'ticket_status',
									//'label' => 'Alternative Phone',
									'filter'=>array('all'=>'All','open'=>'Open','closed'=>'Closed'),
									'value' => function($data){
										if($data->ticket_status == 'open'){
											return 'Open';
										} else if($data->ticket_status == 'closed'){
											return 'Closed';
										} else {
											return '-';
										}
								 	}
								],
								[
									'label' => 'Noc Incharge',
									'attribute'=>'noc_incharge',									
									'value' => function($model){
										return $model->user->name;
								 	}
								],						        
                                [
                                'header'=>'Action',
                                'class' => 'yii\grid\ActionColumn',
                                'options'=>['width'=>100],
								'template'=>'{view} {update} {activate} {delete} ', //{link}
                                'buttons' => [
                               	'view' => function ($url,$model) {
	                       			if($model->complain_type == 'Broadband')
	                       				return Html::a('<i class="fa fa-eye"></i>',['customercomplain/broadbandview','id'=>$model->complain_id]);
	                       			else if($model->complain_type == 'Dedicated')
	                       				return Html::a('<i class="fa fa-eye"></i>',['customercomplain/dedicatedview','id'=>$model->complain_id]);
	                       			else if($model->complain_type == 'Local Loop')
	                       				return Html::a('<i class="fa fa-eye"></i>',['customercomplain/localloopview','id'=>$model->complain_id]);	
	                			},
	                			'update' => function ($url,$model,$key) {
	                       			if($model->complain_type == 'Broadband')
	                       				return Html::a('<i class="fa fa-pencil"></i>', ['/customercomplain/broadbandupdate','id'=>$model->complain_id],['title'=>'Update','target'=>'_blank']);
	                       			else if($model->complain_type == 'Dedicated')
	                       				return Html::a('<i class="fa fa-pencil"></i>', ['/customercomplain/dedicatedupdate','id'=>$model->complain_id],['title'=>'Update','target'=>'_blank']);
	                       			else if($model->complain_type == 'Local Loop')
	                       				return Html::a('<i class="fa fa-pencil"></i>', ['/customercomplain/localloopupdate','id'=>$model->complain_id],['title'=>'Update','target'=>'_blank']);
	                			},
	                			'activate' => function ($url, $model) {
							            
							            if($model->complain_type == 'Broadband'){
											$resLink = '/customercomplain/broadbandrespond';
                                    		$jobAlcnLink = '/customercomplain/broadbandjoballocation';
										}                                    		
		                                else if($model->complain_type == 'Dedicated'){
											$resLink = '/customercomplain/dedicatedrespond';
											$jobAlcnLink = '/customercomplain/dedicatedjoballocation';
										}
		                                else if($model->complain_type == 'Local Loop'){
											$resLink = '/customercomplain/locallooprespond';
											$jobAlcnLink = '/customercomplain/localloopjoballocation';
										}		                                	
							            
							            if($model->support_site == 'offsite'){
											$url = Url::to([$resLink, 'id' => $model->complain_id]);
							            	return Html::a(' <span class=" 	glyphicon glyphicon-exclamation-sign" title = "Respond" ></span> ', 'javascript:void(0)', ['class' => 'list', 'value' => $url]);
										} else {
											$url = Url::to([$jobAlcnLink, 'id' => $model->complain_id]);
							            	return Html::a(' <span class=" 	glyphicon glyphicon-exclamation-sign" title = "Job Allocation" ></span> ', 'javascript:void(0)', ['class' => 'list2', 'value' => $url]);							            
										}
							            
							        },
                                'delete' => function ($url, $model) {                                   
                                    if($model->complain_type == 'Broadband')
                                    	$deletLink = '/customercomplain/broadbanddelete';
	                                else if($model->complain_type == 'Dedicated')    
	                                    $deletLink = '/customercomplain/dedicateddelete';
	                                else if($model->complain_type == 'Local Loop')
	                                	$deletLink = '/customercomplain/localloopdelete';
	                                	    
	                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', [$deletLink,'id'=>$model->complain_id], [
	                                    'title' => Yii::t('app', 'delete'),
	                                    'data-method'=>'POST',
	                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this complain?'),
                                    ]);
                                }

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
						
						Modal::begin([
						    'id'     => "modal-online",
						    'header' => '<h3 class="text-center">Onsite Complain Job Allocation</h3>',
						]);
						echo "<div id='modalContentOnline'></div>";
						Modal::end();
						
						$this->registerJs(
						    "$(document).on('ready pjax:success', function() {
						            $('.list').click(function(e){
						               e.preventDefault(); //for prevent default behavior of <a> tag.
						               $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));			   
						           });
						        });
						    ");
						    
						$this->registerJs(
					    "$(document).on('ready pjax:success', function() {
					            $('.list2').click(function(e){
					               e.preventDefault(); //for prevent default behavior of <a> tag.
								   $('#modal-online').modal('show').find('#modalContentOnline').load($(this).attr('value'));
					           });
					        });
					    ");
						?>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
        function deleteAll(){ 
            var ids = $('#grid').yiiGridView('getSelectedRows');
            if(ids.length >  0){
            if(confirm("Are you sure you want to delete the selected complains?")){
                $.ajax({
                    type     : 'POST',
                    data     : { ids : ids },
                    cache    : false,
                    url      : "<?php echo $deleteLink; ?>",
                    success  : function(response) {
                                                    $.pjax.reload({container:'#customercomplain-grid',async:false});

                                                    if(response == 'success'){
                                                        $("#success").css("display", "block");
                                                        $('#success').html("complains(s) deleted successfully");

                                                    }
                                                $( "#success" ).fadeOut(5000);
                                                $.pjax.reload({container: '#customercomplain-grid',async:false});

                        }

                    });
                }
            }else{
                alert("Please select the records to delete");
            }
        }
    </script>
