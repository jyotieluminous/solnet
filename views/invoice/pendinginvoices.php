<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Customerinvoice */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Pending Amount';

?>
<style>
.modal-dialog
{
	width:800px!important;
}
</style>
<div class="box box-default">
	<div class="box-body">
	<div class="customer-index">
<?php
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
	'customer.solnet_customer_id',	
	'invoice_number',
	[
		'attribute'=>'pending_amount',
		
		'value'=> function($data){
			return number_format($data->pending_amount,2);
			
		},
	],
	'status',
	[
		'attribute'=>'invoice_date',
		'options'=>['width'=>'15%'],
		'value'=>function($data){
				return date('d-m-Y',strtotime($data->invoice_date));
		},
	],
	//'invoice_date',	
];		
echo GridView::widget([
						'dataProvider' => $dataProvider,
						//'filterModel' => $searchModel,
						'columns' => $gridColumns
						//'id'=>'grid',

						]);
?>
	</div>				
	</div>
</div>	