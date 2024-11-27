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

// Suppression d'un événement
if (isset($_GET['supprimer_id'])) {
    $id_evenement = (int) $_GET['supprimer_id']; // Récupérer l'ID de l'événement à supprimer

    // Requête SQL pour supprimer l'événement
    $requete_suppression = "DELETE FROM evenements WHERE id = $id_evenement";
    
    if (mysqli_query($db, $requete_suppression)) {
        echo "<p style='color: green;'>Événement supprimé avec succès.</p>";
    } else {
        echo "<p style='color: red;'>Erreur lors de la suppression de l'événement : " . mysqli_error($db) . "</p>";
    }
}

// Récupération des matchs depuis la base de données
$requete = "SELECT * FROM evenements";
$resultat = mysqli_query($db, $requete);

// Fermeture de la connexion à la base de données
mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des matchs</title>
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
        <h2>Liste des événements sportifs</h2>
        
        <!-- Table des événements -->
        <table border="1">
            <thead>
                <tr>
                    <th>Nom de l'événement</th>
                    <th>Équipe 1</th>
                    <th>Équipe 2</th>
                    <th>Cote Équipe 1</th>
                    <th>Cote Équipe 2</th>
                    <th>Cote Égalité</th>
                    <th>Date du match</th>
                    <th>Id Bookmaker</th>
                    <th>Actions</th> <!-- Colonne pour les actions -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Vérifier si des résultats ont été trouvés
                if (mysqli_num_rows($resultat) > 0) {
                    // Afficher chaque ligne du résultat
                    while ($row = mysqli_fetch_assoc($resultat)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nom_evenement']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['equipe_1']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['equipe_2']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['cote_equipe_1']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['cote_equipe_2']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['cote_egalite']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['date_match']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_bookmaker']) . "</td>";
                        echo "<td><a href='listematchs.php?supprimer_id=" . $row['id'] . "' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet événement ?\");'>Supprimer</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>Aucun événement trouvé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>Copyright ©2024</p>
    </footer>
</body>
</html>
