<?php
try { 
    // Connection à la base de donnée
    $db = new PDO('mysql:host=localhost;dbname=blog', 'root', 'root');

    // On force les échanges en UTF8
    $db->exec('SET NAMES "UTF8"');
    
} catch (PDOException $e) {
    //En cas de problème on émet un message d'erreur
    echo 'Erreur : ' . $e->getMessage();
    die;
}
