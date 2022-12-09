<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\NotFoundHttpException;

/* @var $this yii\web\View */
/* @var $model app\models\Package */
if(!empty($model)){
$this->title = $model->package_title;
$this->params['breadcrumbs'][] = ['label' => 'Packages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="package-view">

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->package_id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->package_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this package?',
                'method' => 'post',
            ],
        ]) ?>
         <?php echo Html::a('Back',['package/index'],['class' => 'btn btn-default']) ?>
    </p>
<div class="box box-default">
    <div class="box-body">
        <?php echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'package_id',
                'package_title',
                 [                      
                'label' => 'Status',
                'value' => ucfirst($model->status),
                 ],
                //'is_deleted',
                //'created_at',
                //'updated_at',
            ],
        ]) ?>
    </div>
</div>

</div>
<?php }else{
   throw new NotFoundHttpException('The requested page does not exist.');
   }?>
