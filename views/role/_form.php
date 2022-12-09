<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\AuthItem;
use app\models\AuthController;
use app\models\AuthItemChild;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;

/* @var $this yii\web\View */
/* @var $model app\models\PropertyTypes */
/* @var $form yii\widgets\ActiveForm */

//$this->title = 'Create Roles';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="body-content animated fadeIn">
<div class="row">
  <div class="col-md-12"> 
     
    <div class="panel-body no-padding">
      <div class="panel rounded shadow">
      
        <?php $form = ActiveForm::begin([
				'options' => [
                	'class' => 'form-horizontal form-bordered'
                 ],
                'fieldConfig' => [
                	'template' => '{label}<div class="col-sm-6">{input}</div>{error}',
                    'labelOptions' => ['class' => 'col-sm-2 control-label']
                 ]
             ]); 
		?>
        <div class="form-body"><br/><br/>
        <?php 
              if($model->name!="")
            	   echo $form->field($model, 'name')->textInput(['placeholder'=>'Role Name','readonly'=>'readonly'])->label('Role Name*');
              else
                 echo $form->field($model, 'name')->textInput(['placeholder'=>'Role Name'])->label('Role Name*');

                 		?>
        <span class="text-muted help-block"></span>
        	<?php 
        		if($model->name=='Super admin'){ ?>
            		<p style="margin-left: 86px;">( <b>Note :</b> <span style="color: green;" >Role name <i>"Super admin"</i> is default role name. So you can not update it.</span>)</p>
            <?php } ?>
        <h4 align="center" style="font-weight: bold;">Check the access for following menus:</h4>
         <div class="table-responsive mb-20">
          	<table class="table table-striped table-primary">
          	 	<thead>
          	 		<tr>
          	 			<th class="text-center border-right" style="font-size: 16px;font-weight: bold;">Menus</th>
                            <th style="font-size: 16px;font-weight: bold;" class="text-center">Access Given</th>
          	 		</tr> 
                   
          	 		<?php if($arrModules){ 
					        
          	 			foreach($arrModules as $key => $value){
							
						
          	 				$formAccess = AuthItemChild::find()->where("parent='" . $model->name."'")->all();

							$checkedAccess = array();
							if($formAccess){
                            	foreach ($formAccess as $data) {
                                	$checkedAccess[$value['controller']][$data->child] = $data->child;
                                }
                                $model->child = $checkedAccess;
                    		} 
                            $arrModuleActions = $authModel->getModuleActions($value['id'], $value['controller']);

                    ?>
          	 		<tr>
						<td class="text-center border-right" style="text-align:center; vertical-align:middle"> <?php echo $value['display_name']; ?></td>
          	 			<td>
          	 			<?php echo $form->field($model, 'child['.$value['controller'].']')->checkboxList($arrModuleActions, ['separator' => ' ', 'class'=>'checkBoxClass radio'])->label(false);?>
          	 			</td>
          	 		</tr>
          	 	<?php 
          	 		}
          	 	} 
          		?>	
          	 	</thead>
          	</table>
         </div>
       </div>
          
          <!-- /.form-body --> 
          <br>
          <div class="form-footer">
            <div class="pull-right"> 
			<?= Html::a('Back to Manage Roles', Yii::$app->request->referrer, ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success' ]) ?>
			<?php echo Html::submitButton($model->isNewRecord ? 'Create Role' : 'Update Role', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);?>
              <?= Html::a('Cancel',['/role'], ['class'=>'btn btn-danger mr-5']) ?>
            </div>
            <div class="clearfix"></div>
          </div>
          <!-- /.form-footer -->
          <?php ActiveForm::end();?>
        </div>
        <!-- /.panel-body --> 
     
      <!-- /.panel --> 
      <!--/ End input fields - horizontal form --> 
    </div>
  </div>
</div>
</div>
