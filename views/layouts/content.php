<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;
use app\models\LoginTemp;
use yii\widgets\Pjax;
?>
<div class="content-wrapper">
    <section class="content-header">
        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1><?= $this->blocks['content-header'] ?></h1>
        <?php } else { ?>
            <h1>
                <?php
                if ($this->title !== null) {
                    echo \yii\helpers\Html::encode($this->title);
                } else {
                    echo \yii\helpers\Inflector::camel2words(
                        \yii\helpers\Inflector::id2camel($this->context->module->id)
                    );
                    echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
                } ?>
            </h1>
        <?php } ?>

        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
</div>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 2.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> Solnet </strong> All rights
    reserved.
</footer>


<!-- Control Sidebar -->
<?php Pjax::begin(); ?>
<aside class="control-sidebar control-sidebar-dark" style="background:#ECF0EE;">
<div class="container" style="padding-left:0px;height:0px;">

        <div class="panel panel-default user_panel" style="background:#ECF0EE;">
            <div class="panel-heading">
                <h3 class="panel-title">Online Users&nbsp;&nbsp;&nbsp;<a id="refresh"><i class="fa fa-refresh"></i></a></h3>
            </div>
            
                    <table class="table-users table" border="0" id="onlineUsers">
                        <?php echo $this->render('online_users.php');?>
                    </table>
                
        </div>

   
</div>
</aside>
<?php Pjax::end(); ?>
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<script>
$(document).ready(function(){
    $('#refresh').click(function(){
      // $('#onlineUsers').html("Hello");
        $.ajax({
                type: "POST",
                url: "<?php echo Yii::$app->getUrlManager()->createUrl(['site/onlineusers']) ?>",
                success: function(data)
                {
                    var data = $.parseJSON(data);
                    $('#onlineUsers').html(data.view);
                    $("#count").html(data.count);
                }
              });
    });
});
</script>
