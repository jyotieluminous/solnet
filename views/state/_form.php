<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Country;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\State */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="box box-default">
  <div class="box-body">
    <div class="row">
        <div class="state-form">
         <?php $form = ActiveForm::begin(['enableAjaxValidation' => true]); ?>
                <div class=" col-md-6">
                    <div class=" required ">
                        <?php 

                        $arrCountry=Country::find()->all();

                        $listCountry=ArrayHelper::map($arrCountry,'country_id','country');

                        echo $form->field($model, 'fk_country_id')->dropDownList($listCountry, ['prompt'=>'Select Country'])->label('Country');
                        ?>  

                    </div>
                     <?php echo $form->field($model, 'vat')->textInput()->label('VAT(%)'); ?>
                </div>
                <div class="form-group required col-md-6">
                    <?php echo $form->field($model, 'state')->textInput(['maxlength' => true]) ?>

                       <?php
                    if($flagShowStatus=='1'){

                        echo $form->field($model, 'status')->radioList(array('active'=>'Active','inactive'=>'Inactive'));
                     }else{
                                       
                        echo $form->field($model, 'status')->radioList(
                         array('active'=>'Active'));
                
                        } ?>
                </div>
                <div class=" col-md-6">
                     <?php echo $form->field($model, 'state_prefix')->textInput(['style' => 'text-transform: uppercase'])->label('State Prefix'); ?>
                      <?php echo $form->field($model, 'signature_email_id')->textInput()->label('Signature Email ID'); ?>
                </div>
                 <div class=" col-md-6">
                   
                   <?= $form->field($model, 'header_address')->textarea(array('rows'=>2,'cols'=>5)); ?>
                   <?= $form->field($model, 'header_telephones')->textarea(array('rows'=>2,'cols'=>5)); ?>
                 </div>
        </div>
    </div>
        <div class="form-group">
            <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

            <?php echo Html::a('Cancel', ['index'], ['class' => 'btn btn-warning']) ; ?>
        </div>
        <?php ActiveForm::end(); ?>
  </div>
</div>
