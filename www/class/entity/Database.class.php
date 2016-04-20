<?php

class Database
{
	private $db;

	public function __construct($host, $dbname, $login, $password)
	{
		try
		{
			$this->db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $login, $password);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

			//return $this->db;
		}
		catch (PDOException $e)
		{
			exit('Erreur de connexion à la Base de Données : ' . $e->getMessage());
		}
	}


	public function executeRequete($sql, $params = false)
	{
		if($params)
		{
			$req = $this->db->prepare($sql);
			$req->execute($params);
		}
		else
		{
			$req = $this->db->query($sql);
		}

		return $req;
	}

	public function getResult(PDOStatement $requete)
	{
		return $requete->fetch();
	}

	public function getAllResults(PDOStatement $requete)
	{
		return $requete->fetchAll();
	}

	public function getNbLignes(PDOStatement $requete)
	{
		return $requete->rowCount();
	}

	public function getLastId()
	{
		return $this->db->lastInsertId();
	}
}