<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Linkcustomepackage */
if(!empty($model)){
$this->title = $model->customer->name;
$this->params['breadcrumbs'][] = ['label' => 'Disconnection Report', 'url' => ['customerpackage/disconnectreport']];
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<style type="text/css">
.serialNumberClass {padding: 10px 0px;}
</style>
<div class="linkcustomepackage-view">

  <!--   <h1><?= Html::encode($this->title) ?></h1> -->
<p>
        <?php 
            if(yii::$app->controller->action->id=='disconnectview'){

            echo Html::a('Back',['disconnectreport'], ['class' => 'btn btn-default']); 

        }
        
        if(Yii::$app->user->identity->fk_role_id=='22'|| Yii::$app->user->identity->fk_role_id=='8' || Yii::$app->user->identity->fk_role_id=='23' || Yii::$app->user->identity->fk_role_id=='24' || Yii::$app->user->identity->fk_role_id=='25')
        {
          $attributes = [
                    //'cust_pck_id',
                 [                      // the currency name of the model
                    'label' => 'Customer ID',
                    'value' => $model->customer->solnet_customer_id,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Customer Name',
                    'value' => $model->customer->name,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Mobile Number',
                    'value' => $model->customer->mobile_no,
                    ],
                     [                      // the currency name of the model
                    'label' => 'Address',
                    'value' => $model->customer->billing_address,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Package title',
                    'value' => $model->package->package_title,
                    ],
                    
                    'package_speed',
                    [                      // the currency name of the model
                    'label' => 'Package title',
                    'value' => $model->speed->speed_type,
                    ],
                    [
                    'attribute'=>'disconnection_date',
                     'value'=>function($data){
                         return date("d-m-Y",strtotime($data->disconnection_date));
                    },
                    ],
                    [
                    'label' => 'Reason for disconnection',
                    'value' => $model->reason_for_disconnection
                    ],
                ];
        }
        else
        {
           $attributes = [
                    //'cust_pck_id',
                 [                      // the currency name of the model
                    'label' => 'Customer ID',
                    'value' => $model->customer->solnet_customer_id,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Customer Name',
                    'value' => $model->customer->name,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Mobile Number',
                    'value' => $model->customer->mobile_no,
                    ],
                     [                      // the currency name of the model
                    'label' => 'Address',
                    'value' => $model->customer->billing_address,
                    ],
                    [                      // the currency name of the model
                    'label' => 'Package title',
                    'value' => $model->package->package_title,
                    ],
                    
                    'package_speed',
                    [                      // the currency name of the model
                    'label' => 'Package title',
                    'value' => $model->speed->speed_type,
                    ],
                    [
                        'label' => 'Price',
                        'value' =>  $model->currency->currency." ".number_format($model->package_price,2),
                    ],
                    [
                    'attribute'=>'disconnection_date',
                     'value'=>function($data){
                         return date("d-m-Y",strtotime($data->disconnection_date));
                    },
                    ],
                    [
                    'label' => 'Reason for disconnection',
                    'value' => $model->reason_for_disconnection
                    ],
                   
                ];
        }
         ?>
</p>
	<div class="box-body">
        
    </div>
    <div class="box box-default">
        <div class="box-body">  
        <h3>Disconnection Report</h3>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => $attributes
            ]) ?>
        <div class="tbllanguage-form View-Customer-sec">
                <?php if(Yii::$app->session->hasFlash('success_msg')) : ?>
                    <div class="alert-success alert fade in" style="width: 50%">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo Yii::$app->session->getFlash('success_msg'); ?>
                    </div>
                <?php endif; ?>
                <h3>Equipments Details</h3>
                <h4>Normal Type</h4>
                <hr />
                <table class="table table-hover table-responsive EquipmentsTable">
                    <thead>
                        <th>#</th>
                        <th>Model Name</th>
                        <th>Brand Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Total Usage duration</th>
                        <th>Unused Duration</th>
                        <th>Action</th>
                        <th>Remark</th>
                    </thead>
                    <tbody>
                        <?php $intCnt = 1;
                            foreach ($arrResultEqupment as $key => $arrValue) { 
                                //echo '<pre>';print_r($model);echo '</pre>';die;
                            if($arrValue->euipment_type == 'normal'){
                                foreach ($arrValue->equmentData as $arrRow) { 
                            ?>
                                <tr>
                                    <td><?php echo $intCnt++; ?></td>
                                    <td><?php echo $arrRow->model_type; ?></td>
                                    <td><?php echo $arrRow->brand_name; ?></td>
                                    <td><?php echo $arrValue->quantity; ?></td>
                                    <td><?php echo $arrValue->price; ?></td>
                                    <td><?php echo $arrValue->return_status; ?></td>
                                    <td><?php 
                                        $strContractStartDate = strtotime($model->contract_start_date);
                                        $strDisconnectionDate = strtotime($model->disconnection_date);

                                        $year1 = date('Y', $strContractStartDate);
                                        $year2 = date('Y', $strDisconnectionDate);

                                        $month1 = date('m', $strContractStartDate);
                                        $month2 = date('m', $strDisconnectionDate);

                                        $strMonthDiffernce = (($year2 - $year1) * 12) + ($month2 - $month1);
                                        
                                        echo $strMonthDiffernce;
                                     ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $strDisconnectionDate = strtotime($model->disconnection_date);
                                        $strContractEndDate   = strtotime($model->contract_end_date);

                                        $year2 = date('Y', $strDisconnectionDate);
                                        $year1 = date('Y', $strContractEndDate);

                                        $month2 = date('m', $strDisconnectionDate);
                                        $month1 = date('m', $strContractEndDate);

                                        $strMonthDiffernce = (($year2 - $year1) * 12) + ($month2 - $month1);
                                        
                                        echo $strMonthDiffernce;
                                        ?>
                                    </td>
                                    <?php $form = ActiveForm::begin(['id'=>'disconnect_form','action' =>['customerpackage/updatestatus']])?>
                                    <td>
                                        <input type="hidden" name="intId" id="intId" value="<?php echo $arrValue->id; ?>">
                                        <input type="hidden" name="txtEquipmentType" id="txtEquipmentType" value="normal">
                                        <span><?php 
                                            $url = Url::to(['customerpackage/updatestatus','id'=>$arrValue->id]);
                                            echo Html::a('Change Status','javascript:void(0)', ['class' => 'updatestatus','title'=>'Update Comment','value'=>$url]);
                                        ?></span>
                                    </td>
                                    <td>
                                        <textarea name="remark" id="remark" class="form-control"><?php echo $arrValue->remark; ?></textarea>
                                    </td>
                                    <?php ActiveForm::end(); ?>
                                </tr>
                            <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>


                <h4>Mac Type</h4>
                <hr />
                <table class="table table-hover table-responsive EquipmentsTable">
                    <thead>
                        <th>#</th>
                        <th>Model Name</th>
                        <th>Brand Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                        <?php $intCnt = 1;
                            foreach ($arrResultEqupment as $key => $arrValue) { 
                            if($arrValue->euipment_type == 'mac'){
                                foreach ($arrValue->equmentData as $arrRow) { 
                                //echo '<pre>';print_r($arrRow);echo '</pre>';die;
                            ?>
                                <tr>
                                    <td><?php echo $intCnt++; ?></td>
                                    <td><?php echo $arrRow->model_type; ?></td>
                                    <td><?php echo $arrRow->brand_name; ?></td>
                                    <td><?php echo $arrValue->quantity; ?></td>
                                    <td><?php echo $arrValue->price; ?></td>
                                    <td>
                                        <table>
                                            <?php 
                                                foreach ($arrValue->equmentMacData as $arrMacRow) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $arrMacRow->status; ?></td>
                                                    <td class="serialNumberClass">
                                                        <span><?php echo $arrMacRow->serial_number; ?></span>
                                                    </td>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                    <td>
                                                        <span><?php 
                                                            $url = Url::to(['customerpackage/updatemacstatus','id'=>$arrMacRow->equipments_mac_id]);
                                                            echo Html::a('Change Status','javascript:void(0)', ['class' => 'updatemacstatus','title'=>'Update Comment','value'=>$url]);
                                                        ?></span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </td>
                                </tr>
                            <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php }else{
    if ( Yii::$app->session->hasFlash('notFoundMessage')):?>
            <div class="alert alert-danger"><?php echo Yii::$app->session->getFlash('notFoundMessage');?></div>
 <?php endif;
  echo Html::a('Back ', ['index'], ['class' => 'btn btn-default']) ;
}
?>
<?php

Modal::begin([
    'id'     => "modal",
    'size'   => 'modal-lg',
    'header' => '<h3 class="text-center">Update Status</h3>',
]);

echo "<div id='modalContent'></div>";
Modal::end();


$this->registerJs(
    "$(document).on('ready pjax:success', function() {
            $('.updatestatus').click(function(e){
               e.preventDefault(); //for prevent default behavior of <a> tag.
               $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));
           });
        });
    ");

Modal::begin([
    'id'     => "modal",
    'size'   => 'modal-lg',
    'header' => '<h3 class="text-center">Update Status</h3>',
]);

echo "<div id='modalContent'></div>";
Modal::end();

$this->registerJs(
    "$(document).on('ready pjax:success', function() {
            $('.updatemacstatus').click(function(e){
               e.preventDefault(); //for prevent default behavior of <a> tag.
               $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));
           });
        });
    ");

?>

<script type="text/javascript">
    $(function() {
        $('.statusClass').change(function() {
            var answer = confirm("Are you sure want to change the status ?");
            if(answer)
            {
                this.form.submit();
            }else{
                return false;
            }
        });
    });
    $(document).ready(function() {
        $('.EquipmentsTable').DataTable();
    });
</script>