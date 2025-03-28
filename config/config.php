<?php

return [
	'global' => [
		'classPath' => "class/",
		'viewPath' => "view/",
//		'timezone' => "Asia/Hong_Kong",
		'timezone' => "",
		'listMax' => 30,
		'lang' => 'hk',
		],
	'database' => [
		0 => [
			'driver' => "mysql",
			'host' => "localhost",
			'port' => 3306,
			'schema' => "superta1_pms",
			'charset' => "utf8mb4",
			'username' => "superta1_webdev",
			'password' => "8swzalE@imVk",

			'options' => [
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				],
			],
		],
	'system' => [

		],
	'phpmailer' => [
		
		"server" => [
//			"Mailer" => 'mail',
			"CharSet" => 'utf-8',
			"SMTPDebug" => 0,
			"Mailer" => 'smtp',
			"Host" => 'smtp.mailgun.org', 
			"SMTPAuth" => true,
			"Username" => 'assistant@ecbq.hk',
			"Password" => '92acf8cca04dadad33a5fbb925585b9e-69a6bd85-c839b56d', 
			"SMTPSecure" => 'tls',
			"Port" => 587
		],
		"recipients" => [
			"setFrom" 		=> ["email"=>'assistant@ecbq.hk', "name"=>'assistant@ecbq.hk'],
			"addReplyTo" 	=> ["email"=>'assistant@ecbq.hk', "name"=>'assistant@ecbq.hk'],
			],
		]

];
