<?php

/*
 * Controller pour la page d'envoi de newsletter
 */
class envoinewsController extends adminController
{
	/*
	 * Récupère tous les emails des membres abonnés à la newsletter
	 */
	public function getEmailsAbonnes()
	{
		$sql = "SELECT email FROM membre WHERE newsletter = 1";

		$stmt = $this->session->getDatabase()->executeRequete($sql);

		$emails = array();

		while($infos = $this->session->getDatabase()->getresult($stmt))
			$emails[] = $infos->email;

		return $emails;
	}

	/*
	 * Enregistre les informations de la newsletter dans la base
	 * Cela permet de garder un historique des news envoyées
	 */
	public function registerNewsletter($sujet, $message)
	{
		$sql = "INSERT INTO newsletter VALUES ('', '$sujet', '$message', NOW())";

		$this->session->getDatabase()->executeRequete($sql);
	}

	/*
	 * Retourne le nombre de membres abonnés à la newsletter
	 */
	public function getNbAbonnes()
	{
		$sql = "SELECT COUNT(*) AS nb_abonnes FROM membre WHERE newsletter = 1";

		$stmt = $this->session->getDatabase()->executeRequete($sql);

		$infos =  $this->session->getDatabase()->getResult($stmt);

		return $infos->nb_abonnes;
	}
}