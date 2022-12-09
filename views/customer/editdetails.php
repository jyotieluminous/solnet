<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'pending_form',
				'action' =>
				['customer/submitdetails', 'id' =>$id]

			])?>
			 <div class="prospect-form">
                 <div class="row" >
                    <div class="col-md-12">

                        <div class="col-md-6 form-group">
						<?php echo $form->field($model, 'name')->textInput(['readOnly'=>'true']) ?>
                        </div>
						
						<div class="col-md-6 form-group ">
                            <?php  echo $form->field($model, 'billing_address')->textInput(['readOnly'=>'true']); ?>
                        </div>
                        
                    </div>
                  

                    </div>
					<div class="row" >
						<div class="col-md-12">
						 
							 <div class="col-md-6 form-group  ">
								<?php echo $form->field($model, 'phone_number')->textInput()?>
							</div>
							
							<div class="col-md-6 form-group  ">
							
							<?php echo $form->field($model, 'fiber_installed')->dropDownList(
								['FTTH'=>'FTTH','power'=>'Power','dig'=>'Wireless'],['prompt'=>'Select Type']); //['prompt'=>'Select Country'] ?>
						</div>
						</div>
					</div>
					
				<div class="row" >
					<div class="col-md-12">
					
						 <div class="col-md-6 form-group  ">
							<?php echo $form->field($model, 'remarks')->textArea();?>
						</div>
					</div>
				</div>
					
                  </div>
              </div>

			<div class="form-group" align="center">
			<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>
			  <?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
			</div>
		</div>
			<?php ActiveForm::end(); ?>
	 </div>


