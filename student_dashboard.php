<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qcm_management";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Échec de connexion : " . $conn->connect_error);
    }

    // Récupérer les informations de l'étudiant
    $etudiant_id = $_SESSION['user_id'];
    $sql = "SELECT nom, prenom, email, filiere FROM etudiants WHERE id = ?";
    
    if (!($etudiant_stmt = $conn->prepare($sql))) {
        throw new Exception("Erreur de préparation (etudiants): " . $conn->error);
    }
    
    $etudiant_stmt->bind_param("i", $etudiant_id);
    
    if (!$etudiant_stmt->execute()) {
        throw new Exception("Erreur d'exécution (etudiants): " . $etudiant_stmt->error);
    }
    
    $etudiant_result = $etudiant_stmt->get_result();
    $etudiant = $etudiant_result->fetch_assoc();
    $etudiant_stmt->close();

    // Récupérer les QCM disponibles (version corrigée sans le champ 'disponible')
    $sql = "SELECT id, titre, description FROM qcm";
    
    if (!($qcm_stmt = $conn->prepare($sql))) {
        throw new Exception("Erreur de préparation (qcm): " . $conn->error);
    }
    
    if (!$qcm_stmt->execute()) {
        throw new Exception("Erreur d'exécution (qcm): " . $qcm_stmt->error);
    }
    
    $qcm_result = $qcm_stmt->get_result();
    $qcm_stmt->close();

    // Récupérer les résultats passés
    $sql = "SELECT r.id, r.score, r.date_passe, q.titre 
            FROM results r
            JOIN qcm q ON r.qcm_id = q.id
            WHERE r.etudiant_id = ?
            ORDER BY r.date_passe DESC
            LIMIT 5";
            
    if (!($results_stmt = $conn->prepare($sql))) {
        throw new Exception("Erreur de préparation (results): " . $conn->error);
    }
    
    $results_stmt->bind_param("i", $etudiant_id);
    
    if (!$results_stmt->execute()) {
        throw new Exception("Erreur d'exécution (results): " . $results_stmt->error);
    }
    
    $results = $results_stmt->get_result();
    $results_stmt->close();

    $conn->close();

} catch (Exception $e) {
    die("Erreur: " . $e->getMessage());
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.3s;
            height: 100%;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .score-high {
            color: #28a745;
            font-weight: bold;
        }
        .score-medium {
            color: #ffc107;
            font-weight: bold;
        }
        .score-low {
            color: #dc3545;
            font-weight: bold;
        }
        .profile-card {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">QCM Platform</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Connecté en tant que: <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . htmlspecialchars($etudiant['nom'])); ?>
                </span>
                <a class="btn btn-outline-light" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="profile-card p-4">
                    <h2>Bienvenue, <?php echo htmlspecialchars($etudiant['prenom']); ?>!</h2>
                    <p class="mb-0">Filière: <?php echo htmlspecialchars($etudiant['filiere']); ?></p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <h3 class="mb-4">QCM Disponibles</h3>
                <div class="row">
                    <?php while ($qcm = $qcm_result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($qcm['titre']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($qcm['description']); ?></p>
                                <a href="take_qcm.php?qcm_id=<?php echo $qcm['id']; ?>" class="btn btn-primary">Commencer</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3 class="mb-4">Vos Derniers Résultats</h3>
                <?php if ($results->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>QCM</th>
                                <th>Score</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($result = $results->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['titre']); ?></td>
                                <td class="<?php 
                                    if ($result['score'] >= 70) echo 'score-high';
                                    elseif ($result['score'] >= 40) echo 'score-medium';
                                    else echo 'score-low';
                                ?>">
                                    <?php echo $result['score']; ?>%
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($result['date_passe'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                </div>
                <?php else: ?>
                <div class="alert alert-info">Vous n'avez pas encore passé de QCM.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>