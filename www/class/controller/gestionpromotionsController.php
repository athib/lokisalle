<?php

class gestionpromotionsController extends adminController
{
	public function getAllPromos()
	{
		$requete = "SELECT * FROM promotion";

		$result = $this->session->getDatabase()->executeRequete($requete);

		$promotions = array();

		while($infos = $this->session->getDatabase()->getResult($result))
			$promotions[] = new Promotion($infos);

		return $promotions;
	}

	public function promoExists($code_promo)
	{
		$requete = "SELECT * FROM promotion WHERE code_promo = :code_promo";
		$params = array(':code_promo' => $code_promo);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		return $this->session->getDatabase()->getResult($result);
	}

	public function ajouterPromo($code, $value)
	{
		$requete = "INSERT INTO promotion (code_promo, reduction) VALUES (:code_promo, :reduction)";

		$params = array(
			':code_promo' => $code,
			':reduction' => $value
		);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}

	public function modifierPromo($id, $code, $valeur)
	{
		$requete = "UPDATE promotion
					SET code_promo = :code_promo,
						reduction = :reduction
					WHERE id_promo = :id_promo";

		$params = array(
			':id_promo' => $id,
			':code_promo' => $code,
			':reduction' => $valeur
		);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}

	public function deletePromo($id)
	{
		$requete = "DELETE FROM promotion WHERE id_promo = :id_promo";
		$params = array(':id_promo' => $id);

		$this->session->getDatabase()->executeRequete($requete, $params);
	}

	public function getPromoById($id)
	{
		$requete = "SELECT * FROM promotion WHERE id_promo = :id_promo";
		$params = array(':id_promo' => $id);

		$result = $this->session->getDatabase()->executeRequete($requete, $params);

		$infos = $this->session->getDatabase()->getResult($result);

		if(!$infos)
			return null;

		return new Promotion($infos);
	}
}