<?php
session_start();

// Aller chercher la liste de toutes les catégories (par ordre alphabétique)
// Nécessaire AVANT de pouvoir afficher le formulaire
// On se connecte à la base
require_once('inc/connect.php');

// On écrit la requête SQL
$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

// Pas de variables, donc utilisation de la méthode query
$query = $db->query($sql);

// On récupère les données
$categories = $query->fetchAll(PDO::FETCH_ASSOC);

// Traiter le formulaire -> Ajouter l'article dans la base et lui attribuer les bonnes catégories
// 1ère partie du traitement, créer l'article
// 2ème partie du traitement, lui affecter la/les catégorie.s
// On vérifie que le formulaire a été envoyé
if(isset($_POST) && !empty($_POST)){
    // On a un formulaire envoyé
    // On vérifie que tout est bien rempli
    require_once('inc/lib.php');
    if(verifForm($_POST, ['titre', 'contenu', 'categories'])){
        // Le formulaire est complet, on peut créer l'article
        // On récupère et on nettoie les valeurs
        $titre = strip_tags($_POST['titre']);
        $contenu = strip_tags($_POST['contenu'], '<div><p><h1><h2><img><strong>');

        // On vérifie si on a une image
        if(isset($_FILES['image']) && !empty($_FILES['image']) && $_FILES['image']['error'] != 4){ // Erreur 4 = pas de fichier
            // On récupère les informations de l'image
            $image = $_FILES['image'];

            // Si on a une erreur de transfert
            if($image['error'] != 0){
                echo 'Une erreur est survenue';
                die;
            }

            // Le fichier est-il png ou jpg ?
            $types = ['image/jpeg', 'image/png'];
            if(!in_array($image['type'], $types)){
                $_SESSION['error'] = 'Le fichier doit être une image png ou jpg';
                header('Location: ajout_article.php');
                die;
            }

            // On génère un nom de fichier
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $nom = md5(uniqid()) . '.' . $extension;
            $nomComplet = __DIR__ . '/uploads/' . $nom;

            // On copie le fichier
            if(!move_uploaded_file($image['tmp_name'], $nomComplet)){
                // Si le transfert échoue
                echo 'Erreur lors de la copie';
                die;
            }

            // On crée les différentes versions de l'image
            // Lors de la création, l'image est copiée et redimensionnée en :
            // - Miniature carrée de 300px : nom-300x300.ext
            // - Image réduite à 75% de la taille originale : nom-75.ext

            thumb(300, $nom);
            thumb(150, $nom);
            thumb(400, $nom);
            resizeImage($nom, 75);
            resizeImage($nom, 200);
            resizeImage($nom, 15);
        }



        // On écrit la requête
        $sql = 'INSERT INTO `articles`(`title`, `content`, `featured_image`, `users_id`) VALUES (:titre, :contenu, :image, :userid);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':userid', 1, PDO::PARAM_INT);
        $query->bindValue(':image', $nom, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        // On récupère l'id de l'article nouvellement créé
        $idArticle = $db->lastInsertId();

        // On récupère dans le $_POST les catégories cochées
        $categories = $_POST['categories'];

        // On ajoute les catégories
        foreach($categories as $categorie){
            // On écrit la requête
            $sql = 'INSERT INTO `articles_categories`(`articles_id`, `categories_id`) VALUES (:idarticle, :idcategorie);';

            // On prépare la requête
            $query = $db->prepare($sql);

            // On injecte les valeurs
            $query->bindValue(':idarticle', $idArticle, PDO::PARAM_INT);
            $query->bindValue(':idcategorie', strip_tags($categorie), PDO::PARAM_INT);

            // On exécute la requête
            $query->execute();
        }

        // On redirige vers la page d'administration
        $_SESSION['message'] = 'Article ajouté avec succès sous le numéro '.$idArticle;
        header('Location: admin_articles.php');
    }else{
        echo "Le formulaire doit être rempli complètement";
    }
}

// On se déconnecte
require_once('inc/close.php');
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
    <?php
        // Y a-t-il un message d'erreur ?
        if(isset($_SESSION['error']) && !empty($_SESSION['error'])){
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        }
    ?>
    <form method="post" enctype="multipart/form-data">
        <!-- Champs titre, contenu et catégories -->
        <div>
            <label for="titre">Titre : </label>
            <input type="text" id="titre" name="titre">
        </div>
        <div>
            <label for="contenu">Contenu : </label>
            <textarea name="contenu" id="contenu" cols="30" rows="10"></textarea>
        </div>
        <h2>Image</h2>
        <div>
            <label for="image">Image : </label>
            <input type="file" name="image" id="image">
        </div>
        <h2>Catégories</h2>
        <?php foreach($categories as $categorie): ?>
            <div>
                <input type="checkbox" name="categories[]" id="cat_<?= $categorie['id'] ?>" value="<?= $categorie['id'] ?>">
                <label for="cat_<?= $categorie['id'] ?>"> <?= $categorie['name'] ?></label>
            </div>
        <?php endforeach; ?>
        <!-- Bouton Ajouter -->
        <button>Ajouter l'article</button>
    </form>
</body>
</html>