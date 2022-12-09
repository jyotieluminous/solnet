<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Customerinvoice */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Update Outstanding invoices remarks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-default">
		<div class="box-body">
			<div class="customerinvoice-form">
			
			
    <?php $form = ActiveForm::begin(); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group  col-md-6">
				<?php
				if(isset($model->customer->name) && $model->customer->name!="")
				{
					$model->name = $model->customer->name;
				}
				 echo $form->field($model, 'name')->textInput(['readonly'=>true]) ?>
			</div>
			<div class="col-md-6 form-group">
				<?php echo $form->field($model, 'invoice_number')->textInput(['readonly'=>true]) ?>
			</div>
		</div>
	</div>
    <div class="row">
		<div class="col-md-12">
			<div class="form-group  col-md-6">
				<?php 
				if(isset($model->customer->linkcustomerpackage->package->package_title) && $model->customer->linkcustomerpackage->package->package_title!="")
				{
					$model->package_title =$model->customer->linkcustomerpackage->package->package_title;
				}
				echo $form->field($model, 'package_title')->textInput(['readonly'=>true]) ?>
			</div>
			<div class="col-md-6 form-group ">
				<?php 
					if(isset($model->customer->linkcustomerpackage->package_speed) && $model->customer->linkcustomerpackage->package_speed!="")
					{
						$model->speed = $model->customer->linkcustomerpackage->package_speed;
					}
					echo $form->field($model, 'speed')->textInput(['readonly'=>true]) 	
				?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group  col-md-6">
				<?php if(isset($model->customer->mobile_no) && $model->customer->mobile_no!="")
					{
						$model->mobile_no = $model->customer->mobile_no;
					}
					echo $form->field($model, 'mobile_no')->textInput(['readonly'=>true]) 	 ?>
			</div>
		</div>
	</div>		
   <div class="row">
		<div class="col-md-12">
			<div class="form-group  col-md-6">
				<?php echo $form->field($modelRemark, 'remark1')->textarea(['rows' => '3','style'=>'resize:none;'])->label('Remark 1') ?>
			</div>
			<div class="form-group  col-md-6">
				<?php echo $form->field($modelRemark, 'remark2')->textarea(['rows' => '3','style'=>'resize:none;'])->label('Remark 2')  ?>
			</div>
		</div>
	</div>
    <div class="form-group">
        <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?php echo Html::a('Cancel', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
			</div>
		

<h3><center><b>Remarks History</b></center></h3>
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
             'attribute'=>'created_date',
              //'options'=>['style'=>'width:30%;'],
              
            'value'=>function($model)
            {
                $date = date('Y-m-d',strtotime($model->created_date));
                return $date;
            },  
            'format' => 'raw',
            ],
            [
                'attribute'=>'name',
                'value'=> 'customers.name',
            ],
            [
                'attribute'=>'invoice_number',
                'value'=> 'invoices.invoice_number',
            ],
            
            [
                'attribute'=>'package_title',
                'value'=>'invoices.linkcustomepackage.package.package_title'
            ],
            [
                //'label'=>'Package Speed',
                'attribute'=>'package_speed',
                'value'=>function($data){
                    return $data->invoices->linkcustomepackage->package_speed;
                }
            ],
            [
                'attribute'=>'mobile_no',
                'value'=>'customers.mobile_no',
            ],
            'remark1:ntext',
            'remark2:ntext',
            [
                'attribute'=>'user_name',
                'value'=>'user.name',
                'label'=>'Admin Name'
            ],
            // 'fk_user_id',
            // 'created_date',
            
        ],
    ]); ?>	
    </div>
	
</div>
<script type="text/javascript">
$( document ).ready(function() {
		$(function(){
				var floatVat = '<?php echo $model->customer->state->vat; ?>';
				$("input[type='text']").blur(function (e) {
					var total = 0;
					$(".form-control").each(function() {
						if (this.readOnly) return;
						total += +this.value;
					});
					var floatCalVat    = (floatVat*total)/100;
					var floatTotalInvcAmt = total + floatCalVat;
					$('#customerinvoice-vat').val(floatCalVat);
					$('#customerinvoice-total_invoice_amount').val(floatTotalInvcAmt);
				});
			});
});
</script>
