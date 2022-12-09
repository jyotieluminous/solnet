<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Currency;
use app\models\Bank;
use app\models\Customerinvoice;
use app\models\Linkcustomepackage;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
use demogorgorn\ajax\AjaxSubmitButton;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Bankdeposit */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="alert-success alert fade in" id="success_status" style="display:none"> </div>
<div class="box box-default">
  <div class="box-body">
     
      <?php $form = ActiveForm::begin(['id'=>'deposite-form','enableAjaxValidation' => true,]); ?>
     <div class="row">
        <div class="bankdeposit-form">
          <div class="form-group required col-md-3">
          <?php
          if($model->isNewRecord){
            echo $form->field($model, 'search_by',['enableAjaxValidation'=>'true'])->radioList(array('id'=>'Customer ID','name'=>'Customer Name',),array('name'=>'Bankdeposit[search_by]')); 
          }
          
          
           if(!$model->isNewRecord){
            echo '<div style="display:block"> ';
            //$model->fk_customer_id = $model->fk_customer_id;
            echo $form->field($model, 'fk_customer_id',['enableAjaxValidation'=>'true'])->widget(Select2::classname(),[
                                'model'=>$model,
                                'data' => $data,
                                'language' => 'en',
                                'options' => ['placeholder' => 'Select Customer ID'],
                                'pluginOptions' => [
                                'allowClear' => true,
                                'multiple'=>false,
                                'disabled' => true
                             ],
                         ]);
            echo "</div>";

            echo '<div id="hiddencustname2" style="display:none"> ';
              echo $form->field($model, 'fk_customer_id',['enableAjaxValidation'=>'true'])->textInput();
            echo "</div>";
          }
          ?>
           <div id="hiddencustid" style="display:none"> 
              <?php echo $form->field($model, 'fk_customer_id',['enableAjaxValidation'=>'true'])->widget(Select2::classname(),[
                                'model'=>$model,
                                'data' => $data,
                                'language' => 'en',
                                'options' => ['placeholder' => 'Select Customer ID'],
                                'pluginOptions' => [
                                'allowClear' => true,
                                'multiple'=>false,
                             ],
                         ]);
                         ?> 
           </div> 
           <!-- <div class="form-group required col-md-3"> -->
           <div id="hiddencustname" style="display:none"> 
              <?php echo $form->field($model, 'customer_name',['enableAjaxValidation'=>'true'])->widget(Select2::classname(),[
                                'model'=>$model,
                                'data' => $cust_name,
                                'language' => 'en',
                                'options' => ['placeholder' => 'Select Customer Name'],
                                'pluginOptions' => [
                                'allowClear' => true,
                                'multiple'=>false,
                             ],
                         ]);

                         ?> 

           </div>             
               <?php 
               $SolnetIdListData = array();
               if(!$model->isNewRecord)
               {
                  
                  $arrInvoiceId    = Customerinvoice::find()->joinWith('customer')->select(['customer_invoice_id','invoice_number','fk_customer_id'])->where(['fk_customer_id'=>$model->fk_customer_id])->andWhere(['IN',['tblcustomerinvoice.status'],array('unpaid','partial')])->asArray()->all();
                  $SolnetIdListData   = ArrayHelper::map($arrInvoiceId,'customer_invoice_id','invoice_number');
                  echo $form->field($model, 'fk_invoice_id')->widget(Select2::classname(),[
                                'model'=>$model,
                                'data'=>$SolnetIdListData,
                                'language' => 'en',
                                'options' => ['placeholder' => 'Select Invoice'],
                                'pluginOptions' => [
                                'allowClear' => true,
                                'multiple'=>false,
                                'disabled'=>true
                             ],
                         ]);
                   echo '<div id="hiddencustname2" style="display:none"> ';
                      echo $form->field($model, 'fk_invoice_id',['enableAjaxValidation'=>'true'])->textInput();
                    echo "</div>";
               }
               else
               {
                  echo $form->field($model, 'fk_invoice_id')->widget(Select2::classname(),[
                                'model'=>$model,
                                'data'=>$SolnetIdListData,
                                'language' => 'en',
                                'options' => ['placeholder' => 'Select Invoice'],
                                'pluginOptions' => [
                                'allowClear' => true,
                                'multiple'=>false,
                             ],
                         ]);
               }
               
              ?>           
             </div>            
            <div class="form-group required col-md-3">
             
                <?php echo $form->field($model, 'amount',['enableAjaxValidation'=>'true'])->textInput() ?>

                <?php 

                    $arrCurrency=Currency::find()->all();

                    $listCurrency=ArrayHelper::map($arrCurrency,'currency_id','currency');

                    echo $form->field($model, 'fk_currency_id')->dropDownList($listCurrency,['prompt'=>'Select Currency'])->label('Currency');
                 ?> 
            </div>
            
            <div class="form-group required col-md-3">

                <?php echo $form->field($model,'deposit_date')->widget(DatePicker::className(), [
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy',
                            'startDate'=>date('Y-m-d')
                        ],
                        
                ]);?>

                <?php echo $form->field($model, 'deposit_type')->dropDownList([ 'cash' => 'Cash', 'transfer' => 'Transfer', 'cheque' => 'Cheque', 'va' => 'Va', ], ['prompt' => 'Select Deposit Type']) ?>

             
                 <?php echo $form->field($model, 'is_solnet_bank')->checkbox(//['id'=>'isCheck']
                 ); ?>

            </div>        
          


             <?php if($model->isNewRecord){ ?>
           
            <div class="form-group required col-md-3">
                <div id="hiddenaccdiv" style="display:none">  
                 <?php $arrAcoountNo=Bank::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
               
                       $listAccountno=ArrayHelper::map($arrAcoountNo,'bank_id','account_no');

                       echo $form->field($model, 'fk_bank_id'
                        )->dropDownList($listAccountno,['class'=>'accountDropdown','id'=>'accountno','prompt'=>'Select Account Number']);?> 
                </div>
                <div id="hiddenbankdiv" style="display:none">  
                <?php echo $form->field($model, 'bank_name')->textInput(['id'=>'bankname', 'readonly' => 'true']) ?>
                </div>
                
                 <div id="hiddenacctext" >  
                  <?php echo $form->field($model, 'account_no')->textInput() ?>
                 </div>
                 
                 <div id="hiddenbanktext" > 
                 <?php echo $form->field($model, 'bank')->textInput(['maxlength' => true]) ?>
                 </div>
            </div>
            
            <?php }
            
             else {

              if($model->is_solnet_bank == 1){ ?>

            <div class="form-group required col-md-3">
                  <div id="editAccDropdown1" >  
                  <?php $arrAcoountNo=Bank::find()->where(['status'=>'active','is_deleted'=>'0'])->all();
                  
                       $listAccountno=ArrayHelper::map($arrAcoountNo,'bank_id','account_no');

                       echo $form->field($model, 'fk_bank_id'
                        )->dropDownList($listAccountno,['class'=>'accountDropdown','id'=>'accountno','prompt'=>'Select Account Number']);?>  
                   
                 <?php echo $form->field($model, 'bank')->textInput(['id'=>'bankname', 'readonly' => 'true']) ?>
                  </div>

                 
                 <div id="editAccText1"  style="display:none"> 
                <?php  echo $form->field($model, 'account_no')?> 
                  
                <?php echo $form->field($model, 'bank')->textInput([]) ?>
                </div>

            <?php } 
             else { ?>
            <div class="form-group required col-md-3">

                 <div id="editAccDropdown2" style="display:none" >  
                <?php $arrAcoountNo=Bank::find()->where(['status'=>'active','is_deleted'=>'0'])->all();

                     $listAccountno=ArrayHelper::map($arrAcoountNo,'bank_id','account_no');

                     echo $form->field($model, 'fk_bank_id'
                      )->dropDownList($listAccountno,['class'=>'accountDropdown','id'=>'accountno','prompt'=>'Select Account Number']);?>  
                 
               <?php echo $form->field($model, 'bank')->textInput(['id'=>'bankname', 'readonly' => 'true']) ?>
                </div>
                 <div id="editAccText1" > 
                <?php  echo $form->field($model, 'account_no')?> 
                  
                <?php echo $form->field($model, 'bank')->textInput([]) ?>
                </div>
            <?php } ?>
          </div>
            <?php  } ?> 
            
      
        <div class="form-group required col-md-3">
            <?php echo  $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>

        <div class="btn-align">
          <?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>

          <?php if($model->isNewRecord){?>
          <?= Html::submitButton('Save & Add More' , ['class' => 'btn btn-primary', 'name'=>'addmore' ,'value' => 'add']) ?>
          <?php } ?>
          
          <?php echo Html::a('Cancel', ['index'], ['class' => 'btn btn-warning']) ;?>     
       </div>
     </div>
     <?php ActiveForm::end(); ?>
  </div>
 </div>
