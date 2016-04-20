<?php

class gestionsallesController extends adminController
{
	public function getAllSalles()
	{
		$requete = "SELECT * FROM salle";

		$result = $this->session->getDatabase()->executeRequete($requete);

		$salles = array();

		while($infos = $this->session->getDatabase()->getResult($result))
		{
			$salles[] = new Salle($infos);
		}

		if(empty($salles))
			return null;

		return $salles;
	}

	public function afficherSalle()
	{
		$salle = '<div class="row"><div class="col-md-12">';

		$salle .= '</div></div>';
	}

	public function salleExists($id)
	{
		$requete = "SELECT * FROM salle WHERE id_salle = :id_salle";
		$params = array(':id_salle' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		return $this->session->getDatabase()->getResult($result);
	}

	public function deleteSalle($id)
	{
		/* DELETE PHOTO */
		$chemin = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE;

		$requete = "SELECT * FROM salle WHERE id_salle = :id_salle";
		$params = array(':id_salle' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);
		$infos = $this->session->getDatabase()->getResult($result);

		$chemin .= $infos->photo;

		unlink($chemin);



		$requete = "DELETE FROM salle WHERE id_salle = :id_salle";
		$params = array(':id_salle' => $id);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}

	public function getSalleById($id)
	{
		$requete = "SELECT * FROM salle WHERE id_salle = :id_salle";
		$params = array(':id_salle' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($result);

		if(!$infos)
			return null;

		return new Salle($infos);
	}

	public function getAllCategoriesFromDb()
	{
		$requete = "SELECT DISTINCT(categorie) FROM salle ORDER bY CAST(categorie AS CHAR) ASC";

		$resultat = $this->session->getDatabase()->executeRequete($requete);

		$categories = array();

		while($infos = $this->session->getDatabase()->getResult($resultat))
		{
			$categories[$infos->categorie] = $infos->categorie;
		}

		return $categories;
	}



	public function ajouterSalle(Salle $salle)
	{
		$requete = "INSERT INTO salle
					(titre, adresse, ville, cp, pays, latitude, longitude, categorie, capacite, description)
					VALUES
					(:titre, :adresse, :ville, :cp, :pays, :latitude, :longitude, :categorie, :capacite, :description)";

		$params = array(
			':titre' => $salle->getInfo('titre'),
			':adresse' => $salle->getInfo('adresse'),
			':ville' => $salle->getInfo('ville'),
			':cp' => $salle->getInfo('cp'),
			':pays' => $salle->getInfo('pays'),
			':latitude' => $salle->getInfo('latitude'),
			':longitude' => $salle->getInfo('longitude'),
			':categorie' => $salle->getInfo('categorie'),
			':capacite' => $salle->getInfo('capacite'),
			':description' => $salle->getInfo('description'),
		);

		$this->session->getDatabase()->executeRequete($requete, $params);

		return $this->session->getDatabase()->getLastId();
	}

	public function modifierSalle(Salle $salle)
	{
		$requete = "UPDATE salle SET
						titre = :titre,
						adresse = :adresse,
						ville = :ville,
						cp = :cp,
						pays = :pays,
						latitude = :latitude,
						longitude = :longitude,
						categorie = :categorie,
						capacite = :capacite,
						description = :description
					WHERE id_salle = :id_salle";

		$params = array(
			':titre' => $salle->getInfo('titre'),
			':adresse' => $salle->getInfo('adresse'),
			':ville' => $salle->getInfo('ville'),
			':cp' => $salle->getInfo('cp'),
			':pays' => $salle->getInfo('pays'),
			':latitude' => $salle->getInfo('latitude'),
			':longitude' => $salle->getInfo('longitude'),
			':categorie' => $salle->getInfo('categorie'),
			':capacite' => $salle->getInfo('capacite'),
			':description' => $salle->getInfo('description'),
			'id_salle' => $salle->getInfo('id_salle')
		);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}

	public function uploadPhoto($id, $titre)
	{
		$photo_bdd = 'images/salles/' . sprintf('%03d', $id) . '_salle_' . strtolower($titre) . '.jpg';
		$nom_photo = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . $photo_bdd;

		move_uploaded_file($_FILES['photo']['tmp_name'], $nom_photo);


		$requete = "UPDATE salle SET photo = :photo WHERE id_salle = :id_salle";

		$params = array(
			':photo' => $photo_bdd,
			':id_salle' => $id
		);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}
}