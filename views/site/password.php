<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Change Password';

?>
<div class="row">
  <div class="col-md-8">
    <div class="box box-default">
      <div class="box-body">
  
       <?php $form = ActiveForm::begin(['method' => 'post',
        'action' => Url::to(['site/changepassword']),'enableAjaxValidation' => true] ); ?>
            <div class="col-md-7 form-group required">

                <?php echo $form->field($model, 'old_password')->passwordInput();?>

                <?php echo $form->field($model, 'new_password')->passwordInput();?>

                <?php echo $form->field($model, 'confirm_password')->passwordInput();?>

            </div>
          
              <div class="col-md-8">
             <div class="col-md-4">
                
              <?php echo Html::submitButton('Save' , ['class' => 'btn btn-primary btn-block btn-flat', 'name'=>'save_button']) ?>
              </div>  
                 <div class="col-md-4">
           
               <?php echo Html::a('Cancel',['site/index'], ['class' => 'btn btn-default btn-block btn-flat']); ?>
               </div>
               </div>
            </div>
        <?php ActiveForm::end(); ?>
       
       </div>
      </div>
    </div>
 </div>
