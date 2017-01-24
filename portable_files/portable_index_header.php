<?php
	require_once("../includes/globals.php");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?></title>

    <!-- Linked stylesheets -->
    <link href="./resources/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./resources/jquery-ui.min.css">
    <link href="<?= APP_PATH_URL ?>../css/navbar-custom1.css" rel="stylesheet">
    <link href="<?= APP_PATH_URL ?>../css/master.css" rel="stylesheet">
    <link href="<?= APP_PATH_URL ?>css/main.css" rel="stylesheet">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="./resources/jquery.min.js"></script>
    <script src="./resources/jquery-ui.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="./resources/bootstrap.min.js"></script>

    <!-- Included Scripts -->
    <script src="<?= APP_PATH_URL ?>js/main.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head>
    <body>
        <style type="text/css">
            .header_bar{
                background-color: #E6DED4;
                font-family: "Times New Roman";
            }

            #pageHeader{
                font-size:22px;
                font-weight:bold;
                color:white;
                text-shadow: 0px 0px 2px #333;
            }

            #famuLogo{
              padding-bottom:2px;
              padding-left:95px;
            }
        </style>

        <div class="header_bar">
            <div class="container">
                <img id="famuLogo" src="./img/famulogo2014.png" alt="FAMU Logo" />
            </div>
        </div>

        <!-- Nav Bar -->
        <nav
            id="pageNavBar"
            class="navbar navbar-default navbar-custom1 navbar-static-top"
            role="navigation">

            <div class="container">
                <div class="navbar-header">
                    <button
                        type="button"
                        class="navbar-toggle"
                        data-toggle="collapse"
                        data-target="#navbarCollapse">

                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./view1.html"><?= APP_NAME ?></a>
                </div>
            </div>
        </nav>
