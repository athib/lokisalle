<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Réservation de salles pour vos réunions, fêtes, dans différentes villes de France">
    <meta name="author" content="">

    <title>Lokisalle - Réservez vos salles de Réunions ou de Fêtes en France</title>

    <!-- JQuery UI Core CSS -->
    <link href="<?php echo RACINE_SITE; ?>js/jquery-ui/jquery-ui.css" rel="stylesheet">

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo RACINE_SITE; ?>css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo RACINE_SITE; ?>css/my_css.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div class="container col-md-12">
        <header>
            <div class="navbar-entete">
                <h1 class="my-title">Lokisalle : <em>Le site de réservation pour vos salles quelque soit l'occasion !</em></h1>
            </div>

            <div class="banniere">
                <?php require_once('banniere.inc.php'); ?>
            </div>
            <div>
                <?php require_once('navbar.inc.php'); ?>
            </div>
        </header>

        <section>
            <?php

                /* AFFICHAGE DES MESSAGES (ERREUR OU VALIDATION) S'IL Y EN A */

                if($session->hasFlashes())
                {
                    $flashes = $session->getFlashes();

                    foreach ($flashes as $type => $message)
                    {
                        echo '<div class="alert alert-' . $type . ' fade in">';
                        echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                        echo "<ul>$message</ul>";
                        echo '</div>';
                    }
                }
            ?>
