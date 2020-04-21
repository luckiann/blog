<?php
//On vérifie si on a un title dans l'url
if(isset($_GET['id']) && !empty($_GET['id'])){
    //Si on a un id et qu'il est pas vide on continue
    $id = $_GET['id'];
    //On se connecte a la base
require_once('inc/connect.php');
    // On écrit la requete avec la variable sql :id
$sql ='SELECT * FROM `articles` WHERE `id`= :id;'; // ; SQL et ; PHP
//rEQUETE avec VARIABLE donc utilisation de la requete dite "préparerée"
$query = $db->prepare($sql);

//On injecte les valeurs dans la requete
$query->bindValue(':id', $id, PDO::PARAM_INT);

//On exécute la requete
$query->execute();

// On récupère les données d'1 utilisateur
$article = $query->fetch(PDO::FETCH_ASSOC);

require_once('inc/close.php');

//Sil'article n'existe pas
if(!$article){ //($article == false)
    echo "l'article n'existe pas";
    die;

}


}else{
    // si on n'a pas d'id on revient index.php
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $article['title'] ?></title>
</head>
<body>
    <article>
        <h1><?= $article['title'] ?> </h1>
        <p>Publié le <?= date('d/m/Y à H:i:s', strtotime($article['created_at']))?></p>
        <div><?= $article['content'] ?></div>
        <a href="<?= $_SERVER['HTTP_REFERER'] ?>">Retour</a>
    </article>
</body>
</html>