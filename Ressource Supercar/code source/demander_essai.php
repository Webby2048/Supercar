<?php
session_start();
// Vérifier si l'utilisateur est connecté, sinon le rediriger vers la page de connexion
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

// Vérifier si l'ID de la voiture est passé en paramètre d'URL
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $car_id = $_GET["id"];

    // Connexion à la base de données
    $servername = "mysql-supercar.alwaysdata.net";
    $username = "supercar";
    $password = "fgh123b456";
    $dbname = "supercar_said";
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if (!$conn) {
        die("Échec de la connexion à la base de données: " . mysqli_connect_error());
    }

    // Récupérer les informations de la voiture à partir de l'ID
    $sql = "SELECT * FROM voitures WHERE id = $car_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $car = mysqli_fetch_assoc($result);
    } else {
        echo "Aucune voiture trouvée avec cet ID.";
        exit();
    }

    // Traitement du formulaire de demande d'essai lors de la soumission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST["nom"];
        $email = $_POST["email"];
        $message = $_POST["message"];

        // Requête SQL pour insérer la demande d'essai dans la base de données
        $insert_sql = "INSERT INTO demandes_essai (voiture_id, nom, email, message) VALUES ('$car_id', '$nom', '$email', '$message')";
        if (mysqli_query($conn, $insert_sql)) {
            echo "Demande d'essai envoyée avec succès.";
            // Vous pouvez également rediriger l'utilisateur vers une page de confirmation ici.
        } else {
            echo "Erreur lors de l'envoi de la demande d'essai: " . mysqli_error($conn);
        }
    }

    // Fermer la connexion à la base de données
    mysqli_close($conn);
} else {
    echo "Aucun ID de voiture spécifié.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SuperCar - Demander un essai</title>
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
        <div class="container">
            <h2>Demander un essai pour la voiture</h2>
            <p>
                <strong>Marque:</strong> <?php echo $car["marque"]; ?><br>
                <strong>Modèle:</strong> <?php echo $car["modele"]; ?><br>
                <strong>Prix:</strong> <?php echo $car["prix"]; ?><br>
                <!-- Ajoutez d'autres informations si nécessaire -->
            </p>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $car_id; ?>">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="message">Message:</label>
                <textarea id="message" name="message" required></textarea><br>

                <input type="submit" value="Envoyer la demande" class="btn btn-primary">
            </form>
        </div>
    </main>
</body>
<footer>
    <p>&copy; <?php echo date("Y"); ?> SuperCar. Tous droits réservés.</p>
</footer>
</html>
