<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "qcm_management");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer l'ID de l'étudiant à modifier
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Récupérer les données de l'étudiant
$sql = $conn->prepare("SELECT id, matricule, nom, prenom, email, filiere FROM etudiants WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
$result = $sql->get_result();
$etudiant = $result->fetch_assoc();
$sql->close();

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["modifier"])) {
    $matricule = $_POST["matricule"];
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $filiere = $_POST["filiere"];

    // Préparation de la requête SQL pour éviter les injections SQL
    $sql = $conn->prepare("UPDATE etudiants SET matricule=?, nom=?, prenom=?, email=?, filiere=? WHERE id=?");
    $sql->bind_param("sssssi", $matricule, $nom, $prenom, $email, $filiere, $id);

    if ($sql->execute()) {
        // Redirection vers index.php après la modification
        header("Location: ajouter_etudiant.php");
        exit(); // Assurez-vous de terminer le script après la redirection
    } else {
        echo "Erreur : " . $sql->error;
    }
    $sql->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Étudiant</title>
    <!-- Intégration de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input, select, button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        input:focus, select:focus, button:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button, .btn {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        button:hover, .btn:hover {
            background-color: #0056b3;
        }

        /* Animation pour les boutons */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        button:hover, .btn:hover {
            animation: pulse 0.5s ease-in-out;
        }

        /* Bouton Retour */
        .back-button {
            display: inline-block;
            margin-top: 10px;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Modifier un Étudiant</h2>
        <!-- Bouton Retour -->
        <a href="ajouter_etudiant.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <form method="post" action="">
            <div class="form-container">
                <input type="text" name="matricule" placeholder="Matricule" value="<?= $etudiant['matricule'] ?? '' ?>" required>
                <input type="text" name="nom" placeholder="Nom" value="<?= $etudiant['nom'] ?? '' ?>" required>
                <input type="text" name="prenom" placeholder="Prénom" value="<?= $etudiant['prenom'] ?? '' ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?= $etudiant['email'] ?? '' ?>" required>
                <select name="filiere" required>
                    <option <?= ($etudiant['filiere'] ?? '') == 'Data and Software Engineering' ? 'selected' : '' ?>>Data and Software Engineering</option>
                    <option <?= ($etudiant['filiere'] ?? '') == 'Actuariat-Finance' ? 'selected' : '' ?>>Actuariat-Finance</option>
                    <option <?= ($etudiant['filiere'] ?? '') == 'Master Systèmes d\'Information & Systèmes Intelligents' ? 'selected' : '' ?>>Master Systèmes d'Information & Systèmes Intelligents</option>
                </select>
                <button type="submit" name="modifier">Modifier</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>