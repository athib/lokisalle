<?php

class inscriptionController extends formController
{
	public function hashPassword($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}

	public function registerToDatabase(User $user)
	{
		$hash_pwd = $this->hashPassword($user->getProperty('password'));
		$user->setProperty('password', $hash_pwd);

		$sql = "INSERT INTO membre
					(pseudo, password, email, prenom, nom, sexe, adresse, ville, cp, pays, statut, newsletter)
					VALUES
					(:pseudo, :password, :email, :prenom, :nom, :sexe, :adresse, :ville, :cp, :pays, :statut, :newsletter)";

		$params = (array(
			':pseudo' => $user->getProperty('pseudo'),
			':password' => $user->getProperty('password'),
			':email' => $user->getProperty('email'),
			':prenom' => $user->getProperty('prenom'),
			':nom' => $user->getProperty('nom'),
			':sexe' => $user->getProperty('sexe'),
			':adresse' => $user->getProperty('adresse'),
			':ville' => $user->getProperty('ville'),
			':cp' => $user->getProperty('cp'),
			':pays' => $user->getProperty('pays'),
			':statut' => $user->getProperty('statut'),
			':newsletter' => User::NEWS_NON_ABONNE
		));

		$this->session->getDatabase()->executeRequete($sql, $params);

		$user->setProperty('id_membre', $this->session->getDatabase()->getLastId());
	}
}