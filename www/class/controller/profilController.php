<?php

/**
 * Class profilController
 * Permet la gestion de toutes les actions effectuées sur la page de profil de l'utilisateur
 */
class profilController extends formController
{
	public function hasGetAction($name)
	{
		return isset($_GET['action']) && $_GET['action'] == $name;
	}

	// Met à jour la base de données avec les infos du formulaire
	public function updateDatabase($id, $post)
	{
		$sql = "UPDATE membre
				SET pseudo = :pseudo,
					email = :email,
					prenom = :prenom,
					nom = :nom,
					sexe = :sexe,
					adresse = :adresse,
					ville = :ville,
					cp = :cp,
					pays = :pays,
					newsletter = :newsletter";

		if(!empty($post['password']))
			$sql .= ", password = :password";

		$sql .= " WHERE id_membre = :id";

		$params = (array(
			':id' => $id,
			':pseudo' => $post['pseudo'],
			':email' => $post['email'],
			':prenom' => $post['prenom'],
			':nom' => $post['nom'],
			':sexe' => $post['sexe'],
			':adresse' => $post['adresse'],
			':ville' => $post['ville'],
			':cp' => $post['cp'],
			':pays' => $post['pays'],
			':newsletter' => $post['newsletter']
		));

		if(!empty($post['password']))
			$params[':password'] = password_hash($post['password'], PASSWORD_BCRYPT);

		$this->session->getDatabase()->executeRequete($sql, $params);
	}

	// Renvoie un tableau d'objets Commande de l'utilisateur $user, NULL si aucune
	public function getCommandes(User $user)
	{
		$requete = "SELECT * FROM commande WHERE id_membre = :id_membre ORDER BY date DESC";

		$params = array('id_membre' => $user->getProperty('id_membre'));

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$commandes = array();

		while($infos = $this->session->getDatabase()->getResult($result))
		{
			if($infos != null)
				$commandes[] = new Commande($infos);
		}

		return $commandes;
	}

	public function hasRequestedFacture()
	{
		return isset($_GET['id_commande']);
	}

	public function getFactureIdRequested()
	{
		if(!preg_match('#^\d+$#', $_GET['id_commande']))
			return false;
		else
			return $_GET['id_commande'];
	}

	// génération et construction de la facture au format PDF
	public function generatePDF(User $user, $id)
	{
		// On récupère la commande et les produits
		
		$requete = "SELECT * FROM commande WHERE id_commande = :id_commande";
		$params = array(':id_commande' => $id);
		$result = $this->session->getDatabase()->executeRequete($requete, $params);
		$infos = $this->session->getDatabase()->getResult($result);

		$commande = new Commande($infos);


		$prod_ctrl = new produitController($this->session);
		$requete = "SELECT id_produit FROM details_commande WHERE id_commande = :id_commande";
		$params = array(':id_commande' => $commande->getInfo('id_commande'));
		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$produits = array();

		while($infos = $this->session->getDatabase()->getResult($result))
			$produits[] = $prod_ctrl->getProduitById($infos->id_produit);


		// Utilisation de la bibliothèque FPDF

		$pdf = new MyPDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();


		// Infos de facturation

		$pdf->SetFont('Arial','',10);

		$pdf->Cell(0, 5, 'Nom : ' . $user->getProperty('nom'), 0, 0);
		$pdf->setX(150);
		$pdf->Cell(40, 5, 'Facture numero : ' . $commande->getInfo('id_commande'),0,1);

		$pdf->Cell(0, 5, 'Prenom : ' . $user->getProperty('prenom'), 0, 0);
		$pdf->setX(150);
		$pdf->Cell(40, 5, 'Montant : ' . $commande->getInfo('montant') . ' euros',0,1);

		$pdf->Cell(0, 5, 'Adresse : ' . $user->getProperty('adresse'), 0, 1);

		$pdf->Cell(0, 5, 'Code Postal : ' . $user->getProperty('cp'), 0, 0);
		$pdf->setX(150);
		$pdf->Cell(40, 5, 'Lokisalle', 0, 1);

		$pdf->Cell(0, 5, 'Ville : ' . $user->getProperty('ville'), 0, 0);
		$pdf->setX(150);
		$pdf->Cell(40, 5, '1 Rue Boswellia', 0, 1);

		$pdf->Cell(0, 5, 'Pays : ' . $user->getProperty('pays'), 0, 0);
		$pdf->setX(150);
		$pdf->Cell(40, 5, '75000 Paris', 0, 1);

		$pdf->Ln(20);


		// Détail de la commande

		$pdf->SetFont('Arial','B',15);
		$pdf->Cell(65);
		$pdf->Cell(60,10,'Salle(s) reservee(s)',0,0,'C');
		$pdf->Ln(20);


		$pdf->SetFont('Arial','',10);

		foreach($produits as $produit)
		{
			$pdf->setFont('Arial', '', 10);
			$pdf->Cell(0,10, 'Salle ' . $produit->getSalle()->getInfo('titre') . ' dans la ville de ' . $produit->getSalle()->getInfo('ville') . ' du ' . $produit->getInfo('date_arrivee') . ' au ' . $produit->getInfo('date_depart'));
			$pdf->ln();
		}


		$pdf->SetFont('Arial','I',12);

		$pdf->ln(20);
		$pdf->Cell(0, 10, 'Lokisalle vous remercie pour votre commande. A tres vite !', 0, 0, 'C');
		$pdf->ln();


		$pdf->Output();
	}
}