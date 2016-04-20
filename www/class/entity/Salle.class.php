<?php

class Salle extends Entity
{
	private $id_salle,
			$titre,
			$adresse,
			$ville,
			$cp,
			$pays,
			$latitude,
			$longitude,
			$categorie,
			$capacite,
			$photo,
			$description;

	private $avis = array();

	private $nbAvis = 0;
	private $moyenneAvis = 0;


	public function __construct($data)
	{
		parent::__construct($data);

		foreach($data as $key => $value)
		{
			$this->$key = $value;
		}
	}

	public function getInfo($name)
	{
		return $this->$name;
	}

	public function setAvis($avis)
	{
		$this->avis = $avis;
	}

	private function updateStats()
	{
		$nb = 0;
		$somme = 0;
		foreach($this->avis as $avis)
		{
			$somme += $avis->getInfo('note');
			$nb++;
		}

		if($nb < 1)
		{
			$this->nbAvis = null;
			$this->moyenneAvis = null;
		}
		else
		{
			$this->nbAvis = $nb;
			$this->moyenneAvis = round($somme / $nb, 1);
		}
	}

	public function getNbAvis()
	{
		$this->updateStats();
		return $this->nbAvis;
	}

	public function getMoyenne()
	{
		$this->updateStats();
		return $this->moyenneAvis;
	}

	public function toArray()
	{
		$vars = array();

		foreach($this as $key => $value)
			$vars[$key] = $value;

		return $vars;
	}
}