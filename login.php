<?php
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["userType"];

    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $dbpassword = "";
    $dbname = "qcm_management";

    $conn = new mysqli($servername, $username, $dbpassword, $dbname);
    
    if ($conn->connect_error) {
        die("Échec de connexion : " . $conn->connect_error);
    }

    if ($role == "teacher") {
        // Authentification pour les professeurs (table users)
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? AND role = 'teacher'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $role;
                $_SESSION['user_type'] = 'teacher';
                header("Location: teacher_dashboard.php");
                exit();
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Email , mot de passe ou rôle incorrect.";
        }
        $stmt->close();
    } elseif ($role == "student") {
        // Authentification pour les étudiants (table etudiants)
        $stmt = $conn->prepare("SELECT id, password FROM etudiants WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $role;
                $_SESSION['user_type'] = 'student';
                header("Location: student_dashboard.php");
                exit();
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Email , mot de passe ou rôle incorrect.";
        }
        $stmt->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        /* Style général */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Conteneur du formulaire */
        .container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        /* Titre */
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 1.5rem;
        }

        /* Messages d'erreur */
        .error {
            color: #ff0000;
            text-align: center;
            margin-bottom: 1rem;
        }

        /* Labels */
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: bold;
        }

        /* Champs de formulaire */
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        /* Bouton de soumission */
        button {
            width: 100%;
            padding: 0.75rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        /* Lien d'inscription */
        p {
            text-align: center;
            margin-top: 1rem;
            color: #555;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Logo INSEA -->
    <img src="insea.png" alt="Logo INSEA" style="display: block; margin: 0 auto 1rem auto; max-width: 100px;">
    
    <h2>Connexion</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <form method="post" action="">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Mot de passe</label>
        <input type="password" name="password" required>
        <label>Type d'utilisateur</label>
        <select name="userType">
            <option value="student">Étudiant</option>
            <option value="teacher">Professeur</option>
        </select>
        <button type="submit">Se connecter</button>
    </form>
    
    <p>Créer un compte ? <a href="register.php">Inscription Professeur</a></p>
</div>

<footer style="position: fixed; bottom: 10px; right: 20px; color: #888; font-size: 0.9rem;">
    &copy;  Copyright ayoub 2024
</body>
</html>