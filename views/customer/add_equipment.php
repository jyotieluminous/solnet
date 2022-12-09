<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

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
	            	<?php $form = ActiveForm::begin(['method' => 'post','id'=>'noofequipment_form','action' =>['customer/getequipment','id' =>$id]])?>
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

	            	<?php $form = ActiveForm::begin(['method' => 'post','id'=>'addequipment_form','action' =>['customer/addequipment','id' =>$id]])?>
		            	<?php
		            	if(isset($intNoOfEqu) && $intNoOfEqu != NULL){
		            		for ($i=0; $i < $intNoOfEqu; $i++) { ?>
								<div class="col-md-12 table-responsive">
									<div class="col-md-3">
										<?php echo $form->field($model, 'fk_equipments_id['.$i.'][]')->dropDownList($arrEquipList,['class'=>'fk_equipments_idClass_'.$i.' form-control','prompt'=>'Select Model Type','onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl('customer/getbrandname?intId='.$i.'&id=').'"+$(this).val(), function( data ) { $(".brandNameClass_'.$i.'").html( data );
											});']); ?>
									</div>
									<?php echo "<div class='brandNameClass_".$i."'></div>"; ?>
									<!-- <div class="col-md-6">
										<?php echo $form->field($model, 'brandName[]')->textInput(['class'=>'brandNameClass'.$i.' form-control','maxlength' => true,'readonly'=> true]) ?>
									</div> -->
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
   </div>
</div>
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
