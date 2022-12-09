<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Forget Password';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<?php if ( Yii::$app->session->hasFlash('succ_sent_link')):?>
    <div class="alert alert-info"><?php echo Yii::$app->session->getFlash('succ_sent_link');?></div>
<?php endif;?> 
<?php if ( Yii::$app->session->hasFlash('error_sent_link')):?>
    <div class="alert alert-danger"><?php echo Yii::$app->session->getFlash('error_sent_link');?></div>
<?php endif;?> 
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>SOLNET</b></a>
    </div> <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Forget Password</p>

        <?php $form = ActiveForm::begin(['id' => 'forget-password-form', 'enableClientValidation' => true]); ?>

       <?php echo $form
            ->field($model, 'email', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => 'Enter your email']) ?>

        <div class="row">
            <!-- /.col -->
            <div class="col-xs-8">
                 <div class="col-xs-6">
                <?php echo Html::submitButton('Verify', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'verify-button']) ?>
                </div>
                <div class="col-xs-6">
                <?php echo Html::a('cancel',['login'], ['class' => 'btn btn-default btn-block btn-flat', 'name' => 'cancel-button']) ?>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <?php ActiveForm::end(); ?>
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