</div>

<script type="text/javascript">
    
  $(function () {

        $("#bankdeposit-is_solnet_bank").click(function () {
            if ($(this).is(":checked")) {

                $("#hiddenaccdiv").show();
                $("#hiddenacctext").hide();
                $("#hiddenbanktext").hide();
                $("#editAccDropdown1").show();
                $("#editAccText1").hide();
                $("#editAccDropdown2").show();
                $("#editAccText2").hide();
               
                 
 
            } else {
               
                $("#hiddenacctext").show();
                $("#hiddenbanktext").show();
                $("#hiddenaccdiv").hide();
                $("#hiddenbankdiv").hide();
                $("#editAccDropdown1").hide();
                $("#editAccText1").show();
                $("#editAccDropdown2").hide();
                $("#editAccText2").show();

            }
        });

        $('#accountno').change(function(){
            
          var intBankId = $(this).val();
            if(intBankId!='')
                {
                    $.ajax({
                      url: '<?php echo yii::$app->request->baseUrl;  ?>/bank/getbankname',
                      type: 'post',
                      data: {id : intBankId},
                      dataType:'json',
                      cache: false,
                      success: function(response){
                        $("#hiddenbankdiv").show();
                        $("#bankname").val(response);
                      }
                    });
                    return false;
                }
        });

        $('#bankdeposit-fk_customer_id').change(function(){
          var intCustId = $(this).val();
          if(intCustId!='')
          {
            $.ajax
            ({
              url: '<?php echo yii::$app->request->baseUrl;  ?>/bankdeposite/getinvoiceid',
              type: 'post',
              data: {id : intCustId},
              //dataType:'json',
              success: function(response)
              {
                $('#bankdeposit-fk_invoice_id').html("");
                $('#bankdeposit-amount').val("");
                $('#bankdeposit-fk_invoice_id').html(response);
              }
            });
          };
        });
        $('#bankdeposit-customer_name').change(function(){
          var intCustId = $(this).val();
          if(intCustId!='')
          {
            $.ajax
            ({
              url: '<?php echo yii::$app->request->baseUrl;  ?>/bankdeposite/getinvoiceid',
              type: 'post',
              data: {id : intCustId},
              //dataType:'json',
              success: function(response)
              {
                $('#bankdeposit-fk_invoice_id').html("");
                $('#bankdeposit-amount').val("");
                $('#bankdeposit-fk_invoice_id').html(response);
              }
            });
          };
        });
        $('#bankdeposit-fk_invoice_id').change(function(){
        
         var invoiceId = $(this).find('option:selected').attr('value');
         
         $.ajax
            ({
              url: '<?php echo yii::$app->request->baseUrl;  ?>/bankdeposite/getinvoiceid',
              type: 'post',
              data: {invId : invoiceId},
              //dataType:'json',
              success: function(response)
              {
                //$('#bankdeposit-fk_invoice_id').html("");
                $('#bankdeposit-amount').val(response);
              }
            }); 
        });

         $('input[type="radio"]').click(function(){
            var data = $(this).attr("value");
            if(data=='id')
            {
              $('#hiddencustid').show();
              $('#hiddencustname').hide();
            }
            else if(data=='name')
            {
              $('#hiddencustname').show();
              $('#hiddencustid').hide();
            }
        });


    });

</script>
