<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\Customer;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Signup Customers';
$this->params['breadcrumbs'][] = $this->title;
$strEndDate = '';
if(isset($_GET['CustomerSearch']['start_date']))
{
	$strDate = $_GET['CustomerSearch']['start_date'];
}
else{
	$strDate = '';
}
if(isset($_GET['CustomerSearch']['end_date']))
{
	$strEndDate = $_GET['CustomerSearch']['end_date'];
}
else{
	$strEndDate = '';
}

$floatIDRTotBalance = 0;
$floatSGDTotBalance = 0;
$floatUSDTotBalance = 0;
$floatTotBalance    = 0;
if(!empty($dataProvider->getModels()))
{

	$model = $dataProvider->getModels();

 	foreach ($dataProvider->getModels() as $key => $val) {
	 if($model[$key]['linkcustomerpackage']->fk_currency_id==1){    //For RP Currency
		 $floatIDRTotBalance  += $val->linkcustomerpackage->package_price;
	 }elseif($model[$key]['linkcustomerpackage']->fk_currency_id==2){  //For SGD Currency
		 $floatSGDTotBalance  += $val->linkcustomerpackage->package_price;
	 }elseif($model[$key]['linkcustomerpackage']->fk_currency_id==3){   //For USD Currency
		 $floatUSDTotBalance  += $val->linkcustomerpackage->package_price;
	 }
     $floatTotBalance = 'RP '.number_format($floatIDRTotBalance,2).' <br/>SGD '.number_format($floatSGDTotBalance,2).' <br/>USD '.number_format($floatUSDTotBalance,2).' <br/>';
    }
}
?>
<p>
<?php 
		$gridColumns = [
							['class' => 'yii\grid\SerialColumn'],
							'solnet_customer_id',
							'name',
							'billing_address:ntext',
							[
								'attribute'=>'installation_address',
								'value'=>function($data){
									return $data->linkcustomerpackage->installation_address;
								}

							], 
							[
								'attribute'=>'package_title',
								'value'=>'linkcustomerpackage.package.package_title'
							],
							[
								'attribute'=>'package_speed',
								'value'=>function($data){
									return $data->linkcustomerpackage->package_speed.' '.$data->linkcustomerpackage->speed->speed_type;
								},
								//'footer' =>'Total'
							],
							[
								'attribute'=>'currency',
								'filter'=>false,
								'value'=>function($data){
									return $data->linkcustomerpackage->currency->currency;
								},
							],
							[
								'attribute'=>'package_price',
								'value'=>function($data){
									return number_format($data->linkcustomerpackage->package_price,2);
								},
								'footer' =>$floatTotBalance,

							],
							[
								'attribute'=>'state',
								'value'=>'state.state',
								
							],
							[
								'attribute'=>'invoice_start_date',
								'value'=>function($data){
									return date('d-m-Y',strtotime($data->linkcustomerpackage->invoice_start_date));
								}

							],
							[
								'attribute'=>'Activation Date',
								'value'=>function($data){
									//return date('d-m-Y',strtotime($data->linkcustomerpackage->activation_date));
									if($data->linkcustomerpackage->activation_date != '0000-00-00 00:00:00'){
										return date('d-m-Y',strtotime($data->linkcustomerpackage->activation_date));	
									}
									else
									{
										return "-";
									}
								}


							],
							[
								'attribute'=>'contract_start_date',
								'value'=>function($data){
									return date('d-m-Y',strtotime($data->linkcustomerpackage->contract_start_date));
								}

							],
							[
								'attribute'=>'contract_end_date',
								'value'=>function($data){
									return date('d-m-Y',strtotime($data->linkcustomerpackage->contract_end_date));
								}

							],
							
							[
								'attribute'=>'sales_person',
								'filter'=>Arrayhelper::map((new Customer)->getUserName(), 'user_id', 'name'),
								'value'=>function($data)
								{
									return $data->user->name;
								}
								//'value'=>'user.user_name'
							],
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->agent_name!=null || $data->agent_name!="")
										return $data->agent_name;
									else
										return "-";
								}
							],
							[
			                    'attribute'=>'created_at',
			                    'value' => function($data){
			                       return date("d-m-Y ",  strtotime($data->created_at));
			                    },
			                    'filter' => DatePicker::widget([
			                        'name' => 'SignupSearch[created_at]',
			                        //'value'=>$strDate,
			                        'template' => '{addon}{input}',
			                            'clientOptions' => [
			                                'autoclose' => true,
			                                'format' => 'yyyy-mm-dd'
			                            ]
			                    ])
			                ],
						];

