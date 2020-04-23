<?php
//Cette page récupère la liste de tous les articles de la base de données
//On sr connecte a la base
require_once('inc/connect.php');

// On écrit la requete sql
$sql = 'SELECT `articles`.*, 
GROUP_CONCAT(`categories`.`name`)
AS category_name 
FROM `articles` 
LEFT JOIN `articles_categories`
ON `articles`.`id` = `articles_categories`.`articles_id`
LEFT JOIN `categories`
ON `articles_categories`.`categories_id` = `categories`.`id`
GROUP BY `articles`.`id`
ORDER BY `created_at` DESC;';
//rEQUETE SANS VARIABLE donc utilisation de la méthode QUERY
$query = $db->query($sql);

//On va chercher les resultats de la requête
$articles = $query->fetchAll(PDO::FETCH_ASSOC);
//On se déconnecte de la base
require_once('inc/close.php');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>liste des articles</title>
</head>

<body>
    <h1>Liste des articles</h1>
    <?php foreach($articles as $article): ?>
        <article>
        <h2><a href="article.php?id=<?= $article['id'] ?>"><?= $article['title'] ?></a></h2>
        <p>
            Publié le <?= date('d/m/Y à H:i:s', strtotime($article['created_at']) )?> 
            Dans 
            <?php
            //Si je recois "sport,actu"
             $categories = explode(',', $article['category_name']);
             //Apres explode j'ai [0 =>  'sports', 1 => 'actualités']
             foreach($categories as $categorie){
                 echo'<a href="#">' . $categorie . '</a> ';
             }
              ?>
              </p>
        <div><?= substr(strip_tags($article['content']), 0, 300) . '...' ?></div>
        
        </article>
    <?php endforeach; ?>
    <button>Ajouter un article </button>

</body>

</html>
<!-- // substr(strip_tags($article['content']), 0, 300)
// On enlève les balises HTML
// $contenuSansHtml = strip_tags($article['content']);

// On raccourcit à 300 caractères
// $contenuRaccourci = substr($contenuSansHtml, 0 , 300); -->
