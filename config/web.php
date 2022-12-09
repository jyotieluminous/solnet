<?php

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');
$params = require(__DIR__ . '/messages.php');
require(__DIR__ . '/constants.php');
use yii\web\Request;
$baseUrl = str_replace('/web', '', (new Request)->getBaseUrl());
use kartik\mpdf\Pdf;
use app\models\LoginTemp;
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
	'modules' => [ 'gridview' => [ 'class' => '\kartik\grid\Module']],
    'components' => [
	    'view' => [
				'theme' => [
					'pathMap' => [
						    '@app/views' => '@app/views/layouts'
					],
				],
    	],
		'customcomponents' => [
            'class' => 'app\components\CustomComponents',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'uQIrZ4FspNTAqSpe_NSGbIzaffWtyYWg',
			'baseUrl' => $baseUrl,
        ],
	 'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
			'authTimeout' => 1800,
            'on beforeLogout'=>function($e)
            {
                $intId=Yii::$app->user->identity->user_id;
                $model = LoginTemp::find()->where(['fk_user_id'=>$intId])->one();
                if($model)
                {
                    $model->find()->where(['fk_user_id'=>$intId])->one()->delete();
                }
            }
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],*/
		'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
           'useFileTransport' => false,
		   //'viewPath' => '@common/mail',
			'transport' => [
					'class' => 'Swift_SmtpTransport',
					'host' => '127.0.0.1',
					'username' => '',
					'password' => '',
				
					'port' => '25',
					'encryption' => '',
				 ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'site/resetpassword/<id>/<key>' => 'site/resetpassword',

                '<controller:\w+>/<action:\w+>/<id:\w+>/<key:\w+>' => '<controller>/<action>', 
	           '<controller:\w+>/<action:\w+>/<id:\d+>/<tab:\w+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',

               
                
				
            ],
        ],
	    'pdf' => [
			'class' => Pdf::classname(),
			'format' => Pdf::FORMAT_A4,
			'orientation' => Pdf::ORIENT_LANDSCAPE,
			'destination' => Pdf::DEST_BROWSER,
        	// refer settings section for all configuration options
    	]
        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    /*$config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];*/

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
