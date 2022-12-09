<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use app\models\Currency;
use app\models\Bank;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Customerinvoice */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Pay Invoice';

?>
<div class="box box-default">
		<div class="box-body">
			<div class="customerinvoice-form">
			<?php if(Yii::$app->session->hasFlash('success_paid')) : ?>
            <div class="alert-success alert fade in">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?php echo Yii::$app->session->getFlash('success_paid'); ?>
            </div>
 			<?php endif; ?>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group required col-md-6">
						<label>Customer Name : </label>
						<?php echo $model->customer->name; ?>
					</div>
					<div class="col-md-6 form-group required">
						<label>Customer ID : </label>
						<?php echo $model->customer->solnet_customer_id; ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-6 form-group required">
						<label>Package Title : </label>
						<?php echo $model->linkcustomepackage->package->package_title; ?>
					</div>
					<div class="col-md-6 form-group required">
						<label>Package Price : </label>
						<?php echo $model->linkcustomepackage->currency->currency.' '. number_format($model->linkcustomepackage->package_price,2)?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-6 form-group required">
							<label>Pending Amount : </label>
							<?php echo  $model->linkcustomepackage->currency->currency.' '.number_format($model->pending_amount,2); ?>
					</div>
					<div class="col-md-6 form-group required">
							<label>Invoice Amount : </label>
							<?php echo  $model->linkcustomepackage->currency->currency.' '.number_format($model->total_invoice_amount,2) ; ?>
					</div>
				</div>
			</div>
				<?php if($model->pending_amount>0) { ?>
    <?php $form = ActiveForm::begin(['id'=>'pay_form','enableAjaxValidation'=>true]); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group col-md-6">
				<?php echo $form->field($model, 'discount',['enableAjaxValidation'=>'true'])->textInput() ?>
			</div>
			<div class="col-md-6 form-group">
				<?php echo $form->field($model, 'bank_amount',['enableAjaxValidation'=>'true'])->textInput() ?>
			</div>
		</div>
	</div>
   <div class="row">
		<div class="col-md-12">
			<div class="form-group col-md-6">
				<?php echo $form->field($model, 'deduct_tax',['enableAjaxValidation'=>'true'])->textInput() ?>
			</div>
			<div class="form-group  col-md-6 required">
				<?php echo $form->field($model, 'payment_amount',['enableAjaxValidation'=>'true'])->textInput() ?>
			</div>
		</div>
	</div>
  <div class="row">
		<div class="col-md-12">
			<div class="form-group col-md-6">
				<?php 
				//if($pay->payment_method)
				echo $form->field($pay, 'payment_method')->dropdownList(['cash'=>'Cash','virtual_transfer'=>'Virtual transfer','bank'=>'Bank','cheque'=>'Cheque','debit_card'=>'Debit Card','credit_card'=>'Credit Card'],['prompt'=>'Select Payment Method']) ?>
			</div>
			<div class="form-group  col-md-6 ">
				<?php echo $form->field($pay, 'reciept_no',['enableAjaxValidation'=>'true'])->textInput() ?>
			</div>
		</div>
	</div>
   <div class="row">
   	 <div class="col-md-12">
   	 		<div class="form-group  col-md-6 required cheque">
				<?php echo $form->field($pay, 'cheque_no')->textInput() ?>
			</div>
   	 </div>
   </div>
   <div class="row">
		<div class="col-md-12">
			<div class="form-group col-md-6 required ">

				<?php echo $form->field($pay, 'payment_date')->widget(
                                            DatePicker::className(), [
                                                'clientOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-mm-yyyy',
                                                ]
                                        ]);?>
			</div>
			<div class="col-md-6 form-group">
				<?php echo $form->field($pay, 'comment')->textArea() ?>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<div class="form-group col-md-6 required ">
			<?php 
				$pay->po_wo_number = "0";
				echo $form->field($pay, 'po_wo_number')->textInput()
			?>
			</div>
			<div class="form-group col-md-6 ">
				<?php 
					echo $form->field($pay, 'receipt')->fileInput() 
				?>
				<?php 
					echo $form->field($pay, 'cheque')->fileInput() 
				?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="form-group col-md-6 required ">
				<?php
					
				 echo $form->field($model, 'check')->checkbox(array('label'=>'Deposite amount in bank','id'=>'bank-deposit')); ?>
			</div>
		</div>
	</div>	
	<div class="bank-form" style="display:none">
	<div class="row">
	<div class="form-group required col-md-3">
              <?php 
              $model->fk_customer_id = $model->fk_customer_id;
              echo $form->field($model, 'fk_customer_id')->widget(Select2::classname(),[
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

               <?php 
               $data = array($model->customer_invoice_id => $model->invoice_number);
               $model->fk_invoice_id = $model->customer_invoice_id;
               echo $form->field($model, 'fk_invoice_id')->widget(Select2::classname(),[
                                'model'=>$model,
                                'data'=>$data,
                                'language' => 'en',
                                'options' => ['placeholder' => 'Select Invoice'],
                                'pluginOptions' => [
                                'allowClear' => true,
                                'multiple'=>false,
                             ],
                         ]);
                         ?>           
             </div> 
             <div class="form-group required col-md-3">
             	<?php 
             	if(!isset($model->amount))
             		$model->amount = $model->total_invoice_amount;
             	echo $form->field($model, 'amount',['enableAjaxValidation'=>'true'])->textInput(); 

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
            
           
            <div class="form-group required col-md-3">

                 <div id="editAccDropdown2" style="display:none" >  
                <?php $arrAcoountNo=Bank::find()->where(['status'=>'active','is_deleted'=>'0'])->all();

                     $listAccountno=ArrayHelper::map($arrAcoountNo,'bank_id','account_no');

                     echo $form->field($model, 'fk_bank_id',['enableAjaxValidation'=>'true']
                      )->dropDownList($listAccountno,['class'=>'accountDropdown','id'=>'accountno','prompt'=>'Select Account Number']);?>  
                 
               <?php echo $form->field($model, 'bank',['enableAjaxValidation'=>'true'])->textInput(['id'=>'bankname', 'readonly' => 'true']) ?>
                </div>
                 <div id="editAccText1" > 
                <?php  echo $form->field($model, 'account_no',['enableAjaxValidation'=>'true'])?> 
                  
                <?php echo $form->field($model, 'bank',['enableAjaxValidation'=>'true'])->textInput([]) ?>
                </div>
            	<div id="hiddenbankdiv" style="display:none">  
                <?php echo $form->field($model, 'bank_name',['enableAjaxValidation'=>'true'])->textInput(['id'=>'banknamehidden', 'readonly' => 'true']) ?>
                </div>
          </div>
            
        <div class="form-group required col-md-3">
            <?php echo  $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
         </div>
     </div>
    <div class="form-group">
        <?php echo Html::submitButton('Save', ['id'=>'pay-save','class' => 'btn btn-success']) ?>
        <?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
        <div class="pull-right">
		    <?php 
		     $url = Url::to(['invoice/pendingamount','id'=>$model->fk_customer_id]);
		    echo Html::a('Check Pending Amount','javascript:void(0)', ['class' => 'pendingamount btn btn-success','value'=>$url]) ?>
		</div>
    </div>

    <?php ActiveForm::end(); ?>
    
    <?php } ?>
	    <div class="row">
			<h1 align="center">Payment History</h1>
			<div class="col-md-12">
				<?php if(Yii::$app->session->hasFlash('success_delete')) : ?>
	            <div class="alert-success alert fade in">
	                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	                    <?php echo Yii::$app->session->getFlash('success_delete'); ?>
	            </div>
					<?php endif; ?>
				<div class="col-md-6 ">
					<?php echo GridView::widget([
						'dataProvider' => $dataProvider,
						//'filterModel' => $searchModel,
						'columns' => [
							['class' => 'yii\grid\SerialColumn'],
							[
								'attribute'=>'invoice_number',
								'value'=>function($data)
								{
									return $data->invoice->invoice_number;
								}
							],
							[
								'attribute'=>'discount',
								'value' => function($data){
									return number_format($data->discount,2);
								},
								'filter'=>false
							],
							[
								'attribute'=>'deduct_tax',
								'value' => function($data){
									return number_format($data->deduct_tax,2);
								},

								'filter'=>false
							],
							[
								'attribute'=>'bank_admin',
								'value' => function($data){
									return number_format($data->bank_admin,2);
								},
								'filter'=>false
							],
							[
								'attribute'=>'payment_method',
								'filter'=>false
							],
							[
								'attribute'=>'amount_paid',
								'value' => function($data){
									return number_format($data->amount_paid,2);
								},
								'filter'=>false
							],
							[
								'attribute'=>'payment_date',
								'value'=>function($data){
									return date("d-m-Y",  strtotime($data->payment_date));
								},
								'filter'=>false
							],
							[
								'attribute'=>'comment',
								'value'=>function($data)
								{
									return $data->comment;
								}
							],
							[
						'header'=>'Action',
						'options'=>['width'=>'140%'],
						'class' => 'yii\grid\ActionColumn',
						'template'=>'{delete}',
						'buttons'=>[
							
							'delete' => function ($url,$model,$key) {
									
									 return Html::a('<i class="fa fa-trash"></i>', ['/invoice/deletepaymenthistory','id'=>$model->payment_id,'action'=>'pay'], ['title' => 'Cancel','data-confirm'=>'Are you sure you want to delete this payment??']);
									
							},
							
						]
					],
						],
					]); ?>
				</div>
			</div>
		</div>

		<hr />
				
			<div class="row">
				<h1 align="center">Images Details</h1>
				<div class="col-md-12">
					<div class="col-md-6">
						<?php 
							if(Yii::$app->session->hasFlash('success_add_msg')) : ?>
				            <div class="alert-success alert fade in">
				                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				                    <?php echo Yii::$app->session->getFlash('success_add_msg'); ?>
				            </div>
			 			<?php endif; ?>
			 			<?php 
							if(Yii::$app->session->hasFlash('success_delete_msg')) : ?>
				            <div class="alert-success alert fade in">
				                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				                    <?php echo Yii::$app->session->getFlash('success_delete_msg'); ?>
				            </div>
			 			<?php endif; ?>
			 			<?php 
							if(Yii::$app->session->hasFlash('error_add_msg')) : ?>
				            <div class="alert-error alert fade in">
				                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				                    <?php echo Yii::$app->session->getFlash('error_add_msg'); ?>
				            </div>
			 			<?php endif; ?>
		 			</div>
		 			<div class="col-md-7">
		 				<table class="table table-striped table-bordered">
		 					<thead>
		 						<tr>
		 							<th>#</th>
		 							<th>Invoice Number</th>
		 							<th>Payment Date</th>
		 							<th>Receipt</th>
		 							<th>Cheque</th>
		 							<th>Action</th>
		 						</tr>
		 					</thead>
		 					<tbody>
		 						<?php $intCount = 1;
		 							foreach ($arrPaymentResultData as $key => $value) {
		 						?>
		 						<tr>
		 							<td><?php echo $intCount++; ?></td>
		 							<td><?php echo $value->invoice->invoice_number; ?></td>
		 							<td><?php echo date("d-m-Y",  strtotime($value->payment_date)); ?></td>
		 							<?php $form = ActiveForm::begin(['action' => ['invoice/uploadimg/'.$value->payment_id],'id'=>'pay_form','enableAjaxValidation'=>true]); ?>
		 								<input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo $value->invoice->customer_invoice_id; ?>">
		 								<input type="hidden" name="intId" id="intId" value="<?php echo $value->payment_id; ?>">
		 								<input type="hidden" name="action" id="action" value="updateImages">
		 								<input type="hidden" name="intinvoiceId" id="intinvoiceId" value="<?php echo $value->invoice->invoice_number; ?>">
		 							<td>
		 								<?php
		 									if($value->receipt != '')
		 									{
		 								?>
		 									<?php $strUrl = Url::to('@web/uploads/invoice/'.$value->receipt); ?>
		 									<a href="<?php echo $strUrl; ?>"  target="_blank">View</a><br/>
		 									<?php $url = Yii::$app->request->baseUrl; ?>
		 									<a href="<?php echo $url."/invoice/deleteimg/".$value->payment_id."?action=deletereceipt"; ?>" title="Delete receipt image" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
		 								<?php 
		 									}
		 									else
		 									{ 
		 										echo $form->field($pay, 'receipt')->fileInput()->label(false);
		 									}
		 								?>
		 							</td>
		 							<td>
		 								<?php
		 									if($value->cheque != '')
		 									{
		 								?>
		 									<?php $strUrl = Url::to('@web/uploads/invoice/'.$value->cheque); ?>
		 									<a href="<?php echo $strUrl; ?>"  target="_blank">View</a><br/>
		 									<?php $url = Yii::$app->request->baseUrl; ?>
		 									<a href="<?php echo $url."/invoice/deleteimg/".$value->payment_id."?action=deletecheque"; ?>"  title="Delete cheque image" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a>
		 								<?php 
		 									}
		 									else
		 									{ 
												echo $form->field($pay, 'cheque')->fileInput()->label(false);
		 									}
		 								?>
		 							</td>
		 							<td>
			 							<?php
		 									if($value->cheque == '' Or $value->receipt == '')
		 									{
		 										echo Html::submitButton('Save', ['id'=>'pay-save','class' => 'btn btn-success']);
			 								}
			 								else
			 								{
			 									echo '-';
			 								}
			 							?>
		 							</td>
		 							<?php ActiveForm::end(); ?>
		 						</tr>
		 						<?php 
		 							}
		 						?>
		 					</tbody>
		 				</table>
		 			</div>
				</div>
			</div>
	</div>
</div>
</div>

<?php

Modal::begin([
    'id'     => "modal",
    'header' => '<h3 class="text-center">Pending Amount</h3>',
]);

echo "<div id='modalContent'></div>";
Modal::end();


$this->registerJs(
    "$(document).on('ready pjax:success', function() {
            $('.pendingamount').click(function(e){
               e.preventDefault(); //for prevent default behavior of <a> tag.
               $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));
           });
        });
    ");

