<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign In';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<?php if ( Yii::$app->session->hasFlash('succ_reset_password')):?>
    <div class="alert alert-info"><?php echo Yii::$app->session->getFlash('succ_reset_password');?></div>
<?php endif;?> 
<div class="login-box">
    <div class="login-logo">
        <!-- <a href="#"><b>SOLNET</b></a> -->
        <?php
            $img = Url::to('@web/images/solnet-logo.png');                 
            $image = '<img src="'.$img.'" />';
             //echo Html::img('@web/images/solnetLogo.jpg', ['class' => 'pull-left img-responsive']);
        ?>
        
        <a href="#"><img src=<?php echo $img;?>></img></a> 
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

       <?php echo $form
            ->field($model, 'email', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('Email')]) ?>

       <?php echo $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <div class="col-xs-8">
                <?php echo $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?php echo Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>

        <?php ActiveForm::end(); ?>
        
        <?php echo Html::a('I forgot my password', ['site/forgetpassword']) ?> </br>

       </div>
  
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->
