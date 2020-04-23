<?php
//Formulaire de connexion 
//Doit afficher en haut de la page "vous etes connectée" si le mail et le mdp sont bon

if(isset($_POST) && !empty($_POST)){
    //On vérifie que tout les champs sont remplis
    require_once('inc/lib.php');
    if(verifForm($_POST, ['mail', 'motdepasse'])){
        $mail = strip_tags($_POST['mail']);
        $pass = ($_POST['motdepasse']);
        
        require_once('inc/connect.php');
        //On écrit la requête sql
        $sql = 'SELECT * FROM `users` WHERE `email` = :email;';

        //On prépare la requête
        $query = $db->prepare($sql);
//On injecte les valeurs dans la requete
$query->bindValue(':email', $mail, PDO::PARAM_STR);

//On exécute la requete
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

if(!$user){
    echo 'Email et/ou mot de passe invalide';
}else{ //On vérifie  que le mot de passe saisie correspond a celui en base
    //password_verify($passEnClairSaisi, $passBaseDeDonnées)
    if(password_verify($pass, $user['password'])){
        echo "Vous êtes connecté.e";

    }else{
        echo"Veuillez remplir tout les champs";

    }

}


    }else{
        echo 'Veuillez remplir tous les champs';
    }
    
    
}

    


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <form   method="post">
    <div>
    <label for="mail"> E-Mail : </label>
    <input type="email" id="mail" name="mail">
    </div>
    <div>
    <label for="pass"> Mot de passe : </label>
    <input type="password" id="motdepasse" name="motdepasse">
    </div>
    <button>Me connecter</button>
</body>
</html>