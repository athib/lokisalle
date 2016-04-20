<?php

class Panier
{
	private $produits,
			$nbProduits,
			$prixTotalHT;

	/*
	 * Initialisation des valeurs du panier
	 */
	public function __construct()
	{
		$this->produits = array();
		$this->nbProduits = 0;
		$this->prixTotalHT = 0;
	}

	/*
	 * Retourne le nombre de produits contenus dans le panier
	 */
	public function getNbProduits()
	{
		return $this->nbProduits;
	}

	/*
	 * Renvoie le prix total TTC du panier
	 * Utilise la constante prédéfinie pour le taux de TVA à appliquer
	 */
	public function getPrixTotalTTC()
	{
		return $this->prixTotalHT * LKS_TVA;
	}

	/*
	 * Renvoie le tableau contenant tous les produits du panier
	 */
	public function getProduits()
	{
		return $this->produits;
	}

	/*
	 * Méthode qui teste si le panier est vide ou non
	 *
	 * TRUE => vide
	 * FALSE => au moins un article
	 */
	public function isEmpty()
	{
		return empty($this->produits);
	}

	/**
	 * Recherche un Produit dans le panier pour vérifier qu'il n'existe pas avant de l'ajouter
	 *
	 * Retourne TRUE si le produit n'existe pas et a bien été ajouté
	 * Retourne FALSE si le produit existe déjà
	 */
	public function ajouterProduit(Produit $produit)
	{
		$exists = false;

		foreach($this->produits as $produitPanier)
		{
			if($produitPanier->getInfo('id_produit') == $produit->getInfo('id_produit'))
			{
				$exists = true;
				break;
			}
		}

		if(!$exists)
		{
			$this->produits[] = $produit;
			$this->nbProduits++;
			$this->prixTotalHT += $produit->getPrix();

			return true;
		}
		else
			return false;
	}

	/**
	 * Recherche un Produit dans le panier pour le supprimer
	 *
	 * Retourne TRUE si le produit a été trouvé et bien supprimé
	 * Retourne FALSE si le produit n'existe pas dans le panier
	 */
	public function retirerProduit(Produit $produit)
	{
		if($this->nbProduits <= 0)
			return null;

		for($i=0; $i<$this->nbProduits; $i++)
		{
			if($this->produits[$i]->getInfo('id_produit') === $produit->getInfo('id_produit'))
			{
				$this->prixTotalHT -= $produit->getPrix();
				$this->nbProduits--;
				unset($this->produits[$i]);

				// on reset les index du tableau après un unset
				$this->produits = array_values($this->produits);

				return true;
			}
		}

		return false;
	}
}