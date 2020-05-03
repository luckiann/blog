<?php
session_start();
require_once 'inc/lib.php';
//On vérifie qu'on a une session user
if(verifForm($_SESSION, ['user'])){
//On vérifie si il est admin
//On transforme les roles en tableau php
$roles = json_decode($_SESSION['user']['roles']);
//vérifier si role contient "role_admin"
if(!in_array('ROLE_ADMIN', $roles)){
    //L'utilisayeur n'est pas admin
    //Page 404
    //On envoie un code réponse 404
    http_response_code(404);
    include('errors/404.php');
    exit;
}


}else{
    //L'utilisateur n'est pas connecté
    //On affiche une erreur 403
    http_response_code(403);
    include('errors/403.php');
    exit;
    

}
//FIN VERIFICATION PERMISSIONS DACCES
require_once 'inc/connect.php';
$sql = 'SELECT * FROM articles ORDER BY created_at ASC';

$query = $db->prepare($sql);

$query->execute();

$articles = $query->fetchAll(PDO::FETCH_ASSOC);

require_once 'inc/close.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Articles</title>
</head>
<body>
    <?php if(verifForm($_SESSION, ['message'])): ?>
        <div style="color:green; font-weight: bold"><?= $_SESSION['message'] ?></div>
    <?php 
        $_SESSION['message'] = '';
        endif;
    ?>
     <?php if(verifForm($_SESSION, ['error'])): ?>
        <div style="color:green; font-weight: bold"><?= $_SESSION['error'] ?></div>
    <?php 
        $_SESSION['error'] = '';
        endif;
        ?>
    <h1>Liste des articles</h1>
    <table>
        <thead>
            <th>ID</th>
            <th>Image</th>
            <th>Titre</th>
            <th>Actions</th>
        </thead>
        <tbody>
            <?php foreach($articles as $article): ?>
                <tr>
                    <td><?= $article['id'] ?></td>
                    <td><?= $article['featured_image'] ?></td>
                    <td><?= $article['title'] ?></td>
                    <td><a href="modif_article.php?id=<?= $article['id'] ?>">Modifier</a> <a href="suppr_article.php?id=<?= $article['id'] ?>">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="ajout_article.php">Ajouter</a>
</body>
</html>