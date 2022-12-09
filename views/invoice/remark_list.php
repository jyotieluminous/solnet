<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
$this->title = 'Daily JOB Report';
$this->params['breadcrumbs'][] = $this->title;
/*echo "<pre>";
print_r($dataProvider->getModels());die;*/
$gridColumns = [
    ['class'=>'yii\grid\SerialColumn'],
						
						'created_date',
						[
							'attribute'=>'name',
							'value'=> 'customers.name',
							  
							
						],
						[
							'attribute'=>'invoice_number',
							'value'=> 'invoices.invoice_number',
							  
							
						],
						
						[
							'attribute'=>'package_title',
							'value'=>'invoices.linkcustomepackage.package.package_title'
													
						],

						
						[
							//'label'=>'Package Speed',
							'attribute'=>'package_speed',
							'value'=>function($data){
								return $data->invoices->linkcustomepackage->package_speed;
							}
						],
						[
							'attribute'=>'mobile_no',
							'value'=>'customers.mobile_no',
						],
						[
							'attribute'=>'remark1',
							'value'=>'remark1',
						],
						[
							'attribute'=>'remark2',
							'value'=>'remark2',
						],
						//'customers.mobile_no',
						//'remark1',
						//'remark2',
						'user.name'
						
						/*[
							'header'=>'Action',
							'class' => 'yii\grid\ActionColumn',
							'template'=>' {link}',
							'buttons'=>[
							 	'link' => function ($url,$model,$key) {
										return Html::a('<i class="fa fa-file-pdf-o"></i>',['/invoice/reminderprint','id'=>$model->customer_invoice_id],['title'=>'Print',
                            'target'=>'_blank',
                            'data-pjax'=>'0']);
									},

							],
						]*/
	];
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<div class="customerinvoice-index"> 
				<?php echo kartik\grid\GridView::widget([
					'dataProvider' => $dataProvider,
					'filterModel' => $searchModel,
					'id'=>'grid',
					'columns' => $gridColumns,
				]); ?>
			</div>
		</div>
	</div>
</div>