<?php
require_once('includes/init.inc.php');

$produits = (new reservationController($session))->getProduitsDispo();



require_once('includes/header.inc.php'); ?>



    <div class="col-md-12">



<div class="row">
    <div class="col-md-12 encadre">
        <p>
            Besoin d'une salle pour organiser une réunion ou un séminaire ? Ou bien d'une salle pour faire la fête ? Et ça partout en France ?<br>
            Ne cherchez plus, Lokisalle est l'outil idéal pour vous simplifier la tâche !
        </p>
        <p>
            Plusieurs types de salles sont disponibles, et ce dans différentes capacités. De la petite salle de réunion, à la grande salle de fête pour 100 personnes, Lokisalle couvre tous les besoins.
        </p>
        <p>
            Pour chaque salle réservée, un assistant sera disponible pour vous apporter son aide sur place (configurer un vidéo projecteur, appeler un taxi, conseils d'activités à proximité).
        </p>
        <p>
            Sélectionnez la ou les salles qui vous intéressent, validez votre commande, puis adressez-nous votre paiement.
        </p>
        <p>
            La réservation d'une salle nécessite avant tout une inscription sur notre site.
        </p>
        <p>
            Bonne visite !
        </p>
    </div>
</div>


    </div>

<div class="row">
    <div class="col-md-12">
        <?php
        $controller = new reservationController($session);

        $produits = $controller->getProduitsDispo(0,3);

        if($produits)
        {
            echo '<hr/>';
            echo '<h2 class="text-center">Nos 3 dernières offres</h2>';
            foreach ($produits as $produit)
            {
                echo $controller->afficheProduit($produit);
            }
        }
        else
        {
            echo '<h3 class="text-center">Oups, rupture de stock ! :-(</h3>';
            echo '<p>Plus aucune salle disponible...</p>';
        }

        ?>
    </div>
</div>

<?php require_once('includes/footer.inc.php'); ?>
