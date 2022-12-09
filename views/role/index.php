<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;



/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchTemplateCategories */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Roles & Access';
$this->params['breadcrumbs'][] = $this->title;
?>
<section id="page-content">
  <!-- Start page header -->
 
  <!-- /.header-content -->
  <div class="body-content animated fadeIn">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <h1>
      <?php //echo Html::encode($this->title) ?>
    </h1>
    <div class="row">
      <div class="col-md-12">
        <?php if(Yii::$app->session->hasFlash('success')) : ?>
        <div class="alert-success alert fade in">
          <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
          <?php echo Yii::$app->session->getFlash('success'); ?> </div>
        <?php endif; ?>
        <p>
        <div style="margin-bottom:20px"> <?php echo Html::a('Create Role', ['add'], ['class' => 'btn btn-success']) ?>
          <div style="float:right">
            <?= Html::a('Reset Filters', ['index'], ['class' => 'btn btn-primary']) ?>
          </div>
        </div>
        </p>
        <p>( <b>Note :</b> <span style="color: green;" >Role name <i>"Super admin"</i> is default role. So you can not delete it.</span> )</p>
        
        <!-- Start sample table -->
        <div class="table-responsive rounded mb-20">
          <?= GridView::widget([
          		'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout'=>"{summary}\n{items}\n{pager}",
                'columns' => [
                	['class' => 'yii\grid\SerialColumn'],
                    	[
                         	'attribute' => 'name',
                            'format' => 'text',
                            'label' => 'Role Name',
                          ],
                      [
                         	'label' => 'Action'  ,       
                            'content' => function ($model, $key, $index, $column) {
                             $class = "";
								if($model->name != 'Super Admin'){
                                	
                               
                                                                                    

							   
							   return Html::a('<i class="fa fa-pencil"></i>',['update', 'id' => $model->name], ['class' => 'btn btn-primary btn-xs rounded']
							);
							       } else{
										 return Html::a('<i class="fa fa-pencil"></i>',['update', 'id' => $model->name], ['class' => 'btn btn-primary btn-xs rounded']
							);
									}
									}
                              ],
                    ],
                    'tableOptions' =>['class' => 'table table-striped table-bordered table-success'],
                    'rowOptions'=>function ($model, $key, $index, $grid){
                            if($model->status=='Deleted'){
                                     return ['class'=>'deleted'];
                            }}
            ]); ?>
        </div>
        <!-- /.table-responsive --> 
        <!--/ End sample table --> 
        
        <!-- Start dropzone js --> 
        
        <!--/ End mini stats social widget --> 
        
      </div>
    </div>
    <!-- /.row --> 
    
  </div>
  </div>
</section>
<!-- /#page-content -->

<!--/ End body content -->