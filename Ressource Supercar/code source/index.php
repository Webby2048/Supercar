<?php
session_start();
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proto";
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Vérifier la connexion
if (!$conn) {
    die("Échec de la connexion à la base de données: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar - Accueil</title>
    <!-- Lien vers le fichier Bootstrap -->
    <link rel="stylesheet" href="bootstrap.css">
    <!-- Lien vers votre fichier de style personnalisé -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>SuperCar</h1>

        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="voitures.php">Liste des voitures</a></li>
                <?php
                // Vérifier si l'utilisateur est administrateur
                if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true) {
                    // Afficher les liens des pages réservées à l'administrateur
                    echo '<li><a href="gestion_stocks.php">Gestion du site</a></li>';
                }
                ?>
                <li><a href="rechercher_voitures.php">Rechercher des voitures</a></li>
            </ul>

            <?php
            // Ajoutez ce code là où vous voulez afficher les boutons de connexion et de déconnexion 
            if (isset($_GET["logout"])) {
                session_unset();
                session_destroy();
                header("Location: index.php"); // Rediriger vers la page d'accueil après la déconnexion
            }

            if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                echo '<a href="login.php?logout=true" class="btn btn-danger">Déconnexion</a>';
            } else {
                echo '<a href="login.php" class="btn btn-primary">Connexion</a>';
            }
            ?>
        </nav>
    </header>
    <main>
        <section class="hero">
            <h2>Bienvenue chez SuperCar</h2>
            <p>L'expérience ultime de conduite de voitures de luxe</p>
            <a href="voitures.php" class="btn btn-primary">Découvrir nos voitures</a>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> SuperCar. Tous droits réservés.</p>
    </footer>
</body>
</html>

<?php
// Fermer la connexion à la base de données
mysqli_close($conn);
?>
