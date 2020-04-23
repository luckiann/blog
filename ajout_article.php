<?php
//Aller chercher la liste de toutes les catégories (par ordre alphabétique)
//Nécessaire avant de pouvpro affficher le formulaire

// Traiter le formulaire -> Ajouter l'article dans la base et lui attribuer les bonnes catégories

require_once('inc/connect.php');
$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

// Pas de variable donc utilisation de la méthode query
$query = $db->query($sql);

$categories = $query->fetchAll(PDO::FETCH_ASSOC);

require_once('inc/close.php');
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
    <form   method="post">
    <div>
    <label for="titre">Ajouter un titre : </label>
    <input type="text" id="titre" name="titre">
    </div>
    <div>
    <label for="contenu">Ajouter du contenu : </label>
    <textarea name="contenu" id="contenu"></textarea>
    </div>
    
        <h2>Catégories</h2>
        <?php foreach($categories as $categorie):   ?>
    <div>
    <input type="checkbox" name="categories[]" id="cat_<?= $categorie['id'] ?>" value="<?= $categorie['id'] ?>">
    <label for="categorie"> <?= $categorie['name'] ?> </label>
    </div>
    
        <?php endforeach; ?>
    <button>Ajouter l'article</button>
</body>
</html>