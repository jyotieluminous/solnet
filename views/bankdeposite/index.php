<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Bank;
use app\models\User;
use app\models\Currency;
use app\models\Bankdeposit;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper; 
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;
use kartik\export\ExportMenu;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BankdepositeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Add Bank Deposit Details';
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

$floatIDRTotBalance = 0;
$floatSGDTotBalance = 0;
$floatUSDTotBalance = 0;
$floatTotBalance    = 0;

if(!empty($dataProvider->getModels())) 
{
    $model = $dataProvider->getModels();
    foreach($dataProvider->getModels() as $key=>$value)
    {
        if($model[$key]->fk_currency_id == 1)
        {   //For IDR Currency
            $floatIDRTotBalance  += $value->amount;
        }
        elseif($model[$key]->fk_currency_id == 2)
        {  //For SGD Currency
             $floatSGDTotBalance  += $value->amount;
        }
        elseif($model[$key]->fk_currency_id == 3)
        {   //For USD Currency
             $floatUSDTotBalance  += $value->amount;
        }
    $floatTotBalance = '<b>IDR '.number_format($floatIDRTotBalance,2).'<br/>SGD '.number_format($floatSGDTotBalance,2).' <br/>USD '.number_format($floatUSDTotBalance,2).'</b><br/>';        
    }
}

/*echo "<pre>";
print_r($dataProvider->getModels());die;*/
$gridColumns = [
                ['class' => 'yii\grid\SerialColumn'],
                
                //'bank_deposit_id'
              
                [
                    'attribute'=>'deposit_date',
                    'value' => function($data){
                    return date("d-m-Y ",  strtotime($data->deposit_date));
                   },
                    'filter' => DatePicker::widget([
                                'name' => 'BankdepositeSearch[deposit_date]',
                                //'value'=>$strDate,
                                'template' => '{addon}{input}',
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-mm-yyyy'
                                    ]
                            ])
                             
                ],
                'customer.name',
                'customer.solnet_customer_id',
                'description:ntext',
                

                [
                    'attribute'=>'deposit_type', 
                    'filter' => [''=>'All','cash'=>'Cash','transfer'=>'Transfer','cheque'=>'Cheque','va'=>'Va']

                ],

                [
                    'attribute'=>'bank',
                    'label'=>'Bank Into',
                    'format'=>'raw',    
                    'value' => function($data)
                    {   
                        if($data->is_solnet_bank == '1')
                        {
                             $arrBankName= Bank::find()->where(['bank_id'=>$data->fk_bank_id])->select('bank_name')->one();
                            
                               return $arrBankName['bank_name'] ;

                        }
                        else
                        {   
                            return $data->bank;
                        }
                    },
                ],

                [
                    'attribute'=>'account_no',
                    'format'=>'raw',    
                    'value' => function($data)
                    {   
                        if($data->is_solnet_bank == '1')
                        {
                             $arrAccountNo= Bank::find()->where(['bank_id'=>$data->fk_bank_id])->select('account_no')->one();
                            
                               return $arrAccountNo['account_no'] ;

                        }
                        else
                        {   
                            return $data->account_no;
                        }
                    },
                    'footer'=>'Total'
                ],

                [
                     'attribute' => 'amount',
					 'value' => function($data){
						return number_format($data->amount,2); 
					 },
                     'options'=>['width'=>100],
                     'footer' =>strip_tags(Bankdeposit::getBankdepositTotal()),
                           
                ],

                [
               
                    'label'=>'Currency',
                    'value'=>'currency.currency',
                    'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' => '']),
                ],
           
          
             ]
?>
<div class="bankdeposit-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Add Bank Deposits', ['create'], ['class' => 'btn btn-success']);?> &nbsp;

        <?php echo Html::a(' Reset Filters',['index'], ['class' => 'btn btn-default']);

        /*echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'filename'=>'bank_deposit'.date('Ymdhis')
        ]);*/ ?>
   </p>
     <div class="alert-success alert fade in" id="success" style="display:none"> </div>
      <?php $form = ActiveForm::begin(['method' => 'get']); ?>
	  
	  
	<div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><b>Search by deposit date</b></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="col-md-8">
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
        </div><!-- /.box-body -->
       
      </div><!-- /.box -->
	  
 
<?php  ActiveForm::end(); ?>
<br>
<?php

//if role is not NOC then show the total
if(Yii::$app->user->identity->fk_role_id!='8' &&  Yii::$app->user->identity->fk_role_id != '22' &&  Yii::$app->user->identity->fk_role_id != '24' &&  Yii::$app->user->identity->fk_role_id != '25' ){
?>
<div class="row">
    <div class="col-md-11 text-right">
        <label>Total Package Price (For only active customers) : <?php echo number_format($totalPackagePrice,2) ?></label>
    </div>
    
 </div>
<?php }?>
 <?php Pjax::begin(['id'=>'bankdeposit-grid']); ?>
