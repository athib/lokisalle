<?php

class Avis extends Entity
{
	private $id_avis,
			$id_membre,
			$id_salle,
			$commentaire,
			$note,
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