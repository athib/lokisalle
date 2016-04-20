<?php

class reservationdetailsController extends produitController
{
	public function hasGetIdValid()
	{
		if(isset($_GET['id_produit']))
		{
			if(preg_match('#^\d+$#', $_GET['id_produit']))
				return true;
		}

		return isset($_GET['id_produit']);
	}

	public function getGetId()
	{
		if(isset($_GET['id_produit']))
			return $_GET['id_produit'];
		else
			return null;
	}

	public function getAvisSalle(Salle $salle)
	{
		$requete = "SELECT * FROM avis WHERE id_salle = :id_salle ORDER BY date DESC";
		$params = array(':id_salle' => $salle->getInfo('id_salle'));

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$avis = array();
		$index = 0;
		while($infos = $this->session->getDatabase()->getResult($result))
		{
			$avis[$index] = new Avis($infos);
			$index++;
		}

		return $avis;
	}

	public function getMembre(Avis $avis)
	{
		$requete = "SELECT * FROM membre WHERE id_membre = :id_membre";
		$params = array(':id_membre' => $avis->getInfo('id_membre'));

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($result);

		if(!$infos)
			return null;

		return new User($infos);
	}

	public function getSuggestions(Produit $produit)
	{
		$requete = "SELECT *
					FROM produit
					NATURAL JOIN salle
					WHERE date_arrivee > CURDATE()
						AND ville = :ville
						AND id_salle != :id_salle
						AND etat = 0
					ORDER BY date_arrivee ASC
					LIMIT 0,3";

		$params = array(
			':ville' => $produit->getSalle()->getInfo('ville'),
			':id_salle' => $produit->getInfo('id_salle')
		);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$suggestions = array();

		while($infos = $this->session->getDatabase()->getResult($result))
		{
			$salle = $this->getSalleById($infos->id_salle);
			$suggestions[] = new Produit($infos, $salle);
		}

		return $suggestions;
	}

	public function hasCommentSent()
	{
		return isset($_POST['post_commentaire']);
	}

	public function registerComment($id_salle, $commentaire, $note)
	{
		$requete = "INSERT INTO avis
					(id_membre, id_salle, commentaire, note, date)
					VALUES
					(:id_membre, :id_salle, :commentaire, :note, NOW())";

		$params = array(
			':id_membre' => $this->session->getUser()->getProperty('id_membre'),
			':id_salle' => $id_salle,
			':commentaire' => $commentaire,
			':note' => $note
		);


		$this->session->getDatabase()->executeRequete($requete, $params);
	}

	public function hasAjoutPanier()
	{
		return isset($_POST['ajout_panier']);
	}
}