<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\widgets\Pjax;
use yii\bootstrap\Modal; 
use yii\helpers\Url; 
use yii\helpers\ArrayHelper; 
use dosamigos\datepicker\DateRangePicker;
use yii\widgets\ActiveForm;
use kartik\export\ExportMenu;
use app\models\Linkcustomepackage;
use app\models\Speed;
use app\models\Currency;


/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Return Goods Report';
$this->params['breadcrumbs'][] = $this->title;

if(isset($_GET['start_date']) && isset($_GET['end_date']))
{
 $strStartDate = $_GET['start_date'];
 $strEndDate = $_GET['end_date'];
}
else{
    $strStartDate = '';
    $strEndDate='';
}

	$actionColumn = [
     		'class'    	=> 'yii\grid\ActionColumn',
    		'header'   	=> 'Action',
    		'template'	=>'{view}',
		    'buttons'  	=> [
	        'view' => function ($url, $data) {
	            return  Html::a('<i class="fa fa-eye"></i>', ['/customerpackage/disconnectview','id'=>$data->id],
	            	['data-toggle'=>'tooltip', 'title'=>'View',]);
	        },
    	], 
	];				  

	if(Yii::$app->user->identity->fk_role_id=='8')
	{
		$gridColumns = [
				['class' => 'yii\grid\SerialColumn'],
				[
					'attribute'=>'name',
					'value'=>function($data)
					{
						return $data->customer->name;
					}
				],	
				[
					'attribute'=>'solnet_customer_id',
					'value'=>function($data)
					{
						return $data->customer->solnet_customer_id;
					}
				],
				[
					'attribute'=>'brand_name',
					'value'=>function($data)
					{
						return $data->equmentInfo->brand_name;
					}
				],
				[
					'attribute'=>'model_type',
					'value'=>function($data)
					{
						return $data->equmentInfo->model_type;
					}
				],
				[
					'attribute'=>'quantity',
					'value'=>function($data)
					{
						return $data->quantity;
					}
				],
				[
					'attribute'=>'Status Of Equipment',
					'value'=>function($data)
					{
						return $data->status;
					}
				],
				[
					'attribute'=>'Equipment Condition',
					'value'=>function($data)
					{
						return $data->return_status;
					}
				],
					$actionColumn
			];
			$showFooter = false;		
	}
	else
	{
		$gridColumns = [
				['class' => 'yii\grid\SerialColumn'],
				[
					'attribute'=>'name',
					'value'=>function($data)
					{
						return $data->customer->name;
					}
				],	
				[
					'attribute'=>'solnet_customer_id',
					'value'=>function($data)
					{
						return $data->customer->solnet_customer_id;
					}
				],
				[
					'attribute'=>'Brand Name',
					'value'=>function($data)
					{
						return $data->equmentInfo->brand_name;
					}
				],
				[
					'attribute'=>'Model',
					'value'=>function($data)
					{
						return $data->equmentInfo->model_type;
					}
				],
				[
					'attribute'=>'Quantity',
					'value'=>function($data)
					{
						return $data->quantity;
					}
				],
				[
					'attribute'=>'Status Of Equipment',
					'value'=>function($data)
					{
						return $data->status;
					}
				],
				[
					'attribute'=>'Equipment Condition',
					'value'=>function($data)
					{
						return $data->return_status;
					}
				],
				$actionColumn
			];
			$showFooter = false;
	}
?>
<p><?= Html::a('Reset Filters', ['customerpackage/disconnectreport'], ['class' => 'btn btn-success']) ?></p>
<div class="alert-success alert fade in" id="success" style="display:none"> </div>
<?php Pjax::begin(['id'=>'disconnect-grid']); ?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<div class="customer-index">
				<?= kartik\grid\GridView::widget([
					'dataProvider' 	=> $dataProvider,
					'filterModel' 	=> $searchModel,
					'columns'		=> $gridColumns,
					'showFooter'	=> $showFooter,
				]); ?>
			</div>
		</div>
	</div>
</div>
<?php Pjax::end(); ?>