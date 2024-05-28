<?php
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    // Rediriger vers la page d'accueil si l'utilisateur n'est pas administrateur
    header("Location: index.php");
    exit();
}

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
    $puissance = $_POST["puissance"];
    $annee_fabrication = $_POST["annee_fabrication"];
    $carburant = $_POST["carburant"];

    // Vérifier si une nouvelle image a été chargée
    if (isset($_FILES["car_image"]) && $_FILES["car_image"]["error"] === UPLOAD_ERR_OK) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["car_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérifier si le fichier est une image
        $check = getimagesize($_FILES["car_image"]["tmp_name"]);
        if ($check !== false) {
            // Enregistrez l'image dans le dossier "images"
            move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file);

            // Stocker le chemin d'accès de l'image dans la base de données
            $insert_sql = "INSERT INTO voitures (marque, modele, prix, image, puissance, annee_fabrication, carburant) VALUES ('$marque', '$modele', '$prix', '$target_file', '$puissance', '$annee_fabrication', '$carburant')";
            if (mysqli_query($conn, $insert_sql)) {
                // Rediriger vers la page "gestion_stocks.php" après l'ajout réussi
                header("Location: gestion_stocks.php");
                exit();
            } else {
                echo "Erreur lors de l'ajout de la voiture: " . mysqli_error($conn);
            }
        } else {
            echo "Le fichier n'est pas une image valide.";
        }
    } else {
        // Ajout sans image
        $insert_sql = "INSERT INTO voitures (marque, modele, prix, puissance, annee_fabrication, carburant) VALUES ('$marque', '$modele', '$prix', '$puissance', '$annee_fabrication', '$carburant')";
        if (mysqli_query($conn, $insert_sql)) {
            // Rediriger vers la page "gestion_stocks.php" après l'ajout réussi
            header("Location: gestion_stocks.php");
            exit();
        } else {
            echo "Erreur lors de l'ajout de la voiture: " . mysqli_error($conn);
        }
    }
}

// Traitement du formulaire de modification de voiture lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_car"])) {
    $car_id = $_POST["car_id"];
    $marque = $_POST["new_marque"];
    $modele = $_POST["new_modele"];
    $prix = $_POST["new_prix"];
    $puissance = $_POST["new_puissance"];
    $annee_fabrication = $_POST["new_annee_fabrication"];
    $carburant = $_POST["new_carburant"];

    // Vérifier si une nouvelle image a été chargée
    if (isset($_FILES["new_car_image"]) && $_FILES["new_car_image"]["error"] === UPLOAD_ERR_OK) {
        $target_file = "images/" . basename($_FILES["new_car_image"]["name"]);
        move_uploaded_file($_FILES["new_car_image"]["tmp_name"], $target_file);
        $update_sql = "UPDATE voitures SET marque='$marque', modele='$modele', prix='$prix', puissance='$puissance', annee_fabrication='$annee_fabrication', carburant='$carburant', image='$target_file' WHERE id = $car_id";
    } else {
        $update_sql = "UPDATE voitures SET marque='$marque', modele='$modele', prix='$prix', puissance='$puissance', annee_fabrication='$annee_fabrication', carburant='$carburant' WHERE id = $car_id";
    }

    if (mysqli_query($conn, $update_sql)) {
        // Rediriger vers la page "gestion_stocks.php" après la mise à jour réussie
        header("Location: gestion_stocks.php");
        exit();
    } else {
        echo "Erreur lors de la mise à jour de la voiture: " . mysqli_error($conn);
    }
}

// Traitement du formulaire de changement de l'image placeholder lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_placeholder"])) {
    // Vérifier si une nouvelle image placeholder a été chargée
    if (isset($_FILES["placeholder_image"]) && $_FILES["placeholder_image"]["error"] === UPLOAD_ERR_OK) {
        $target_file = "images/" . basename($_FILES["placeholder_image"]["name"]);
        move_uploaded_file($_FILES["placeholder_image"]["tmp_name"], $target_file);
        $update_sql = "UPDATE placeholders SET image='$target_file' WHERE id = 1";
        if (mysqli_query($conn, $update_sql)) {
            // Rediriger vers la page "gestion_stocks.php" après le changement réussi de l'image placeholder
            header("Location: gestion_stocks.php");
            exit();
        } else {
            echo "Erreur lors du changement de l'image placeholder: " . mysqli_error($conn);
        }
    } else {
        echo "Aucune nouvelle image placeholder n'a été sélectionnée.";
    }
}

// Traitement du formulaire de suppression de voitures lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_cars"])) {
    // Vérifier si des modèles de voitures ont été sélectionnés
    if (isset($_POST["modeles"]) && is_array($_POST["modeles"])) {
        foreach ($_POST["modeles"] as $modele) {
            // Échapper les caractères spéciaux pour éviter les injections SQL
            $modele = mysqli_real_escape_string($conn, $modele);

            // Requête SQL pour supprimer les voitures ayant le modèle sélectionné
            $delete_sql = "DELETE FROM voitures WHERE modele = '$modele'";
            if (mysqli_query($conn, $delete_sql)) {
                // Rediriger vers la page "gestion_stocks.php" après la suppression réussie
                header("Location: gestion_stocks.php");
                exit();
            } else {
                echo "Erreur lors de la suppression de la voiture: " . mysqli_error($conn);
            }
        }
    } else {
        echo "Veuillez sélectionner au moins un modèle de voiture à supprimer.";
    }
}

