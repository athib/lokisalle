<?php
require_once('includes/init.inc.php');

// Vérification de l'authentification de l'utilisateur
if(!$session->hasUser())
{
    $session->addFlashes(LKS_FLASH_ERROR, 'Vous devez être identifié pour accéder à la page de profil.');
    $session->redirect('connexion');

    exit();
}

/* INITIALISATION DES VARIABLES ET CONTROLLEURS */
$user = $session->getUser();
$controller = new profilController($session);

// Génération de facture PDF
if($controller->hasRequestedFacture())
{
    $controller->generatePDF($user, $controller->getFactureIdRequested());
}

/*****************************
 * VERIFICATION DU FORMULAIRE
 */
if($_POST)
{
    /***********************************
     * Contrôle de la saisie du pseudo *
     ***********************************/
    if($controller->isEmptyField($_POST['pseudo']))
    {
        $controller->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "pseudo" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['pseudo'], LKS_FORMAT_PSEUDO))
    {
        $controller->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PSEUDO_MSG);
    }
    elseif(!$controller->fieldChanged('pseudo', $_POST['pseudo']) && !$controller->canUsePseudo($_POST['pseudo']))
    {
        $controller->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, 'Ce pseudo est déjà utilisé.');
    }


    /****************************************
     * Contrôle de la saisie du mot de passe *
     *****************************************/
    if($controller->isEmptyField($_POST['old_password']))
    {
        if(!$controller->isEmptyField($_POST['password']) || !$controller->isEmptyField($_POST['password_confirm']))
        {
            $controller->addError('password');
            $session->addFlashes(LKS_FLASH_ERROR, 'Vous devez saisir votre ancien mot de passe avant de le modifier.');
        }
    }
    else
    {
        if(!password_verify($_POST['old_password'], $user->getProperty('password')))
        {
            $controller->addError('password');
            $session->addFlashes(LKS_FLASH_ERROR, 'L\'ancien mot de passe ne correspond pas.');
        }
        elseif($controller->isEmptyField($_POST['password']) || $controller->isEmptyField($_POST['password_confirm']))
        {
            $controller->addError('password');
            $session->addFlashes(LKS_FLASH_ERROR, 'Vous devez saisir un nouveau mot de passe et sa confirmation');
        }
        elseif(!$controller->isValid($_POST['password'], LKS_FORMAT_PASSWORD) || !$controller->isValid($_POST['password_confirm'], LKS_FORMAT_PASSWORD))
        {
            $controller->addError('password');
            $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PASSWORD_MSG);
        }
        elseif(!$controller->isPasswordConfirmed($_POST['password'], $_POST['password_confirm']))
        {
            $controller->addError('password');
            $session->addFlashes(LKS_FLASH_ERROR, 'Le nouveau mot de passe et sa confirmation doivent être identiques.');
        }
    }


    /***********************************
     * Contrôle de la saisie de l'email *
     ************************************/
    if($controller->isEmptyField($_POST['email']))
    {
        $controller->addError('email');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "e-mail" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['email'], LKS_FORMAT_EMAIL))
    {
        $controller->addError('email');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_EMAIL_MSG);
    }
    elseif(!$controller->fieldChanged('email', $_POST['email']) && !$controller->canUseEmail($_POST['email']))
    {
        $controller->addError('email');
        $session->addFlashes(LKS_FLASH_ERROR, 'Cet email est déjà utilisé.');
    }


    /**********************************
     * Contrôle de la saisie du prénom *
     ***********************************/
    if($controller->isEmptyField($_POST['prenom']))
    {
        $controller->addError('prenom');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "prénom" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['prenom'], LKS_FORMAT_PRENOM))
    {
        $controller->addError('prenom');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PRENOM_MSG);
    }

    /*******************************
     * Contrôle de la saisie du nom *
     ********************************/
    if($controller->isEmptyField($_POST['nom']))
    {
        $controller->addError('nom');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "nom" est obligatoire');
    }
    elseif(!$controller->isValid($_POST['nom'], LKS_FORMAT_NOM))
    {
        $controller->addError('nom');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_NOM_MSG);
    }


    /*************************************
     * Contrôle de la saisie de l'adresse *
     **************************************/
    if($controller->isEmptyField($_POST['adresse']))
    {
        $controller->addError('adresse');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "adresse" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['adresse'], LKS_FORMAT_ADRESSE))
    {
        $controller->addError('adresse');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_ADRESSE_MSG);
    }

    /************************************
     * Contrôle de la saisie de la ville *
     *************************************/
    if($controller->isEmptyField($_POST['ville']))
    {
        $controller->addError('ville');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "ville" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['ville'], LKS_FORMAT_VILLE))
    {
        $controller->addError('ville');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_VILLE_MSG);
    }

    /***************************************
     * Contrôle de la saisie du code postal *
     ****************************************/
    if($controller->isEmptyField($_POST['cp']))
    {
        $controller->addError('cp');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le code postal est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['cp'], LKS_FORMAT_CP))
    {
        $controller->addError('cp');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_CP_MSG);
    }

    /********************************
     * Contrôle de la saisie du pays *
     *********************************/
    if($controller->isEmptyField($_POST['pays']))
    {
        $controller->addError('pays');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le champ "pays" est obligatoire.');
    }
    elseif(!$controller->isValid($_POST['pays'], LKS_FORMAT_PAYS))
    {
        $controller->addError('pays');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PAYS_MSG);
    }


    /* SI LE FORMULAIRE NE CONTIENT AUCUNE ERREUR */
    if(!$controller->hasError())
    {
        $session->addFlashes(LKS_FLASH_OK, 'Vos modifications ont été prises en compte');

        $controller->updateDatabase($user->getProperty('id_membre'), $_POST);

        $updatedUser = $session->getUserFromPseudo($_POST['pseudo']);

        $session->setUser($updatedUser);
        $session->redirect('profil');

        exit();
    }
    else
    {
        $_GET['action'] = 'edit';
    }
}

