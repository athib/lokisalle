<?php

class mdpperduController extends adminController
{
	/* Génération d'un mdp de 5 caractères aléatoires */
	public function genererMDP()
	{
		$alphanum = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890';
		$alphanum = str_shuffle($alphanum);

		$new_password = substr($alphanum, 0, 5);

		return $new_password;
	}

	public function updateDBPassword($email, $password)
	{
		$sql = "SELECT * FROM membre WHERE email = :email";
		$params = array(':email' => $email);

		$stmt = $this->session->getDatabase()->executeRequete($sql, $params);

		$infos = $this->session->getDatabase()->getResult($stmt);

		if(!$infos)
			return null;

		$user = new User($infos);



		$hash = password_hash($password, PASSWORD_BCRYPT);

		$sql = "UPDATE membre SET password = :password WHERE id_membre = :id_membre";
		$params = array(
			':password' => $hash,
			'id_membre' => $user->getProperty('id_membre')
		);

		$this->session->getDatabase()->executeRequete($sql, $params);

		return $user;
	}

	public function envoyerMail(User $user, $new_password)
	{
		// Construction des en-têtes de l'email (format HTML, UTF8)
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset="utf-8"' . "\r\n";
		$headers .= 'From: Lokisalle <' . LKS_CONTACT . '>' . "\r\n";

		$message = 'Bonjour, ' . $user->getProperty('pseudo') . "\r\n";
		$message .= "\r\n";
		$message .= 'Voici votre nouveau mot de passe : ' . $new_password . "\r\n";
		$message .= 'Pensez bien à le modifier dès votre prochaine connexion !' . "\r\n";
		$message .= "\r\n";

		if(mail($user->getProperty('email'), 'Votre nouveau mot de passe sur Lokisalle', $message, $headers))
			$this->session->addFlashes(LKS_FLASH_OK, 'Votre nouveau mot de passe a été envoyé à ' . $user->getProperty('email'));
		else
			$this->session->addFlashes(LKS_FLASH_ERROR, 'Erreur lors de l\'envoi du mail.');
	}
}