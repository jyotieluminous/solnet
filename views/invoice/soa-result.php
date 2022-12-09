<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

use kartik\export\ExportMenu;

$this->title = 'Statement Of Account Result';

$floatTotalAmount = 0;
$floatTotalUnpaid = 0;
$floatTotalPaid = 0;

if(!empty($dataProvider->getModels())) 
{
	
	$model = $dataProvider->getModels();
	
 foreach ($model as $key => $val) {
	 	$floatTotalPaid += $val->paid_amount;
	 	$floatTotalUnpaid += $val->pending_amount;
	 	$floatTotalAmount += $val->total_invoice_amount;
    }
}


$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
						
						[
							'attribute'=>'invoice_number',
							'value'=>'invoice_number',
						],
						[
							'attribute'=>'invoice_date',
							'value'=> 'invoice_date',
							'footer' =>'Total'
						],
						[
							'attribute'=>'total_invoice_amount',
							'value'=> 'total_invoice_amount',
							'footer' => $floatTotalAmount,
						],
						[
							'attribute'=>'paid_amount',
							'value'=> 'paid_amount',
							'footer' => $floatTotalPaid,
						],
						[
							'label'=>'invoice Balance',
							'attribute'=>'pending_amount',
							'value'=>'pending_amount',
							'footer' => $floatTotalUnpaid,
						],
						[
							'attribute'=>'payment_term',
							'value'=> function($data){
										if(isset($data->linkcustomepackage->payment_term) && !empty($data->linkcustomepackage->payment_term))
										{
											return $data->linkcustomepackage->payment_term.' Day(s)';
										}else{
											return '-';
										}
									}
						],
						[
							'attribute'=>'due_date',
							'value'=>'due_date'
						],
						[
							'attribute'=>'no_of_days_past_due',
							'value'=>function($data){ return $data->getnumberofdays($data->due_date); }
							
						],
						
];
?>
<?php
	// Renders a export dropdown menu
 echo '<b>Export To Excel </b> :- '.ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
	'filename'=>'customer_invoice'.date('Ymdhis')
]);
?>
	<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form">
				<div class="row">
					<div class="col-md-12">
						<?php echo kartik\grid\GridView::widget([
								'dataProvider' => $dataProvider,
								'filterModel' => $searchModel,
								'showFooter' =>true,
								'columns' => $gridColumns,
							]); 
						?>
						
					</div>
				</div>
			</div>
		</div>
	</div>
