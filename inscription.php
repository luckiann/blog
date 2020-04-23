<?php
// Cette page doit permettre à l'utilisateur de s'inscrire sur le site 
// On gère pas le chiffrement de mots de passe pour le moment
if(isset($_POST) && !empty($_POST)){
    //On vérifie que tout les champs sont remplis
    require_once('inc/lib.php');
    if(verifForm($_POST, ['mail', 'motdepasse'])){
        //Ici le formulaire est complet
        //On récupère les valeurs des champs
        $mail = strip_tags($_POST['mail']);
        
        $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_BCRYPT);

        // On se connecte à la base

        require_once('inc/connect.php');
        //On écrit la requête sql
        $sql = 'INSERT INTO `users`(`email`, `password`) VALUES (:email, :password);';

        //On prépare la requête
        $query = $db->prepare($sql);
//On injecte les valeurs dans la requete
$query->bindValue(':email', $mail, PDO::PARAM_STR);
$query->bindValue(':password', $motdepasse, PDO::PARAM_STR);


//On exécute la requete
$query->execute();


//- Rediriger l'utilisateur 
header('Location: index.php');
// - Récupérer et afficher l'id de l'enregistrement ajouté
// - Faire toute action nécessaire en fonction du site

    }else{
        echo 'Tous les champs sont obligatoires';
    }
    
    


    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription sur le site</title>
</head>
<body>
    <h1>Inscription sur le site</h1>
    <form   method="post">
    <div>
    <label for="mail">Adresse Mail: </label>  <input type="email" id="mail" name="mail">
    </div>
    <div>
    <label for="motdepasse">Mot de Passe : </label>  <input type="password" id="motdepasse" name="motdepasse">
    </div>
    <button>Ajouter moi au site ! </button>
</body>
</html>