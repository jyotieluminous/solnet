<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Reset Password';

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<?php if ( Yii::$app->session->hasFlash('err_reset_password')):?>
    <div class="alert alert-danger"><?php echo Yii::$app->session->getFlash('err_reset_password');?></div>
<?php endif;?> 

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>SOLNET</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Reset you Password</p>

       <?php $form = ActiveForm::begin(['method' => 'post',
        'action' => Url::to(['site/newpassword','id'=>
       $userId]),'enableAjaxValidation' => true] ); ?>

       <?php echo $form->field($model, 'new_password',
                $fieldOptions2)->passwordInput();?>

                <?php echo $form->field($model, 'confirm_password',
                $fieldOptions2)->passwordInput();?>

        <div class="row">
            <div class="col-xs-4">
               <?php echo Html::submitButton('Save' , ['class' => 'btn btn-primary btn-block btn-flat', 'name'=>'save_button']) ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?php echo Html::a('Cancel',['site/index'], ['class' => 'btn btn-default btn-block btn-flat']); ?>
            </div>
            <!-- /.col -->
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->


