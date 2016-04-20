<?php

require_once('includes/init.inc.php');

/* CONTROLE SI UTILISATEUR CONNECTE */

if($session->hasUser())
{
    $session->addFlashes(LKS_FLASH_WARNING, 'Vous êtes déjà connecté.<br/>Vous allé être redirigé dans 3 secondes...');
    header('Refresh: 3; profil.php');
}

// Controlleur de la page pour les vérification et les requêtes SQL
$controller = new connexionController($session);

/****************************
* Vérifications du formulaire
*****************************/
if($_POST)
{
    /***************************
     * Contrôle du pseudo
     */
    if($controller->isEmptyField($_POST['pseudo']))
    {
        $controller->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, 'Vous devez saisir un pseudo');
    }
    elseif(!$controller->isValid($_POST['pseudo'], LKS_FORMAT_PSEUDO))
    {
        $controller->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PSEUDO_MSG);
    }

    /***************************
     * Contrôle du password
     */
    if($controller->isEmptyField($_POST['password']))
    {
        $controller->addError('password');
        $session->addFlashes(LKS_FLASH_ERROR, 'Vous devez saisir un mot de passe');
    }
    elseif(!$controller->isValid($_POST['password'], LKS_FORMAT_PASSWORD))
    {
        $controller->addError('password');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PASSWORD_MSG);
    }


    /* SI TOUT EST OK, ON VERIFIE QUE LE PSEUDO ET LE PASSWORD CORRESPONDENT */

    if(!$controller->hasError())
    {
        if(!$controller->canLogin($session, $_POST['pseudo'], $_POST['password']))
        {
            $controller->addError('pseudo');
            $controller->addError('password');
            $session->addFlashes(LKS_FLASH_ERROR, 'Le couple pseudo/mdp n\'existe pas.');
        }
    }


    /* SI TOUT EST OK */

    if(!$controller->hasError())
    {
        $session->addFlashes(LKS_FLASH_OK, 'Connexion validée !');

        // Création du cookie si l'utilisateur l'a demandé
        if(isset($_POST['rappel']) && $_POST['rappel'] == 'rappel')
            $session->setCookie('pseudo', $_POST['pseudo'], time() + LKS_COOKIE_TIME);

        // Mise à jour de la session, création du panier et redirection vers la page de profil
        $user = $session->getUserFromPseudo($_POST['pseudo']);

        $session->setUser($user);
        $session->createPanier();

        $session->redirect('profil');

        exit();
    }
}

require_once('includes/header.inc.php');


?>

<div class="row">
    <div class="col-md-12">
        <h1 class="text-center">Se connecter</h1>
    </div>
</div>


<div class="row">

<form action="connexion.php" method="post" class="col-md-offset-3 col-md-6">
        <?php
            $builder = new Builder();

            // Si cookie, on pré remplit le champ pseudo
            if($session->hasCookie('pseudo'))
            {
                $userdata = array('pseudo' => $session->getCookie('pseudo'));
                echo $builder->generateFormInput($userdata, $controller, 'text', 'pseudo', 'Votre pseudo', LKS_LABEL_HIDE);
                echo $builder->generateFormInput($_POST, $controller, 'password', 'password', 'Votre mot de passe', LKS_LABEL_HIDE);
            }
            else
            {
                echo $builder->generateFormInput($_POST, $controller, 'text', 'pseudo', 'Votre pseudo', LKS_LABEL_HIDE);
                echo $builder->generateFormInput($_POST, $controller, 'password', 'password', 'Votre mot de passe', LKS_LABEL_HIDE);
            }
        ?>

    <div class="form-group text-right">
        <a href="mdpperdu.php" class="help-block"><small><em>J'ai oublié mon mot de passe.</em></small></a>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="rappel" value="rappel" /> Se souvenir de moi
        </label>
    </div>

    <button type="submit" class="btn btn-primary pull-right">Connexion</button>
</form>

</div>

<?php require_once('includes/footer.inc.php'); ?>
