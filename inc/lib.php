<?php
/**
 * Fonction universelle de vérification de formulaire
 *
 * @param array $superglobale Variable $_POST ou $_GET
 * @param array $champs Tableau des champs à vérifier
 * @return bool
 */
function verifForm($superglobale, $champs){
    // Fonction universelle de vérification de formulaire
    // Boucler sur "champs"
    foreach($champs as $champ){
        // Vérifier si le champ existe
        // Vérifier si le champ n'est pas vide
        if(isset($superglobale[$champ]) && !empty($superglobale[$champ])){
            $reponse = true;
        }else{
            return false;
        }
    }
    // Envoyer la réponse "return"
    return $reponse;
}
/**
 * Fonction universelle de vérification de formulaire
 *
 * @param int $superglobale Variable $_POST ou $_GET
 * @param string $champs Tableau des champs à vérifier
 */
function thumb($taille, $nom){
    //On sépare nom et extension
    $debutNom = pathinfo($nom, PATHINFO_FILENAME);
    $extension = pathinfo($nom, PATHINFO_EXTENSION);

    $nomComplet = __DIR__. '/../uploads/' . $nom;
    $infosImage = getimagesize($nomComplet);

//Définition des dimensions de l'image finale
$largeurFinale = $taille;
$hauteurFinale = $taille;

//Crée un fichier vide une image virtuelle dans une variable LE RECEPTACLE
$imageDest = imagecreatetruecolor($largeurFinale, $hauteurFinale);

//On charge l'image source en mémoire (en fonction de son type)
switch ($infosImage['mime']) {
    case 'image/jpeg':
        $imageSrc = imagecreatefromjpeg($nomComplet);
        break;
    case 'image/png':
        $imageSrc = imagecreatefrompng($nomComplet);
        break;
}
//On initialise les décalages et on gère le cas "image carrée"
$decalageX = 0;
$decalageY = 0;
//Si largeur > hauteur
if($infosImage[0] > $infosImage[1]){
$decalageX = ($infosImage[0] - $infosImage[1]) /2;
    $tailleCarreSrc = $infosImage[1];
}
//Si largeur < hauteur
if($infosImage[0] <= $infosImage[1]){
$decalageY = ($infosImage[1] - $infosImage[0]) /2;
    $tailleCarreSrc = $infosImage[0];
}

//Copier l'image source dans l'image de
imagecopyresampled(
    $imageDest,  // Image dans laquelle on copie l'image d'origine
    $imageSrc, // Image d'origine
    0, // Décalage horizontal dans l'image de destination
    0, // Décalage vertical dans l'image de destination
    $decalageX, // Décalage horizontal dans l'image source
    $decalageY, // Décalage vertical dans l'image source
    $largeurFinale, // Largeur de la zone cible dans l'image de destination 
    $hauteurFinale, // Hauteur de la zone cible dans l'image de destination
    $tailleCarreSrc, // Largeur de la zone cible dans l'image source
    $tailleCarreSrc // Hauteur de la zone cible dans l'image source
);
// imagecopyresampled($imageDest, $imageSrc, $dx['dest_x'], $dx['dest_y'],  $dx['src_x'],  $dx['src_y'],  $dx['dst_w'],  $dx['dst_h'],  $dx['src_w'],  $dx['src_h'] );

//On définie le chemain d'enregistrement et le nom du fichier
$nomDest = __DIR__ . '/../uploads/' . $debutNom. '-' .$taille.'x' .$taille.'.'.$extension;

switch($infosImage['mime']){
    case 'image/png':
        imagepng($imageDest, $nomDest);
    break;
    case 'image/jpeg':
        imagejpeg($imageDest, $nomDest);
    break;
    
}
//Libérer la mémoire penser a les suppr a la fin on en a plus besoin
imagedestroy($imageDest);
imagedestroy($imageSrc);
}
/**
 * Fonction universelle de vérification de formulaire
 *
 * @param string $superglobale Variable $_POST ou $_GET
 * @param int $champs Tableau des champs à vérifier
 */
function resizeImage($nom, $pourcentage){
    //On sépare nom et extension
    $debutNom = pathinfo($nom, PATHINFO_FILENAME);
    $extension = pathinfo($nom, PATHINFO_EXTENSION);

    $nomComplet = __DIR__. '/../uploads/' . $nom;
    $infosImage = getimagesize($nomComplet);
 
//Définition des dimensions de l'image finale
$largeurFinale = $infosImage[0] * $pourcentage/100;
$hauteurFinale = $infosImage[0] * $pourcentage/100;

//Crée un fichier vide une image virtuelle dans une variable LE RECEPTACLE
$imageDest = imagecreatetruecolor($largeurFinale, $hauteurFinale);

//On charge l'image source en mémoire (en fonction de son type)
switch ($infosImage['mime']) {
    case 'image/jpeg':
        $imageSrc = imagecreatefromjpeg($nomComplet);
        break;
    case 'image/png':
        $imageSrc = imagecreatefrompng($nomComplet);
        break;
}
//On initialise les décalages et on gère le cas "image carrée"
$decalageX = 0;
$decalageY = 0;
//Si largeur > hauteur
if($infosImage[0] > $infosImage[1]){
$decalageX = ($infosImage[0] - $infosImage[1]) /2;
    $tailleCarreSrc = $infosImage[1];
}
//Si largeur < hauteur
if($infosImage[0] <= $infosImage[1]){
$decalageY = ($infosImage[1] - $infosImage[0]) /2;
    $tailleCarreSrc = $infosImage[0];
}

//Copier l'image source dans l'image de
imagecopyresampled(
    $imageDest,  // Image dans laquelle on copie l'image d'origine
    $imageSrc, // Image d'origine
    0, // Décalage horizontal dans l'image de destination
    0, // Décalage vertical dans l'image de destination
    0, // Décalage horizontal dans l'image source
    0, // Décalage vertical dans l'image source
    $largeurFinale, // Largeur de la zone cible dans l'image de destination 
    $hauteurFinale, // Hauteur de la zone cible dans l'image de destination
    $infosImage[0], // Largeur de la zone cible dans l'image source
    $infosImage[1] // Hauteur de la zone cible dans l'image source
);
// imagecopyresampled($imageDest, $imageSrc, $dx['dest_x'], $dx['dest_y'],  $dx['src_x'],  $dx['src_y'],  $dx['dst_w'],  $dx['dst_h'],  $dx['src_w'],  $dx['src_h'] );

//On définie le chemain d'enregistrement et le nom du fichier
$nomDest = __DIR__ . '/../uploads/' . $debutNom . '-' . $pourcentage.'.' . $extension;

switch($infosImage['mime']){
    case 'image/png':
        imagepng($imageDest, $nomDest);
    break;
    case 'image/jpeg':
        imagejpeg($imageDest, $nomDest);
    break;
    
}
//Libérer la mémoire penser a les suppr a la fin on en a plus besoin
imagedestroy($imageDest);
imagedestroy($imageSrc);

}