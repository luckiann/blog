<?php
//Vu qu'on utilisera phpmailer on importe ses fichiers
require_once('inc/PHPMailer/Exception.php');
require_once('inc/PHPMailer/PHPMailer.php');
require_once('inc/PHPMailer/SMTP.php');

//PHPMailer est en PHP orienté Objet
//On appelle les classes Exception de PHPMailer
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

//On vérifie qu'on recoit un mail dans le POST
if (isset($_POST['mail']) && !empty($_POST['mail'])) {
    //On a recu une adresse email
    // On récupère et on nettoie les données
    $email = strip_tags($_POST['mail']);

    //On ouvre la base 
    require_once('inc/connect.php');

    // On va chercher un utilisateur dans la base 
    //On écrit la requête
    $sql = 'SELECT * FROM `users` WHERE `email` = :email;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte (terme scientifique) les valeurs
    $query->bindValue(':email', $email, PDO::PARAM_STR);

    // On exécute la requête
    $query->execute();

    // On récupère les données
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo 'Email invalide';
    } else {
        $token = md5(uniqid());
        $sql = "UPDATE `users` SET `reset_token` = '$token' WHERE `id` =  " . $user['id'];

        //On écrit la requete

        $query = $db->query($sql);

        require_once('inc/close.php');

        //On instancie PHPMailer
        $mail = new PHPMailer();

        //On configure PHPMailer
        //On utilise SMTP
        $mail->isSMTP();

        //On définit le serveur SMTP
        $mail->Host = 'localhost';

        //On définit le port du serveur
        $mail->Port = 1025;

        //On met en place le charset utf-8
        $mail->CharSet = 'utf-8';
        //Fin de la configuration

        //On essaie d'envoyer un mail
        try {
            //On définit l'expéditeur
            $mail->setFrom('laziza@jeteveux.fr', 'Nom du site');

            //On défini le destinataire
            $mail->addAddress($user['email'], $user['name']);

            //On défini le sujet du mail
            $mail->Subject = 'Réinitialisation de mot de passe pour le compte '.$user['email'];

            //On défini que le message sera envoyé en HTML
            $mail->isHTML();

            //On définit le corps du message
            $mail->Body = '<h1>Réinitialisation de mot de passe</h1>
            <p>Une réinitialisation de mot de passe a été demandée pour votre compte '.$user['email'].', si vous avez effectué cette demande, veuillez cliquer sur le lien ci-dessous</p>
            <a href="http://localhost/blog/reset_pass.php?token='.$token.'">http://localhost/blog/reset_pass.php?token='.$token.'</a>';

            $mail->AltBody = 'Ceci est le texte en format text brut';


            //Envoi du mail
            $mail->send();

            echo 'Le mail est envoyé';
        } catch (Exception $e) {
            echo $e->errorMessage();
        }
    }
}


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>oublie de mot de passe</title>
</head>

<body>
    <h1>Oublie du mot de passe</h1>
    <p>Veuillez entrer votre adresse e-mail ci-dessous</p>
    <form method="post">
        <div>
            <label for="mail">E-mail : </label>
            <input type="email" id="mail" name="mail">
        </div>
        <button>Valider</button>
    </form>
</body>

</html>