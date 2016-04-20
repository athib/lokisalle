<?php

require_once('includes/init.inc.php');


if($session->hasUser())
{
    $session->addFlashes(LKS_FLASH_OK, 'Vous avez été déconnecté.');
    $session->unsetUser();
}

$session->redirect('index');
