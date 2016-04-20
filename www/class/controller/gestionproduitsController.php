<?php

class gestionproduitsController extends adminController
{
	/*
	 * Retourne un tableau contenant tous les Produits présents dans la base de données
	 * Tri selon les valeurs passées en paramètre
	 * Retourne NULL si aucun produit trouvé
	 */
	public function getAllProduits($champ_a_trier, $ordre_tri)
	{
		// Formatage du nom du champ à trier pour éviter les erreurs d'ambiguité en SQL
		if($champ_a_trier == 'date_arrivee')
			$champ_a_trier = 'produit.date_arrivee';
		elseif($champ_a_trier == 'date_depart')
			$champ_a_trier = 'produit.date_depart';

		$requete = "SELECT 	id_produit,
							DATE_FORMAT(date_arrivee, '%d/%m/%Y') AS date_arrivee,
							DATE_FORMAT(date_depart, '%d/%m/%Y') AS date_depart,
							id_salle,
							titre,
							ville,
							code_promo,
							prix,
							etat
					FROM produit
					NATURAL JOIN salle
					LEFT JOIN promotion ON produit.id_promo = promotion.id_promo
					ORDER BY $champ_a_trier $ordre_tri";

		try
		{
			$result = $this->session->getDatabase()->executeRequete($requete);
		}
		catch(PDOException $e)
		{
			$this->session->addFlashes(LKS_FLASH_ERROR, 'Le champ a trier est incorrect.');
			$this->session->redirect('gestion_produits');
			exit();
		}


		$produits = array();

		while($infos = $this->session->getDatabase()->getResult($result))
		{
			$salle = $this->getSalleById($infos->id_salle);
			$produits[] = new Produit($infos, $salle);
		}

		if(empty($produits))
			return null;

		return $produits;
	}

	/*
	 * Récupère la salle correspondant à l'id donné en paramètre
	 * Retourne une instance de Salle
	 * Ou NULL si la salle n'existe pas
	 */
	private function getSalleById($id)
	{
		$requete = "SELECT * FROM salle WHERE id_salle = :id_salle";
		$params = array(':id_salle' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($result);

		if(!$infos)
			return null;

		return new Salle($infos);
	}

	/*
	 * Récupère le produit correspondant à l'id donné en paramètre
	 * Retourne une instance de Produit
	 * Ou NULL si le produit n'existe pas
	 */
	public function getProduitById($id)
	{
		$requete = "SELECT * FROM produit WHERE id_produit = :id_produit";
		$params = array(':id_produit' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($result);

		if(!$infos)
			return null;

		return new Produit($infos);
	}

	/*
	 * Récupère toutes les salles de la base de données
	 * Renvoi un tableau associatif contenant les champs demandés et leur valeur associée
	 * Permet un affichage plus explicite dans le menu déroulant pour ajouter un produit.
	 */
	public function getAllSallesFromDb()
	{
		$requete = "SELECT id_salle, titre, ville FROM salle GROUP BY titre ORDER by titre ASC";

		$resultat = $this->session->getDatabase()->executeRequete($requete);

		$salles = array();

		while($infos = $this->session->getDatabase()->getResult($resultat))
		{
			$salles[$infos->id_salle] = $infos->id_salle . ' - ' . $infos->titre . ' (' . $infos->ville . ')';
		}

		return $salles;
	}

	/*
	 * Récupère touts les produits de la base de données
	 * Renvoi un tableau associatif contenant les champs demandés et leur valeur associée
	 * Permet un affichage plus explicite dans le menu déroulant pour ajouter un produit.
	 */
	public function getAllPromoFromDb()
	{
		$requete = "SELECT id_promo, code_promo FROM promotion ORDER by code_promo ASC";

		$resultat = $this->session->getDatabase()->executeRequete($requete);

		$promotions = array('0' => 'Aucune promotion');

		while($infos = $this->session->getDatabase()->getResult($resultat))
		{
			$promotions[$infos->id_promo] = $infos->code_promo;
		}

		return $promotions;
	}

	/*
	 * Ajoute un nouveau produit dans la base de données
	 * Les informations du produit sont passées en paramètre et viennent du formulaire
	 */
	public function ajouterProduit($post)
	{
		if($post['id_promo'] == 0)
			$post['id_promo'] = null;

		$requete = "INSERT INTO produit (date_arrivee, date_depart, id_salle, id_promo, prix, etat)
					VALUES (:date_arrivee, :date_depart, :id_salle, :id_promo, :prix, :etat)";

		$params = array(
			':date_arrivee' => $post['date_arrivee'],
			':date_depart' => $post['date_depart'],
			':id_salle' => $post['id_salle'],
			'id_promo' => $post['id_promo'],
			':prix' => $post['prix'],
			':etat' => $post['etat']
		);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}

	/*
	 * Modifie un produit dans la base de données
	 * Les informations du produit sont passées en paramètre et viennent du formulaire
	 */
	public function modifierProduit($post)
	{
		if($post['id_promo'] == 0)
			$post['id_promo'] = null;

		$requete = "UPDATE produit SET
						date_arrivee = :date_arrivee,
						date_depart = :date_depart,
						id_salle = :id_salle,
						id_promo = :id_promo,
						prix = :prix,
						etat = :etat
					WHERE id_produit = :id_produit";

		$params = array(
			':date_arrivee' => $post['date_arrivee'],
			':date_depart' => $post['date_depart'],
			':id_salle' => $post['id_salle'],
			'id_promo' => $post['id_promo'],
			':prix' => $post['prix'],
			':etat' => $post['etat'],
			':id_produit' => $post['id_produit']
		);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}

	/*
	 * Vérifie qu'un produit existe dans la base de données
	 * L'id produit est passé en paramètre
	 */
	public function produitExists($id)
	{
		$requete = "SELECT * FROM produit WHERE id_produit = :id_produit";
		$params = array(':id_produit' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		return $this->session->getDatabase()->getResult($result);
	}

	/*
	 * Supprime le produit correspondant à l'id donné en paramètre
	 */
	public function deleteProduit($id)
	{
		$requete = "DELETE FROM produit WHERE id_produit = :id_produit";
		$params = array(':id_produit' => $id);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}

	/*
	 * Vérifie la disponibilité d'une salle sur des dates données
	 */
	public function salleOccupee($id_produit, $id_salle, $date_debut, $date_fin)
	{
		// On recherche si une salle spécifiée en paramètre existe dans la base
		// Et si cette dernière est déjà réservée pour des dates qui chevauchent celles données
		$requete = "SELECT *
					FROM produit
					WHERE id_salle = :id_salle
						AND (
								(date_arrivee BETWEEN '$date_debut' AND '$date_fin')
								OR (date_depart BETWEEN '$date_debut' AND '$date_fin')
						)
						OR (
							date_arrivee > '$date_debut' AND date_depart < '$date_fin'
						)";

		$params = array(':id_salle' => $id_salle);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($result);

		// Si résultat vide, la salle est dispo, on retourne null
		if(!$infos)
			return null;
		// cas où la modificaiton concerne un autre champ que les dates (= pas de modif-
		elseif($infos && $infos->id_produit == $id_produit && $infos->date_arrivee == $date_debut && $infos->date_depart == $date_fin)
			return null;
		// La salle est disponible, on retourne une instance du produit déjà existant pour afficher une erreur
		else
			return new Produit($infos);
	}
}