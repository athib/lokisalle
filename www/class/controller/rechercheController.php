<?php

class rechercheController extends formController
{
	public function isAfterToday($date)
	{
		$date_saisie = new DateTime($date);
		$date_actuelle = new DateTime();

		return $date_saisie->getTimestamp() >= $date_actuelle->getTimestamp();
	}

	public function getAllVillesFromDb()
	{
		$requete = "SELECT DISTINCT(ville) FROM salle ORDER by ville ASC";

		$resultat = $this->session->getDatabase()->executeRequete($requete);

		$villes = array();

		while($infos = $this->session->getDatabase()->getResult($resultat))
		{
			$villes[$infos->ville] = $infos->ville;
		}

		return $villes;
	}

	public function getSalleByVille($id, $ville)
	{
		$requete = "SELECT *
                	FROM salle
                	WHERE id_salle = :id_salle
                	AND ville = :ville";

		$params = array(
			':id_salle' => $id,
			':ville' => $ville
		);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($result);

		if($infos)
			return new Salle($infos);
		else
			return null;
	}

	public function getProduitsByDateAndVille($date, $ville)
	{
		$requete = "SELECT *
					FROM produit
					WHERE date_arrivee > :date_arrivee
					AND etat = 0
					ORDER BY date_arrivee ASC";

		$params = array(':date_arrivee' => $date);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$produits = array();

		while($infos = $this->session->getDatabase()->getResult($result))
		{
			$salle = $this->getSalleByVille($infos->id_salle, $ville);
			if($salle == null)
				continue;
			$produits[] = new Produit($infos, $salle);
		}

		return $produits;
	}

	public function afficheProduits3x3(array $produits)
	{
		$controller_produit = new produitController($this->session);

		$content = '<div class="row">';

		// compteur permettant la gestion des lignes toutes les 3 colonnes
		$compteur = 1;
		foreach ($produits as $produit)
		{
			$content .= $controller_produit->afficheProduit($produit);

			if($compteur%3 == 0)
			{
				$content .= '</div>';
				$content .= '<div class="row">';
			}

			$compteur++;
		}

		$content .= '</div>';

		return $content;
	}
}