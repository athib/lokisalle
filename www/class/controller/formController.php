<?php

/*
 * Classe comprenant les méthodes de vérification des différents formulaires du site.
 *
 * Contient une instance de la session pour accéder à ses données (base de donnée entre autre)
 */
class formController
{
	protected $session;
	protected $errors;

	/*
	 * Initialisation des propriétés
	 */
	public function __construct(Session $session)
	{
		$this->session = $session;
		$this->errors = array();
	}

	/*
	 * Pour le système de gestion des erreurs
	 * Ajoute une erreur "$name" à la liste
	 */
	public function addError($name)
	{
		$this->errors[$name] = true;
	}

	/*
	 * Pour le système de gestion des erreurs
	 * Récupère l'erreur "$name" de la liste
	 */
	public function getError($name)
	{
		return $this->errors[$name];
	}

	/*
	 * Pour le système de gestion des erreurs
	 * Si le paramètre est défini
	 * Vérifie si l'erreur passée en paramètre existe. Retourne :
	 * - TRUE si l'erreur existe
	 * - FALSE sinon
	 *
	 * Si le paramètre n'est pas défini
	 * Vérifie si une erreur existe. Retourne
	 * - TRUE si une erreur est définie
	 * - FALSE si 0 erreur
	 */
	public function hasError($name = null)
	{
		if($name == null)
		{
			foreach($this->errors as $key => $value)
			{
				if(!empty($this->errors[$key]))
					return true;
			}
			return false;
		}

		return isset($this->errors[$name]);
	}


	/*
	 * Vérifie si une chaine de caractère est vide. Retourne :
	 *
	 * TRUE : si vide
	 * FALSE : sinon
	 */
	public function isEmptyField($input)
	{
		return empty($input);
	}

	/*
	 * Vérifie qu'une chaine de caractères correspond au format spécifié via une
	 * expression régulière
	 */
	public function isValid($input, $regex)
	{
		return preg_match($regex, $input);
	}

	/*
	 * Vérifie que 2 chaines de caractères sont identiques
	 * En l'occurence pour le mot de passe et sa vérification
	 */
	public function isPasswordConfirmed($password, $password_confirm)
	{
		return $password === $password_confirm;
	}


	/*
	 * Vérifie si le pseudo est disponible. Retourne :
	 *
	 * FALSE si un utilisateur utilise déjà ce pseudo
	 * TRUE si personne
	 */
	public function canUsePseudo($pseudo)
	{
		$user = $this->session->getUserFromPseudo($pseudo);

		if($user)
			return false;
		else
			return true;
	}

	/*
	 * Vérifie si l'email est disponible. Retourne :
	 *
	 * FALSE si un utilisateur utilise déjà l'email
	 * TRUE si personne
	 */
	public function canUseEmail($email)
	{
		$user = $this->session->getUserFromEmail($email);

		if($user)
			return false;
		else
			return true;
	}

	/*
	 * Vérifie si un champ a été modifié par rapport au données de l'utilisateur
	 * (édition du profil)
	 */
	public function fieldChanged($field, $new)
	{
		return $new == $this->session->getUser()->getProperty($field);
	}

	/*
	 * Vérifie si une photo a été choisie dans un formulaire
	 *
	 * TRUE : photo selectionnée
	 * FALSE : vide
	 */
	public function hasPhotoSelected()
	{
		return !empty($_FILES['photo']['name']);
	}

	/*
	 * Compare la date passée en paramètre avec la date du jour
	 * Une conversion est effectuée car la date passée en paramètre est au format YYYY-MM-DD
	 *
	 * TRUE : date supérieure à date du jour
	 * FALSE sinon
	 */
	public function laterThanToday($date)
	{
		$today_format = (new DateTime())->format('Y-m-d');

		$today = (new DateTime($today_format));
		$date_input = new DateTime($date);

		return $date_input >= $today;
	}

	/*
	 * Compare 2 dates. Retourne :
	 *
	 * TRUE si date1 > date2
	 * FALSE sinon (< ou =)
	 */
	public function laterThanDate($date1, $date2)
	{
		$date1 = new DateTime($date1);
		$date2 = new DateTime($date2);

		return $date1 > $date2;
	}
}