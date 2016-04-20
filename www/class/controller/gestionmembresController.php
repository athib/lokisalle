<?php

class gestionmembresController extends adminController
{
	/*
	 * Retourne un tableau contenant tous les membres de la base de données sous forme d'objet "User"
	 */
	public function getAllMembres()
	{
		$requete = "SELECT * FROM membre";

		$result = $this->session->getDatabase()->executeRequete($requete);

		$membres = array();

		while($infos = $this->session->getDatabase()->getResult($result))
			$membres[] = new User($infos);

		return $membres;
	}

	/*
	 * Supprime de la base de données l'utilisateur correspondant à l'id passé en paramètre
	 * Retourne le résultat de la requete, pour vérifier si l'utilisateur existait et a été supprimé
	 * ou si l'id ne correspond à aucun utilisateur, et donc la requete échoue.
	 */
	public function deleteUser($id)
	{
		$requete = "DELETE FROM membre WHERE id_membre = :id_membre";
		$params = array(':id_membre' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		return $this->session->getDatabase()->getNbLignes($result);
	}

	/*
	 * Met à jour le statut d'un membre dont l'id est donné en paramètre.
	 * Passe à 1 si vaut 0
	 * Et inversement
	 */
	public function updateStatus($id)
	{
		// On récupère l'utilisateur

		$requete = "SELECT * FROM membre WHERE id_membre = :id_membre";
		$params = array(':id_membre' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);
		$infos = $this->session->getDatabase()->getResult($result);

		$membre = new User($infos);

		// on vérifie son statut actuel pour le changer ensuite
		if($membre->getProperty('statut') == 0)
			$new_statut = 1;
		else
			$new_statut = 0;

		// Update bdd
		$requete = "UPDATE membre SET statut = :statut WHERE id_membre = :id_membre";
		$params = array(
			':statut' => $new_statut,
			':id_membre' => $id
		);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}
}