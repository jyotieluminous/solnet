<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\EmailLogs;
use kartik\export\ExportMenu;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EmailLogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Logs';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'email_to',
                'label'=>'Email sent to',
                'value'=>function($data)
                {
                    return $data->email_to;
                }
            ],
           //'email_to:email',
            'subject',
            [
                'attribute'=>'is_customer',
                'filter'=>array('Yes'=>'Yes','No'=>'No'),
                'label'=>'Sent to Customer',
                'value'=>function($data)
                {
                    return $data->is_customer;
                }
            ],
            [
                'attribute'=>'is_user',
                'filter'=>array('Yes'=>'Yes','No'=>'No'),
                'label'=>'Sent to User',
                'value'=>function($data)
                {
                    return $data->is_user;
                }
            ],
            [
                'attribute'=>'sent_to_id',
                'label'=>'Sent to User/Customer',
                'value'=>function($data)
                {
                    $model = new EmailLogs();
                    $name = "";
                    if($data->is_customer=='Yes')
                    {
                        $name = $model->getName($data->sent_to_id,'customer');
                        if($name)
                        {
                            $name = $name." (Customer)";
                        }
                        else
                            $name="-";
                    }
                    if($data->is_user=='Yes')
                    {
                         $name = $model->getName($data->sent_to_id,'user');
                         if($name)
                         {
                            $name = $name." (User)";
                         }
                         else
                            $name="-";
                    }
                    return $name;
                }
            ],
            [
                'attribute'=>'sent_by',
                'filter'=>array('System'=>'System','User'=>'User'),
                'label'=>'Email Sent By',
                'value'=>function($data)
                {
                    return $data->sent_by;
                }
            ],
            // 'sent_by',
             [
                'attribute'=>'sent_by_user_id',
                'label'=>'Email Sent By(User)',
                'value'=>function($data)
                {
                    $name = "";
                    $model = new EmailLogs();
                    if($data->sent_by=='User')
                    {
                        $name = $model->getName($data->sent_by_user_id,'user');
                        if($name)
                        {
                            return $name;
                        }
                    }
                    else
                    {
                        return "-";
                    }
                }
            ],
             [
                'attribute'=>'sent_date',
                'value'=> function($data){
                       return date("d-m-Y",  strtotime($data->sent_date));
                },
                'filter' => DatePicker::widget([
                            'name' => 'EmailLogSearch[sent_date]',
                            //'value'=>$strDate,
                            'template' => '{addon}{input}',
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd-mm-yyyy'
                                ]
                        ])
                 
            ],
            /*[
                'attribute'=>'sent_date',
                'options'=>['width'=>'15%'],
                'filter'=>false,
                'value'=>function($data){
                        return date('d-m-Y',strtotime($data->sent_date));
                },
            ],*/
            // 'sent_by_user_id',
            // 'sent_date',

            [
                'header'=>'Action',
                'options'=>['width'=>100],
                'template'=>'{view}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a(
                            '<span class="fa fa-eye"></span>', 
                            ['email/view','id'=>$model->email_log_id]);
                    },
                   
                ],
                'class' => 'yii\grid\ActionColumn'
            ],
        ];
?>
<p style="display: inline-block;">
<?php echo Html::a('Reset Filters', ['/email/index'], ['class' => 'btn btn-success']) ?>
</p><br>
<?php
    // Renders a export dropdown menu
 /*echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'filename'=>'email_logs'.date('Ymdhis')
]);*/
?>
<p></p>
<div class="box box-default">
        <div class="box-body">
            <div class="tbllanguage-form">
<div class="email-logs-index">

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
    ]); ?>
</div>
</div>
</div>
</div>
