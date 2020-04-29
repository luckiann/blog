<?php
session_start();
//Ce fichier sert a suppr une catégorie

if (isset($_GET['id']) && !empty($_GET['id'])) {
    //On va aller chercher la catégorie dans la base
    $id = strip_tags($_GET['id']);
    //On se connecte
    require_once('inc/connect.php');
    //On écrit la requête sql
    $sql = 'SELECT * FROM `articles` WHERE `id` = :id;';
    //On prépare la requête
    $query = $db->prepare($sql);
    //On injecte les valeurs dans la requete
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    //On exécute la requete
    $query->execute();
    $article = $query->fetch(PDO::FETCH_ASSOC);
    
    
    if(!$article){
        header('Location: admin_articles.php');
    }
    if($article['featured_image'] != null){

           
        $debutNom = pathinfo($article['featured_image'], PATHINFO_FILENAME);

        //On récupère la liste des fichiers dnas uploads
        $fichiers = scandir(__DIR__ . '/uploads/');
      
        //Vu que c'est un tableau on boucle
        foreach($fichiers as $fichier){
            //Si le nom du fichier commence par $debutnom
            if(strpos($fichier, $debutNom) === 0){
                //On supprime le fichier
                unlink(__DIR__ . '/uploads/' . $fichier);
            }
        }
        }
        $sql = 'DELETE FROM `articles_categories` WHERE `articles_id` = :id;'; 
        $query = $db->prepare($sql);
    //On injecte les valeurs dans la requete
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    
    //On exécute la requete
    $query->execute();

    //Supprimer l'article
    $sql = 'DELETE FROM `articles` WHERE `id` = :id;'; 
    $query = $db->prepare($sql);
    //On injecte les valeurs dans la requete
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    
    //On exécute la requete
    $query->execute();
    require_once('inc/close.php');
$_SESSION['message'] = "L'article numéro $id a été supprimé";
header('Location: admin_articles.php');
} else {
    //pas d'id 
    //oN REDIRIGE LE VISITEUR
    //On redirige vers une autre page (liste des catégories par exemple)
    header('Location: admin_articles.php');
}
?>