require_once('includes/header.inc.php');

?>

<div class="row">
    <h1 class="text-center">Bienvenue <?php echo $user->getProperty('pseudo'); ?> !</h1>
</div>

<div class="row">
<div class="col-md-12">
    <div class="col-md-6 fiche-produit">
    <div class="row">
        <h2 class="text-center">Vos informations</h2>
    </div>

    <?php
        $builder = new Builder();

        // Dans le cas où l'utilisateur souhaite modifier ses informations
        // On affiche un formulaire de modification
        if($controller->hasGetAction('edit'))
        {
            $userData = $user->toArray();

            echo '<form action="profil.php" method="post">';

            echo '<fieldset><legend>Changer mon mot de passe</legend>';
            echo $builder->generateFormInput($_POST, $controller, 'password', 'old_password', 'Ancien mot de passe', LKS_LABEL_HIDE);
            echo $builder->generateFormInput($_POST, $controller, 'password', 'password', 'Votre nouveau de mot de passe', LKS_LABEL_HIDE);
            echo $builder->generateFormInput($_POST, $controller, 'password', 'password_confirm', 'Confirmation de mot de passe', LKS_LABEL_HIDE);
            echo '</fieldset>';

            echo $builder->generateFormInput($userData, $controller, 'text', 'pseudo', 'Votre pseudo');
            echo $builder->generateFormInput($userData, $controller, 'email', 'email', 'Votre e-mail');
            echo $builder->generateFormInput($userData, $controller, 'text', 'prenom', 'Votre prénom');
            echo $builder->generateFormInput($userData, $controller, 'text', 'nom', 'Votre nom');
            echo $builder->generateFormRadio('sexe', 'Vous êtes : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', unserialize(LKS_DATA_RADIO_SEX), $user->getProperty('sexe'));
            echo $builder->generateFormInput($userData, $controller, 'text', 'adresse', 'Votre adresse');
            echo $builder->generateFormInput($userData, $controller, 'text', 'cp', 'Votre code postal');
            echo $builder->generateFormInput($userData, $controller, 'text', 'ville', 'Votre ville');
            echo $builder->generateFormInput($userData, $controller, 'text', 'pays', 'Votre pays');
            echo $builder->generateFormRadio('newsletter', 'Newsletter : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', unserialize(LKS_DATA_YES_NO), $user->getProperty('newsletter'));

            echo '<div class="form-group">';
            echo '<button type="submit" class="btn btn-primary">Valider les modifications</button>';
            echo '<p class="help-block"><em><a href="profil.php">Annuler</a></em></p>';
            echo '</div>';

            echo '</form>';
        }
        // sinon, de simple labels sans champ de saisie
        else
        {
            echo $builder->generateFormLabel('Pseudo', $user->getProperty('pseudo'));
            echo $builder->generateFormLabel('E-mail', $user->getProperty('email'));
            echo $builder->generateFormLabel('Prénom', $user->getProperty('prenom'));
            echo $builder->generateFormLabel('Nom', $user->getProperty('nom'));

            $gender = $user->getProperty('sexe') == 'h' ? 'un homme' : 'une femme';
            echo $builder->generateFormLabel('Vous êtes', $gender);

            echo $builder->generateFormLabel('Adresse', $user->getProperty('adresse'));
            echo $builder->generateFormLabel('Code postal', $user->getProperty('cp'));
            echo $builder->generateFormLabel('Ville', $user->getProperty('ville'));
            echo $builder->generateFormLabel('Pays', $user->getProperty('pays'));

            $statut = $user->getProperty('statut') == User::ROLE_ADMIN ? 'Administrateur' : 'Membre';
            echo $builder->generateFormLabel('Statut', $statut);

            $newsletter = $user->getProperty('newsletter') == User::NEWS_ABONNE ? 'Oui' : 'Non';
            echo $builder->generateFormLabel('Newsletter', $newsletter);

            echo '<br>';
            echo '<p class="text-right"><a href="?action=edit">&Gt; Modifier mes informations</a></p>';
        }
    ?>
    </div>

    <div class="col-md-offset-1 col-md-5 fiche-produit">
        <div class="row">
            <h2 class="text-center">Vos commandes</h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr><th class="text-center" colspan="3">Historique</th></tr>
                        <tr>
                            <td>Numéro de commande</td>
                            <td>Date</td>
                            <td>Facture</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        /* AFFICHAGE DES COMMANDES DE L'UTILSIATEUR */
                        
                        $commandes = $controller->getCommandes($user);

                        if(empty($commandes))
                            echo '<tr><td colspan="3" class="text-center"><em>Vous n\'avez passé aucune commande.</em></td></tr>';
                        else
                        {
                            foreach($commandes as $commande)
                            {
                                echo '<tr>';

                                echo '<td>' . $commande->getInfo('id_commande') . '</td>';
                                echo '<td>' . (new DateTime($commande->getInfo('date')))->format('d/m/Y') . '</td>';
                                echo '<td><a target="_blank" href="?id_commande=' . $commande->getInfo('id_commande') . '">Voir</a></td>';

                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>
</div>



<?php require_once('includes/footer.inc.php'); ?>
