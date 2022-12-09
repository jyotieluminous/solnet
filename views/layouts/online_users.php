<?php
use app\models\LoginTemp;

$getOnlineUsers = array();
$model = new LoginTemp();
$getOnlineUsers = $model->getOnlineUsers();

?>
<tbody>
   <?php
        if($getOnlineUsers)
        {
          if(isset($getOnlineUsers['online_users']))
            {
            foreach($getOnlineUsers['online_users'] as $key=>$value)
            {
   ?>
             <tr>
                <td style="color:black;">
                    <i class="fa fa-circle" style="color:#58D68D;"></i>&nbsp;&nbsp;&nbsp;<?php echo "  ".$value;?>
                </td>  
            </tr>
   <?php             
            }
          }
            if(isset($getOnlineUsers['away_users']))
            {
              foreach($getOnlineUsers['away_users'] as $key=>$value)
              {
         ?>
                   <tr>
                      <td style="color:black;">
                          <i class="fa fa-circle" style="color:#EBEE29;"></i>&nbsp;&nbsp;&nbsp;<?php echo "  ".$value;?>
                      </td>  
                  </tr>
    <?php             
              }
            }
        }
        else
        {
   ?>
            <tr>
                <td style="color:black;">
                   No user is online
                </td>  
            </tr>
   <?php       
        }
            
   ?>
</tbody>