<?php

class Promotion extends Entity
{
	private $id_promo,
			$code_promo,
			$reduction;

	public function __construct($data)
	{
		parent::__construct($data);

		foreach($data as $key => $value)
		{
			$this->$key = $value;
		}
	}

	public function getId()
	{
		return $this->id_promo;
	}

	public function getCodePromo()
	{
		return $this->code_promo;
	}

	public function getReduction()
	{
		return $this->reduction;
	}

	public function toArray()
	{
		$data = array(
			'id_promo'   => $this->id_promo,
			'code_promo' => $this->code_promo,
			'reduction'  => $this->reduction
		);

		return $data;
	}
}