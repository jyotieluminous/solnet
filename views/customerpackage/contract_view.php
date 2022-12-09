<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : contract_vie.php
# Created on : 28th June 2017 by Swati Jadhav.
# Update on  : 28th June 2017 by Swati Jadhav.
# Purpose : View disconnected customer details.
############################################################################################
*/
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Linkcustomepackage */

$this->title = $model->customer->name;
$this->params['breadcrumbs'][] = ['label' => 'Contract report', 'url' => ['customerpackage/customercontract']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
#tblcustomercontractfiles-description {width: 35%;}
input[type="file"] {display: block;}
.imageThumb {max-height: 110px;border: 2px solid;padding: 1px;cursor: pointer;margin: 10px auto;}
.upload-btn-wrapper {position: relative;overflow: hidden;display: inline-block;}
.FileUploadbtn {border: 2px solid gray;color: gray;background-color: white;padding: 8px 20px;border-radius: 8px;font-size: 20px;font-weight: bold;}
.upload-btn-wrapper input[type=file] {font-size: 100px;position: absolute;left: 0;top: 0;opacity: 0;width: 10%;}
#description{width: 40%;height: 90px;}
.form-group.has-success label {color: #000 !important;}
.form-group.has-success .form-control, .form-group.has-success .input-group-addon {border-color: #000 !important;}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<div class="linkcustomepackage-view">
    <!--   <h1><?= Html::encode($this->title) ?></h1> -->
<p>
    <?php 
        if(yii::$app->controller->action->id=='contractview'){
            echo Html::a('Back',['customercontract'], ['class' => 'btn btn-default']); 
        }
     ?>
</p>
    <div class="box box-default">
        <div class="box-body">  
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                   
                 [                      
                    'label' => 'Customer ID',
                    'value' => $model->customer->solnet_customer_id,
                    ],
                    [                     
                    'label' => 'Customer Name',
                    'value' => $model->customer->name,
                    ],
                    [                     
                    'label' => 'Mobile Number',
                    'value' => $model->customer->mobile_no,
                    ],
                     [                     
                    'label' => 'Address',
                    'value' => $model->customer->billing_address,
                    ],

                    
                    [
                    'label'=>'Contract Number',
                    'value'=>function($data){
                            if(empty($data->contract_number)){
                                return '--';
                            }
                            else{
                                return $data->contract_number;
                            }
                        }
                    ],

                    [                      //
                    'label' => 'Package title',
                    'value' => $model->package->package_title,
                    ],
                    
                    'package_speed',
                    [                      
                    'label' => 'Package title',
                    'value' => $model->speed->speed_type,
                    ],
                    'package_price',
                    [                      
                    'label' => 'Package title',
                    'value' => $model->currency->currency,
                    ],

                    [                      
                    'label' => 'First Sign Up Date',
                    'value'=>function($data){
                      return date('m-d-Y',strtotime($data->customer->first_invoice_date));
                      },
                    ],
                
                    [                      
                    'attribute'=>'contract_start_date',
                    'value'=>function($data){
                      return date('m-d-Y',strtotime($data->contract_start_date));
                      },
                    ],
                    
                    [                      
                    'attribute'=>'contract_end_date',
                    'value'=>function($data){
                      return date('m-d-Y',strtotime($data->contract_end_date));
                      },
                    ],
                    //'contract_status',
                    [
                    'label'=>'Contract Status',
                    'value'=>function($data){
                            if(empty($data->contract_status)){
                                return '--';
                            }
                            else{
                                return $data->contract_status;
                            }
                        }
                    ],

                ],
            ]) ?>
            <?php //echo '<pre>';print_r($arrCustomerData);die; ?>
            <hr />
            <div class="col-md-6">
                <?php if(Yii::$app->session->hasFlash('success_msg')) : ?>
                <div class="alert-success alert fade in">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo Yii::$app->session->getFlash('success_msg'); ?>
                </div>
                <?php endif; ?>
                <?php if(Yii::$app->session->hasFlash('error_msg')) : ?>
                <div class="alert-danger alert fade in">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo Yii::$app->session->getFlash('error_msg'); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-12">
                <h2 class="text-center">My Documents</h2>
            </div>
            <div class="col-md-8">
                <table class="table table-striped table-bordered" id="contract_files_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Files</th>
                            <th>Descriptions</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $intCount = 1;
                           foreach ($arrCustomerData as $value){
                            $strUrl = Url::to('@web/uploads/contract_files/'.$value->file_name);
                            $url    = Yii::$app->request->baseUrl;
                        ?>
                        <tr>
                            <td><?php echo $intCount++; ?></td>
                            <td><a href="<?php echo $strUrl; ?>" target="_blank">View</a></td>
                            <td><?php echo $value->description; ?></td>
                            <td><a href="<?php echo $url."/customerpackage/deletedoc/".$value->customer_contract_id.""; ?>" title="Delete Doc" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i></a></td>
                        </tr>
                        <?php 
                            }
                        ?>
                    </tbody>
                </table>
            </div>

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'],'id'=>'pay_form','method'=>'post']); ?>
                <input type="hidden" name="intCustomerId" id="intCustomerId" value="<?php echo $model->fk_customer_id; ?>">
                <div class="form-group col-md-12 upload-btn-wrapper">
                    <h2 class="text-center">Upload Documents</h2>
                    <button class="FileUploadbtn">Upload a file</button>
                    <?php 
                        echo $form->field($customerContractFilesModel, 'file_name[]')->fileInput(['multiple' => true,'id'=>'files'])->label(false);
                    ?>
                </div><br/>
                <div class="form-group col-md-6">
                    <?php echo Html::submitButton('Save', ['id'=>'pay-save','class' => 'btn btn-success']); ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function(e) {
      var files = e.target.files,
      filesLength = files.length;
      var fileType = $("#files").val().split('.').pop();
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          if(fileType=='pdf' || fileType=='xls' || fileType=='csv' || fileType=='doc'){
                fileResult = "<?php echo Yii::$app->request->baseUrl ?>/images/doc_img.png";
            }else{
                fileResult = e.target.result;
            }
          $("<div class=\"col-md-12 \">"+
            "<span class=\"imgDiv\">"+
            
            "<img class=\"imageThumb\" src=\"" +fileResult+ "\" title=\"" + file.name + "\"/>" +
            "<br/>" +
            "<label for=\"description\">Description : </label>" +
            "<textarea name=\"description[]\" id=\"description\" class=\"form-control\" required></textarea>"+
            "</span></div>").insertAfter("#files");
            $(".remove").click(function(){
            $(this).parent(".imgDiv").remove();
          });
        });
        fileReader.readAsDataURL(f);
      }
    });
  } else {
    alert("Your browser doesn't support to File API")
  }
});

$(document).ready(function() {
    $('#contract_files_table').DataTable();
} );
</script>
