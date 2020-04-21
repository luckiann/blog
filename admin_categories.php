<?php
//Cette page récupère la liste de tous les articles de la base de données
//On sr connecte a la base
require_once('inc/connect.php');
//On écrit la requête
$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

//Pas de variable donc utilisation de la méthode "query
$query = $db->query($sql);

//On va chercher les données dans $query
$categories = $query->fetchAll(PDO::FETCH_ASSOC);

//La variable $categories contient TOUTES les cat de la base de données 

//On peut déconnecter la base
require_once('inc/close.php');

// On peut passer à l'affichage HTML

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Catégories</title>
</head>

<body>
    <h1>Liste des catégories</h1>
    <table>
        <thead>
            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
            
        </thead>
        <tbody>
        <?php 
        foreach($categories as $categorie): ?>
        <tr>
            <td><?= $categorie['id'] ?></td>
            <td><?= $categorie['name'] ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>

        </tbody>

    </table>
    <a href="ajout_categorie.php">Ajouter une catégorie</a>
</body>

</html>