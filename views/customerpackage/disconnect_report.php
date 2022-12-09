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
   
   $this->title = 'Disconnection Report';
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
   			             'class'    => 'yii\grid\ActionColumn',
   					    'header'   => 'Action',
   					    'template'=>'{view} {print}{update}',
   					     'buttons'  => [
   					     	'update' => function ($url,$data,$key) {
   								$url = Url::to(['customerpackage/updatecomment','id'=>$data->cust_pck_id]);
      								return Html::a('<i class="fa fa-pencil"></i>','javascript:void(0)', ['class' => 'updatecomment','title'=>'Update Comment','value'=>$url]);
   							},
   					        'view' => function ($url, $data) {
   					           
   					            return  Html::a('<i class="fa fa-eye"></i>', ['/customerpackage/disconnectview','id'=>$data->cust_pck_id], [
   			                          
   			                        'data-toggle'=>'tooltip', 
   			                        'title'=>'View',
   			                        
   			                   	 ]);
   					        },
   
   					        'print' => function ($url, $data) {
   					           
   					            return  Html::a('<i class="fa fa-print"></i>', ['/customerpackage/print','id'=>$data->cust_pck_id], [
   			                          
   			                        'data-toggle'=>'tooltip', 
   			                        'title'=>'Print',
   			                        'target'=>'_blank',
   			                        'data-pjax'=>'0'
   			                   	 ]);
   					        },
   					    ],
   					    
   					  ];				  
   
   if(Yii::$app->user->identity->fk_role_id=='22' || Yii::$app->user->identity->fk_role_id=='8' || Yii::$app->user->identity->fk_role_id=='23' || Yii::$app->user->identity->fk_role_id=='24' || Yii::$app->user->identity->fk_role_id=='25'){
   
   	$gridColumns=[
   				['class' => 'yii\grid\SerialColumn'],
   						
   				[
   					'attribute'=>'solnet_customer_id',
   					'value'=>'customer.solnet_customer_id',
   				],
   
   				[
   					'attribute'=>'name',
   					'value'=>'customer.name',
   					
   				],
   				[
   					'attribute'=>'state',
   					'value'=>function($data)
   					{
   						return $data->customer->state->state;
   					}
   				],
   				'installation_address',
   				/*[
   					'attribute'=>'billing_address',
   					'value'=>'customer.billing_address',
   					
   				],*/
   				//'state.state',
   				[
   					'attribute'=>'sales_person',
   					'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
   					'value'=>function($data)
   					{
   						return $data->customer->user->name;
   					}
   					//'value'=>'user.user_name'
   				],
   				[
   					'attribute'=>'agent_name',
   					'value'=>function($data)
   					{
   						if($data->customer->agent_name!=null || $data->customer->agent_name!="")
   							return $data->customer->agent_name;
   						else
   							return "-";
   					}
   				],
   				[
   					'attribute'=>'mobile_no',
   					'value'=>'customer.mobile_no',
   					
   				],
   
   				[
   					'attribute'=>'package_title',
   					'value'=>'package.package_title',
   					
   				],
   
   				'package_speed',
   
   				[
   					'attribute'=>'speed_type',
   					'value'=>'speed.speed_type',
   					'filter' => Html::activeDropDownList($searchModel, 'fk_speed_id', ArrayHelper::map(Speed::find()->asArray()->all(), 'speed_id', 'speed_type'),['class'=>'form-control','prompt' => '']),
   					'footer'=>'Total'
   				],
   				
   
          			[
   					'attribute'=>'fk_currency_id',
   					'value'=>'currency.currency',
   					'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' => '']),
   					
   				],
   
   			
   
   				[
   	                'attribute'=>'disconnection_date',
   	                'value'=>function($data){
   	                        return date("d-m-Y",strtotime($data->disconnection_date));
   	                },
   	                'filter' => DatePicker::widget([
   	                            'name' => 'LinkcustomepackageSearch[disconnection_date]',
   	                            //'value'=>$strDate,
   	                            'template' => '{addon}{input}',
   	                            'clientEvents' => [
               						'changeDate' => true
           						],
   								'clientOptions' => [
   									'autoclose' => true,
   									'format' => 'yyyy-mm-dd',	
                               	],
       					  ])
                    
               	],
               	[
               		'attribute'=>'reason_for_disconnection',
               		'value'=>function($data)
               		{
               			return $data->reason_for_disconnection;
               		}
               	],
               	
   				$actionColumn
   			];
   	$showFooter = false;		
   }
   else
   {
   	$gridColumns=[
   				['class' => 'yii\grid\SerialColumn'],
   						
   				[
   					'attribute'=>'solnet_customer_id',
   					'value'=>'customer.solnet_customer_id',
   					
   				],
   
   				[
   					'attribute'=>'name',
   					'value'=>'customer.name',
   					
   				],
   				[
   					'attribute'=>'state',
   					'value'=>function($data)
   					{
   						return $data->customer->state->state;
   					}
   				],
   				'installation_address',
   				/*[
   					'attribute'=>'billing_address',
   					'value'=>'customer.billing_address',
   					
   				],*/
   				//'state.state',
   				[
   					'attribute'=>'sales_person',
   					'filter'=>Arrayhelper::map($user, 'user_id', 'name'),
   					'value'=>function($data)
   					{
   						return $data->customer->user->name;
   					}
   					//'value'=>'user.user_name'
   				],
   				[
   					'attribute'=>'agent_name',
   					'value'=>function($data)
   					{
   						if($data->customer->agent_name!=null || $data->customer->agent_name!="")
   							return $data->customer->agent_name;
   						else
   							return "-";
   					}
   				],
   				[
   					'attribute'=>'mobile_no',
   					'value'=>'customer.mobile_no',
   					
   				],
   
   				[
   					'attribute'=>'package_title',
   					'value'=>'package.package_title',
   					
   				],
   
   				'package_speed',
   
   				[
   					'attribute'=>'speed_type',
   					'value'=>'speed.speed_type',
   					'filter' => Html::activeDropDownList($searchModel, 'fk_speed_id', ArrayHelper::map(Speed::find()->asArray()->all(), 'speed_id', 'speed_type'),['class'=>'form-control','prompt' => '']),
   					'footer'=>'Total'
   				],
   				//'package_price',
   				
          			
   				[
   			         'attribute' => 'package_price',
   					 'value' => function($data){
   						 $price = number_format($data->package_price,2);
   						 return $price;
   					 },
   			         'footer' => strip_tags(Linkcustomepackage::getDisconnectionTotal()),
   			               
          			],
   
          			[
   					'attribute'=>'fk_currency_id',
   					'value'=>'currency.currency',
   					'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' => '']),
   					
   				],
   
   			
   
   				[
   	                'attribute'=>'disconnection_date',
   	                'value'=>function($data){
   	                        return date("d-m-Y",strtotime($data->disconnection_date));
   	                },
   	                'filter' => DatePicker::widget([
   	                            'name' => 'LinkcustomepackageSearch[disconnection_date]',
   	                            //'value'=>$strDate,
   	                            'template' => '{addon}{input}',
   	                            'clientEvents' => [
               						'changeDate' => true
           						],
   								'clientOptions' => [
   									'autoclose' => true,
   									'format' => 'yyyy-mm-dd',	
                               	],
       					  ])
                    
               	],
               	[
               		'attribute'=>'reason_for_disconnection',
               		'value'=>function($data)
               		{
               			return $data->reason_for_disconnection;
               		}
               	],
   				$actionColumn
   				
   			];
   			$showFooter = true;
   }
    
    ?>
