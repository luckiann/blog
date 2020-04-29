<?php
session_start();
// Vu qu'on utilisera PHPMailer, on importe ses fichiers
require_once('inc/PHPMailer/Exception.php');
require_once('inc/PHPMailer/PHPMailer.php');
require_once('inc/PHPMailer/SMTP.php');

// PHPMailer est en PHP Orienté Objet
// On appelle les classes Exception et PHPMailer
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// On vérifie qu'on a un token dans l'url et qu'il n'est pas vide
if(isset($_GET['token']) && !empty($_GET['token'])){
    // Le token existe-t-il en base de données ?
    // On récupère et on nettoie les données
    $token = strip_tags($_GET['token']);

    // On se connecte à la base
    require_once('inc/connect.php');

    // On écrit la requête
    $sql = 'SELECT * FROM `users` WHERE `reset_token` = :token;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les données
    $query->bindValue(':token', $token, PDO::PARAM_STR);

    // On exécute
    $query->execute();

    // On récupère les données
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    // On ne sait pas encore si le token est bon
    if(!$user){
        $_SESSION['error'] = 'Le lien de réinitialisation de mot de passe a déjà été utilisé';
        header('Location: index.php');
    }
    require_once('inc/lib.php');
    // On traite le changement de mot de passe
    if(verifForm($_POST, ['pass1', 'pass2'])){
        // On récupère les données
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];

        // On vérifie que les 2 mots de passe sont identiques
        if($pass1 == $pass2){
            // Ici les 2 mots de passe sont identiques
            // On chiffre le mot de passe
            $pass = password_hash($pass1, PASSWORD_BCRYPT);

            // On écrit la requête
            $sql = "UPDATE `users` SET `password` = '$pass', `reset_token` = null WHERE `id` = ".$user['id'];

            // On exécute la requête
            $query = $db->query($sql);

            // Permet de connaître le nombre d'enregistrements affectés par notre requête
            //die("Nombre de lignes modifiées : ". $query->rowCount());

            // On envoie un mail de confirmation
            // On instancie le service Mail
            $mail = new PHPMailer();

            // On configure le serveur
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->Port = 1025;
            $mail->CharSet = 'utf-8';
            // Fin configuration

            try{
                $mail->setFrom('notre@site.fr', 'Notre site');

                $mail->addAddress($user['email'], $user['name']);

                $mail->Subject = 'Mot de passe changé';

                $mail->isHTML();

                $mail->Body = '
                    <h1>Mot de passe changé</h1>
                    <p>Votre mot de passe a été changé avec succès sur notre site exceptionnel</p>
                    <a href="http://localhost/blog">Venez, c\'est par ici</a>
                ';

                $mail->send();

            }catch(Exception $e){
                echo $e->errorMessage();
            }

            $_SESSION['message'] = 'Votre mot de passe a été modifié avec succès';
            header('Location: connexion.php');

        }else{
            $_SESSION['error'] = 'Les deux mots de passe ne sont pas identiques';
            header('Location: '. $_SERVER['HTTP_REFERER']);
            die;
        }
    }


}else{
    $_SESSION['error'] = 'Vous n\'avez pas demandé de réinitialisation de mot de passe';
    header('Location: index.php');
}



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
</head>
<body>
    <h1>Réinitialisation de mot de passe</h1>
    <?php
        if(isset($_SESSION['error']) && !empty($_SESSION['error'])){
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        }
    
    ?>
    <form method="post">
        <div>
            <label for="pass1">Mot de passe : </label>
            <input type="password" name="pass1" id="pass1">
        </div>
        <div>
            <label for="pass2">Confirmer le mot de passe :</label>
            <input type="password" name="pass2" id="pass2">
        </div>
        <button>Modifier le mot de passe</button>
    </form>
</body>
</html>