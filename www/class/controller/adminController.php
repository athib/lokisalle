<?php

/**
 * Class adminController
 * Générique, et héritée par certains controlleurs
 */
class adminController
{
	protected $session;

	public function __construct(Session $session)
	{
		$this->session = $session;
	}
	
	public function getGetParam($name)
	{
		return isset($_GET[$name]) ? $_GET[$name] : null;
	}
}