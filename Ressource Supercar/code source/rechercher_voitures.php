<?php
session_start();
// Connexion à la base de données (même code que précédemment)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proto";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Vérifier la connexion
if (!$conn) {
    die("Échec de la connexion à la base de données: " . mysqli_connect_error());
}

// Vérifier si le formulaire de recherche a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_marque = $_POST["marque"];
    $search_prix_min = $_POST["prix_min"];
    $search_prix_max = $_POST["prix_max"];
    $search_puissance_min = $_POST["puissance_min"];
    $search_puissance_max = $_POST["puissance_max"];
    $search_annee = $_POST["annee"];
    $search_carburant = $_POST["carburant"];

    // Requête SQL pour rechercher des voitures en fonction des critères sélectionnés
    $sql = "SELECT * FROM voitures WHERE 1=1";

    if (!empty($search_marque)) {
        $sql .= " AND marque LIKE '%$search_marque%'";
    }

    if (!empty($search_prix_min)) {
        $sql .= " AND prix >= $search_prix_min";
    }

    if (!empty($search_prix_max)) {
        $sql .= " AND prix <= $search_prix_max";
    }

    if (!empty($search_puissance_min)) {
        $sql .= " AND puissance >= $search_puissance_min";
    }

    if (!empty($search_puissance_max)) {
        $sql .= " AND puissance <= $search_puissance_max";
    }

    if (!empty($search_annee)) {
        $sql .= " AND annee_fabrication = $search_annee";
    }

    if (!empty($search_carburant)) {
        $sql .= " AND carburant = '$search_carburant'";
    }

    $result = mysqli_query($conn, $sql);
} else {
    // Ne pas exécuter de requête si aucune recherche n'a été effectuée
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar - Rechercher des voitures</title>
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
        <section class="search-cars">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <h2 class="text-center">Rechercher des voitures</h2>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="marque">Marque :</label>
                                <input type="text" class="form-control" id="marque" name="marque" placeholder="Marque">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="prix_min">Prix minimum :</label>
                                    <input type="number" class="form-control" id="prix_min" name="prix_min" placeholder="Prix minimum">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="prix_max">Prix maximum :</label>
                                    <input type="number" class="form-control" id="prix_max" name="prix_max" placeholder="Prix maximum">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="puissance_min">Puissance minimum :</label>
                                    <input type="number" class="form-control" id="puissance_min" name="puissance_min" placeholder="Puissance minimum">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="puissance_max">Puissance maximum :</label>
                                    <input type="number" class="form-control" id="puissance_max" name="puissance_max" placeholder="Puissance maximum">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="annee">Année de fabrication :</label>
                                    <input type="number" class="form-control" id="annee" name="annee" placeholder="Année de fabrication">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="carburant">Type de carburant :</label>
                                    <select class="form-control" id="carburant" name="carburant">
                                        <option value="">Sélectionner un type de carburant</option>
                                        <option value="Essence">Essence</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Hybride">Hybride</option>
                                        <option value="Électrique">Électrique</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Rechercher</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($result !== false): ?>
        <section class="search-results">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="text-center">Résultats de la recherche</h2>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Marque</th>
                                    <th>Modèle</th>
                                    <th>Prix</th>
                                    <th>Puissance</th>
                                    <th>Année de fabrication</th>
                                    <th>Carburant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row["marque"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["modele"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["prix"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["puissance"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["annee_fabrication"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["carburant"]); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-center">Aucune voiture trouvée.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <footer>
        <p class="text-center">&copy; <?php echo date("Y"); ?> SuperCar. Tous droits réservés.</p>
    </footer>
</body>
</html>

<?php
// Fermer la connexion à la base de données
mysqli_close($conn);
?>
