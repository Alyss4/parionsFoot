<?php
session_start();

// Vérifier si l'utilisateur est connecté et s'il est bookmaker
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bmaker') {
    header('Location: index.php'); // Redirection vers la page d'accueil si non connecté ou mauvais rôle
    exit();
}

// Connexion à la base de données
$db_username = 'root';
$db_password = '';
$db_name = 'paris_sportifs';
$db_host = 'localhost';

$db = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$db) {
    die('Erreur de connexion : ' . mysqli_connect_error());
}

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_evenement') {
    // Récupération et sécurisation des données du formulaire
    $nom_evenement = mysqli_real_escape_string($db, htmlspecialchars($_POST['nom_evenement']));
    $equipe_1 = mysqli_real_escape_string($db, htmlspecialchars($_POST['equipe_1']));
    $equipe_2 = mysqli_real_escape_string($db, htmlspecialchars($_POST['equipe_2']));
    $cote_equipe_1 = (float) $_POST['cote_equipe_1'];
    $cote_equipe_2 = (float) $_POST['cote_equipe_2'];
    $cote_egalite = (float) $_POST['cote_egalite'];
    $date_match = mysqli_real_escape_string($db, htmlspecialchars($_POST['date_match']));

    // Si l'ID du bookmaker n'est pas défini, on met NULL dans la base de données
    $id_bookmaker = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    // Vérifier si tous les champs sont remplis
    if (empty($nom_evenement) || empty($equipe_1) || empty($equipe_2) || empty($cote_equipe_1) || empty($cote_equipe_2) || empty($cote_egalite) || empty($date_match)) {
        echo "<p style='color: red;'>Tous les champs doivent être remplis.</p>";
    } else {
        // Insertion des données dans la table `evenements`
        $requete = "
            INSERT INTO evenements (nom_evenement, equipe_1, equipe_2, cote_equipe_1, cote_equipe_2, cote_egalite, date_match, id_bookmaker)
            VALUES ('$nom_evenement', '$equipe_1', '$equipe_2', $cote_equipe_1, $cote_equipe_2, $cote_egalite, '$date_match', $id_bookmaker)
        ";

        if (mysqli_query($db, $requete)) {
            echo "<p style='color: green;'>Événement ajouté avec succès.</p>";
        } else {
            echo "<p style='color: red;'>Erreur lors de l'ajout de l'événement : " . mysqli_error($db) . "</p>";
        }
    }
}

// Fermeture de la connexion à la base de données
mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bookmaker - Ajouter un événement</title>
    <link rel="stylesheet" href="style.css?v=1.0">
    <link rel="icon" class="icon" href="img/parionsfoot.png">
</head>
<body>
    <header>
        <img id='logoestock' src="img/parionsfoot.png" alt="logo ParionsFoot" />
        <nav>
            <ul class="navbar">
                <li><a href="bmaker.php">Ajouter un événement</a></li>
                <li><a href="listematchs.php">Liste des matchs</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Ajouter un événement sportif</h2>
        <form action="bmaker.php" method="POST">
            <label for="nom_evenement">Nom de l'événement :</label><br>
            <input type="text" id="nom_evenement" name="nom_evenement" required><br><br>

            <label for="equipe_1">Équipe 1 :</label><br>
            <input type="text" id="equipe_1" name="equipe_1" required><br><br>

            <label for="equipe_2">Équipe 2 :</label><br>
            <input type="text" id="equipe_2" name="equipe_2" required><br><br>

            <label for="cote_equipe_1">Cote Équipe 1 :</label><br>
            <input type="number" id="cote_equipe_1" name="cote_equipe_1" step="0.01" required><br><br>

            <label for="cote_equipe_2">Cote Équipe 2 :</label><br>
            <input type="number" id="cote_equipe_2" name="cote_equipe_2" step="0.01" required><br><br>

            <label for="cote_egalite">Cote Égalité :</label><br>
            <input type="number" id="cote_egalite" name="cote_egalite" step="0.01" required><br><br>

            <label for="date_match">Date de l'événement :</label><br>
            <input type="datetime-local" id="date_match" name="date_match" required><br><br>

            <button type="submit" name="action" value="ajouter_evenement">Ajouter l'événement</button>
        </form>
    </main>

    <footer>
        <p>Copyright ©2024</p>
    </footer>
</body>
</html>
