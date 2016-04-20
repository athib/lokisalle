<?php

class panierController extends produitController
{
	private $post;

	public function __construct(Session $session)
	{
		parent::__construct($session);
		$this->post = array();
	}


	public function setPostInfos($post)
	{
		$this->post = $post;
	}

	public function hasDeleteAction()
	{
		return isset($_GET['action']) && $_GET['action'] == 'delete';
	}

	public function hasGetAction($name)
	{
		return isset($_GET['action']) && $_GET['action'] == $name;
	}

	public function getGetId()
	{
		if(!isset($_GET['id']))
			return null;
		elseif(!preg_match('#^\d+$#', $_GET['id']))
			return false;
		else
			return $_GET['id'];
	}

	public function hasViderAction()
	{
		return isset($_GET['action']) && $_GET['action'] == 'vider';
	}

	public function viderPanier()
	{
		unset($_SESSION['panier']);
		$this->session->createPanier();
	}

	public function hasCGV()
	{
		return isset($this->post['cgv']) && $this->post['cgv'] == 1;
	}
	
	/**
	 * Valide la commande
	 * - Ajout dans la table commande
	 * - Ajout dans la table details_commande
	 * - modifie l'etat des produits dans la table
	 */
	public function passerCommande()
	{
		$user = $this->session->getUser();
		$panier = $this->session->getPanier();
		$produits = $panier->getProduits();

		$montantTotal = $panier->getPrixTotalTTC();


		/******************************
		 * Etape 1
		 * Ajout dans la table commande
		 ******************************/
		$requete = "INSERT INTO commande (montant, id_membre, date)
					VALUES (:montant, :id_membre, NOW())";

		$params = array(
			':montant' => $montantTotal,
			':id_membre' => $user->getProperty('id_membre')
		);

		try
		{
			$this->session->getDatabase()->executeRequete($requete, $params);
		}
		catch(PDOException $e)
		{
			$this->session->addFlashes(LKS_FLASH_ERROR, 'Erreur insertion "commande" :' . $e->getMessage());
		}


		/**************************************
		 * Etape 2
		 * Ajout dans la table details_commande
		 **************************************/
		$id_commande = $this->session->getDatabase()->getLastId();

		$requete = "INSERT INTO details_commande (id_commande, id_produit)
					VALUES (:id_commande, :id_produit)";

		$params = array(
			':id_commande' => $id_commande,
			':id_produit' => ''
		);

		try
		{
			foreach($produits as $produit)
			{
				$params[':id_produit'] = $produit->getInfo('id_produit');
				$this->session->getDatabase()->executeRequete($requete, $params);
			}
		}
		catch(PDOException $e)
		{
			$this->session->addFlashes(LKS_FLASH_ERROR, 'Erreur insertion "details_commande" :' . $e->getMessage());
		}


		/**************************************
		 * Etape 3
		 * Rendre les produits indisponibles
		 * ie : etat passe de 0 à 1
		 **************************************/
		$requete = "UPDATE produit SET etat = 1 WHERE id_salle = :id_salle";
		$params = array(':id_salle' => '');

		try
		{
			foreach($produits as $produit)
			{
				$params[':id_salle'] = $produit->getSalle()->getInfo('id_salle');
				$this->session->getDatabase()->executeRequete($requete, $params);
			}
		}
		catch(PDOException $e)
		{
			$this->session->addFlashes(LKS_FLASH_ERROR, 'Erreur update etat dans "produit" :' . $e->getMessage());
		}

		return $id_commande;
	}

	public function getCommandeById($id)
	{
		$sql = "SELECT * FROM commande WHERE id_commande = :id_commande";
		$params = array(':id_commande' => $id);

		$stmt = $this->session->getDatabase()->executeRequete($sql, $params);

		$infos = $this->session->getDatabase()->getResult($stmt);

		return new Commande($infos);
	}

	public function envoyerMailRecap(Panier $panier, User $user, Commande $commande)
	{
		// Construction des en-têtes de l'email (format HTML, UTF8)
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset="utf-8"' . "\r\n";
		$headers .= 'From: ' . LKS_CONTACT . '<' . LKS_CONTACT . '>' . "\r\n";

		$message = 'Bonjour, ' . $user->getProperty('prenom') . "\r\n";
		$message .= "\r\n";
		$message .= 'Voici un récapitulatif de votre commande n°' . $commande->getInfo('id_commande') . ' d\'un montant de ' . $commande->getInfo('montant') . '€.' . "\r\n";
		$message .= "\r\n";

		foreach($panier->getProduits() as $produit)
		{
			$message .= 'Produit n°' . $produit->getInfo('id_produit') . ', Prix : ' . $produit->getPrix() . '€ du ' . $produit->getInfo('date_arrivee') . ' au ' . $produit->getInfo('date_depart') . "\r\n";
			$message .= 'Salle réservée : ' . $produit->getSalle()->getInfo('titre') . ', Ville : ' . $produit->getSalle()->getInfo('ville') . "\r\n";
			$message .= "\r\n";
		}

		$message .= 'Merci, et à bientôt sur Lokisalle !';

		if(mail($user->getProperty('email'), 'Votre commande sur Lokisalle', $message, $headers))
			$this->session->addFlashes(LKS_FLASH_OK, 'Un email récapitulatif a été envoyé à ' . $user->getProperty('email'));
		else
			$this->session->addFlashes(LKS_FLASH_ERROR, 'Erreur lors de l\'envoi du mail récapitulatif.');
	}
}