<?php

// On récupère tous les produits disponibles
$produits_carousel = (new reservationController($session))->getProduitsDispo();

?>


/* AFFICHAGE DU CAROUSSEL AVEC LES PHOTOS DES SALLES ENCORE DISPONIBLES */

<div id="carousel" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <?php
        $index = 0;
        foreach($produits_carousel as $produit_carousel)
        {
	        echo '<li data-target="#carousel" data-slide-to="0"' . ($index==0 ? ' class="active"' : '') . '></li>';
	        $index++;
        }
        ?>

	</ol>

	<!-- Wrapper for slides -->
	<div class="carousel-inner" role="listbox">
	
		<?php
		$index = 0;
		foreach($produits_carousel as $produit_carousel)
		{
			echo '<div class="item' . ($index==0 ? ' active' : '') . '">';
			echo '<img src="' . RACINE_SITE . $produit_carousel->getSalle()->getInfo('photo') . '" alt="Salle ' . $produit_carousel->getSalle()->getInfo('titre') . '">';
			echo '<div class="carousel-caption">';
			echo ' </div>';
			echo '</div>';
			$index++;
		}
		?>
	
	</div>

</div>