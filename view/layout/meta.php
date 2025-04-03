<!DOCTYPE html>
<html>
<head>
	
	<meta charset="utf-8">
	<title><?=L('title');?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="description" content="<?=L('title');?>">
	<meta name="keywords" content="<?=L('title');?>">
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?=L('title');?>">
	<meta property="og:description" content="<?=L('title');?>">	
	<meta name="twitter:title" content="<?=L('title');?>">
	<meta name="twitter:description" content="<?=L('title');?>">
	<link rel="shortcut icon" href="<?php Utility\WebSystem::path("images/favicon.ico");?>">
    
	<!-- CSS -->
	 
	<!--
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans+TC:100,300,400|Bilbo+Swash+Caps">
	<link rel="stylesheet" type="text/css" href="<?php Utility\WebSystem::path("node_modules/bootstrap/dist/css/bootstrap.min.css");?>">	
	<link rel="stylesheet" type="text/css" href="<?php Utility\WebSystem::path("node_modules/@fortawesome/fontawesome-free/css/all.min.css");?>">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="<?php Utility\WebSystem::path("node_modules/flatpickr/dist/flatpickr.min.css");?>">
	<link rel="stylesheet" type="text/css" href="<?php Utility\WebSystem::path("node_modules/datatables.net-dt/css/jquery.dataTables.min.css");?>">
	-->

	<script src="<?php Utility\WebSystem::path("js/kaiadmin/plugin/webfont/webfont.min.js");?>"></script>
	<script>
		WebFont.load({
			google: {"families":["Public Sans:300,400,500,600,700"]},
			custom: {"families":["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['<?php Utility\WebSystem::path("css/kaiadmin/fonts.min.css");?>']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>
	<link href="<?php Utility\WebSystem::path("css/kaiadmin/bootstrap.min.css");?>" rel="stylesheet" type="text/css">
	<link href="<?php Utility\WebSystem::path("css/kaiadmin/plugins.min.css");?>" rel="stylesheet" type="text/css">
	<link href="<?php Utility\WebSystem::path("css/kaiadmin/kaiadmin.min.css");?>" rel="stylesheet" type="text/css">
	<link href="<?php Utility\WebSystem::path("css/admin.css");?>" rel="stylesheet" type="text/css">  

	<!--<link href="<?php Utility\WebSystem::path("css/kaiadmin/bootstrap-4.5.2.min.css");?>" rel="stylesheet" type="text/css"> -->

	<link href="<?php Utility\WebSystem::path("css/kaiadmin/bootstrap-multiselect.css");?>" rel="stylesheet" type="text/css"> 
	<link href="<?php Utility\WebSystem::path("css/kaiadmin/prettify.min.css");?>" rel="stylesheet" type="text/css"> 

	<link href="<?php Utility\WebSystem::path("css/kaiadmin/flatpickr.min.css");?>" rel="stylesheet" type="text/css"> 

	<link href="<?php Utility\WebSystem::path("vendor/donatj/simplecalendar/src/css/SimpleCalendar.css");?>" rel="stylesheet" type="text/css"> 	
	<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css" rel="stylesheet" type="text/css"> 
	
	<style>
		.select2-container {
			z-index: 9999;
		}

        .select2-container--default .select2-selection--multiple {
            border: 2px solid #ebedf2 !important;
            border-radius: 4px;
			padding: .2rem 1rem;
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #ebedf2 !important;
            border-radius: 4px;
			height: 40px !important;			
        }

		.select2-container--default .select2-selection--single .select2-selection__arrow {
			height: 40px !important;
			position: absolute;
			top: 1px;
			right: 1px;
			width: 20px;
		}		

		.select2-container--default .select2-selection--single .select2-selection__rendered {
			color: #444;
			line-height: 40px !important;
		}		

    </style>
	
	
</head>
<body>
