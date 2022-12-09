<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\model\users;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Change Password';

?>

<div class="row">
        <div class="col-md-12">
         <?php $form = ActiveForm::begin(['id' => 'password-form']); ?>
            <div class="col-md-6 form-group required">
                <?= $form->field($model, 'old_passwprd')->passwordInput();?>
                <?= $form->field($model, 'new_passwprd')->passwordInput();?>
                <?= $form->field($model, 'confirm_passwprd')->passwordInput();?>
            </div>
        <?php ActiveForm::end(); ?>
        </div>
    </div>>
