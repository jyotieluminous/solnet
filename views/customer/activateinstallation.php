<?php
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="box box-default">
	<div class="box-body">
		<div class="tbllanguage-form">
			<?php $form = ActiveForm::begin(['id'=>'pending_form',
				'action' =>
				['customer/submitactivation', 'id' =>$id]

			])?>
			 <div class="prospect-form">
                 <div class="row" >
                    <div class="col-md-12">

                        <div class="col-md-6 form-group">
                            <label>Customer Name :</label>
                        </div>
                        <div class="col-md-6 form-group ">
                             <?php echo ucfirst($model->customer->name); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6 form-group ">
                            <label>Customer Address :</label>
                        </div>
                        <div class="col-md-6 form-group ">
                            <?php echo $model->customer->billing_address; ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-6 form-group ">
                            <label>Package Title :</label>
                        </div>
                        <div class="col-md-6 form-group ">
                            <?php echo $model->package->package_title;?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-6 form-group ">
                            <label>Package Speed :</label>
                        </div>
                        <div class="col-md-6 form-group  ">
                            <?php echo $model->package_speed;
                            echo $model->speed->speed_type; ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-6 form-group  ">
                            <label>Installation Date</label>
                        </div>

                        <div class="col-md-6 form-group ">

                          <?php $strActivationDate = strtotime($model->activation_date);
                          
          							  if($model->activation_date!='0000-00-00 00:00:00' && !is_null($model->activation_date))
          							  {
          							   $model->activation_date = date('Y-m-d',$strActivationDate);
          							  }else{
          							   $model->activation_date = date('Y-m-d');
          							  }
                          
                           ?>

					 		          <?php echo $form->field($model, 'activation_date')->textInput(['class'=>'datepicker'])->label(false);?>
                        </div>
                      </div>
					            <div class="col-md-12">
                        <table class="table table-hover table-responsive" id="EquipmentsTable">
                          <thead>
                            <th>#</th>
                            <th>Model Name</th>
                            <th>Brand Name</th>
                            <th>Mac Address</th>
                          </thead>
                          <tbody>
                            <?php $intCnt = 1;
                              foreach ($arrResultEqupment as $key => $arrValue) { 
                                foreach ($arrValue->equmentData as $arrRow) { 
                            ?>
                                <tr>
                                  <td><?php echo $intCnt; ?></td>
                                  <td><?php echo $arrRow->model_type; ?></td>
                                  <td><?php echo $arrRow->brand_name; ?></td>
                                  <td>
                                    <?php if($arrValue->equmentMacData != ''){ 
                                      foreach ($arrValue->equmentMacData as $arrMacRow) { 
                                        if($arrMacRow->serial_number != ''){ ?>
                                            <span><?php echo $arrMacRow->serial_number," | "; ?></span>
                                          <?php }else{ ?>
                                            <span><?php echo $arrMacRow->mac_address," | "; ?></span>
                                    <?php }
                                      }
                                    }else{ ?>
                                      <span>Blank</span>
                                    <?php
                                    } ?>
                                </tr>
                            <?php
                                }
                                $intCnt++;
                              }
                            ?>
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
    $(this).parent().parent().find('.modal-dialog').modal('hide');
    // $('#modal').modal('hide');
    // $('#modal_details').modal('hide');
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
</script>
