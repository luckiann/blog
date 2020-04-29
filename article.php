<?php
session_start();
// Cette page affiche 1 article

// On vérifie si on a un id dans l'url et si il n'est pas vide
if(isset($_GET['id']) && !empty($_GET['id'])){
    // On a un id, on le récupère
    $id = $_GET['id'];

    // On se connecte à la base de données
    require_once('inc/connect.php');

    // On écrit la requête
    $sql = 'SELECT `articles`.*, GROUP_CONCAT(`categories`.`name`) as category_name FROM `articles` LEFT JOIN `articles_categories` ON `articles`.`id` = `articles_categories`.`articles_id` LEFT JOIN `categories` ON `articles_categories`.`categories_id` = `categories`.`id` WHERE `articles`.`id` = :id GROUP BY `articles`.`id` ;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte les variables
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère les données (1 seul article)
    $article = $query->fetch(PDO::FETCH_ASSOC);

    // On se déconnecte
    require_once('inc/close.php');

    // Si l'article n'existe pas
    if(!$article){ // ($article == false)
        echo "L'article n'existe pas";
        die;
    }

}else{
    // On n'a pas d'id donc on redirige vers index.php
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
    <?php include_once('inc/header.php'); ?>
    <article>
        <h1><?= $article['title'] ?></h1>
        <p>
            Publié le <?= date('d/m/Y à H:i:s', strtotime($article['created_at'])) ?>
            dans 
                <?php
                    
                    // si je reçois "Sports,Actualités"
                    $categories = explode(',', $article['category_name']);
                    // Après explode j'ai [0 => 'Sports', 1 => 'Actualités']
                    foreach($categories as $categorie){
                        echo '<a href="#">' . $categorie . '</a> ';
                    }
                ?>
    
        </p>
        <div><?= $article['content'] ?></div>
    </article>
    <a href="<?= $_SERVER['HTTP_REFERER'] ?>">Retour</a>
</body>
</html>