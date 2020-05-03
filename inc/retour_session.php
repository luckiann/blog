<?php
//On restaure la session si besoin
//On vérifie si un cookie remember existe
if(isset($_COOKIE['remember']) && !empty($_COOKIE['remember'])){
    require_once('connect.php');
    $sql = 'SELECT * FROM `users` WHERE `remember_token` = :token;';
    $query = $db->prepare($sql);
    $query->bindValue(':token', $_COOKIE['remember'], PDO::PARAM_STR);
    $query->execute();
    $user=$query->fetch(PDO::FETCH_ASSOC);
    
    if($user){
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'email' => $user['email'],
            'name'  => $user['name'],
            'roles' => $user['roles']
        ];
    }
}
?>