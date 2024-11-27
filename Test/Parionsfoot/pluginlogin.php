<?php
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    // Connexion à la base de données
    $db_username = 'root';
    $db_password = '';
    $db_name = 'paris_sportifs';
    $db_host = 'localhost';

    $db = mysqli_connect($db_host, $db_username, $db_password, $db_name)
        or die('Could not connect to database');

    // Sécurisation des entrées utilisateur
    $username = mysqli_real_escape_string($db, htmlspecialchars($_POST['username']));
    $password = mysqli_real_escape_string($db, htmlspecialchars($_POST['password']));

    if (!empty($username) && !empty($password)) {
        // Requête pour récupérer l'utilisateur et vérifier le mot de passe
        $requete = "
            SELECT id, role 
            FROM user 
            WHERE login = '$username' AND password = MD5('$password')
        ";
        $exec_requete = mysqli_query($db, $requete);

        if ($exec_requete && mysqli_num_rows($exec_requete) > 0) {
            $user = mysqli_fetch_assoc($exec_requete);

            // Stocker les informations utilisateur dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $username;

            // Redirection selon le rôle
            if ($user['role'] === 'admin') {
                header('Location: admin.php');
                exit;
            } elseif ($user['role'] === 'bmaker') {
                header('Location: bmaker.php');
                exit;
            } elseif ($user['role'] === 'player') {
                header('Location: player.php');
                exit;
            } else {
                echo "Rôle utilisateur inconnu.";
            }
        } else {
            // Si les identifiants sont incorrects
            echo "Identifiants invalides. Veuillez réessayer.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }

    // Fermeture de la connexion
    mysqli_close($db);
} else {
    // Redirection vers la page d'accueil si accès direct
    header('Location: index.php');
    exit();
}
?>
