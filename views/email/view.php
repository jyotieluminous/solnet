<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\EmailLogs;
/* @var $this yii\web\View */
/* @var $model app\models\EmailLogs */

$this->title = 'View Email Log';
$this->params['breadcrumbs'][] = ['label' => 'Email Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <?php /*echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'email_log_id:email',
            'email_to:email',
            'subject',
            'is_customer',
            'is_user',
            'sent_to_id',
            'sent_by',
            'sent_by_user_id',
            'sent_date',
        ],
    ]) */?>
<div class="box box-default">
        <div class="box-body">
            <div class="tbllanguage-form View-Customer-sec">
                <h2 align="center">Email Log Details</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6 form-group">
                            <label>Email Sent to :</label>
                            <?php echo ucfirst($model->email_to); ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Subject :</label>
                            <?php echo ucfirst($model->subject); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6 form-group">
                            <label>Sent to User/Customer:</label>
                            <?php 
                                $name = "";
                                if($model->is_customer=='Yes')
                                {
                                    $name = $model->getName($model->sent_to_id,'customer');
                                    if($name)
                                    {
                                        $name = $name." (Customer)";
                                    }
                                }
                                if($model->is_user=='Yes')
                                {
                                     $name = $model->getName($model->sent_to_id,'user');
                                     if($name)
                                     {
                                        $name = $name." (User)";
                                     }
                                }
                                echo $name; 
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email Sent By:</label>
                            <?php echo ucfirst($model->sent_by); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6 form-group">
                            <label>Email Sent By(User):</label>
                            <?php
                            $name = "-";
                                if($model->sent_by=='User')
                                {
                                     $name = $model->getName($model->sent_by_user_id,'user');
                                     if($name)
                                     {
                                        echo $name;
                                     }
                                }
                                else
                                {
                                    echo $name;
                                }

                              ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Sent date :</label>
                            <?php echo date('d-m-Y',strtotime($model->sent_date)); ?>
                        </div>
                    </div>
                </div>

             </div>
        </div>
</div>                