<?php

require_once('includes/init.inc.php');

$builder = new Builder();
$form_ctrl = new formController($session);

/*************************
* TRAITEMENT DU FORMULAIRE
**************************/

if($_POST)
{
    /* CONTROLE DU PSEUDO */

    if($form_ctrl->isEmptyField($_POST['pseudo']))
    {
        $form_ctrl->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le pseudo est obligatoire');
    }
    elseif(!$form_ctrl->isValid($_POST['pseudo'], LKS_FORMAT_PSEUDO))
    {
        $form_ctrl->addError('pseudo');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_PSEUDO_MSG);
    }


    /* CONTROLE DE L'EMAIL */

    if($form_ctrl->isEmptyField($_POST['email']))
    {
        $form_ctrl->addError('email');
        $session->addFlashes(LKS_FLASH_ERROR, 'L\'email est obligatoire.');
    }
    elseif(!$form_ctrl->isValid($_POST['email'], LKS_FORMAT_EMAIL))
    {
        $form_ctrl->addError('email');
        $session->addFlashes(LKS_FLASH_ERROR, LKS_FORMAT_EMAIL_MSG);
    }


    /* CONTROLE DU MESSAGE */

    if($form_ctrl->isEmptyField($_POST['message']))
    {
        $form_ctrl->addError('message');
        $session->addFlashes(LKS_FLASH_ERROR, 'Le message ne peut être vide.');
    }
    else
    {
        $_POST['message'] = htmlspecialchars($_POST['message']);
    }


    // Si le formulaire a été rempli correctement
    if(!$form_ctrl->hasError())
    {
        // Construction des en-têtes de l'email (format HTML, UTF8)
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset="utf-8"' . "\r\n";
        $headers .= 'From: ' . $_POST['pseudo'] . '<' . $_POST['email'] . '>' . "\r\n";

        // On converti les retours à la ligne pour un affichage correcte dans l'email HTML
        $message = nl2br($_POST['message']);

        if(mail(LKS_CONTACT, 'Contact Lokisalle', $message, $headers))
        {
            $session->addFlashes(LKS_FLASH_OK, 'Votre message a bien été envoyé.');
            $session->redirect('contact');
            exit();
        }
        else
            sessionAddFlashes(LKS_FLASH_ERROR, 'Une erreur est survenue lors de l\'envoi, veuillez réessayer
            ultérieurement.');
    }
}


require_once('includes/header.inc.php');

?>

<div class="row">
    <h1 class="text-center">Formulaire de contact</h1>
</div>


<div class="row">

<form action="contact.php" method="post" class="col-md-offset-3 col-md-6">
    <?php
        if($session->hasUser())
            $userdata = $session->getUser()->toArray();
        else
            $userdata = $_POST;

        echo $builder->generateFormInput($userdata, $form_ctrl, 'text', 'pseudo', 'Votre pseudo', LKS_LABEL_HIDE);
        echo $builder->generateFormInput($userdata, $form_ctrl, 'email', 'email', 'Votre e-mail', LKS_LABEL_HIDE);
    ?>

    <div class="form-group has-feedback <?php if($_POST) { if($form_ctrl->hasError('message')) echo 'has-error'; else echo 'has-success'; } ?>">
        <label for="message" class="sr-only">Votre message</label>
        <textarea name="message" id="message" class="form-control" rows="5" placeholder="Votre message..."><?php
                if($_POST)
                {
                    if(!$form_ctrl->hasError('message'))
                        echo $_POST['message'];
                }
            ?></textarea>
    </div>

    <?php
        echo $builder->generateFormButton('envoyer_message', 'Envoyer le message', false);
    ?>
</form>

</div>


<?php require_once('includes/footer.inc.php'); ?>
