<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

//echo '<pre>';print_r($arrResultEqupment);echo '</pre>';die;
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'comment_form','action' =>['customerpackage/updatemacstatus', 'id' =>$id]])?>
			 <div class="prospect-form">
	            <div class="row">
		            <div class="col-md-12">
		               	<table class="table table-hover table-responsive EquipmentsTable">
		                    <thead>
		                        <th>#</th>
		                        <th>Mac Address</th>
		                    </thead>
		                    <tbody>
		                    	<?php $intCnt = 1;
		                    	foreach ($arrResultEqupment as $key => $arrValue) { 
    								//echo '<pre>';print_r($arrValue->quantity);echo '</pre>';die; ?>
                                <tr>
                                    <td><?php echo $intCnt++; ?></td>
                                    <td>
                                    	<select class="form-control" name="status" id="status">
                                    		<option value="active" <?php if($arrValue->status == 'active'){ echo 'selected';} ?> >Active</option>
                                    		<option value="returned" <?php if($arrValue->status == 'returned'){ echo 'selected';} ?> >Returned</option>
                                    		<option value="broken" <?php if($arrValue->status == 'broken'){ echo 'selected';} ?> >Broken</option>
                                    	</select>
                                    </td>
                                </tr>
                            <?php } ?>
		                    </tbody>
		                </table>
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
    $('#modal').modal('hide');
});

	
</script>