?>

<script type="text/javascript">
$( document ).ready(function() {

		var pending_amt = '<?php echo $model->pending_amount; ?>';
		var changedAmt=0;
		$( "input[type='text']" ).change(function() {
			$("#customerinvoice-payment_amount").parent().next(".validation").remove();
			var $this = $(this);
			var payment_amt  = $('#customerinvoice-payment_amount').val();
			var discount  = $('#customerinvoice-discount').val();
			var bank_amount  = $('#customerinvoice-bank_amount').val();
			var deduct_tax  = $('#customerinvoice-deduct_tax').val();
			var changedAmt =  pending_amt - discount - bank_amount - deduct_tax ;
			var totalAmt =   +discount + +bank_amount + +deduct_tax ;
			var ids = ["customerpayment-reciept_no", "customerpayment-cheque_no", "customerinvoice-payment_amount","customerpayment-payment_date","customerpayment-po_wo_number","customerinvoice-amount","customeinvoice-account_no","customerinvoice-bankname","customerinvoice-description","customerinvoice-deposit_date" ];


			if($.inArray($this.attr('id'), ids) > -1){
				totalAmt = +totalAmt + +payment_amt;
			}else{
				$('#customerinvoice-payment_amount').val(changedAmt);
			}
			if(changedAmt<0 || totalAmt>pending_amt || payment_amt<0){ //&&
				$(':input[type="submit"]').prop('disabled', true);
				 $("#customerinvoice-payment_amount").parent().after("<div class='validation' style='color:red;margin-bottom: 20px;'>Invalid payment amount</div>");
			}else{
				$(':input[type="submit"]').prop('disabled', false);
				$("#customerinvoice-payment_amount").parent().next(".validation").remove();
			}
		});

	 $('.cheque').hide();

	  $('#customerpayment-payment_method').change(function(){

		  if($(this).val()=='cheque'){
			  $('.cheque').show();
		  }
		  else{
			  $('.cheque').hide();
		  }
	  });
	  
	  if($('#bank-deposit').is(':checked'))
	  {
	  	$('.bank-form').show();
	  }


	  
	  $('#bank-deposit').click(function(){
            if($(this).prop('checked') == true){
                $('.bank-form').show();
            }
            else if($(this).prop('checked') == false){
                $('.bank-form').hide();
            }
        });

	  $("#customerinvoice-is_solnet_bank").click(function () {

            if ($(this).is(":checked")) {
            	
                /*$("#hiddenaccdiv").show();
                $("#hiddenacctext").hide();
                $("#hiddenbanktext").hide();
                $("#editAccDropdown1").show();*/
                $("#editAccText1").hide();
                $("#editAccDropdown2").show();
                /*$("#editAccText2").hide();
                $("#hiddenbankdiv").hide();*/
 
            } else {
               
                /*$("#hiddenacctext").show();
                $("#hiddenbanktext").show();
                $("#hiddenaccdiv").hide();
                $("#hiddenbankdiv").hide();
                $("#editAccDropdown1").hide();*/
                $("#editAccText1").show();
                $("#editAccDropdown2").hide();
                /*$("#editAccText2").show();*/

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
                        //$("#hiddenbankdiv").show();
                        $("#bankname").val(response);
                        $('#banknamehidden').val(response);
                      }
                    });
                    return false;
                }
        });

});
</script>
