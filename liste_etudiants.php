<?php
include("teacher_dashboard.php");

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "qcm_management");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Supprimer un étudiant
if (isset($_GET["supprimer"])) {
    $id = intval($_GET["supprimer"]); // Sécurisation
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
$sql = "SELECT id, matricule, nom, prenom, email, filiere FROM etudiants";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Étudiants</title>
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

        .dashboard-button {
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

        .dashboard-button:hover {
            background-color: #0056b3;
        }

        .actions a {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
        }

        .actions a.supprimer {
            background-color: #dc3545; /* Rouge */
            color: white;
        }

        .actions a.supprimer:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .actions a.resultats {
            background-color: #28a745; /* Vert */
            color: white;
        }

        .actions a.resultats:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .dashboard-button:hover, .actions a:hover {
            animation: pulse 0.5s ease-in-out;
        }

        tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Liste des Étudiants</h2>

        <a href="teacher_dashboard.php" class="dashboard-button">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>

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
                                    <a href='liste_etudiants.php?supprimer={$row['id']}' class='supprimer'>
                                        <i class='fas fa-trash'></i> Supprimer
                                    </a>
                                    <a href='resultats_qcm.php?id_etudiant={$row['id']}' class='resultats'>
                                        <i class='fas fa-poll'></i> Résultats QCM
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
