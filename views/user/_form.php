<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Tblusers */
/* @var $form yii\widgets\ActiveForm */

?>
 

<div class="box box-default">
	<div class="box-body">
		<div class="tblusers-form">
			<?php $form = ActiveForm::begin(); ?>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group required col-md-6">
						<?php echo $form->field($model, 'fk_role_id')->dropDownList(
							$roleList,['prompt'=>'Select Role']); // ?>
					</div>
					<div class="col-md-6 form-group required">
						<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group required col-md-6">
						<?php echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
					</div>
					<div class="col-md-6 form-group required">
						<?php echo $form->field($model, 'status')->radioList(array('active'=>'Active','inactive'=>'Inactive')); ?>
						
					</div>
				</div>
			</div>
			<?php
			if($model->isNewRecord){
			?>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group required col-md-6">
						<?php echo $form->field($model, 'password')->textInput(['type' =>'password']) ?>
					</div>
					<div class="form-group required col-md-6">
						<?php echo $form->field($model, 'confirm_password')->textInput(['type' =>'password']) ?>
					</div>
				</div>
			</div>

			<?php } ?>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group required col-md-6">

				<?php
					if(!$model->isNewRecord)
					{
						$model->user_states = $arrStateData;
					}
					
					echo $form->field($model, 'user_states')->widget(Select2::classname(),[
                                'model'=>$model,
                                'data' => $statesList,
                                'language' => 'en',
                                'options' => ['placeholder' => 'Select States'],
                                'pluginOptions' => [
                                'allowClear' => true,
                                'multiple'=>true,
                             ],
                         ]); 


					/*echo $form->field($model, 'states[]')->dropDownList($statesList,
					     [
					      'multiple'=>'multiple',
					      'class'=>'chosen-select input-md required',              
					     ]             
					    )->label("Add Categories");*/ 
					/*echo $form->field($model, 'states')->widget(Select2::classname(), [
						//'model'=>$model,
                        //'name' => 'states',
                        'data' => $statesList,
                        'language' => 'en',
                        'options' => ['placeholder' => 'Select States','multiple'=>true],
                        'pluginOptions' => [
                        //'allowClear' => true,
                        //'multiple'=>true,
                        ],
                    ]);*/
					 /*echo Select2::widget([
						'model' => $model,
						'name'=>'states',
						//'attribute' => 'states',
						'data' => $statesList,
						
						'options' => [
							'placeholder' => 'Select State',
							'multiple' => true,
							
						]
					]);*/
				?>
					</div>
				</div>
			</div>


			<div class="form-group">
			<?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
			<?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
			</div>

			<?php ActiveForm::end(); ?>

		</div>
	</div>
</div>

