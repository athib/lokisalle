<?php

class reservationController extends produitController
{
	public function getProduitsDispo($limiteDebut = -1, $limiteFin = -1)
	{
		$requete = "SELECT *
                	FROM produit
                	WHERE etat = 0 AND date_arrivee >= CURDATE()
                	ORDER BY date_arrivee DESC";

		if($limiteDebut != -1 && $limiteFin != -1)
		{
			$requete .= " LIMIT $limiteDebut,$limiteFin";
		}

		$result = $this->session->getDatabase()->executeRequete($requete);

		$produits = array();
		$indexProduit = 0;

		while($infos = $this->session->getDatabase()->getResult($result))
		{
			$salle = $this->getSalleById($infos->id_salle);
			$produits[$indexProduit] = new Produit($infos, $salle);
			$indexProduit++;
		}

		return $produits;
	}
}