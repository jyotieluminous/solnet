<?php
use yii\helpers\Html;
use app\models\Tblusers;
use app\models\State;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\LoginTemp;
use app\models\Log;
/* @var $this \yii\web\View */
/* @var $content string */

function isMobileDevice() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
if(isMobileDevice()){
    echo "It is a mobile device";die;
}
else {
   // echo "It is desktop or computer device";die;
}
$arrStates = array();
$arrUsers = array();
$checkIfInactive = array();
$arrUsersActivity = array();
$sessionData = Yii::$app->session;

$roleId = Yii::$app->user->identity->fk_role_id;
$userId = Yii::$app->user->identity->user_id;
if($roleId=='1')
{
    $arrStatesList   = State::find()->where(['status'=>'active'])->all();
    $arrStates  = ArrayHelper::map($arrStatesList,'state_id','state');
    $arrStates['all'] = 'All';
    ksort($arrStates);
}
elseif($sessionData->get('userStates'))
{
    $arrStates = $sessionData->get('userStates');
     $arrStates['all'] = 'All';
     ksort($arrStates);
}

?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">SOLNET</span><span class="logo-lg">SOLNET</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <li>
                <div class="row" style="width: 490px;">
                 <?php
                    $model = new Tblusers();
                    $form = ActiveForm::begin();
                 ?>
                 
                   <div class="col-md-12 ">
                    <div class="col-md-3 stateDropdown"><label>Select State:-</label></div>
                        <div class="col-md-6 stateDropdown">
                         <?php

                         if($sessionData->get('user_state_id'))
                         {
                            if($sessionData->get('user_state_id')=='all')
                            {
                                $model->states = 'all';
                            }
                            else
                            {
                                $model->states = $sessionData->get('user_state_id');
                            }
                            
                         }
                         elseif($sessionData->get('userStates'))
                         {
                            $model->states = 'all';
                            $sessionData->set('user_state_id',$model->states);
                         }
                         elseif(!$sessionData->get('userStates'))
                         {
                            if( $roleId=="1")
                            {
                                $model->states = 'all';
                                $sessionData->set('user_state_id',$model->states);
                            }
                            else
                            {
                                $model->states = "null"; 
                            }
                            
                         }
                         //echo $model->states;die;
                         echo $form->field($model, 'states')->label(false)->widget(Select2::classname(),[
                                'model'=>$model,
                                'data' => $arrStates,
                                'language' => 'en',
                                'options' => ['placeholder' => 'Select States'],
                                'pluginOptions' => [
                                'allowClear' => true,
                                'multiple'=>false,
                             ],
                         ]); 

                     ?>
                    </div>
                     <?php
                      //echo Html::submitButton('Save', ['class' => 'btn btn-success stateDropdown','id'=>'btnSubmit'])
                     ?>
                   </div>
                </div>
                <?php
                  ActiveForm::end();
                  $model = new LoginTemp();
                    $getOnlineUsers = $model->getOnlineUsers();
                    
                    if(isset($getOnlineUsers['online_users']))
                    {
                        $count=count($getOnlineUsers['online_users']);
                    }
                    else
                    {
                        $count = 0;
                    }
                ?>
               
                </li>
                <?php
                  if($roleId=='1' || $roleId=='2' ||$roleId=='10')
                  {
                ?>
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-users"></i>
                        <span class="label label-success" id="count"><?php echo $count;?></span>
                    </a>
                </li>
                <?php
                  }
                ?>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="hidden-xs"><i class="fa fa-unlock"></i></span><span class="hidden-xs"> <?php if(isset(Yii::$app->user->identity->name) && !empty(Yii::$app->user->identity->name)){ echo Yii::$app->user->identity->name; } ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?php echo Html::a(
                                    'Change Password',
                                    ['/user/changepassword'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</header>


<script>
$(document).ready(function(){
    $('#tblusers-states').change(function(event){
        var stateSelected = $(this).val();

        if(stateSelected!="")
        {
             $.ajax({
                type: "POST",
                url: "<?php echo Yii::$app->getUrlManager()->createUrl(['site/state']) ?>",
                data: {state_id : stateSelected},
              });
        
        }
        event.preventDefault();
       // alert();
    });
});
/*var click=false;
var unloaded = false;
$('body').click(function(){ 
  click = true;
  
});


$(window).on('beforeunload', unload);
$(window).on('unload', unload);  
function unload(){     
alert(unloaded);
alert(click); */
    /*if(!unloaded && !click){

        $('body').css('cursor','wait');
        $.ajax({
            type: 'POST',
            async: false,
            url: "<?php echo Yii::$app->getUrlManager()->createUrl(['site/logout']) ?>",
            success:function(){ 
                unloaded = true; 
                $('body').css('cursor','default');
            },
            timeout: 10000
        });
    }*/
//}

</script>