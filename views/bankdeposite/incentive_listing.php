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
   use app\models\Linkcustomepackage;
   use app\models\BankdepositeSearch;
   
   /* @var $this yii\web\View */
   /* @var $searchModel app\models\BankdepositeSearch */
   /* @var $dataProvider yii\data\ActiveDataProvider */
   
   $this->title = 'Manage Sales Incentive';
   $this->params['breadcrumbs'][] = $this->title;
   
   if(isset($_GET['start_date']) && isset($_GET['end_date']))
   {
    $strStartDate = $_GET['start_date'];
    $strEndDate = $_GET['end_date'];
   }
   else{
       $strStartDate = '';
       $strEndDate   = '';
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
   ?>
<br/>
<button type="button" class="btn btn-success" id="exportcsv">Import to CSV</button>
<br/><br/>
<div class="bankdeposit-index">
   <?php Pjax::begin(['id'=>'bankdeposit-grid']); ?>
   <div class="box box-default">
      <div class="box-body">
         <?php $form = ActiveForm::begin(['method' => 'get','options' => ['autocomplete' => 'off']]); ?>
         <div class="">
            <div class="box-body">
               <div class="col-md-2">
                  <p style="text-align: center;font-size: 20px;font-weight: 700;">Search by Created date : </p>
               </div>
               <div class="col-md-6">
                  <?php echo DateRangePicker::widget([
                     'name'     => 'start_date',
                     'value'    => $strStartDate,
                     'nameTo'   => 'end_date',
                     'valueTo'  => $strEndDate,
                     'clientOptions' => [
                             'autoclose'=>true,
                             'format' => 'dd-mm-yyyy'
                         ]
                      ]);?>
               </div>
               <div class="col-md-3">
                  <?php echo Html::submitButton('Search',['class' => 'btn btn-success']) ?>
                  <?php echo Html::a('Reset Filters', ['bankdeposite/incentive'], ['class' => 'btn btn-success']) ; ?>
               </div>
            </div>
         </div>
         <?php  ActiveForm::end(); ?>
         <hr />
         <div class="horizontal-scroll"> 
            <?php echo GridView::widget([
               'dataProvider' => $dataProvider,
               'filterModel'  => $searchModel,
               'id'           => 'grid',
               'showFooter'   => true,
               'columns'      => [
                   ['class'   => 'yii\grid\SerialColumn'],
                   [
                       'attribute'=>'created_at',
                       'value' => function($data){
                          return date("d-m-Y ",  strtotime($data->created_at));
                       },
                       'filter'    => DatePicker::widget([
                           'name'  => 'BankdepositeSearch[created_at]',
                           //'value' => $strDate,
                           'template' => '{addon}{input}',
                           'clientOptions' => [
                               'autoclose' => true,
                               'format' => 'yyyy-mm-dd'
                           ]
                       ])       
                   ],
                   [
                       'attribute'=>'deposit_date',
                       'value' => function($data){
                          return date("d-m-Y ",  strtotime($data->deposit_date));
                       },
                       'filter' => DatePicker::widget([
                           'name'          => 'BankdepositeSearch[deposit_date]',
                           'template'      => '{addon}{input}',
                           'clientOptions' => [
                               'autoclose' => true,
                               'format'    => 'yyyy-mm-dd'
                           ]
                       ])            
                   ],
                   [
                       'attribute' => 'solnet_customer_id',
                       'value' => function($data)
                       {
                           return $data->customer->solnet_customer_id;
                       }
                   ],
                   [
                       'attribute' => 'name',
                       'value' => function($data)
                       {
                           return $data->customer->name;
                       }
                   ],
                   [
                       'attribute' => 'installation address',
                       'value' => function($data)
                       {
                           $arrResult = BankdepositeSearch::getLinkdata($data->customer->customer_id);
                           if(!empty($arrResult))
                           {
                              return $arrResult[0]['installation_address'];
                           }
                           else
                           {
                              return '-';
                           }
                       }
                   ],
                   
                   [
                       'attribute' => 'billing_address',
                       'value' => function($data)
                       {
                           return $data->customer->billing_address;
                       }
                   ],
                   [
                       'attribute' => 'invoice_number',
                       'value' => function($data)
                       {
                          if(isset($data->customerinvoice->invoice_number))
                          {

                             return $data->customerinvoice->invoice_number;
                          }
                          else
                          {
                            return "NULL";
                          }
                       }
                   ],
                   [
                       'attribute' => 'package_title',
                       'value' => function($data){
                           $arrResult = BankdepositeSearch::getLinkdata($data->customer->customer_id);
                           if(!empty($arrResult))
                           {
                               return $arrResult[0]['package_title'];
                           }
                           else
                           {
                               return '-';
                           }
                       },
                   ],
                   [
                       'attribute' => 'package_price',
                       'value' => function($data){
                           $arrResult = BankdepositeSearch::getLinkdata($data->customer->customer_id);
                           if(!empty($arrResult))
                           {
                              return number_format($arrResult[0]['package_price'],2);
                           }
                           else
                           {
                              return '-';
                           }
                       },
                       //'footer' => strip_tags(Linkcustomepackage::getContractTotal()),
                   ],
                   [
                       'attribute' => 'installation_fee',
                       'value' => function($data){
                           if(isset($data->customerinvoice->customer_invoice_id)){
                            $arrResult = BankdepositeSearch::getUsagePeriod($data->customerinvoice->customer_invoice_id);
                            if(!empty($arrResult))
                            {
                                return number_format($arrResult[0]['installation_fee'],2);
                            }
                            else return '-';
                           }
                           else return '-';
                       },
                   ],
                   [
                       'attribute' => 'other_fee',
                       'value' => function($data){
                        if(isset($data->customerinvoice->customer_invoice_id)){
                           $arrResult = BankdepositeSearch::getUsagePeriod($data->customerinvoice->customer_invoice_id);
                           if(!empty($arrResult))
                           {
                               return number_format($arrResult[0]['other_service_fee'],2);
                           }
                           else return '-';
                        }
                        else return '-';
                       },
                   ],
                   [
                       'attribute' => 'Usage Period From',
                       'value' => function($data){
                        if(isset($data->customerinvoice->customer_invoice_id)){
                           $arrResult = BankdepositeSearch::getUsagePeriod($data->customerinvoice->customer_invoice_id);
                           if(!empty($arrResult))
                           {
                               return date('d-m-Y',strtotime($arrResult['0']['usage_period_from']));
                           }
                            else return '-';
                         }
                         else return '-';
                       },
                   ],
                   [
                       'attribute' => 'Usage Period To',
                       'value' => function($data){

                        if(isset($data->customerinvoice->customer_invoice_id)){
                           $arrResult = BankdepositeSearch::getUsagePeriod($data->customerinvoice->customer_invoice_id);
                           if(!empty($arrResult))
                           {
                               return date('d-m-Y',strtotime($arrResult['0']['usage_period_to']));
                           }
                           else return '-';
                        }
                        else return '-';
                       },
                       'footer' => 'Total', 
                   ],
                   [
                       'attribute' => 'Amount deposit',
                       'value' => function($data){
                           return number_format($data->amount,2); 
                       },
                       'options'=> ['width'=>100],
                       'footer' => $floatTotBalance,         
                   ],
                   [
                       'attribute'=>'sales_person',
                       'filter' => Arrayhelper::map($user, 'user_id', 'name'),
                       'value' => function($data)
                       {
                           return $data->customer->user->name;
                       }
                   ],
                   [
                       'attribute' => 'agent_name',
                       'value' => function($data)
                       {
                           if($data->customer->agent_name!=null || $data->customer->agent_name!="")
                               return $data->customer->agent_name;
                           else
                               return "-";
                       }
                   ],
                   [
                       'attribute'=>'fiber_installed',
                       'filter'=>array(''=>'All','power'=>'Power','dig'=>'DIG','FTTH'=>'FTTH'),
                       'value' => function($data){
                           if($data->customer->fiber_installed == null){
                               return '-';
                           }else{
                               return $data->customer->fiber_installed;
                           }
                        }
                   ],
                   [
                       'header'    => 'Action',
                       'class'     => 'yii\grid\ActionColumn',
                       'template'  => '{link} ',
                       'buttons'   => [
                           'link'  => function ($url,$data,$key) { 
                            if(isset($data->customerinvoice->customer_invoice_id)) $invoice_id=$data->customerinvoice->customer_invoice_id;else $invoice_id=0;
                               return Html::a('<i class="fa fa-file-pdf-o"></i>',['/invoice/pdf','id' => $invoice_id,'state' => $data->customer->fk_state_id],['target'=>'_blank', 'class' => 'pdfachor'],['title'=>'Print Invoice']);
                           },
                       ]
                   ],
               ],            
               ]); ?>
            <?php Pjax::end(); ?>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">
   $('.pdfachor').on('click', function() {
       window.open($(this).attr('href'));
       return false;
   });
   
   $('#exportcsv').on('click',function(){
        var data = $('form').serialize();
        var fileType = 'CSV';
        
        var created_at = "<?php if(isset($_GET['BankdepositeSearch']['created_at'])){ echo $_GET['BankdepositeSearch']['created_at'];};?>";
        var deposit_date = "<?php if(isset($_GET['BankdepositeSearch']['deposit_date'])){ echo $_GET['BankdepositeSearch']['deposit_date'];};?>";
        var name = "<?php if(isset($_GET['BankdepositeSearch']['name'])){ echo $_GET['BankdepositeSearch']['name'];};?>";
        
        var billing_address = "<?php if(isset($_GET['BankdepositeSearch']['billing_address'])){ echo $_GET['BankdepositeSearch']['billing_address'];};?>";
        var invoice_number = "<?php if(isset($_GET['BankdepositeSearch']['invoice_number'])){ echo $_GET['BankdepositeSearch']['invoice_number'];};?>";
        var sales_person = "<?php if(isset($_GET['BankdepositeSearch']['sales_person'])){ echo $_GET['BankdepositeSearch']['sales_person'];};?>";
        var agent_name = "<?php if(isset($_GET['BankdepositeSearch']['agent_name'])){ echo $_GET['BankdepositeSearch']['agent_name'];};?>";
        var fiber_installed = "<?php if(isset($_GET['BankdepositeSearch']['fiber_installed'])){ echo $_GET['BankdepositeSearch']['fiber_installed'];};?>";
        var start_date = "<?php if(isset($_GET['start_date'])){ echo $_GET['start_date'];};?>";
        var end_date = "<?php if(isset($_GET['end_date'])){ echo $_GET['end_date'];};?>";     
        $.each($(".checkbox-row:checked"), function(){
           ids.push($(this).val());
        });
        console.log(invoice_number);
        //console.log(data);
         $.ajax({
             url: '<?php echo yii::$app->request->baseUrl;  ?>/bankdeposite/incentivecsv',
            dataType: 'html',
            type: 'POST',
            data: {
                data:data,
                name:name,
                created_at:created_at,
                deposit_date:deposit_date,
                billing_address:billing_address,
                invoice_number:invoice_number,
                start_date:start_date,
                end_date:end_date,
                sales_person:sales_person,
                agent_name:agent_name,
                fiber_installed:fiber_installed
            },
            beforeSend: function() {
                $("#exportcsv").html("Please Wait");
                $('#exportcsv').prop('disabled', true);
            },
            success: function(data) {
            $("#exportcsv").html("Import to CSV");
            $('#exportcsv').prop('disabled', false);   
            window.location.href = '<?php echo yii::$app->request->baseUrl;  ?>/bankdeposite/incentivecsvdownload?fileName='+data; 
            }
        });                             
    }); 
</script>