?>

<?php echo '&nbsp;'.Html::a('Reset Filters', ['/report/signup'], ['class' => 'btn btn-success']);
	
	// Renders a export dropdown menu
 	echo ExportMenu::widget([
    	'dataProvider' 	=> $dataProvider,
    	'columns' 		=> $gridColumns,
    	'filename'		=> 'Customer_contract'.date('Ymdhis')
	]);

?>

 </p>
<?php $form = ActiveForm::begin([
				'action' => ['report/signup'],
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
		<div class="col-md-3 text-right"><label>Select Invoice Date Range :-</label></div>
		<div class="col-md-6">

		<?php echo DateRangePicker::widget([
			'name' => 'CustomerSearch[start_date]',
			'value' => $strDate,
			'nameTo' => 'CustomerSearch[end_date]',
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
 <div class="row">
 	<div class="col-md-11 text-right">
 		<label>Total Package Price (For only active customers) : <?php echo number_format($totalPrice,2) ?></label>
 	</div>
 	
 </div>
 <div class="alert-success alert fade in" id="success" style="display:none"> </div>


<div class="box box-default">
		<div class="box-body">
			<div class="tbllanguage-form">
				<div class="customer-index"  style="overflow: auto;overflow-y: hidden;">

					<?php echo GridView::widget([
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'id'=>'grid',
						'showFooter' =>true,
						'columns' => [
							['class' => 'yii\grid\SerialColumn'],
							'solnet_customer_id',
							'name',
							'billing_address:ntext',
							[
								'attribute'=>'installation_address',
								'value'=>function($data){
									return $data->linkcustomerpackage->installation_address;
								}

							],
							[
								'attribute'=>'package_title',
								'value'=>'linkcustomerpackage.package.package_title'
							],
							[
								'attribute'=>'package_speed',
								'value'=>function($data){
									return $data->linkcustomerpackage->package_speed.' '.$data->linkcustomerpackage->speed->speed_type;
								},
								//'footer' =>'Total'
							],
							[
								'attribute'=>'currency',
								'filter'=>false,
								'value'=>function($data){
									return $data->linkcustomerpackage->currency->currency;
								},
							],
							[
								'attribute'=>'package_price',
								'value'=>function($data){
									return number_format($data->linkcustomerpackage->package_price,2);
								},
								'footer' =>$floatTotBalance,

							],
							[
								'attribute'=>'state',
								'value'=>'state.state',
								
							],
							[
								'attribute'=>'invoice_start_date',
								'value'=>function($data){
									return date('d-m-Y',strtotime($data->linkcustomerpackage->invoice_start_date));
								}

							],
							[
								'attribute'=>'Activation Date',
								'value'=>function($data){
									if($data->linkcustomerpackage->activation_date != '0000-00-00 00:00:00'){
										return date('d-m-Y',strtotime($data->linkcustomerpackage->activation_date));	
									}
									else
									{
										return "-";
									}
									
								}

							],
							[
								'attribute'=>'contract_start_date',
								'value'=>function($data){
									return date('d-m-Y',strtotime($data->linkcustomerpackage->contract_start_date));
								}

							],
							[
								'attribute'=>'contract_end_date',
								'value'=>function($data){
									return date('d-m-Y',strtotime($data->linkcustomerpackage->contract_end_date));
								}

							],
							
							[
								'attribute'=>'sales_person',
								'filter'=>Arrayhelper::map((new Customer)->getUserName(), 'user_id', 'name'),
								'value'=>function($data)
								{
									return $data->user->name;
								}
								//'value'=>'user.user_name'
							],
							[
								'attribute'=>'agent_name',
								'value'=>function($data)
								{
									if($data->agent_name!=null || $data->agent_name!="")
										return $data->agent_name;
									else
										return "-";
								}
							],
							[
			                    'attribute'=>'created_at',
			                    'value' => function($data){
			                       return date("d-m-Y ",  strtotime($data->created_at));
			                    },
			                    'filter' => DatePicker::widget([
			                        'name' => 'SignupSearch[created_at]',
			                        //'value'=>$strDate,
			                        'template' => '{addon}{input}',
			                            'clientOptions' => [
			                                'autoclose' => true,
			                                'format' => 'yyyy-mm-dd'
			                            ]
			                    ])
			                ],
						],
					]); ?>
				</div>
			</div>
		</div>
	</div>
