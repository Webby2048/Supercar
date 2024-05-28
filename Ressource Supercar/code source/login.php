<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

// Vérifier si le formulaire de connexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Remplacez cette partie par votre logique de vérification d'identifiants
    if ($username === "admin" && $password === "password") {
        // Démarrer une session et enregistrer l'état de connexion
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username; // Sauvegarder le nom d'utilisateur dans la session

        // Vérifier si l'utilisateur est l'administrateur
        if ($username === "admin") {
            $_SESSION["is_admin"] = true; // Créer une variable de session spécifique pour l'administrateur
        }

        // Rediriger vers la page index.php après la connexion réussie
        header("Location: index.php");
        exit();
    } else {
        $error = "Identifiants invalides. Veuillez réessayer.";
    }
}

// Gérer la déconnexion
if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar - Connexion</title>
    <!-- Lien vers le fichier Bootstrap -->
    <link rel="stylesheet" href="bootstrap.css">
    <!-- Lien vers votre fichier de style personnalisé -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>SuperCar</h1>
    </header>

    <main>
        <section class="login">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <h2 class="text-center">Connexion</h2>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="username">Nom d'utilisateur:</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Mot de passe:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Se connecter</button>
                        </form>
                        <?php
                        if (isset($error)) {
                            echo "<p class='text-center text-danger'>$error</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p class="text-center">&copy; <?php echo date("Y"); ?> SuperCar. Tous droits réservés.</p>
    </footer>
</body>
</html>
