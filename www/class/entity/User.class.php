<?php

class User
{
	const ROLE_ADMIN = 1;
	const ROLE_MEMBRE = 0;
	const NEWS_ABONNE = 1;
	const NEWS_NON_ABONNE = 0;

	private $id_membre,
			$pseudo,
			$password,
			$email,
			$prenom,
			$nom,
			$sexe,
			$adresse,
			$ville,
			$cp,
			$pays,
			$statut = null,
			$newsletter = null;

	public function __construct($data)
	{
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		}

		if($this->statut == null)
			$this->statut = self::ROLE_MEMBRE;

		if($this->newsletter == null)
			$this->newsletter = self::NEWS_NON_ABONNE;

		return $this;
	}

	public function getProperty($name)
	{
		return $this->$name;
	}

	public function setProperty($name, $value)
	{
		$this->$name = $value;
	}

	public function isAdmin()
	{
		return $this->statut == self::ROLE_ADMIN;
	}


	public function toArray()
	{
		$data = array(
			'id_membre' => $this->id_membre,
			'pseudo' => $this->pseudo,
			'email' => $this->email,
			'prenom' => $this->prenom,
			'nom' => $this->nom,
			'sexe' => $this->sexe,
			'adresse' => $this->adresse,
			'ville' => $this->ville,
			'cp' => $this->cp,
			'pays' => $this->pays,
			'statut' => $this->statut,
			'newsletter' => $this->newsletter
		);

		return $data;
	}
}