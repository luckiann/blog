<?php
//Cette page récupère la liste de tous les articles de la base de données
//On sr connecte a la base
require_once('inc/connect.php');
//On écrit la requête 
$sql = 'SELECT * FROM `users` ORDER BY `email` ASC;';

//Pas de variable donc utilisation de la méthode "query
$query = $db->query($sql);

//On va chercher les données dans $query
$users = $query->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Liste des utilisateurs</title>
</head>

<body>
    <h1>Liste des utilisateurs</h1>
    <table>
        <thead>
            <th>ID</th>
            <th>Email</th>
            <th>Actions</th>

        </thead>
        <tbody>
            <?php
            foreach ($users as $user) : ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td><a href="user.php?id=<?= $user['id'] ?>">Afficher</a>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>

    </table>
</body>

</html>