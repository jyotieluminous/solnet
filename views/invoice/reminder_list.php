<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use app\models\Currency;
use yii\helpers\Url; 
use yii\bootstrap\Modal;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reminder Invoices List';
$this->params['breadcrumbs'][] = $this->title;


$gridColumns = [
    ['class'=>'yii\grid\SerialColumn'],
						
						[
							'attribute'=>'custid',
							'value'=>'customer.solnet_customer_id',
							'group'=>true,
						],
						[
							'attribute'=>'name',
							'value'=> 'customer.name',
							 'group'=>true, 
							
						],
					
						[
							'attribute'=>'package_title',
							'value'=>'linkcustomepackage.package.package_title'
													
						],

						

						[
							//'label'=>'Package Speed',
							'attribute'=>'package_speed',
							'value'=>function($data){
								return $data->linkcustomepackage->package_speed.' '.$data->linkcustomepackage->speed->speed_type;
							}
						],
						'invoice_number',
						[
							'attribute'=>'invoice_date',
							'options'=>['width'=>'15%'],
							'value'=>function($data){
									return date('d-m-Y',strtotime($data->invoice_date));
							},
							'filter' => DatePicker::widget([
                                'name' => 'CustomerinvoiceSearch[invoice_date]',
                                'template' => '{addon}{input}',
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd'
                                    ]
                            ])
						],
						
						[
							'label'=> 'Total Invoice Amount',
							'value' => function($data){
								return number_format($data->total_invoice_amount,2);
							},
						],
						
						[
							'label'=> 'Pending Amount',
							'value' => function($data){
								return number_format($data->pending_amount,2);
							},
						],

						[
							'attribute'=>'currency',
							'filter'=>Arrayhelper::map($currency, 'currency_id', 'currency') ,
							'value'=>'linkcustomepackage.currency.currency'
						],


						[
							'attribute'=>'due_date',
							'options'=>['width'=>'15%'],
							'value'=>function($data){
									return date('d-m-Y',strtotime($data->due_date));
							},
							'filter' => DatePicker::widget([
                                'name' => 'CustomerinvoiceSearch[due_date]',
                                'template' => '{addon}{input}',
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd'
                                    ]
                            ])
						],
						[
							'attribute'=>'status',
							'filter'=>array('paid'=>'Paid','cancelled'=>'Cancelled','unpaid'=>'Unpaid','partial'=>'Partial'),
							'value'=>function($data){
								return ucfirst($data->status);
							}
						],
						[
							'attribute'=>'payment_term',
							'value'=>function($data){
								if(!empty($data->linkcustomepackage->payment_term)){
									return 'Net '.$data->linkcustomepackage->payment_term.' Days';
								}
								else{
									return '--';
								}
								
							}
						],

						[
							'label'=>'Reminder Mail Sent',
							'attribute'=>'is_remider_mail_sent',
							'filter'=>array('yes'=>'Yes','no'=>'No'),
							'value'=>function($data){
								return ucfirst($data->is_remider_mail_sent);
							},
						],

						[
						'attribute'=>'send_mail',
						'group'=>true,				 
						'value' => function ( $data) {
                  		 return  Html::a(Yii::t('app', ' {modelClass}', [
                          'modelClass' => 'Reminder to '.$data->customer->solnet_customer_id,
                          ]), ['invoice/remindermodal','id'=>$data->fk_customer_id], ['class' => 'btn btn-danger popup', 'id' => 'popupModal_'.$data->fk_customer_id]);      
             
						  },
						  'format'=>'raw',
						],

						[
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
						]
	];
?>


<?php if ( Yii::$app->session->hasFlash('error_sent_reminder')):?>
    <div class="alert alert-danger"><?php echo Yii::$app->session->getFlash('error_sent_reminder');?></div>
<?php endif;
 if ( Yii::$app->session->hasFlash('succ_sent_reminder')):?>
    <div class="alert alert-info"><?php echo Yii::$app->session->getFlash('succ_sent_reminder');?></div>
<?php endif;?>
<?php if ( Yii::$app->session->hasFlash('errorMessage')):?>
    <div class="alert alert-danger"><?php echo Yii::$app->session->getFlash('errorMessage');?></div>
<?php endif;?>  

<p style="display: inline-block;">
<?php echo Html::a('Reset Filters', ['/invoice/reminderlist'], ['class' => 'btn btn-success']) ?>
</p>



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

<?php 
/*Modal::begin([
    'id'     => "modal",
     //'headerOptions' => ['id' => 'modalHeader'],
    'header' => '<h3 class="text-center">Customer Contract</h3>',
]);
echo "<div id='modalContent'></div>";
Modal::end();


$this->registerJs(
    "$(document).on('ready pjax:success', function() {
            $('.list').click(function(e){
               e.preventDefault(); //for prevent default behavior of <a> tag.

               $('#modal').modal('show').find('#modalContent').load;
              
                
           });
        });
    ");*/

    yii\bootstrap\Modal::begin(['id' =>'modal']);
    yii\bootstrap\Modal::end();


    $this->registerJs("$(function() {

   $('.popup').click(function(e) {

     e.preventDefault();
     $('#modal').modal('show').find('.modal-content')
     .load($(this).attr('href'));
   });
});");

?>
