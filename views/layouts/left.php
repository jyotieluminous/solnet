      <aside class="main-sidebar">
    <section class="sidebar">
       <?php 
		$arrMenu = [];
		if(isset(yii::$app->user->identity->fk_role_id) && !empty(yii::$app->user->identity->fk_role_id))
		{
		


			if(yii::$app->user->identity->fk_role_id=='1'){
			/******FOR SUPER ADMIN*******/
			$arrMenu = [
							['label' => 'Manage Roles','icon' => 'user-plus','url' => ['role/index']],
							['label' => 'Manage System Users','icon' => 'user-plus','url' => ['/user']],
							['label' => 'Manage Customer', 'icon' => 'user', 'url' => ['customer/index']],
							['label' => 'Manage Pending Installation','icon' => 'exclamation-circle','url' => ['customer/pendinginstallation']],
							['label' => 'Manage Pending Activation', 'icon' => 'hourglass-3', 'url' => ['customer/pending']],
							['label' => 'Manage Billing Customer', 'icon' => 'file-text-o', 'url' => ['customer/billing']],
							['label' => 'Manage Disconnection Report ','icon' => 'power-off','url' => ['customerpackage/disconnectreport']],
							['label' => 'Manage Invoices','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Manage Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/index']],
									['label' => 'Outstanding Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/outstanding']],
									['label' => 'Generate Custom Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/generate']],
								],
                    		],
							['label' => 'Manage State','icon' => 'flag-o','url' => ['/state']],  
							['label' => 'Manage Package','icon' => 'dropbox ','url' => ['/package']],
							['label' => 'Manage Bank ','icon' => 'university','url' => ['/bank']],
							['label' => 'Manage Bank Deposit','icon' => 'money','url' => ['/bankdeposite']],
							['label' => 'Manage Prospect ','icon' => 'briefcase','url' => ['/prospect']],
							['label' => 'Manage Activity Log ','icon' => 'history','url' => ['/log']],
							
							['label' => 'Reports','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Payment Collection', 'icon' => 'file-code-o', 'url' => ['report/paymentreport']],
									// ['label' => 'Contract Report ','icon' => 'handshake-o','url' => ['customerpackage/customercontract']],
									['label' => 'Summary Collection','icon' => 'money','url' => ['report/summarycollectionreport']],
									['label' => 'Signup Report ','icon' => 'fa fa-flag-o','url' => ['report/signup']],
									['label' => 'Revenue Report ','icon' => 'fa fa-flag-o','url' => ['report/revenue']],
								],
                    		],
							['label' => 'General Settings ','icon' => 'cog','url' => ['generalsettings/generalsettings']],
							['label' => 'Take Database Backup','icon' => 'cog','url' => ['generalsettings/dbbackup']],
							['label' => 'Reminder ','icon' => 'bell','url' => ['invoice/reminderlist']],
							['label' => 'Statement Of Account ','icon' => 'money','url' => ['invoice/soa']],
							['label' => 'Service Report ','icon' => 'file','url' => ['servicereport/index']],
						];	
			}elseif(yii::$app->user->identity->fk_role_id=='2'){
				/******FOR  ADMIN*******/
				$arrMenu = [
								
							['label' => 'Manage Customer', 'icon' => 'user', 'url' => ['customer/index']],
							['label' => 'Manage Pending Installation','icon' => 'exclamation-circle','url' => ['customer/pendinginstallation']],
							['label' => 'Manage Pending Activation', 'icon' => 'hourglass-3', 'url' => ['customer/pending']],
							['label' => 'Manage Billing Customer', 'icon' => 'file-text-o', 'url' => ['customer/billing']],
							
							['label' => 'Manage Invoices','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Manage Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/index']],
									['label' => 'Outstanding Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/outstanding']],
									['label' => 'Generate Custom Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/generate']],
								],
                    		],
							['label' => 'Manage State','icon' => 'flag-o','url' => ['/state']],  
							['label' => 'Manage Package','icon' => 'dropbox ','url' => ['/package']],
							['label' => 'Manage Bank ','icon' => 'university','url' => ['/bank']],
							['label' => 'Manage Bank Deposit','icon' => 'money','url' => ['/bankdeposite']],
							['label' => 'Manage Prospect ','icon' => 'briefcase','url' => ['/prospect']],
							['label' => 'Manage Activity Log ','icon' => 'history','url' => ['/log']],
							
							['label' => 'Reports','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Payment Collection', 'icon' => 'file-code-o', 'url' => ['report/paymentreport']],
									// ['label' => 'Contract Report ','icon' => 'handshake-o','url' => ['customerpackage/customercontract']],
									['label' => 'Summary Collection','icon' => 'money','url' => ['report/summarycollectionreport']],
									['label' => 'Signup Report ','icon' => 'fa fa-flag-o','url' => ['report/signup']],
									['label' => 'Revenue Report ','icon' => 'fa fa-flag-o','url' => ['report/revenue']],
									['label' => 'Disconnection Report ','icon' => 'power-off','url' => ['/customerpackage/disconnectreport']],
								],
                    		],
							['label' => 'General Settings ','icon' => 'cog','url' => ['generalsettings/generalsettings']],
							['label' => 'Take Database Backup','icon' => 'cog','url' => ['generalsettings/dbbackup']],
							['label' => 'Statement Of Account ','icon' => 'money','url' => ['invoice/soa']],
							['label' => 'Reminder ','icon' => 'bell','url' => ['invoice/reminderlist']],

							];
				
			}elseif(yii::$app->user->identity->fk_role_id=='3' || yii::$app->user->identity->fk_role_id=='9'){
				/******FOR SALES ADMIN*******/
				$arrMenu = [
								['label' => 'Manage Customer', 'icon' => 'users', 'url' => ['customer/index']],
								['label' => 'Manage Billing Customer', 'icon' => 'file-text-o', 'url' => ['customer/billing']],
								['label' => 'Manage Pending Installation','icon' => 'exclamation-circle','url' => ['customer/pendinginstallation']],
								['label' => 'Manage Prospect ','icon' => 'briefcase','url' => ['/prospect']],
								
								['label' => 'Reports','icon' => 'file-o','url' => '#',
								'items' => [
									//['label' => 'Payment Collection', 'icon' => 'file-code-o', 'url' => ['report/paymentreport']],
									//['label' => 'Contract Report ','icon' => 'handshake-o','url' => ['customerpackage/customercontract']],
									//['label' => 'Summary Collection','icon' => 'money','url' => ['report/summarycollectionreport']],
									['label' => 'Signup Report ','icon' => 'fa fa-flag-o','url' => ['report/signup']],
									//['label' => 'Revenue Report ','icon' => 'fa fa-flag-o','url' => ['report/revenue']],
									['label' => 'Disconnection Report ','icon' => 'power-off','url' => ['/customerpackage/disconnectreport']],
									],
								],
							];
								

				
			}elseif(yii::$app->user->identity->fk_role_id=='4'){
				/******FOR BILLING ADMIN*******/
				$arrMenu = [
							
							['label' => 'Manage Customer', 'icon' => 'user', 'url' => ['customer/index']],
							['label' => 'Manage Pending Installation','icon' => 'exclamation-circle','url' => ['customer/pendinginstallation']],
							['label' => 'Manage Pending Activation', 'icon' => 'hourglass-3', 'url' => ['customer/pending']],
							['label' => 'Manage Billing Customer', 'icon' => 'file-text-o', 'url' => ['customer/billing']],
							['label' => 'Manage Disconnection Report ','icon' => 'power-off','url' => ['/customerpackage/disconnectreport']],
							['label' => 'Manage Invoices','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Manage Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/index']],
									['label' => 'Outstanding Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/outstanding']],
									['label' => 'Generate Custom Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/generate']],
								],
                    		],
							['label' => 'Manage State','icon' => 'flag-o','url' => ['/state']],  
							['label' => 'Manage Package','icon' => 'dropbox ','url' => ['/package']],
							['label' => 'Manage Bank ','icon' => 'university','url' => ['/bank']],
							['label' => 'Manage Bank Deposit','icon' => 'money','url' => ['/bankdeposite']],
							['label' => 'Manage Prospect ','icon' => 'briefcase','url' => ['/prospect']],
							['label' => 'Reports','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Payment Collection', 'icon' => 'file-code-o', 'url' => ['report/paymentreport']],
									// ['label' => 'Contract Report ','icon' => 'handshake-o','url' => ['customerpackage/customercontract']],
									['label' => 'Summary Collection','icon' => 'money','url' => ['report/summarycollectionreport']],
									['label' => 'Signup Report ','icon' => 'fa fa-flag-o','url' => ['report/signup']],
									['label' => 'Revenue Report ','icon' => 'fa fa-flag-o','url' => ['report/revenue']],
								],
                    		],
							['label' => 'General Settings ','icon' => 'cog','url' => ['generalsettings/generalsettings']],
							['label' => 'Take Database Backup','icon' => 'cog','url' => ['generalsettings/dbbackup']],
                            ['label' => 'Statement Of Account ','icon' => 'money','url' => ['invoice/soa']],
							['label' => 'Reminder ','icon' => 'bell','url' => ['invoice/reminderlist']],
							];
				
			}elseif(yii::$app->user->identity->fk_role_id=='5'){
				/******FOR BILLING1 ADMIN*******/
				$arrMenu = [
								
								['label' => 'Manage Customer', 'icon' => 'user', 'url' => ['customer/index']],
								['label' => 'Manage Pending Activation', 'icon' => 'hourglass-3', 'url' => ['customer/pending']],
								['label' => 'Manage Billing Customer', 'icon' => 'file-text-o', 'url' => ['customer/billing']],
								['label' => 'Manage Bank Deposit','icon' => 'money','url' => ['/bankdeposite']],
								['label' => 'Statement Of Account ','icon' => 'money','url' => ['invoice/soa']],
								['label' => 'Reminder ','icon' => 'bell','url' => ['invoice/reminderlist']],

							];
				
		  }elseif(yii::$app->user->identity->fk_role_id=='6'){
				/******FOR BILLING2 ADMIN*******/
				$arrMenu = [
								
								['label' => 'Manage Billing Customer', 'icon' => 'file-text-o', 'url' => ['customer/billing']],
								['label' => 'Manage Bank Deposit','icon' => 'money','url' => ['/bankdeposite']],
								['label' => 'Manage Invoices','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Outstanding Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/outstanding']],
								],
                    		],
								['label' => 'Reports','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Payment Collection', 'icon' => 'file-code-o', 'url' => ['report/paymentreport']],
									
								],
                    		],
                    		
                    		    ['label' => 'Statement Of Account ','icon' => 'money','url' => ['invoice/soa']],
								['label' => 'Reminder ','icon' => 'bell','url' => ['invoice/reminderlist']],

							];
				
		}elseif(yii::$app->user->identity->fk_role_id=='7'){
				/******FOR BILLING3 ADMIN*******/
				$arrMenu = [
								['label' => 'Manage Invoices','icon' => 'file-o','url' => '#',
								'items' => [
									['label' => 'Manage Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/index']],
									['label' => 'Outstanding Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/outstanding']],
									['label' => 'Generate Custom Invoices', 'icon' => 'file-code-o', 'url' => ['invoice/generate']],
								],
                    		],

							];
				
		}elseif(yii::$app->user->identity->fk_role_id=='8'){
				/******FOR NOC ADMIN*******/
				$arrMenu = [
								
						['label' => 'Manage Customer', 'icon' => 'user', 'url' => ['customer/index']],
						['label' => 'Manage Pending Installation','icon' => 'exclamation-circle','url' => ['customer/pendinginstallation']],
                    	['label' => 'Manage Billing Customer', 'icon' => 'file-text-o', 'url' => ['customer/billing']],
                    	['label' => 'Manage Disconnection Report', 'icon' => 'file-text-o', 'url' => ['customerpackage/disconnectreport']]
							];	
		 }
		}	
		?>
        <?php echo dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => $arrMenu
				//['label' => 'Logout','icon' => 'lock','url' => ['/site/logout']],
            ]
        ) ?>
        
       
    </section>
</aside>
