
<!-- Navigation -->
<nav class="navbar navbar-inverse">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo RACINE_SITE; ?>index.php">Accueil</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <?php
                    if($session->hasUser())
                    {
                        echo '<li><a href="' . RACINE_SITE . 'reservation.php"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Liste des salles</a></li>';
                        echo '<li><a href="' . RACINE_SITE . 'recherche.php"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Rechercher</a></li>';
                        echo '<li><a href="' . RACINE_SITE . 'profil.php"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Mon profil</a></li>';
                        echo '<li><a href="' . RACINE_SITE . 'panier.php"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Mon panier (' . $session->getPanier()->getNbProduits() . ')</a></li>';
                        if($session->getUser()->isAdmin())
                        {
                            ?>
                            <li class="dropdown">
                                <a href="<?php echo RACINE_SITE; ?>admin/index.php" class="dropdown-toggle"  data-hover="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-wrench aria-hidden="true"></span> Administration <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo RACINE_SITE; ?>admin/gestion_salles.php">Gestion des salles</a></li>
                                    <li><a href="<?php echo RACINE_SITE; ?>admin/gestion_produits.php">Gestion des produits</a></li>
                                    <li><a href="<?php echo RACINE_SITE; ?>admin/gestion_avis.php">Gestion des avis</a></li>
                                    <li><a href="<?php echo RACINE_SITE; ?>admin/gestion_promotions.php">Gestion des promotions</a></li>
                                    <li><a href="<?php echo RACINE_SITE; ?>admin/gestion_commandes.php">Gestion des commandes</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="<?php echo RACINE_SITE; ?>admin/gestion_membres.php">Gestion des membres</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="<?php echo RACINE_SITE; ?>admin/statistiques.php"><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> Statistiques</a></li>
                                    <li><a href="<?php echo RACINE_SITE; ?>admin/envoi_newsletter.php"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Envoyer une newsletter</a></li>
                                </ul>
                            </li>
                            <?php
                        }
                        echo '<li><a href="' . RACINE_SITE . 'deconnexion.php"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Se déconnecter</a></li>';
                    }
                    else
                    {
                        echo '<li><a href="' . RACINE_SITE . 'reservation.php"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Liste des salles</a></li>';
                        echo '<li><a href="' . RACINE_SITE . 'recherche.php"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Rechercher</a></li>';
                        echo '<li><a href="' . RACINE_SITE . 'connexion.php"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Se connecter</a></li>';
                        echo '<li><a href="' . RACINE_SITE . 'inscription.php"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> S\'inscrire</a></li>';
                    }
                ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>
