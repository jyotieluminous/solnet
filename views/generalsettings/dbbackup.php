<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Package;
use yii\widgets\Pjax;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\Generalsettings */

$this->title = 'Database Backup & Restore';
$this->params['breadcrumbs'][] = ['label' => 'Generalsettings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$roleId = Yii::$app->user->identity->fk_role_id;
?>
<div class="generalsettings-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(' Take Database Backup', ['backup'], ['class' => 'btn btn-primary fa fa-upload']) ?>    </p>
        <?php if($roleId == '1'){?>
    <div class="box box-default">
        <div class="box-body">
            <div class="tbllanguage-form">
                <div class="package-index">
                    <?php Pjax::begin(['id'=>'package-grid']); ?>
                       <?php echo GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'id' => 'grid',
                            'columns' => [
                                'id',
                                'filename',
                                'datetime',
                                [
                                'class' => 'yii\grid\ActionColumn',    
                                 'header'=>'action',
                                 'template'=>'{download}',
                                 'buttons' => [                                  
                                    'download' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-download "></span>', $url, [
                                                    'title' => Yii::t('app', 'Download Database'),
                                                    'data-method'=>'POST',
                                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to download this database?'),
                                        ]);
                                     },
                                  ],
                                ],
                            ],
    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
<?php } else
{
    ?>
     <div class="box box-default">
        <div class="box-body">
            <div class="tbllanguage-form">
                <div class="package-index">
                    <?php Pjax::begin(['id'=>'package-grid']); ?>
                       <?php echo GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'id' => 'grid',
                            'columns' => [
                                'id',
                                'filename',
                                'datetime',
                                /*[
                                'class' => 'yii\grid\ActionColumn',    
                                 'header'=>'action',
                                 'template'=>'{download}',
                                 'buttons' => [                                  
                                    'download' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-download "></span>', $url, [
                                                    'title' => Yii::t('app', 'Download Database'),
                                                    'data-method'=>'POST',
                                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to download this database?'),
                                        ]);
                                     },
                                  ],
                                ],*/
                            ],
    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}?>

</div>
