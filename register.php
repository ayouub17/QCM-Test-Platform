<?php
session_start();
$message = "";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connexion à la base de données
    $conn = new mysqli("localhost", "root", "", "qcm_management");
    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    // Récupération des données du formulaire avec validation
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Vérifier si les champs sont remplis
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        die("Tous les champs sont obligatoires.");
    }

    // Vérification du format de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Format d'email invalide.");
    }

    // Hachage sécurisé du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Vérifier si l'email existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Erreur : cet email est déjà utilisé.");
    }
    $stmt->close();

    // Insérer l'utilisateur dans la table users
    $stmt = $conn->prepare("INSERT INTO users (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, 'teacher')");
    $stmt->bind_param("ssss", $nom, $prenom, $email, $hashed_password);

    if (!$stmt->execute()) {
        die("Erreur lors de l'inscription de l'utilisateur : " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Redirection après succès
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Professeur</title>
    <link rel="stylesheet" href="register_teacher.css">
</head>
<body>
    <div class="container">
        <h2>Création du compte Professeur</h2>
        <form method="post" action="">
            <label>Nom</label>
            <input type="text" name="nom" required>
            <label>Prénom</label>
            <input type="text" name="prenom" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Mot de passe</label>
            <input type="password" name="password" required>
            <button type="submit">S'inscrire</button>
        </form>
        <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
    </div>
</body>
</html>
