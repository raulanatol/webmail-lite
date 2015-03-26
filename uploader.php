<?php

const MAX_EMAILS_FOR_INSERT = 50;

//Сheck that we have a file
if ((!empty($_FILES["uploaded_file"])) && ($_FILES['uploaded_file']['error'] == 0)) {
    //Check if the file is JPEG image and it's size is less than 350Kb
    $filename = basename($_FILES['uploaded_file']['name']);
    $uploadFile = dirname(__FILE__) . '/uploads/' . time() . $filename;

    if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $uploadFile)) {
        $fp = fopen($uploadFile, 'rb');
        $emails2Add = array();
        $tmpList = array();
        $emailsForInsert = MAX_EMAILS_FOR_INSERT;
        while (($line = fgets($fp)) !== false) {
            if (strlen($line) > 3 && strpos($line, '@') !== false) {
                $secureLine = preg_replace("/\r\n|\r|\n/", '', $line);
                $secureLine = trim($secureLine);
                $tmpList[] = "('" . $secureLine . "')";
                if (--$emailsForInsert < 0) {
                    $emailsForInsert = MAX_EMAILS_FOR_INSERT;
                    $emails2Add[] = implode(', ', $tmpList);
                    $tmpList = array();
                }
            }
        }

        if (sizeof($tmpList) > 0) {
            $emails2Add[] = implode(',', $tmpList);
        }

        unlink($uploadFile);
        //Crear todos los inserts de $emailsForTnsert
        $multiQuery = '';
        foreach ($emails2Add as $insert) {
            $multiQuery .= "INSERT IGNORE INTO massive_email_list (email) VALUES" . $insert . ";";
        }

        $dbHost = 'localhost';
        $dbName = 'webmail_lite';
        $dbUser = 'root';
        $dbPass = 'root';
        $batchConnection = new mysqli($dbHost, $dbUser, $dbPass, $dbName, 8889);
        if ($batchConnection->connect_error) {
            echo '<b>Error con la base de datos</b>';
        } else {
            $sqlResult = $batchConnection->multi_query($multiQuery);
            if ($sqlResult) {
                echo '<b>Datos cargados correctamente</b>';
            } else {
                echo '<b>Error al cargar los datos</b>';
            }
        }
    }
} else {
    echo "Error: No se ha encontrado el fichero";
}




