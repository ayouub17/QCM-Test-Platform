<?php
include("teacher_dashboard.php");

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "qcm_management");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si un ID étudiant est passé
if (!isset($_GET['id_etudiant'])) {
    die("Aucun étudiant sélectionné.");
}

$id_etudiant = intval($_GET['id_etudiant']);

// Récupérer les informations de l'étudiant
$sql_etudiant = $conn->prepare("SELECT nom, prenom, matricule FROM etudiants WHERE id = ?");
$sql_etudiant->bind_param("i", $id_etudiant);
$sql_etudiant->execute();
$result_etudiant = $sql_etudiant->get_result();

if ($result_etudiant->num_rows == 0) {
    die("Étudiant non trouvé.");
}

$etudiant = $result_etudiant->fetch_assoc();

// Récupérer les résultats du QCM de cet étudiant
$sql_resultats = $conn->prepare("
    SELECT r.score, r.date_passe, q.titre AS qcm_nom
    FROM results r
    INNER JOIN qcm q ON r.qcm_id = q.id
    WHERE r.etudiant_id = ?
    ORDER BY r.date_passe DESC
");
$sql_resultats->bind_param("i", $id_etudiant);
$sql_resultats->execute();
$result_resultats = $sql_resultats->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats QCM - <?php echo htmlspecialchars($etudiant['prenom'] . " " . $etudiant['nom']); ?></title>
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
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }

        .dashboard-button {
            display: inline-block;
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
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
        
        .score-excellent {
            color: #28a745;
            font-weight: bold;
        }
        
        .score-bon {
            color: #17a2b8;
        }
        
        .score-moyen {
            color: #ffc107;
        }
        
        .score-faible {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Résultats de <?php echo htmlspecialchars($etudiant['prenom'] . " " . $etudiant['nom']); ?> (Matricule : <?php echo htmlspecialchars($etudiant['matricule']); ?>)</h2>

    <a href="liste_etudiants.php" class="dashboard-button">
        <i class="fas fa-arrow-left"></i> Retour à la liste des étudiants
    </a>

    <?php if ($result_resultats->num_rows > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th>Nom du QCM</th>
                    <th>Score obtenu</th>
                    <th>Date de passage</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result_resultats->fetch_assoc()) {
                    // Déterminer la classe CSS en fonction du score
                    $score_class = '';
                    if ($row['score'] >= 80) {
                        $score_class = 'score-excellent';
                    } elseif ($row['score'] >= 50) {
                        $score_class = 'score-bon';
                    } elseif ($row['score'] > 0) {
                        $score_class = 'score-moyen';
                    } else {
                        $score_class = 'score-faible';
                    }
                    
                    echo "<tr>
                            <td>" . htmlspecialchars($row['qcm_nom']) . "</td>
                            <td class='" . $score_class . "'>" . htmlspecialchars($row['score']) . "%</td>
                            <td>" . htmlspecialchars(date('d/m/Y H:i', strtotime($row['date_passe']))) . "</td>
                          </tr>";
                }
                ?> 
            </tbody>
        </table>
    <?php } else { ?>
        <p>Aucun résultat trouvé pour cet étudiant.</p>
    <?php } ?>
</div>

</body>
</html>

<?php
$conn->close();
?>