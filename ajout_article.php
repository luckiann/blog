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

//On vérifie si on a une image
    if(isset($_FILES['image']) && !empty($_FILES['image']) && $_FILES['image']
    ['error'] != 4){
        //On récupère les données
        $image = $_FILES['image'];

        //On vérifie que le ttransfert s'est mal passé 'error'=0
        if($image['error'] != 0){

            echo 'Une erreur s\'est produite, devinez laquelle. 0x6845328486';
            die;
        }

        //On limite les images aux png et jpg (jpeg aussi)
        $types =['image/png', 'image/jpeg'];

       //On vérifie si le type du fichier est absent de la liste
        if(!in_array($image['type'], $types)){
            echo "Le type de fichier doit être une image jpg ou png";
            die;
        }
        //Le transfert s'est bien déroulé on déplace l'image temporaire après lui avoir généré un nouveau nom 
        //Générer un nom pour le fichier -> nom + extension
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        // On génère un nom "aléatoire"
        $nom = md5(uniqid()) .'.' . $extension;
        
        //On génère le nom complet vers le dossier de destination
        $nomComplet = __DIR__ . '/uploads/' . $nom;
         //On déplace le fichier 
         if(!move_uploaded_file($image['tmp_name'], $nomComplet)){
            echo "Le fichier n'a pas été copié! ";
            die;
        }
   
thumb(300, $nom);
thumb(150, $nom);
thumb(400, $nom);
resizeImage($nom, 75);
        //Thumb = thumbnail (timbrre poste)
// Prendre reg.jpg
// Générer une miniature carré de 200*200
// Son nom devra être reg-200x200.jpg
}
        //On écrit la requête 
        $sql = 'INSERT INTO `articles`(`title`,`content`, `featured_image`, `users_id` ) VALUES (:titre, :contenu, :image, :userid);';

        //On prépare la requête
        $query = $db->prepare($sql);
        //On injecte les valeurs dans la requete
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':userid', 1, PDO::PARAM_INT);
        $query->bindValue(':image', $nom, PDO::PARAM_STR);

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
    <form method="post" enctype="multipart/form-data">
        <div>
            <label for="titre">Ajouter un titre : </label>
            <input type="text" id="titre" name="titre">
        </div>
        <div>
            <label for="contenu">Ajouter du contenu : </label>
            <textarea name="contenu" id="contenu"></textarea>
        </div>

        <h2>Image</h2>

    <div>
        <label for="image">Fichier : </label>
         <input type="file" name="image" id="image">       <!--//Un seul fichier / plusieur ajouter -> multiple <- apres id="ficher" -->
    </div>
    
    

        <h2>Catégories</h2>
        <?php foreach ($categories as $categorie) :   ?>
            <div>
                <input type="checkbox" name="categories[]" id="cat_<?= $categorie['id'] ?>" value="<?= $categorie['id'] ?>">
                <label for="categorie"> <?= $categorie['name'] ?> </label>
            </div>

        <?php endforeach; ?>
        <button>Ajouter l'article</button>
    </form>
</body>

</html>