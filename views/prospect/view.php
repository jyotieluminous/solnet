<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Currency;
use yii\web\NotFoundHttpException;


/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'View Prospect';
$this->params['breadcrumbs'][] = ['label' => 'Prospect', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbllanguage-form View-Customer-sec">
    <p>
        <?php  if(!empty($model)){
            if(yii::$app->controller->action->id=='view'){
                 echo '&nbsp;&nbsp;'.Html::a('Update ', ['update','id' => $model->prospect_id], ['class' => 'btn btn-primary']) ;

                 echo '&nbsp;&nbsp;'.Html::a('Delete', ['delete', 'id' => $model->prospect_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this prospect?',
                        'method' => 'post',
                    ],
                ]);
                 echo '&nbsp;&nbsp;'.Html::a('Back ', ['index'], ['class' => 'btn btn-default']) ;


            }
        ?>
    </p>
     <p align="right">
     <?php
                    if(yii::$app->controller->action->id=='view'){
                            echo Html::a('<i class="fa fa-print"></i> Print', ['/prospect/pdf','id'=>$model->prospect_id], [
                            'class'=>'btn btn-danger',
                            'data-toggle'=>'tooltip',
                            'title'=>'Will open the generated PDF file in a new window',
                            'target'=>'_blank'
                        ]);
                    }
                ?>
    </p>

<div class="box box-default">
    <div class="box-body">
        <div class="row">
            <div class="prospect-form View-Customer-sec">
                 <div class="col-md-12" >
                  <div class="box box-default">
                    <div class="box-body">
                        <h3 align="center">Personal Details</h3>
                        <div class="col-md-12 col-md-offset-1">
                            <div class="col-md-6 form-group">
                               <label>Name :</label>
                                <?php echo ucfirst($model->customer_name); ?>
                            </div>
                            <div class="form-group col-md-6">
                               <label>Email :</label>
                            <?php echo $model->email; ?>
                            </div>
                        </div>
                        <div class="col-md-12 col-md-offset-1">
                            <div class="col-md-6 form-group">
                               <label>Mobile Number :</label>
                                <?php if($model->mobile_no =='') {
                                echo '  --';
                                }
                                else{
                                    echo $model->mobile_no;
                                } ?>

                            </div>
                            <div class="form-group col-md-6">
                               <label>Address :</label>
                                <?php echo $model->address; ?>
                            </div>
                        </div>
                         <div class="col-md-12 col-md-offset-1">
                            <div class="col-md-6 form-group">
                               <label>Person Incharge :</label>
                                <?php if($model->person_incharge =='') {
                                echo '  --';
                                }
                                else{
                                    echo $model->person_incharge;
                                } ?>

                            </div>
                        </div>

                    </div>
                </div>
              </div>
            </div>

                <div class="col-md-12" >
                  <div class="box box-default">
                    <div class="box-body">
                    <h3 align="center">Current ISP Details</h3>

                        <div class="col-md-12 col-md-offset-1">
                            <div class="col-md-6 form-group">
                                <label>Current ISP :</label>
                                <?php if($model->current_isp =='') {
                                echo '  --';
                                }
                                else{
                                    echo $model->current_isp;
                                } ?>


                            </div>
                            <div class="form-group col-md-6">
                               <label>Current Package :</label>
                                <?php if($model->current_package =='') {
                                echo '  --';
                                }
                                else{
                                    echo $model->current_package;
                                } ?>
                            </div>
                        </div>

                        <div class="col-md-12 col-md-offset-1">
                            <div class="col-md-6 form-group">
                                <label>Current ISP Bill :</label>
                                <?php if(empty($model->current_isp_bill)) {
                                echo '   --';
                                }
                                else{
                                    //echo $model->current_isp_bill.' ';

                                     if(!empty($model->fk_currency_id)){
                                        echo $model->currentcurrency->currency.' '. number_format($model->current_isp_bill,2);
                                        }
                                        else{
                                            echo '-';
                                        }
                                    /*$strCurrency=Currency::findOne($model->current_currency);

                                   if(!empty($strCurrency)){
                                    echo $strCurrency->currency;
                                    }else{
                                        echo '-';
                                    }*/
                                } ?>


                            </div>
                            <div class="form-group col-md-6">
                               <label>Current Contract End Date :</label>
                                <?php if(empty($model->current_contract_end_date)|| $model->current_contract_end_date=='0000-00-00') {
                                echo '  --';
                                }
                                else{
                                    echo date('d-m-Y',strtotime($model->current_contract_end_date));
                                } ?>
                            </div>
                        </div>
                     </div>
                 </div>
             </div>



                <div class="col-md-12" >
                  <div class="box box-default">
                    <div class="box-body">
                    <h3 align="center">Proposed ISP Details</h3>
                        <div class="col-md-12 col-md-offset-1">
                            <div class="col-md-6 form-group">
                                <label>Package Title:</label>
                                <?php if($model->package->package_title =='') {
                                echo '  --';
                                }
                                else{
                                    echo $model->package->package_title;
                                } ?>


                            </div>
                            <div class="form-group col-md-6">
                               <label>Package speed:</label>
                                <?php if($model->package_speed =='') {
                                echo '  --';
                                }
                                else{
                                    echo $model->package_speed;
                                    echo $model->speed->speed_type;
                                } ?>
                            </div>
                        </div>


                        <div class="col-md-12 col-md-offset-1">
                            <div class="col-md-6 form-group">
                                <label>Estimate Sign Up Date :</label>
                                <?php if(empty($model->estimate_sign_up_date)) {
                                echo '  --';
                                }
                                else{
                                    echo date('d-m-Y',strtotime($model->estimate_sign_up_date));

                                } ?>


                            </div>
                            <div class="form-group col-md-6">
                                <label>Quotation Date :</label>
                                    <?php if(empty($model->quotation_date)|| $model->quotation_date=='0000-00-00') {
                                    echo '  --';
                                    }
                                    else{
                                        echo date('d-m-Y',strtotime($model->quotation_date));

                                    } ?>

                            </div>
                        </div>

                        <div class="col-md-12 col-md-offset-1">
                            <div class="col-md-6 form-group">
                                <label>Price Quote :</label>
                                <?php if(empty($model->price_quote)){
                                echo '  --';
                                }
                                else{
                                  //  echo $model->price_quote.'  ';
                                    if(!empty($model->fk_currency_id)){
                                        echo $model->currency->currency.'  '. number_format($model->price_quote,2) ;
                                        }
                                        else{
                                            echo '-';
                                        }
                                } ?>
                            </div>
                             <div class="col-md-6 form-group">
                                <label>Success Rate:</label>
                                <?php if($model->success_rate =='') {
                                echo '--';
                                }
                                else{
                                    echo $model->success_rate; echo '%';

                                } ?>
                            </div>

                        </div>

                         <div class="col-md-12 col-md-offset-1">

                            <div class="col-md-6 form-group">
                                <label>Sales Person :</label>
                                <?php echo $model->user->name; ?>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Deal Closed:</label>
                                    <?php if($model->is_deal_closed =='') {
                                    echo '  --';
                                    }
                                    else{
                                        echo $model->is_deal_closed;
                                    } ?>
                            </div>
                        </div>
                     </div>
                 </div>
              </div>
          </div>
         </div>
      </div>
    </div>
 <?php }else{
   throw new NotFoundHttpException('The requested page does not exist.');
    } ?>
</div>
