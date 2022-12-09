      <aside class="main-sidebar">
    <section class="sidebar">
       <?php 
		$arrMenu = [];
		$arrMenuDisplay = [];
		if(isset(yii::$app->user->identity->fk_role_id) && !empty(yii::$app->user->identity->fk_role_id))
		{
			$arrMenu = [
							['label' => 'Manage Roles','icon' => 'user-plus','url' => ['role/index']],
							['label' => 'Manage System Users','icon' => 'user-plus','url' => ['user/index']],
							['label' => 'Manage Customer', 'icon' => 'user', 'url' => ['customer/index']],
							['label' => 'Manage Pending Installation','icon' => 'exclamation-circle','url' => ['customer/pendinginstallation']],
							['label' => 'Manage Pending Activation', 'icon' => 'hourglass-3', 'url' => ['customer/pending']],
							['label' => 'Manage Billing Customer', 'icon' => 'file-text-o', 'url' => ['customer/billing']],
							['label' => 'Customer Services','icon' => 'tasks','url' => ['service/addservice']], 
							['label' => 'Manage Disconnection Report ','icon' => 'power-off','url' => ['customerpackage/disconnectreport']],
							['label' => 'Manage Invoices','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Manage Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/index']],
									['label' => 'Outstanding Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/outstanding']],
									['label' => 'Generate Custom Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/generate']],
									['label' => 'Custom Service Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/service']],
								],
                    		],
                    		['label' => 'Manage Customer complain ','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Manage Broadband Complains', 'icon' => 'file-code-o', 'url' => ['customercomplain/broadbandindex']],
									['label' => 'Manage Broadband Job Allocations ', 'icon' => 'file-code-o', 'url' => ['customercomplain/broadbandjoballocationindex']],
									['label' => 'Manage Dedicated Complains', 'icon' => 'file-code-o', 'url' => ['customercomplain/dedicatedindex']],
									['label' => 'Manage Dedicated Job Allocations', 'icon' => 'file-code-o', 'url' => ['customercomplain/dedicatedjoballocationindex']],
									['label' => 'Manage Local Loop Complains', 'icon' => 'file-code-o', 'url' => ['customercomplain/localloopindex']],
									['label' => 'Manage Local Loop Job Allocations', 'icon' => 'file-code-o', 'url' => ['customercomplain/localloopjoballocationindex']],
									['label' => 'Manage Complains Report', 'icon' => 'file-code-o', 'url' => ['customercomplain/complainreport']],
								],
                    		],
                    		['label' => 'Customer Support','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Manage Customer Support', 'icon' => 'file-code-o', 'url' => ['customersupport/index']],
									
									['label' => 'Customer Support Payments', 'icon' => 'file-code-o', 'url' => ['customersupport/customersupportpayment']],
								],
                    		],
                    		
                    		['label' => 'Daily JOB Report','icon' => 'tasks','url' => ['remarks/index']], 
							['label' => 'Manage State','icon' => 'flag-o','url' => ['state/index']],  
							['label' => 'Manage Package','icon' => 'dropbox ','url' => ['package/index']],
							['label' => 'Manage Bank ','icon' => 'university','url' => ['bank/index']],
							['label' => 'Manage Bank Deposit','icon' => 'money','url' => ['bankdeposite/index']],
							['label' => 'Manage Sales Incentive','icon' => 'money','url' => ['bankdeposite/incentive']],
							['label' => 'Manage Prospect ','icon' => 'briefcase','url' => ['prospect/index']],
							['label' => 'Manage Activity Log ','icon' => 'history','url' => ['log/index']],
							['label' => 'Manage Email Logs ','icon' => 'envelope','url' => ['email/index']],
							['label' => 'Reports','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Payment Collection', 'icon' => 'file-code-o', 'url' => ['report/paymentreport']],
									['label' => 'Contract Report ','icon' => 'handshake-o','url' => ['customerpackage/customercontract']],
									['label' => 'Summary Collection','icon' => 'money','url' => ['report/summarycollectionreport']],
									['label' => 'Signup Report ','icon' => 'fa fa-flag-o','url' => ['report/signup']],
									['label' => 'Revenue Report ','icon' => 'fa fa-flag-o','url' => ['report/revenue']],
									['label' => 'Sales Revenue Report ','icon' => 'fa fa-flag-o','url' => ['report/salesrevenue']],
								],
                    		],
                    		
							['label' => 'General Settings ','icon' => 'cog','url' => ['generalsettings/generalsettings']],
							['label' => 'Database Operation','icon' => 'upload','url' => ['generalsettings/dbbackup']],
							['label' => 'Temporary Disconnect Users','icon' => 'scissors','url' => ['customer/tempusers']],
							['label' => 'Reminder ','icon' => 'bell','url' => ['invoice/reminderlist']],
							['label' => 'Statement Of Account ','icon' => 'money','url' => ['invoice/soa']],
							['label' => 'Service Report ','icon' => 'file','url' => ['servicereport/index']],
						];
			/*echo "<pre>";
			print_r($arrMenu);die;*/			
			/*if(yii::$app->user->identity->fk_role_id=='1')
			{
				$arrMenuDisplay = [['label' => 'Manage Roles','icon' => 'user-plus','url' => ['role/index']]];
			}	*/		
			foreach($arrMenu as $key=>$value)
			{
				$route = $value['url'][0];
				if($route=='#')
				{
					foreach($value['items'] as $k=>$val)
					{
						
						$subRoute = $val['url'][0];
						if(\Yii::$app->user->can($subRoute))
						{
							$arrMenuDisplay[$key]['label'] = $value['label'];
							$arrMenuDisplay[$key]['icon'] = $value['icon'];
							$arrMenuDisplay[$key]['url'] = $value['url'];
							$arrMenuDisplay[$key]['items'][$k] = $val;
							
						}
					}
				}
				if(\Yii::$app->user->can($route))
				{
					$arrMenuDisplay[] = $value;
				}
				
			}		
			
		}	
		
		?>
        <?php echo dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => $arrMenuDisplay
				//['label' => 'Logout','icon' => 'lock','url' => ['/site/logout']],
            ]
        ) ?>
        
       
    </section>
</aside>
