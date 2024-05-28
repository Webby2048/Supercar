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

// Récupérer l'image placeholder depuis la table "placeholders"
$placeholder_image = "";
$sql_placeholder = "SELECT image FROM placeholders WHERE id = 1";
$result_placeholder = mysqli_query($conn, $sql_placeholder);
if ($row_placeholder = mysqli_fetch_assoc($result_placeholder)) {
    $placeholder_image = $row_placeholder["image"];
}

// Image par défaut si la base de données ne retourne pas d'image
$default_image = "images/default_car_image.jpg";

// Récupérer les voitures de la base de données
$sql = "SELECT * FROM voitures";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar - Voitures</title>
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
        <h2 class="text-center">Liste des voitures</h2>

        <div class="container">
            <div class="row">
                <?php
                // Afficher les voitures
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="col-md-4">';
                        echo '<div class="card mb-4">';
                        // L'image pour chaque voiture
                        $image = !empty($row["image"]) ? $row["image"] : $default_image;
                        echo '<img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($row["modele"]) . '" class="card-img-top">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($row["modele"]) . '</h5>';
                        // Afficher la marque, le prix, la puissance, l'année de fabrication et le type de carburant de la voiture
                        echo '<p class="card-text">Marque: ' . htmlspecialchars($row["marque"]) . '</p>';
                        echo '<p class="card-text">Prix: ' . htmlspecialchars($row["prix"]) . '</p>';
                        echo '<p class="card-text">Puissance: ' . htmlspecialchars($row["puissance"]) . '</p>';
                        echo '<p class="card-text">Année de fabrication: ' . htmlspecialchars($row["annee_fabrication"]) . '</p>';
                        echo '<p class="card-text">Carburant: ' . htmlspecialchars($row["carburant"]) . '</p>';
                        echo '<a href="demander_essai.php?id=' . $row["id"] . '" class="btn btn-primary">Demander un essai</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="col-md-12">';
                    echo '<p class="text-center">Aucune voiture disponible.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

    </main>

    <!-- ... -->

    <footer class="fixed-bottom">
        <p class="text-center">&copy; <?php echo date("Y"); ?> SuperCar. Tous droits réservés.</p>
    </footer>
</body>
</html>

<?php
// Fermer la connexion à la base de données
mysqli_close($conn);
?>
