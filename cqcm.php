<?php
// create_qcm.php

session_start(); // Démarrer la session pour vérifier si l'utilisateur est connecté

// Vérifier si l'utilisateur est connecté et a le rôle de professeur
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Rediriger vers la page de connexion
    exit();
}

// Inclure la connexion à la base de données
require 'db.php';

// Récupérer l'ID du professeur connecté
$professeur_id = $_SESSION['user_id'];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];

    // Insérer le QCM dans la base de données
    $sql = "INSERT INTO qcm (titre, description, professeur_id) VALUES (:titre, :description, :professeur_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titre' => $titre,
        ':description' => $description,
        ':professeur_id' => $professeur_id
    ]);

    // Rediriger vers une page de confirmation ou afficher un message
    header("Location: qcm_created.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un QCM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Créer un QCM</h1>
    <form method="POST" action="create_qcm.php">
        <div>
            <label for="titre">Titre du QCM :</label>
            <input type="text" id="titre" name="titre" required>
        </div>
        <div>
            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>
        <div>
            <input type="submit" value="Créer le QCM">
        </div>
    </form>
</body>
</html>