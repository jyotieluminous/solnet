<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

use yii\widgets\Pjax;
use yii\bootstrap\Modal;

//echo '<pre>';print_r($model);echo '<pre>';die;

$this->title = 'Add Equipment';
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
	.error{
		color: red;
	}
</style>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<div class="prospect-form">
	            <div class='row'>
	            	<?php $form = ActiveForm::begin(['method' => 'post','id'=>'noofequipment_form','action' =>['customercomplain/getequipment','id' =>$id]])?>
	            	<div class="col-md-12 table-responsive">
	            		<?php if(Yii::$app->session->hasFlash('error_msg')) : ?>
				            <div class="alert-danger alert fade in">
				                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				                    <?php echo Yii::$app->session->getFlash('error_msg'); ?>
				            </div>
			 			<?php endif; ?>
			 			<?php if(Yii::$app->session->hasFlash('success_msg')) : ?>
				            <div class="alert-success alert fade in">
				                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
				                    <?php echo Yii::$app->session->getFlash('success_msg'); ?>
				            </div>
			 			<?php endif; ?>
	            		<div class="form-group">
	            			<label>No of Equipment :</label>
	            			<input type="text" name="nofoequipment" id="nofoequipment" class="form-control" value="<?php echo isset($intNoOfEqu) ? $intNoOfEqu : ""; ?>"  required=""  style="width: 50%"  onkeypress='javascript:return isNumber(event)'>
	            		</div>
		            	<div class="form-group">
		            		<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>
		            	</div>
	            	</div>
	            	<?php ActiveForm::end(); ?>

	            	<?php $form = ActiveForm::begin(['method' => 'post','id'=>'addequipment_form','action' =>['customercomplain/addequipment','id' =>$id]])?>
		            	<?php
		            	if(isset($intNoOfEqu) && $intNoOfEqu != NULL){
		            		for ($i=0; $i < $intNoOfEqu; $i++) { ?>
								<div class="col-md-12 table-responsive">
									<div class="col-md-3">
										<?php echo $form->field($model, 'fk_equipments_id['.$i.'][]')->dropDownList($arrEquipList,['class'=>'fk_equipments_idClass_'.$i.' form-control','prompt'=>'Select Model Type','onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl('customercomplain/getbrandname?intId='.$i.'&id=').'"+$(this).val(), function( data ) { $(".brandNameClass_'.$i.'").html( data );
											});']); ?>
									</div>
									<?php echo "<div class='brandNameClass_".$i."'></div>"; ?>
								</div>
			            	<?php } ?>
								<div class="form-group col-md-12">
									<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>
									<?php echo Html::button('Quit', ['class'=> 'btn btn-default closemodal']) ;?>
								</div>
			            	<?php
			            	} ?>
	            	</div>
	            	<?php ActiveForm::end(); ?>
			</div>
      	</div>
   </div>
</div>

<div class="box box-default">
   	<div class="box-body">
		<div class="tbllanguage-form View-Customer-sec">
			<?php if(Yii::$app->session->hasFlash('error_msg_eqip')) : ?>
	            <div class="alert-danger alert fade in" style="width: 50%">
	                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	                <?php echo Yii::$app->session->getFlash('error_msg_eqip'); ?>
	            </div>
 			<?php endif; ?>

			<?php if(Yii::$app->session->hasFlash('success_msg_eqip')) : ?>
	            <div class="alert-success alert fade in" style="width: 50%">
	                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	                <?php echo Yii::$app->session->getFlash('success_msg_eqip'); ?>
	            </div>
 			<?php endif; ?>
			
			<h3> Equipments Details</h3>
            <h4><b>Normal Type</b></h4>
            <hr />
            <table class="table table-hover table-responsive EquipmentsTable">
                <thead>
                    <th>#</th>
                    <th>Model Name</th>
                    <th>Brand Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Action</th>
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
                                <td><?php echo $arrValue->status; ?></td>
                                <td>
	                            <?php 
	                            if($arrValue->status == 'active')
	                            {
				                    $url = Url::to(['/customercomplain/returnequipmentbroadband', 'id' => $arrValue->id]);
				                    echo Html::a('<i class="fa fa-mail-reply-all deleteBtn" title="Return"></i>', 'javascript:void(0)', ['class' => 'return', 'value' => $url]);
	                        	}
	                        	else
	                        	{
	                        		echo '-';
	                        	}
	                        	?>
	                        	</td>
                            </tr>
                        <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>


            <h4><b>Mac Type</b></h4>
            <hr />
            <table class="table  table-responsive EquipmentsTable">
                <thead>
                    <th>#</th>
                    <th>Model Name</th>
                    <th>Brand Name</th>
                    <th>Quantity</th>
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
                                <td>
                                    <table class="table">
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
                                                <?php 
                                                if($arrMacRow->status == 'active')
                                                {
                                                ?>
	                                                
	                                                    <?php 
										                    $url = Url::to(['/customercomplain/returnmacequipmentbroadband', 'id' => $arrMacRow->equipments_mac_id]);
										                    echo Html::a('<i class="fa fa-mail-reply-all deleteBtn" title="Return"></i>', 'javascript:void(0)', ['class' => 'return', 'value' => $url]);
										                ?>
	                                                
	                                            <?php 
	                                        	}
	                                        	else
	                                        	{
	                                        		echo '-';
	                                        	} ?>
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
<?php
	Modal::begin([
	    'id'     => "modal",
	    'header' => '<h3 class="text-center">Respond Job Allocation</h3>',
	]);
	echo "<div id='modalContent'></div>";
	Modal::end();
	$this->registerJs(
	    "$(document).on('ready pjax:success', function() {
	            $('.return').click(function(e){
	               e.preventDefault(); //for prevent default behavior of <a> tag.
	               $('#modal').modal('show').find('#modalContent').load($(this).attr('value'));			   
	           });
	        });
	    ");						
?>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script type="text/javascript">
	function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;

        return true;
    } 
    $(document).ready(function() {
	   $("#addequipment_form").validate({
      	rules: {
         "CustomerEquipments[fk_equipments_id][]"     : 'required',
         "quntity[]"     : 'required',
         "mac_address[]" : 'required',
      	},
	     messages: 
        {
            "CustomerEquipments[fk_equipments_id][]" :{
                required:'Please Select Model type',
            },
            "quntity[]" :{
                required:'Please Enter a quntity',
            },
            "mac_address[]" :{
                required:'Please select atleast one mac address.',
            },
        },
	   });
	});
</script>
