<?php
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    // Rediriger vers la page d'accueil si l'utilisateur n'est pas administrateur
    header("Location: index.php");
    exit();
}

// Le reste du code de la page ici...

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

// Traitement du formulaire d'ajout de voiture lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_car"])) {
    $marque = $_POST["marque"];
    $modele = $_POST["modele"];
    $prix = $_POST["prix"];

    // Requête SQL pour ajouter la voiture dans la base de données
    $insert_sql = "INSERT INTO voitures (marque, modele, prix) VALUES ('$marque', '$modele', '$prix')";
    if (mysqli_query($conn, $insert_sql)) {
        // Rediriger vers la page "gestion_stocks.php" après l'ajout réussi
        header("Location: ajouter_voiture.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout de la voiture: " . mysqli_error($conn);
    }
}

// Traitement du formulaire de modification de voiture lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_car"])) {
    $car_id = $_POST["car_id"];
    $marque = $_POST["marque"];
    $modele = $_POST["modele"];
    $prix = $_POST["prix"];

    // Requête SQL pour mettre à jour les informations de la voiture dans la base de données
    $update_sql = "UPDATE voitures SET marque='$marque', modele='$modele', prix='$prix' WHERE id = $car_id";
    if (mysqli_query($conn, $update_sql)) {
        // Rediriger vers la page "gestion_stocks.php" après la mise à jour réussie
        header("Location: ajouter_voiture.php");
        exit();
    } else {
        echo "Erreur lors de la mise à jour de la voiture: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar - Gestion du site</title>
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
                if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true) {
                    // Afficher les liens des pages réservées à l'administrateur
                    echo '<li><a href="ajouter_voiture.php">Ajouter une voiture</a></li>';
                    echo '<li><a href="supprimer_voitures.php">Supprimer des modèles de voitures</a></li>';
                }
                ?>
                <li><a href="rechercher_voitures.php">Rechercher des voitures</a></li>
            </ul>
            <!-- Ajoutez ce code là où vous voulez afficher le bouton de connexion et de déconnexion -->
            <?php

            if (isset($_GET["logout"])) {
                session_unset();
                session_destroy();
                header("Location: index.php"); // Rediriger vers la page d'accueil après la déconnexion
            }

            if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                echo '<a href="login.php?logout=true" class="btn">Déconnexion</a>';
            } else {
                echo '<a href="login.php" class="btn">Connexion</a>';
            }
            ?>
        </nav>
    </header>
    <main>
        <!-- Formulaire d'ajout de voiture -->
        <h2>Ajouter une voiture</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="marque">Marque:</label>
            <input type="text" id="marque" name="marque" required><br>

            <label for="modele">Modèle:</label>
            <input type="text" id="modele" name="modele" required><br>

            <label for="prix">Prix:</label>
            <input type="number" id="prix" name="prix" required><br>

            <!-- Ajoutez le champ pour charger l'image de la voiture -->
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required><br>

            <input type="submit" name="add_car" value="Ajouter">
        </form>

        <!-- Formulaire de modification de voiture -->
        <h2>Modifier une voiture</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="car_id">Sélectionnez une voiture:</label>
            <select id="car_id" name="car_id" required>
                <option value="">-- Choisissez une voiture --</option>
                <?php
                // Récupérer les voitures de la base de données pour le formulaire de modification
                $sql = "SELECT id, marque, modele FROM voitures";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row["id"] . '">' . $row["marque"] . ' ' . $row["modele"] . '</option>';
                    }
                }
                ?>
            </select><br>

            <label for="marque">Nouvelle marque:</label>
            <input type="text" id="marque" name="marque" required><br>

            <label for="modele">Nouveau modèle:</label>
            <input type="text" id="modele" name="modele" required><br>

            <label for="prix">Nouveau prix:</label>
            <input type="number" id="prix" name="prix" required><br>

            <input type="submit" name="update_car" value="Modifier">
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> SuperCar. Tous droits réservés.</p>
    </footer>
</body>
</html>
