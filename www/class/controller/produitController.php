<?php

class produitController
{
	protected $session;

	public function __construct(Session $session)
	{
		$this->session = $session;
	}


	private function tronquerTexte($text)
	{
		$cut = substr($text, 0, LKS_FICHE_DESC_LIMIT);
		$lastSpacePos = strrpos($cut, ' ');
		$text = substr($cut, 0, $lastSpacePos - 1);
		$text .= '...';

		return $text;
	}

	public function getSalleById($id)
	{
		$requete = "SELECT *
                	FROM salle
                	WHERE id_salle = :id_salle";

		$params = array(':id_salle' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($result);

		return new Salle($infos);
	}

	public function afficheProduit(Produit $produit)
	{
		$vignette = '<div class="col-xs-12 col-sm-4 col-md-4 portfolio-item">';
		$vignette .= '<img class="img-responsive" src="' . $produit->getSalle()->getInfo('photo') . '" alt="" />';

		if($produit->hasPromo())
			$vignette .= '<img class="img-responsive img-promo" src="images/promotion.png" alt="En promotion"/>';

		$vignette .= '<h3>Salle ' . $produit->getSalle()->getInfo('titre') . '</h3>';
		$vignette .= '<h5>À ' . $produit->getSalle()->getInfo('ville') . '</h5>';
		$vignette .= '<h6>du ' . $produit->getInfo('date_arrivee') . ' au ' . $produit->getInfo('date_depart') . '</h6>';
		$vignette .= '<p>' . $this->tronquerTexte($produit->getSalle()->getInfo('description')) . '</p>';
		//$vignette .= '<p><small><a href="reservation_details.php?id_produit=' . $produit->getInfo('id_produit') . '">&Gt; Voir la fiche détaillée</a></small></p>';
		$vignette .= '<div><p><a href="reservation_details.php?id_produit=' . $produit->getInfo('id_produit') . '">Voir la fiche</a></p>';

		if($this->session->hasUser() && $produit->isAvailable())
		{
			//$vignette .= '<p><small><a href="panier.php?action=ajouter&id=' . $produit->getInfo('id_produit') . '">&Gt; Ajouter au panier</a></small></p>';
			$vignette .= '<p><a href="panier.php?action=ajouter&id=' . $produit->getInfo('id_produit') . '">Ajouter au panier</a></p>';
		}

		$vignette .= '</div>'; // fermeture div boutons
		$vignette .= '</div>';

		return $vignette;
	}

	public function getProduitById($id)
	{
		$requete = "SELECT *
                	FROM produit
                	WHERE id_produit = :id_produit";

		$params = array(':id_produit' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($result);

		if(!$infos)
			return null;

		$salle = $this->getSalleById($infos->id_salle);

		return new Produit($infos, $salle);
	}
}