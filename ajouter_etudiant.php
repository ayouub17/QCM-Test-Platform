<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "qcm_management");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Ajouter un étudiant
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ajouter"])) {
    $matricule = $_POST["matricule"];
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $filiere = $_POST["filiere"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hasher le mot de passe

    // Préparation de la requête SQL pour éviter les injections SQL
    $sql = $conn->prepare("INSERT INTO etudiants (matricule, nom, prenom, email, filiere, password) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssssss", $matricule, $nom, $prenom, $email, $filiere, $password);

    if ($sql->execute()) {
        echo "Étudiant ajouté avec succès.";
    } else {
        echo "Erreur : " . $sql->error;
    }
    $sql->close();
}

// Supprimer un étudiant
if (isset($_GET["supprimer"])) {
    $id = intval($_GET["supprimer"]); // Conversion en entier pour éviter les injections SQL

    // Préparation de la requête SQL pour éviter les injections SQL
    $sql = $conn->prepare("DELETE FROM etudiants WHERE id = ?");
    $sql->bind_param("i", $id);

    if ($sql->execute()) {
        echo "Étudiant supprimé avec succès.";
    } else {
        echo "Erreur : " . $sql->error;
    }
    $sql->close();
}

// Récupérer la liste des étudiants
$sql = "SELECT id, matricule, nom, prenom, email, filiere FROM etudiants"; // Ne pas récupérer le mot de passe
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Étudiants</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
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

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

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

        /* Styles pour les boutons Modifier et Supprimer */
        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .actions a {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .actions a.modifier {
            background-color: #28a745; /* Vert */
            color: white;
        }

        .actions a.modifier:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .actions a.supprimer {
            background-color: #dc3545; /* Rouge */
            color: white;
        }

        .actions a.supprimer:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        /* Animation pour les boutons */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        button:hover, .back-button:hover, .actions a:hover {
            animation: pulse 0.5s ease-in-out;
        }

        /* Effet de survol sur les lignes du tableau */
        tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestion des Étudiants</h2>
        <form method="post" action="">
            <div class="form-container">
                <input type="text" name="matricule" placeholder="Matricule" required>
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Mot de passe" required> <!-- Champ mot de passe -->
                <select name="filiere" required>
                    <option>Data and Software Engineering</option>
                    <option>Actuariat-Finance</option>
                    <option>Master Systèmes d'Information & Systèmes Intelligents</option>
                </select>
                <button type="submit" name="ajouter">Ajouter</button>
            </div>
        </form>

        <!-- Bouton Retour -->
        <a href="teacher_dashboard.php" class="back-button">Retour au tableau de bord</a>

        <!-- Liste des étudiants -->
        <table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Filière</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['matricule']}</td>
                                <td>{$row['nom']}</td>
                                <td>{$row['prenom']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['filiere']}</td>
                                <td class='actions'>
                                    <a href='modifier_etudiant.php?id={$row['id']}' class='modifier'>
                                        <i class='fas fa-edit'></i> Modifier
                                    </a>
                                    <a href='ajouter_etudiant.php?supprimer={$row['id']}' class='supprimer'>
                                        <i class='fas fa-trash'></i> Supprimer
                                    </a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Aucun étudiant trouvé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>