<div class="box box-default">
  <div class="box-body">
    <div class="horizontal-scroll"> 
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'id'=>'grid',
            'showFooter' => true,
            'columns' =>[

                ['class' => 'yii\grid\SerialColumn'],
                //['class' => 'yii\grid\CheckboxColumn'],


                //'bank_deposit_id'
              
                [
                    'attribute'=>'deposit_date',
                    'value' => function($data){
                       return date("d-m-Y ",  strtotime($data->deposit_date));
                    },
                    'filter' => DatePicker::widget([
                                'name' => 'BankdepositeSearch[deposit_date]',
                                //'value'=>$strDate,
                                'template' => '{addon}{input}',
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd'
                                    ]
                            ])
                             
                ],
                [
                    'attribute'=>'name',
                    'value'=>function($data)
                    {
                        return isset($data->customer->name)?$data->customer->name:'-';
                    }
                ],
                [
                    'attribute'=>'solnet_customer_id',
                    'value'=>function($data)
                    {
                        return isset($data->customer->solnet_customer_id)?$data->customer->solnet_customer_id:'-';
                    }
                ],
                //'customerinvoice.invoice_number',
                [
                    'attribute'=>'invoice_number',
                    'value'=>function($data)
                    {
                        return isset($data->customerinvoice->invoice_number)?$data->customerinvoice->invoice_number:'-';
                    }
                ],
                [
                    'attribute'=>'state',
                    'value'=>function($data)
                    {
                        return isset($data->customer->state->state)?$data->customer->state->state:'-';
                    }
                ],
                /*'customer.name',
                'customerinvoice.invoice_number',*/
                [
                'attribute'=>'description',
                'value'=>function($data){
                    return mb_strimwidth($data->description, 0, 50, '...'); 
                  }
                ],

                [
                    'attribute'=>'deposit_type', 
                    'filter' => [''=>'All','cash'=>'Cash','transfer'=>'Transfer','cheque'=>'Cheque','va'=>'Va']

                ],

                [
                    'attribute'=>'bank',
                    'label'=>'Bank Into',
                    'format'=>'raw',    
                    'value' => function($data)
                    {   
                        if($data->is_solnet_bank == '1')
                        {
                             $arrBankName= Bank::find()->where(['bank_id'=>$data->fk_bank_id])->select('bank_name')->one();
                            
                               return $arrBankName['bank_name'] ;

                        }
                        else
                        {   
                            return $data->bank;
                        }
                    },
                ],

                [
                    'attribute'=>'account_no',
                    'format'=>'raw',    
                    'value' => function($data)
                    {   
                        if($data->is_solnet_bank == '1')
                        {
                             $arrBankName= Bank::find()->where(['bank_id'=>$data->fk_bank_id])->select('account_no')->one();
                            
                               return $arrBankName['account_no'] ;

                        }
                        else
                        {   
                            return $data->account_no;
                        }
                    },
                    'footer'=>'<b>'.'Total'.'</b>'
                ],

                [
                     'attribute' => 'amount',
					  'value' => function($data){
						return number_format($data->amount,2); 
					 },
                     'options'=> ['width'=>100],
                     'footer' => $floatTotBalance,
                           
                ],

                [
               
                    'label'=>'Currency',
                    'value'=>'currency.currency',
                    'filter' => Html::activeDropDownList($searchModel, 'fk_currency_id', ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency'),['class'=>'form-control','prompt' => '']),
                ],
                               
            
               /* need to ask client 
                [
                     'attribute' => 'is_solnet_bank',
                     'options'=>['width'=>30],
       
                ],*/
                 
                // 'is_deleted',
                 
                  [
                    'attribute'=>'created_at',
                    'value' => function($data){
                       return date("d-m-Y ",  strtotime($data->created_at));
                    },
                    'filter' => DatePicker::widget([
                                'name' => 'BankdepositeSearch[created_at]',
                                //'value'=>$strDate,
                                'template' => '{addon}{input}',
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd'
                                    ]
                            ])
                             
                ],
                // 'updated_at',

                 [
                'header'=>'Action',
                'options'=>['width'=>100],
                'template'=>' {update} {view} {print} {delete}  ',
                'buttons' => [

                'print' => function ($url, $data) {
                       
                        return  Html::a('<i class="fa fa-print"></i>', ['/bankdeposite/print','id'=>$data->bank_deposit_id], [
                              
                            //'data-toggle'=>'tooltip', 
                            'title'=>'Print',
                            'target'=>'_blank',
                            'data-pjax'=>'0'
                         ]);
                    },
                 ],

                'class' => 'yii\grid\ActionColumn'
                ],
            ],
                       
        ]); ?>
     <?php Pjax::end(); ?>
    </div>
   </div>
  </div>
</div>
