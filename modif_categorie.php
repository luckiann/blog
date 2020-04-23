<?php
// Cette page permet de modifier une catégorie 
// On récupère dans l'url l'id de la catégorie à modifier
// Par l'intermédiaire de $_GET    

if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    //On a un id et il n'est pas vide
    $id = strip_tags($_GET['id']);
    //On va aller chercher la catégorie dans la base
    //On se connecte
    require_once('inc/connect.php');
    //On écrit la requête sql
    $sql = 'SELECT * FROM `categories` WHERE `id` = :id;';
    //On prépare la requête
    $query = $db->prepare($sql);
    //On injecte les valeurs dans la requete
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    //On exécute la requete
    $query->execute();

    //On récupère les données 
    $categorie = $query->fetch(PDO::FETCH_ASSOC);


    //Ssi la catégorie n'existe pas
    if (!$categorie) {
        echo 'la catégorie n\'existe pas !';
        die;
    } //On vérifie avec post le formulaire
    //On vérifie si name existe et n'est pas nul
    
    if (isset($_POST['nom']) && !empty($_POST['nom'])) {
        //On modifie l'enregistrement dans la base 
        //On récupère le nom saisi et on nettoie
        $nom = strip_tags($_POST['nom']);
        //On est déja connectés 
        //On écrit la requête SQL
        $sql = 'UPDATE `categories` SET `name` = :cequonveut WHERE `id` = :id;';

        //On prépare la requête
        $query = $db->prepare($sql);

        //On injecte les valeurs dans la requete
        $query->bindValue(':cequonveut', $nom, PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        //On exécute la requete
        $query->execute();

        //On redirige vers une autre page (liste des catégories par exemple)
        header('Location: admin_categories.php');

        //On déconnecte la base
        require_once('inc/close.php');
    }
} else {
    //On n'a pas d'id 
    header('Location: admin_categories.php');
}





?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier</title>
</head>

<body>
    <h1>Modifier un catégorie</h1>
    <form method="post">
        <div>
            <label for="nom">Nom de la catégorie : </label>
            <input type="text" id="nom" name="nom" value="<?= $categorie['name'] ?>">
        </div>
        <button>Modifier la catégorie</button>
</body>

</html>