<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use dosamigos\datetimepicker\DateTimePicker;
use yii\widgets\Pjax;
if($complain_type == 'Broadband') {
	$link = 'customercomplain/submitbjrespond';
} else if($complain_type == 'Dedicated'){
	$link = 'customercomplain/submitdjrespond';
} else if($complain_type == 'Local Loop'){
	$link = 'customercomplain/submitljrespond';
}
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'respond_form',
				'action' =>
				[$link, 'id' =>$id],
				'options' => [
				'enctype' => 'multipart/form-data'
				],

			])?>
			 <div class="prospect-form">
                 <div class="row" >
                 	<div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo  $form->field($model, 'job_completed_date')->widget(
								DateTimePicker::className(), [
								'clientOptions' => [
									'autoclose' => true,
									'size' => 'ms',
									'template' => '{input}',
									'format' => 'yyyy-mm-dd h:i',
									'inline' => true,
									'pickerPosition'=> "bottom-left",
								]
							]);
							?>
						</div>						
					</div>
                 	<div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo $form->field($model, 'actual_problem')->textArea(['maxlength' => true]) ?>
						</div>						
					</div>
					<div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo $form->field($model, 'permanent_solution')->textArea(['maxlength' => true]) ?>
						</div>						
					</div>
					
                    <div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo $form->field($model, 'link_status')->dropDownList([''=>'Select Link Status','up' => 'UP', 'down' => 'Down', 'unstable' => 'UNSTABLE']); ?>
						</div>						
					</div>

					<div class="col-md-12">
						<div class="form-group required col-md-12">						
						<?php echo 
							$form->field($model, 'is_replace')->radioList([
							    'yes' => 'Yes',
							    'no' => 'No',
							]);
						?>
						</div>						
					</div>					

					<?php 
					if(Yii::$app->user->identity->fk_role_id !='23')
			        { ?>
						<div class="col-md-12">
							<div class="form-group required col-md-12">						
							<?php echo $form->field($model, 'ticket_status')->dropDownList([''=>'Select Status Status','open' => 'Open', 'closed' => 'Closed']); ?>
							</div>						
						</div>
			        <?php 
			    	}
			        ?>
					
					<div class="col-md-12">
						<div class="form-group col-md-12">						
						<?php //echo $form->field($model, 'filepath')->fileInput(['maxlength' =>true ,'id'=>'uploadFile']) ?>
						<?= $form->field($imageModel, 'filepath[]')->fileInput(['multiple' => true]) ?>
					
						<?php Pjax::begin(['id'=>'image1-grid']); ?>
						<ul class="upImages" id="preview-template">
						<div class="alert-success alert fade in" id="delete_success" style="display:none"> </div>
						<?php if(!empty($getUplodedDocs))
				 				{ 
					 				foreach($getUplodedDocs as $key=>$val)
									{
								?>
								<li>
	                                <?php echo $val['filepath']; 
	                                 /*$complainDoc = $val['filepath'];
									 $complainDoc = Url::to('@web/uploads/complain_docs/'.$val['complain_id'].'/'.$complainDoc);
									 echo Html::a('View uploaded document',$complainDoc,['target'=>'_blank']);*/
	                                ?>&nbsp;
	                                <?php echo Html::a('Remove','javascript:void(0);',[
											'title' => Yii::t('yii', 'Remove'),
											'onclick'=>"
											$.ajax({
												type     : 'GET',
												data     : { id : '".$val['doc_id']."', filepath : '".$val['filepath']."',complain_id : '".$val['fk_complain_id']."'},
												cache    : false,
												url  	 : '".yii::$app->request->baseUrl."/customercomplain/remove/',
												beforeSend :function(xhr){
												
											},
											success  : function(response) {
											
												if(response)
												{
												
												$.pjax.reload({container:'#image1-grid',async:false});		
												$('#delete_success').css('display','block');
	                                            $('#delete_success').html('Document deleted successfully');
	                                            //return false;
	       										
												}
												
											}
											});return false;",'id'=>'remove_image'
	                					]); ?>
                                </li>
								<?php		
									}
								}
						?>
						</ul>
						<?php Pjax::end();?>
						</div>						
					</div>	


								       					
                  </div>
              </div>

			<div class="form-group" align="center">
			<?php echo Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ;?>
			<?php echo Html::button('Quit', ['class'=> 'btn btn-default closemodal']) ;?>
			</div>
		</div>
			<?php ActiveForm::end(); ?>
	 </div>
   </div>
</div>

<script type="text/javascript">
	$('.closemodal').click(function() {
    	$(this).parent().parent().find('.modal-dialog').modal('hide');    
	});

	$(document).ready(function () {
        var today = new Date();
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose:true,
            endDate: "today",
            maxDate: today,
            defaultDate : today,
        }).on('changeDate', function (ev) {
                $(this).datepicker('hide');
            });


    });
function isNumber(evt) {
    var iKeyCode = (evt.which) ? evt.which : evt.keyCode
    if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57)){
        return false;
    }else{
      return true;
    }
  } 
  $( ".equmPrice" ).keyup(function() {

    var price    = $(this).parent().parent().find('.equmPrice').val();
    var quntity  = $(this).parent().parent().find('.equmQuntity').val();
    var intCnt   = $(this).parent().parent().find('.intCnt').val();
        
    var intTotalPrice = quntity * price;
    
    $(this).parents().find('.equmTotal_'+intCnt).val(intTotalPrice);
  });
</script>
