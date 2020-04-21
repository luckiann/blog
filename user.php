<?php
//On vérifie si on a un id dans l'url
if(isset($_GET['id']) && !empty($_GET['id'])){
    //Si on a un id et qu'il est pas vide on continue
    $id = $_GET['id'];
    //On se connecte a la base
require_once('inc/connect.php');
    // On écrit la requete avec la variable sql :id
$sql ='SELECT * FROM `users` WHERE `id`= :id;'; // ; SQL et ; PHP
//rEQUETE avec VARIABLE donc utilisation de la requete dite "préparerée"
$query = $db->prepare($sql);

//On injecte les valeurs dans la requete
$query->bindValue(':id', $id, PDO::PARAM_INT);

//On exécute la requete
$query->execute();

// On récupère les données d'1 utilisateur
$user = $query->fetch(PDO::FETCH_ASSOC);

require_once('inc/close.php');

}else{
    // si on n'a pas d'id on revient amin_users.php
    header('Location: admin_users.php');
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations de l'utilisateur</title>
</head>

<body>
    <h1>Information de l'utilisateur <?= $user['id'] ?></h1>
    <p>Email : <?= $user['email'] ?></p>
    <p>Mot de passe : <?php echo $user['password'] ?></p>
    <p><a href="<?= $_SERVER['HTTP_REFERER']?>">Retour</a></p>
    
</body>

</html>