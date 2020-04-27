<?php
//Ce fichier sert a suppr une catégorie

if (isset($_GET['id']) && !empty($_GET['id'])) {
    //On a un id et il n'est pas vide
    $id = strip_tags($_GET['id']);
    //On va aller chercher la catégorie dans la base
    //On se connecte
    require_once('inc/connect.php');
    //On écrit la requête sql
    $sql = 'DELETE FROM `article` WHERE `id` = :id;';
    //On prépare la requête
    $query = $db->prepare($sql);
    //On injecte les valeurs dans la requete
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    //On exécute la requete
    $query->execute();

    //On déconnecte la base
    require_once('inc/close.php');
    //On redirige vers une autre page (liste des catégories par exemple)
    header('Location: admin_article.php');

} else {
    //pas d'id 
    //oN REDIRIGE LE VISITEUR
    //On redirige vers une autre page (liste des catégories par exemple)
    header('Location: admin_article.php');
}
?>
