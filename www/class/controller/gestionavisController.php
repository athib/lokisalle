<?php

/*
 * Controller pour la page de gestion des avis
 * (Administrateur uniquement)
 */
class gestionavisController extends adminController
{
	/*
	 * Récupère tous les Avis contenus dans la base de données.
	 * Tri des résultats en fonction des paramètres "champ" et "tri"
	 *
	 * Retourne un tableau d'objets Avis
	 */
	public function getAllAvisFromDb($champ_a_trier, $ordre_tri)
	{
		// formatage pour la requete, afin d'éviter l'erreur d'ambiguité

		if($champ_a_trier == 'date')
			$champ_a_trier = 'avis.date';

		$requete = "SELECT 	id_avis,
							id_membre,
							id_salle,
							commentaire,
							note,
							DATE_FORMAT(date, '%d/%m/%Y à %H:%i:%s') AS date
 					FROM avis
 					ORDER BY $champ_a_trier $ordre_tri";

		try
		{
			$result = $this->session->getDatabase()->executeRequete($requete);
		}
		catch(PDOException $e)
		{
			$this->session->addFlashes(LKS_FLASH_ERROR, 'Le champ a trier est incorrect.');
			$this->session->redirect('gestion_avis');
			exit();
		}

		$avis = array();

		while($infos = $this->session->getDatabase()->getResult($result))
		{
			$avis[] = new Avis($infos);
		}

		return $avis;
	}

	/*
	 * Supprime l'avis ayant l'id passé en paramètre de la base de données.
	 */
	public function deleteAvis($id)
	{
		$requete = "DELETE FROM avis WHERE id_avis = :id_avis";
		$params = array(':id_avis' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		return $this->session->getDatabase()->getNbLignes($result);
	}
}