<?php
//ici on traite le formulaire
// A t-on un $_POST
if(isset($_POST) && !empty($_POST)){
    //On vérifie que tout les champs sont remplis
    require_once('inc/lib.php');
    if(verifForm($_POST, ['nom'])){
        $nom = strip_tags($_POST['nom']);
        require_once('inc/connect.php');
        //On écrit la requête sql
        $sql = 'INSERT INTO `categories`(`name`)VALUES (:nom);';

        //On prépare la requête
        $query = $db->prepare($sql);
//On injecte les valeurs dans la requete
$query->bindValue(':nom', $nom, PDO::PARAM_STR);

//On exécute la requete
$query->execute();

//On déconnecte la base
require_once('inc/close.php');
//Ici on peut 
//- Rediriger l'utilisateur 
header('Location: admin_categories.php');
// - Récupérer et afficher l'id de l'enregistrement ajouté
// - Faire toute action nécessaire en fonction du site

    }else{
        echo 'Attention il faut rentrer un nom';
    }
    
    


    }



?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une catégorie</title>
</head>
<body>
    <h1>Ajouter une catégorie</h1>
    <form   method="post">
    <div>
    <label for="nom">Nom de la catégorie : </label>
    <input type="text" id="nom" name="nom">
    </div>
    <button>Ajouter la catégorie</button>
</body>
</html>