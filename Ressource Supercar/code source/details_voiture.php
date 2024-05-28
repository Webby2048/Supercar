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

// Vérifier si l'ID de la voiture est passé en paramètre d'URL
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $car_id = $_GET["id"];

    // Récupérer les informations de la voiture à partir de l'ID
    $sql = "SELECT * FROM voitures WHERE id = $car_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $car = mysqli_fetch_assoc($result);
    } else {
        echo "Aucune voiture trouvée avec cet ID.";
        exit();
    }
} else {
    echo "Aucun ID de voiture spécifié.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site de vente de voitures - Détails de la voiture</title>
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
              // Vérifier si l'utilisateur est connecté et s'il n'est pas administrateur
              if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
                  echo '<li><a href="inscription.php" class="btn">Inscription</a></li>';
              }
          ?>

          <?php
          // Ajoutez ce code là où vous voulez afficher les boutons de connexion et de déconnexion 
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
        <h2>Détails de la voiture</h2>
        <p>
            <strong>Marque:</strong> <?php echo $car["marque"]; ?><br>
            <strong>Modèle:</strong> <?php echo $car["modele"]; ?><br>
            <strong>Prix:</strong> <?php echo $car["prix"]; ?><br>
            <!-- Ajoutez d'autres informations si nécessaire -->
        </p>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> SuperCar. Tous droits réservés.</p>
    </footer>
</body>
</html>
