<?php

class connexionController extends formController
{
	/*
	 * Vérifie que le couple pseudo/mot de passe existe dans la base de données.
	 */
	public function canLogin($session, $pseudo, $password)
	{
		$user = $session->getUserFromPseudo($pseudo);

		if($user)
		{
			if(password_verify($password, $user->getProperty('password')))
			{
				return true;
			}
		}
		return false;
	}
}