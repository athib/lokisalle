<?php
require_once('includes/init.inc.php');


/* INITIALISATION DES VARIABLES */

$controller = new reservationController($session);
$produits = $controller->getProduitsDispo();
$panier = $session->getPanier();

/* SI DES PRODUITS SONT DISPONIBLES ON LES AFFICHE */
if($produits)
{
    $content = '<div class="row">';

    // compteur permettant la gestion des lignes toutes les 3 colonnes
    $compteur = 1;
    foreach ($produits as $produit)
    {
        $content .= $controller->afficheProduit($produit);

        if($compteur%3 == 0)
        {
            $content .= '</div>';
            $content .= '<div class="row">';
        }

        $compteur++;
    }

    $content .= '</div>';
}
else
{
    $content .= '<h2 class="text-center">Oups, rupture de stock ! :-(</h2>';
    $content .= '<p>Plus aucune salle disponible...</p>';
}




require_once('includes/header.inc.php');

?>

<div class="row">
    <div class="col-md-12">
        <h2 class="text-center">Nos salles disponibles</h2>
    </div>
</div>


<?php echo $content; ?>



<?php require_once('./includes/footer.inc.php'); ?>