<?php
   Modal::begin([
       'id'     => "modal",
       'header' => '<h3 class="text-center">Update comment</h3>',
   ]);
   
   echo "<div id='modalContent'></div>";
   Modal::end();
   
   
   $this->registerJs(
       "$(document).on('ready pjax:success', function() {
               $('.updatecomment').click(function(e){
                  e.preventDefault(); //for prevent default behavior of <a> tag.
                  $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));
              });
           });
       ");
   
   ?>
<p><?= Html::a('Reset Filters', ['customerpackage/disconnectreport'], ['class' => 'btn btn-success']) ?> &nbsp;
   <?php
      // Renders a export dropdown menu
      echo ExportMenu::widget([
         'dataProvider' => $dataProvider,
         'columns' => $gridColumns,
         'filename'=>'disconnection_report'.date('Ymdhis')
      ]);
      ?>
</p>
<div class="alert-success alert fade in" id="success" style="display:none"> </div>
<?php $form = ActiveForm::begin(['method' => 'get']); ?>
<div class="row">
   <div class="col-md-3 " align="right">
      <b>Select Disconnection Date Range:</b>
   </div>
   <div class="col-md-6">
      <?php echo DateRangePicker::widget([
         'name' => 'start_date',
         'value' => $strStartDate,
         'nameTo' => 'end_date',
         'valueTo' => $strEndDate,
         'clientOptions' => [
                     'autoclose'=>true,
                     'format' => 'dd-mm-yyyy'
                 ]
          ]);?>
   </div>
   <div class="col-md-3">
      <?php echo Html::submitButton('Search',['class' => 'btn btn-success']) ?>
   </div>
</div>
<?php  ActiveForm::end(); ?>
<br>
<?php Pjax::begin(['id'=>'disconnect-grid']); ?>
<div class="box box-default">
   <div class="box-body">
      <div class="tbllanguage-form">
         <div class="customer-index">
            <?= kartik\grid\GridView::widget([
               'dataProvider' => $dataProvider,
               'filterModel' => $searchModel,
               'columns'=>$gridColumns,
               'showFooter'=>$showFooter,
               		    
               	]); ?>
         </div>
      </div>
   </div>
</div>
<?php Pjax::end(); ?>
<?php 
   Modal::begin([
       'id'     => "modal",
       'header' => '<h3 class="text-center">Disconnect Report</h3>',
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