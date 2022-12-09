<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Package */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box box-default">
  <div class="box-body">
    <div class="row">
        <div class="package-form">

        <?php $form = ActiveForm::begin(); ?>
          <div class="form-group required col-md-6">

          <?php echo $form->field($model, 'package_title')->textInput(['maxlength' => true]) ?>

          <?php if($flagShowStatus=='1'){

                  echo $form->field($model, 'status')->radioList(array('active'=>'Active','inactive'=>'Inactive'));
               }else{
                                 
                  echo $form->field($model, 'status')->radioList(
                   array('active'=>'Active'));
          
            } ?>
           </div>
           </div>
         </div>
        <div class="form-group">
            <?php echo Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>

            <?php echo Html::a('Cancel',['package/index'],['class' => 'btn btn-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        </div>
   
  </div>
</div>
