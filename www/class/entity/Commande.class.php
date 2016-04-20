<?php

class Commande extends Entity
{
	private $id_commande,
		$montant,
		$id_membre,
		$date;

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
}