<?php

class Produit extends Entity
{
	private $id_produit,
			$date_arrivee,
			$date_depart,
			$id_salle,
			$id_promo,
			$prix,
			$etat; // 1 - produit reservé, 0 - produit disponible

	private $salle;

	private $promotion;


	public function __construct($data, Salle $salle = null)
	{
		parent::__construct($data);

		foreach($data as $key => $value)
		{
			$this->$key = $value;
		}

		if(!$salle)
			$this->salle = $this->loadSalle();
		else
			$this->salle = $salle;

		$this->promotion = $this->loadPromotion();
	}

	public function loadSalle()
	{
		$requete = "SELECT * FROM salle WHERE id_salle = :id_salle";
		$params = array(':id_salle' => $this->id_salle);

		$db = new Database(LOKISALLE_DB_HOST, LOKISALLE_DB_NAME, LOKISALLE_DB_LOGIN, LOKISALLE_DB_PASSWORD);

		$result = $db->executeRequete($requete, $params);
		$infos = $db->getResult($result);

		return new Salle($infos);
	}

	private function loadPromotion()
	{
		if($this->id_promo == null)
			return null;

		$requete = "SELECT * FROM promotion WHERE id_promo = :id_promo";
		$params = array(':id_promo' => $this->id_promo);

		$db = new Database(LOKISALLE_DB_HOST, LOKISALLE_DB_NAME, LOKISALLE_DB_LOGIN, LOKISALLE_DB_PASSWORD);

		$result = $db->executeRequete($requete, $params);
		$infos = $db->getResult($result);

		return new Promotion($infos);
	}

	public function getPromotion()
	{
		return $this->promotion;
	}

	public function getInfo($name)
	{
		return $this->$name;
	}

	public function getSalle()
	{
		return $this->salle;
	}

	public function isAvailable()
	{
		return !$this->etat;
	}

	public function hasPromo()
	{
		return $this->promotion ? true : false;
	}

	public function getPrix()
	{
		if($this->promotion)
			return $this->prix - ($this->prix * $this->promotion->getReduction() / 100);
		else
			return $this->prix;
	}

	public function setInfo($name, $value)
	{
		$this->$name = $value;
	}

	public function toArray()
	{
		$vars = array();

		foreach($this as $key => $value)
			$vars[$key] = $value;

		return $vars;
	}
}