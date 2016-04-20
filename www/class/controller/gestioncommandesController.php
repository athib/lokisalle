<?php

class gestioncommandesController extends adminController
{
	/*
	 * Récupère toutes les commandes de la bases de données
	 * Retour un tableau d'objets Commande
	 */
	public function getAllCommandesFromDb($champ_a_trier, $ordre_tri)
	{
		// Formatage du nom du champ pour le tri (évite l'erreur d'ambiguité)
		if($champ_a_trier == 'date')
			$champ_a_trier = 'commande.date';

		$requete = "SELECT  id_commande,
							montant,
							id_membre,
							DATE_FORMAT(date, '%d/%m/%Y à %H:%i:%s') AS date
					FROM commande
					ORDER BY $champ_a_trier $ordre_tri";

		try
		{
			$resultat = $this->session->getDatabase()->executeRequete($requete);
		}
		catch(PDOException $e)
		{
			$this->session->addFlashes(LKS_FLASH_ERROR, 'Le champ a trier est incorrect.');
			$this->session->redirect('gestion_commandes');
			exit();
		}

		$commandes = array();

		while($infos = $this->session->getDatabase()->getResult($resultat))
		{
			// ajoute un lien pour rendre le numéro de commande cliquable
			$infos->id_commande = '<a href="gestion_commandes.php?id_commande=' . $infos->id_commande . '">' . $infos->id_commande . '</a>';
			$commandes[] = new Commande($infos);
		}

		return $commandes;
	}

	/*
	 * Retourne le montant total des commandes du site
	 */
	public function getTotalCA()
	{
		$requete = "SELECT SUM(montant) AS total FROM commande";

		$resultat = $this->session->getDatabase()->executeRequete($requete);

		$infos = $this->session->getDatabase()->getResult($resultat);

		return $infos->total;
	}

	/*
	 * Récupère la commande correspondant à l'id passé en paramètre. Retourne :
	 * - l'objet Commande si elle existe
	 * - null si non
	 */
	public function getCommandeById($id)
	{
		$requete = "SELECT * FROM commande WHERE id_commande = :id_commande";
		$params = array(':id_commande' => $id);

		$resultat = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($resultat);

		if(!$infos)
			return null;

		return new Commande($infos);
	}

	/*
	 * Retourne les détails d'une commande passée en paramètre, triée en fonction des paramètres donnés
	 */
	public function getDetailsCommande(Commande $commande, $champ_a_trier, $ordre_tri)
	{
		// Formatage du champ à trier pour éviter l'ambiguité lors de la requete sur plusieurs tables
		switch($champ_a_trier)
		{
			case 'id_commande' :
				$champ_a_trier = 'commande.id_commande';
				break;

			case 'date' :
				$champ_a_trier = 'commande.date';
				break;

			case 'montant' :
				$champ_a_trier = 'produit.prix';
				break;

			case 'id_produit' :
				$champ_a_trier = 'produit.id_produit';
				break;

			case 'id_salle' :
				$champ_a_trier = 'salle.id_salle';
				break;

			case 'titre' :
				$champ_a_trier = 'salle.titre';
				break;

			case 'ville' :
				$champ_a_trier = 'salle.ville';
				break;
		}


		$requete = "SELECT 	id_commande,
							montant,
							DATE_FORMAT(date, '%d/%m/%Y') AS date,
							id_membre,
							pseudo,
							details_commande.id_produit,
							produit.id_salle,
							titre,
							salle.ville
					FROM details_commande
					  NATURAL JOIN commande
					  NATURAL JOIN membre
					  LEFT JOIN produit ON details_commande.id_produit = produit.id_produit
					  INNER JOIN salle ON produit.id_salle = salle.id_salle
					WHERE id_commande = :id_commande
					ORDER BY $champ_a_trier $ordre_tri";

		$params = array(':id_commande' => $commande->getInfo('id_commande'));

		$resultat = $this->session->getDatabase()->executeRequete($requete, $params);

		$details_commande = array();

		while($infos = $this->session->getDatabase()->getResult($resultat))
		{
			$details_commande[] = $infos;
		}


		return $details_commande;
	}
}