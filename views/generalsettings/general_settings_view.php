<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'General Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
 <?php if ( Yii::$app->session->hasFlash('errorMessage')):?>
    <div class="alert alert-danger"><?php echo Yii::$app->session->getFlash('errorMessage');?></div>
<?php endif;?> 

<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="setting-form">
                   <div class="col-md-12 ">
                   <?php $form = ActiveForm::begin(['action'=>['generalsettings/updatesettings'], 'id'=>'setting_form']); ?>   
                        <?php foreach ($arrSettings as $key => $settings) {?>
                        <div class="col-md-6">
                            <div class="col-md-6 form-group">
                               <label><?php echo ucfirst($settings->label);
                               ?>:</label> 
                               <?php echo $form->field($settings, 'label')->hiddenInput([ 'name'=>'label[]','maxlength' => true])->label(false) ?> 
                            </div>
                           <div class="col-md-4 form-group">

                            <?php 
                            if($settings->name == 'INVOICE_INCR_ID' || $settings->name == 'CUST_INC_ID'){
                              echo $form->field($settings, 'value')->textInput(['name'=>'value[]','maxlength' => true, 'readonly' => true])->label(false); 
                            }                            
                            else
                            {
                              echo $form->field($settings, 'value')->textInput(['name'=>'value[]','maxlength' => true])->label(false);  
                            }
                            ?>
                          

                            <?php echo $form->field($settings, 'settings_id')->hiddenInput([ 'name'=>'id[]','maxlength' => true])->label(false) ?>

                            </div>
                        </div>
                    
                         <?php } ?>
                    </div>

            <div align="center">
                            <?php echo Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
                            
                            <?php echo Html::a('Cancel', ['site/index'], ['class' => 'btn btn-warning']) ;?>     
            </div> 
            <?php ActiveForm::end(); ?> 
        </div>
    </div>
    </div>
</div>