// Récupérer les voitures de la base de données
$sql = "SELECT * FROM voitures";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar - Gestion des stocks</title>
    <!-- Lien vers le fichier Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === true) {
                    // Afficher les liens des pages réservées à l'administrateur
                    echo '<li><a href="gestion_stocks.php">Gestion du site</a></li>';
                }
                ?>
                <li><a href="rechercher_voitures.php">Rechercher des voitures</a></li>
            </ul>

            <!-- Ajoutez ce code là où vous voulez afficher le bouton de déconnexion -->
            <?php
            if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                echo '<a href="login.php?logout=true" class="btn">Déconnexion</a>';
            }
            ?>
        </nav>
    </header>
    <main class="container mt-5">

        <!-- Formulaire d'ajout de voiture -->
        <h2>Ajouter une voiture</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="marque">Marque:</label>
                <input type="text" class="form-control" id="marque" name="marque" required>
            </div>
            <div class="form-group">
                <label for="modele">Modèle:</label>
                <input type="text" class="form-control" id="modele" name="modele" required>
            </div>
            <div class="form-group">
                <label for="prix">Prix:</label>
                <input type="number" class="form-control" id="prix" name="prix" required>
            </div>
            <div class="form-group">
                <label for="puissance">Puissance:</label>
                <input type="number" class="form-control" id="puissance" name="puissance" required>
            </div>
            <div class="form-group">
                <label for="annee_fabrication">Année de fabrication:</label>
                <input type="number" class="form-control" id="annee_fabrication" name="annee_fabrication" required>
            </div>
            <div class="form-group">
                <label for="carburant">Type de carburant:</label>
                <select class="form-control" id="carburant" name="carburant" required>
                    <option value="">Sélectionner un type de carburant</option>
                    <option value="Essence">Essence</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Hybride">Hybride</option>
                    <option value="Électrique">Électrique</option>
                </select>
            </div>
            <!-- Champ pour charger l'image de la voiture -->
            <div class="form-group">
                <label for="car_image">Image:</label>
                <input type="file" class="form-control-file" id="car_image" name="car_image" accept="image/*">
            </div>
            <button type="submit" name="add_car" class="btn btn-primary">Ajouter</button>
        </form>

        <!-- Formulaire de modification de voiture -->
        <h2>Modifier une voiture</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="car_id">Sélectionnez une voiture:</label>
                <select class="form-control" id="car_id" name="car_id" required>
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
                </select>
            </div>
            <div class="form-group">
                <label for="new_marque">Nouvelle marque:</label>
                <input type="text" class="form-control" id="new_marque" name="new_marque" required>
            </div>
            <div class="form-group">
                <label for="new_modele">Nouveau modèle:</label>
                <input type="text" class="form-control" id="new_modele" name="new_modele" required>
            </div>
            <div class="form-group">
                <label for="new_prix">Nouveau prix:</label>
                <input type="number" class="form-control" id="new_prix" name="new_prix" required>
            </div>
            <div class="form-group">
                <label for="new_puissance">Nouvelle puissance:</label>
                <input type="number" class="form-control" id="new_puissance" name="new_puissance" required>
            </div>
            <div class="form-group">
                <label for="new_annee_fabrication">Nouvelle année de fabrication:</label>
                <input type="number" class="form-control" id="new_annee_fabrication" name="new_annee_fabrication" required>
            </div>
            <div class="form-group">
                <label for="new_carburant">Nouveau type de carburant:</label>
                <select class="form-control" id="new_carburant" name="new_carburant" required>
                    <option value="">Sélectionner un type de carburant</option>
                    <option value="Essence">Essence</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Hybride">Hybride</option>
                    <option value="Électrique">Électrique</option>
                </select>
            </div>
            <!-- Champ pour charger une nouvelle image de voiture -->
            <div class="form-group">
                <label for="new_car_image">Nouvelle image:</label>
                <input type="file" class="form-control-file" id="new_car_image" name="new_car_image" accept="image/*">
            </div>
            <button type="submit" name="update_car" class="btn btn-primary">Modifier</button>
        </form>

        <!-- Formulaire de changement de l'image placeholder -->
        <h2 id="change_placeholder">Changer l'image placeholder</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="placeholder_image">Nouvelle image placeholder:</label>
                <input type="file" class="form-control-file" id="placeholder_image" name="placeholder_image" accept="image/*">
            </div>
            <button type="submit" name="update_placeholder" class="btn btn-primary">Changer l'image placeholder</button>
        </form>

        <!-- Formulaire de suppression de voiture -->
        <h2>Supprimer des modèles de voitures</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <p>Sélectionnez les modèles de voitures à supprimer :</p>
            <?php
            // Récupérer les modèles de voitures de la base de données pour le formulaire de suppression
            $sql = "SELECT DISTINCT modele FROM voitures";
            $result = mysqli_query($conn, $sql);

            // Afficher les modèles de voitures
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="form-check">';
                    echo '<input class="form-check-input" type="checkbox" name="modeles[]" value="' . htmlspecialchars($row["modele"]) . '">';
                    echo '<label class="form-check-label">' . $row["modele"] . '</label>';
                    echo '</div>';
                }
            } else {
                echo "Aucun modèle de voiture trouvé.";
            }
            ?>
            <br>
            <button type="submit" name="delete_cars" class="btn btn-danger">Supprimer</button>
        </form>
    </main>

    <footer>
        <p class="text-center">&copy; <?php echo date("Y"); ?> SuperCar. Tous droits réservés.</p>
    </footer>

    <!-- Scripts JavaScript Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Fermer la connexion à la base de données
mysqli_close($conn);
?>
