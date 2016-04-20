<?php

class Session
{
	private $bdd;

	public function __construct($bdd)
	{
		session_start();
		$this->bdd = $bdd;
	}


	public function getDatabase()
	{
		return $this->bdd;
	}


	public function hasFlashes()
	{
		return isset($_SESSION['flash']);
	}

	public function addFlashes($type, $message)
	{
		if(!isset($_SESSION['flash'][$type]))
			$_SESSION['flash'][$type] = "<li>$message</li>";
		else
			$_SESSION['flash'][$type] .= "<li>$message</li>";
	}

	public function getFlashes()
	{
		$flash = $_SESSION['flash'];
		unset($_SESSION['flash']);

		return $flash;
	}


	public function getUserFromPseudo($pseudo)
	{
		$requete = "SELECT * FROM membre WHERE pseudo = :pseudo";
		$params = array(':pseudo' => $pseudo);

		$resultat = $this->bdd->executeRequete($requete, $params);

		if($resultat->rowCount() == 0)
			return null;
		else
		{
			$data = $this->bdd->getResult($resultat);
			return new User($data);
		}
	}

	public function getUserFromEmail($email)
	{
		$requete = "SELECT * FROM membre WHERE email = :email";
		$params = array(':email' => $email);

		$resultat = $this->bdd->executeRequete($requete, $params);

		if($resultat->rowCount() == 0)
			return null;
		else
		{
			$data = $this->bdd->getResult($resultat);
			return new User($data);
		}
	}


	public function hasUser()
	{
		return isset($_SESSION['user']);
	}

	public function setUser(User $user)
	{
		$_SESSION['user'] = $user;
	}

	public function getUser()
	{
		return $_SESSION['user'];
	}

	public function unsetUser()
	{
		unset($_SESSION['user']);
	}


	public function hasCookie($name)
	{
		return isset($_COOKIE[$name]);
	}

	public function getCookie($name)
	{
		return $_COOKIE[$name];
	}

	public function setCookie($name, $value, $duration)
	{
		setcookie($name, $value, $duration);
	}


	public function createPanier()
	{
		$_SESSION['panier'] = new Panier();
	}

	public function getPanier()
	{
		if(!isset($_SESSION['panier']))
			$this->createPanier();

		return $_SESSION['panier'];
	}

	public function updatePanier(Panier $panier)
	{
		$_SESSION['panier'] = $panier;
	}


	public function redirect($page, $params = '')
	{
		header('Location: ' . $page . '.php' . $params);
	}
}