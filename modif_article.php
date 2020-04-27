<?php

require_once('inc/connect.php');
$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

// Pas de variable donc utilisation de la méthode query
$query = $db->query($sql);

$categories = $query->fetchAll(PDO::FETCH_ASSOC);

//On vérifie si un id est passé dans l'url
if (isset($_GET['id']) && !empty($_GET['id'])) {
    //Un id est donné 
    //On récupère l'id et on le nettoie
    $id = strip_tags($_GET['id']);

    //On écrit la requete
    $sql = 'SELECT * FROM `articles` WHERE `id` = :id;';

    $query = $db->prepare($sql);
    //On injecte les valeurs dans la requete
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    //On exécute la requete
    $query->execute();
    $article = $query->fetch(PDO::FETCH_ASSOC);
    if (!$article) {
        header('Location: admin_articles.php');
    }
    //L'article existe on va chercher les categories dans lesquels il a été cocher
    $sql = 'SELECT * FROM `articles_categories`WHERE `articles_id` = :id;';
    $query = $db->prepare($sql);
    //On injecte les valeurs dans la requete
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    //On exécute la requete
    $query->execute();
    $categoriesArticle = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
    header('Location: admin_articles.php');
}
//On vérifie que le formulaire a été envoyé
if (isset($_POST) && !empty($_POST)) {
    require_once('inc/lib.php');
    if (verifForm($_POST, ['titre', 'contenu', 'categories'])) {
        //On récupère et on nettoie les valeurs
        $titre = strip_tags($_POST['titre']);
        $contenu = strip_tags($_POST['contenu'], '<div><p><h1><h2><img><strong>');

        //On récupère le nom de l'image dans la base de données 
        $nom = $article['featured_image'];

        //On vérifie si on a une image
        if (isset($_FILES['image']) && !empty($_FILES['image']) && $_FILES['image']['error'] != 4) {
            //On récupère les données
            $image = $_FILES['image'];

            //On vérifie que le ttransfert s'est mal passé 'error'=0
            if ($image['error'] != 0) {

                echo 'Une erreur s\'est produite, devinez laquelle. 0x6845328486';
                die;
            }

            //On limite les images aux png et jpg (jpeg aussi)
            $types = ['image/png', 'image/jpeg'];

            //On vérifie si le type du fichier est absent de la liste
            if (!in_array($image['type'], $types)) {
                echo "Le type de fichier doit être une image jpg ou png";
                die;
            }
            //Le transfert s'est bien déroulé on déplace l'image temporaire après lui avoir généré un nouveau nom 
            //Générer un nom pour le fichier -> nom + extension
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            // On génère un nom "aléatoire"
            $nom = md5(uniqid()) . '.' . $extension;

            //On génère le nom complet vers le dossier de destination
            $nomComplet = __DIR__ . '/uploads/' . $nom;
            //On déplace le fichier 
            if (!move_uploaded_file($image['tmp_name'], $nomComplet)) {
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

            //On gère la suppr des anciennes images
            // On récup la première parti du nom de fichier de l'ancienne image
           if ($article ['featured_image'] != null){

           
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
        }





        //On écrit la requête 
        $sql = 'UPDATE `articles` SET `title` = :titre, `content` = :contenu, `featured_image` = :image WHERE `id` = :id;';

        //On prépare la requête
        $query = $db->prepare($sql);
        //On injecte les valeurs dans la requete
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':image', $nom, PDO::PARAM_INT);
        $query->bindValue(':id', $id, PDO::PARAM_STR);

        //on exécute la requete

        //On exécute la requete
        $query->execute();

        $sql = 'DELETE FROM `articles_categories` WHERE `articles_id` = :id';
        $query = $db->prepare($sql);
        //On injecte les valeurs dans la requete
        $query->bindValue(':id', $id, PDO::PARAM_STR);

        $query->execute();



        //On récupère l'id de l'article nouvellement crée
        $idArticle = $db->lastInsertId();

        //On récupère dans post les cat cochées
        $categories = $_POST['categories'];

        //On ajoute les catégories
        foreach ($categories as $categorie) {
            //Ajouter les catégories

            $sql = 'INSERT INTO `articles_categories`(`articles_id`, `categories_id`) VALUES (:idarticle, :idcategorie);';
            $query = $db->prepare($sql);
            $query->bindValue(':idarticle', $id, PDO::PARAM_INT);
            $query->bindValue(':idcategorie', strip_tags($categorie), PDO::PARAM_INT);
            $query->execute();
        }

        header('Location: admin_articles.php');
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
            <input type="text" id="titre" name="titre" value="<?= $article['title'] ?> ">
        </div>
        <div>
            <label for="contenu">Ajouter du contenu : </label>
            <textarea name="contenu" id="contenu"> <?= $article['content'] ?> </textarea>
        </div>

        <h2>Image</h2>

        <div>
            <label for="image">Fichier : </label>
            <input type="file" name="image" id="image">
            <!--//Un seul fichier / plusieur ajouter -> multiple <- apres id="ficher" -->
        </div>



        <h2>Catégories</h2>
        <?php foreach ($categories as $categorie) :
            $checked = '';
            foreach ($categoriesArticle as $cat) {

                if ($cat['categories_id'] == $categorie['id']) {
                    $checked = 'checked';
                }
                //équivalent du if Condition ternaire -> $checked = ($cat['categories_id'] == $categorie['id']) ? 'checked' : '';
            }
        ?>
            <div>
                <input type="checkbox" name="categories[]" id="cat_<?= $categorie['id'] ?>" value="<?= $categorie['id'] ?>" <?= $checked ?>>
                <label for="categorie"> <?= $categorie['name'] ?> </label>
            </div>

        <?php endforeach; ?>
        <button>Ajouter l'article</button>
    </form>
</body>

</html>