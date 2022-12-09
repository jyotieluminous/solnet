<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Bank */

$this->title = $model->customer->name;
$this->params['breadcrumbs'][] = ['label' => 'Pending Installation', 'url' => ['customer/pendinginstallation']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-view">

    <p>
         <?php if(yii::$app->controller->action->id=='installationview'){
         /*echo Html::a('Update', ['update', 'id' => $model->fk_customer_id], ['class' => 'btn btn-primary']);
        echo Html::a('Delete', ['delete', 'id' => $model->fk_customer_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this bank?',
                'method' => 'post',
            ],
        ]);*/

        echo Html::a('Back',['pendinginstallation'], ['class' => 'btn btn-default']);
         }

         if(yii::$app->controller->action->id=='installationprint'){
            echo '<h3>Pending Installation Details</h3>';
         }


        ?>
    </p>
  <div class="box box-default">
    <div class="box-body">
        <?php echo DetailView::widget([
            'model' => $model,
            'attributes' => [

                [
                'label' => 'Name',
                'value' => $model->customer->name,
                ],

                [
                'label' => 'Billing Address',
                'value' => $model->customer->billing_address,
                ],
                [
                'label' => 'Installation Address',
                'value' => $model->installation_address,
                ],
                [
                'label' => 'Mobile Number',
                'value' => $model->customer->mobile_no,
                ],
				[
                'label' => 'Email Address',
                'value' => $model->customer->email_address,
                ],
				[
                'label' => 'Email IT',
                'value' => $model->customer->email_it,
                ],
				[
                'label' => 'IT PIC',
                'value' => $model->customer->it_pic,
                ],
                [
                'label' => 'Package Title',
                'value' => $model->package->package_title,
                ],

                [
                'label' => 'Package Speed',
                'value' => $model->package_speed.' '.$model->speed->speed_type ,
                ],
                [
                'label' => 'Order Received Date',
                'value' =>  date('d-m-Y',strtotime($model->order_received_date)),
                ],
				[
                'label' => 'Additional Info',
                'value' => $model->customer->additional_info,
                ],
				
                [
                'label' => 'Sales Person',
                'value' => function($date){
                             $arrName=User::find()->select('name')
                                ->where(['user_id' => $date->userid])
                                ->one();
                                if(!empty($arrName)){
                                   return $arrName->name;
                                }
                                else{
                                    return '-';
                                }

                            },

                ],
            ],
        ]) ?>
    </div>
  </div>
</div>
