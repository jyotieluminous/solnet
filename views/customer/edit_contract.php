<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'edit_contract_form',
				'action' =>['customer/submitcontract', 'id' =>$id]
			])?>
			 <div class="prospect-form">
                 <div class="col-md-12" >
                    
                        <div class="col-md-6">
                        	<label>Customer ID :</label> </div>
                           
                            
                            <label>Customer Name :</label>
                             
                         
                    
                            <label>Package Title :</label>
                            

                            <label>Package Speed :</label>
                            

                            <label> Contract Number :</label>
                           

                            <label> Contract Status:</label>
                            
                       </div>
                       <div class="col-md-6">

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
    $('#modal').modal('hide');
});
</script>