<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TblusersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage System Users';
$this->params['breadcrumbs'][] = $this->title;
?>
 <p>
 <?php echo Html::a('Add System User', ['create'], ['class' => 'btn btn-primary']) ?>
 <?php echo Html::a('&nbsp;Reset Filters', ['/user/index'], ['class' => 'btn btn-success']) ?>
 <?php echo Html::a('&nbsp;Delete Selected','javascript:void(0)',['class' => 'btn btn-danger','onClick'=>'javascript:deleteAll()']) ?>
 
 </p>
 
 <div class="alert-success alert fade in" id="success_status" style="display:none"> </div>
 <?php if(Yii::$app->session->hasFlash('success_status')) : ?>
            <div class="alert-success alert fade in">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?php echo Yii::$app->session->getFlash('success_status'); ?>
            </div>
 <?php endif; ?>
<?php Pjax::begin(['id'=>'system-user-grid']); ?>
<div class="box box-default">
	<div class="box-body">
		<div class="tblusers-index">
  
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'rowOptions' => function ($model, $index, $widget, $grid) {
                            if(Yii::$app->user->identity->user_id==$model->user_id) {
                                return ['class' => 'hide'];
                            } else {
                               return ['class' => 'read'];
                            }
                         },
		'id'=>'grid',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			['class' => 'yii\grid\CheckboxColumn'],
            [
				'attribute'=>'fk_role_id',
				'filter'=>$roleList,
				'value'=>'roles.role'
			],
            'name',
            'email:email',
			[
				'attribute'=>'status',
				 'label'=>'Status',
				 'filter'=>[''=>'All','active'=>'Active','inactive'=>'Inactive'],
				 'format'=>'raw',
				 'value' => function($data){
						if($data->email!=Yii::$app->user->identity->email)
						{
							if($data->status=='active')
							{
								$url = Url::to('@web/images/active.png');
								$strAltText =  'Active';
							}else{
								$url = Url::to('@web/images/inactive.png');	
								$strAltText =  'Inctive';
							}
							 $redirect_url = Yii::$app->request->baseUrl.'/user/togglestatus';
							 return Html::a('<img src='.$url.' class="switch-button" />', 'javascript:void(0);', ['title' => $strAltText,'onclick'=>"javascript:changeStatus('".$data->status."','".$data->user_id."','".$redirect_url."','system-user-grid','User status changed successfully.')"]); 
						}
						else{
							return '-';
						}

				 }
			],
            [
				'header'=>'Action',
	 			'template' => '{view} {update} {delete}',
				'class' => 'yii\grid\ActionColumn',
				'buttons' => [
							'view' => function ($url, $model) {
									return Html::a('<span class="fa fa-eye"></span>', $url, [
												'title' => Yii::t('app', 'View'),
												'class'=>'',                                  
									]);
							},
							'update' => function ($url, $model) {
								
									return Html::a('<span class="fa fa-pencil"></span>', $url, [
												'title' => Yii::t('app', 'Update'),
												'class'=>'',                                  
									]);
							},
	
							'delete' => function ($url, $model) {
								if($model->email!=Yii::$app->user->identity->email)
								{
									return Html::a('<i class="fa fa-trash"></i>', $url, [
												'title' => Yii::t('app', 'Delete'),
												'class'=>'',
												'data-confirm'=>'Are you sure want to delete this user?'
									]);
								}
							},
				]
			],
	 
			
        ],
    ]); ?>
    <?php Pjax::end(); ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	function deleteAll(){ 
		var ids = $('#grid').yiiGridView('getSelectedRows');
		if(ids.length >  0){
		if(confirm("Are you sure to delete this users?")){
			$.ajax({
				type     : 'POST',
				data     : { ids : ids },
				cache    : false,
				url  	 : "<?php echo yii::$app->request->baseUrl.'/user/deletemultiple' ?>",
				success  : function(response) {
												$.pjax.reload({container:'#system-user-grid',async:false});
												$(window).scrollTop($('.box').offset().top);

												if(response == 'success'){
													$("#success").css("display", "block");
													$('#success').html("Users deleted successfully");

												}
											$( "#success" ).fadeOut(5000);
											$.pjax.reload({container: '#system-user-grid',async:false});

					}

				});
			}
		}else{
			alert("Please select the records to delete");
		}
	}
</script>
