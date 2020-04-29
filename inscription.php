<?php
// Cette page doit permettre à l'utilisateur de s'inscrire sur le site
// On ne gère pas le chiffrement de mots de passe pour le moment

// On vérifie que le $_POST existe et n'est pas vide
if(isset($_POST) && !empty($_POST)){
    // On vérifie que tous les champs sont existants et remplis
    require_once('inc/lib.php');
    if(verifForm($_POST, ['nom','mail', 'pass'])){
        // Ici le formulaire est complet
        // On récupère les valeurs des champs
        $nom = strip_tags($_POST['nom']);
        $mail = strip_tags($_POST['mail']);

        // On récupère le mot de passe et on le chiffre
        $pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);

        // On se connecte à la base
        require_once('inc/connect.php');

        // On écrit la requête
        $sql = 'INSERT INTO `users`(`email`, `password`, `name`) VALUES (:email, :password, :nom);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs
        $query->bindValue(':email', $mail, PDO::PARAM_STR);
        $query->bindValue(':password', $pass, PDO::PARAM_STR);
        $query->bindValue(':nom', $nom, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        // On redirige vers la page d'accueil
        header('Location: index.php');

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
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    <form method="post">
        <div>
            <label for="nom">Nom : </label>
            <input type="text" id="nom" name="nom">
        </div>
        <div>
            <label for="mail">E-mail : </label>
            <input type="email" id="mail" name="mail">
        </div>
        <div>
            <label for="pass">Mot de passe : </label>
            <input type="password" id="pass" name="pass">
        </div>
        <button>M'inscrire</button>
    </form>
</body>
</html>