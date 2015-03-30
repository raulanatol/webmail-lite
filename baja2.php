<?php

include "libraries/MailSo/Base/Blacklist.php";
include "libraries/afterlogic/api.php";

$email = htmlspecialchars($_POST["inputEmail"]);

if (\MailSo\Base\Blacklist::addEmailToBlackList($email)) {
    echo 'Baja correcta';
} else {
    echo 'No se ha podido dar de baja. Sentimos los incovenientes. Envíenos un email a info@consultoriaprotecciondedatos.es';
}