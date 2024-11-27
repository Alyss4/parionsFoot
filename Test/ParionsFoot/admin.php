<?php
session_start();

// Vérification si l'utilisateur est connecté et s'il est admin
if (!isset($_SESSION['name']) || $_SESSION['name'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Connexion à la base de données
$db_username = 'root';
$db_password = '';
$db_name = 'paris_sportifs';
$db_host = 'localhost';
$db = mysqli_connect($db_host, $db_username, $db_password, $db_name)
    or die('could not connect to database');

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $new_role = mysqli_real_escape_string($db, $_POST['role']);

    // Récupération du rôle actuel de l'utilisateur
    $current_role_query = "SELECT role FROM user WHERE id = $user_id";
    $current_role_result = mysqli_query($db, $current_role_query);
    $current_user = mysqli_fetch_assoc($current_role_result);

    // Empêcher les modifications sur les administrateurs (si role = 'admin')
    if ($current_user && $current_user['role'] !== 'admin') {
        $update_query = "UPDATE user SET role = '$new_role' WHERE id = $user_id";
        mysqli_query($db, $update_query);
    }
}

// Récupération des utilisateurs
$users_query = "SELECT id, login, role FROM user";
$users_result = mysqli_query($db, $users_query);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin - Gestion des utilisateurs</title>
    <link rel="stylesheet" href="style.css?v=1.0">
    <link rel="icon" class="icon" href="img/parionsfoot.png">
</head>
<body>
    <header id='index'>
        <img id='logoestock' src="img/parionsfoot.png" alt="logo ParionsFoot" />
    </header>

    <main id='mindex'>
        <h1>Gestion des utilisateurs</h1>

        <table>
            <thead>
                <tr>
                    <th>Nom d'utilisateur</th>
                    <th>Rôle actuel</th>
                    <th>Modifier le rôle</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($users_result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['login']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <form method="POST" action="admin.php">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="role">
                                    <option value="player" <?php echo $user['role'] === 'player' ? 'selected' : ''; ?>>Joueur</option>
                                    <option value="bmaker" <?php echo $user['role'] === 'bmaker' ? 'selected' : ''; ?>>Bookmaker</option>
                                    <option value="admin" disabled>Admin (non modifiable)</option>
                                </select>
                                <button type="submit">Modifier</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>

    <footer id='findex'>
        <p id='copyright'>Copyright ©2024</p>
        <a id='logo'><img src="img/logo.png" alt="logo" /></a>
    </footer>

    <?php mysqli_close($db); ?>
</body>
</html>
