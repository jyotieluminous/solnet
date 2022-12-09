<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tblusers */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Manage system users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
        <?php echo Html::a('Add System User', ['create'], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Update System User', ['update', 'id' => $model->user_id], ['class' => 'btn btn-success']) ?>
        <?php echo Html::a('Delete System User', ['delete', 'id' => $model->user_id], ['class' => 'btn btn-danger','data' => [
                'confirm' => 'Are you sure you want to delete this item?','method' => 'post'],
        ]) ?>
        <?php echo Html::a('Manage System Users', ['index'], ['class' => 'btn btn-warning']) ?>
</p>
<div class="box box-default">
	<div class="box-body">
		<div class="tblusers-view">
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-6 form-group">
					<label>Name :</label>
					<?php echo ucfirst($model->name); ?>
				</div>
				<div class="form-group col-md-6">
					<label>Role :</label>
					<?php echo ucfirst($model->roles->role); ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-6 form-group">
					<label>Email :</label>
					<?php echo $model->email; ?>
				</div>
				<div class="form-group col-md-6">
					<label>Status :</label>
					<?php echo ucfirst($model->status); ?>
				</div>
			</div>
		</div>
		</div>
	</div>
</div>
