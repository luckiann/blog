<?php
//Aller chercher la liste de toutes les catégories (par ordre alphabétique)
//Nécessaire avant de pouvpro affficher le formulaire

// Traiter le formulaire -> Ajouter l'article dans la base et lui attribuer les bonnes catégories

require_once('inc/connect.php');
$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

// Pas de variable donc utilisation de la méthode query
$query = $db->query($sql);

$categories = $query->fetchAll(PDO::FETCH_ASSOC);


//On vérifie que le formulaire a été envoyé
if (isset($_POST) && !empty($_POST)) {
    require_once('inc/lib.php');
    if (verifForm($_POST, ['titre', 'contenu', 'categories'])) {
        //On récupère et on nettoie les valeurs
        $titre = strip_tags($_POST['titre']);
        $contenu = strip_tags($_POST['contenu'], '<div><p><h1><h2><img><strong>');

        //On écrit la requête 
        $sql = 'INSERT INTO `articles`(`title`,`content`, `users_id` ) VALUES (:titre, :contenu, :userid);';

        //On prépare la requête
        $query = $db->prepare($sql);
        //On injecte les valeurs dans la requete
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':userid', 1, PDO::PARAM_INT);

        //on exécute la requete

        //On exécute la requete
        $query->execute();

        //On récupère l'id de l'article nouvellement crée
        $idArticle= $db->lastInsertId();

        //On récupère dans post les cat cochées
        $categories = $_POST['categories'];

        //On ajoute les catégories
        foreach($categories as $categorie){
            //Ajouter les catégories

    $sql = 'INSERT INTO `articles_categories`(`articles_id`, `categories_id`) VALUES (:idarticle, :idcategorie);';
    $query = $db->prepare($sql);
    $query->bindValue(':idarticle', $idArticle, PDO::PARAM_INT);
    $query->bindValue(':idcategorie', strip_tags($categorie), PDO::PARAM_INT);
    $query->execute();
 
        }

        header('Location: index.php');
    } else {
        echo "Le fomulaire doit etre rempli complètement";
    }
}

// header('Location: index.php');

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un article</title>
</head>

<body>
    <h1>Ajouter un article</h1>
    <form method="post">
        <div>
            <label for="titre">Ajouter un titre : </label>
            <input type="text" id="titre" name="titre">
        </div>
        <div>
            <label for="contenu">Ajouter du contenu : </label>
            <textarea name="contenu" id="contenu"></textarea>
        </div>

        <h2>Catégories</h2>
        <?php foreach ($categories as $categorie) :   ?>
            <div>
                <input type="checkbox" name="categories[]" id="cat_<?= $categorie['id'] ?>" value="<?= $categorie['id'] ?>">
                <label for="categorie"> <?= $categorie['name'] ?> </label>
            </div>

        <?php endforeach; ?>
        <button>Ajouter l'article</button>
</body>

</html>