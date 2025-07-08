<?php
session_start();
require_once 'db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Vérification des paramètres
if (!isset($_GET['qcm_id'], $_GET['score'], $_GET['total'])) {
    header("Location: student_dashboard.php");
    exit();
}

$qcm_id = intval($_GET['qcm_id']);
$score = intval($_GET['score']);
$total_questions = intval($_GET['total']);
$correct_answers = round(($score / 100) * $total_questions);
$etudiant_id = $_SESSION['user_id'];

// Récupérer les infos du QCM
$qcm_stmt = $conn->prepare("SELECT titre, description FROM qcm WHERE id = ?");
$qcm_stmt->bind_param("i", $qcm_id);
$qcm_stmt->execute();
$qcm_result = $qcm_stmt->get_result();

if ($qcm_result->num_rows === 0) {
    header("Location: student_dashboard.php");
    exit();
}

$qcm_data = $qcm_result->fetch_assoc();

// Déterminer la classe CSS en fonction du score
$score_class = '';
if ($score >= 70) {
    $score_class = 'text-success';
} elseif ($score >= 40) {
    $score_class = 'text-warning';
} else {
    $score_class = 'text-danger';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats du QCM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .result-card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .score-display {
            font-size: 5rem;
            font-weight: bold;
        }
        .progress {
            height: 25px;
            border-radius: 12px;
        }
        .progress-bar {
            transition: width 1s ease-in-out;
        }
        .answer-stats {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation (identique à vos autres pages) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="student_dashboard.php">QCM Platform</a>
            <div class="navbar-nav ms-auto">
            
                <a class="btn btn-outline-light" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card result-card border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h3 class="mb-0 text-center">Résultats du QCM</h3>
                    </div>
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4"><?php echo htmlspecialchars($qcm_data['titre']); ?></h2>
                        <p class="text-muted text-center mb-5"><?php echo htmlspecialchars($qcm_data['description']); ?></p>

                        <!-- Score principal -->
                        <div class="text-center mb-5">
                            <div class="score-display mb-3 <?php echo $score_class; ?>">
                                <?php echo $score; ?>%
                            </div>
                            <div class="progress mb-3">
                                <div class="progress-bar <?php echo $score_class; ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo $score; ?>%" 
                                     aria-valuenow="<?php echo $score; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <p class="answer-stats">
                                <?php echo $correct_answers; ?> bonnes réponses sur <?php echo $total_questions; ?> questions
                            </p>
                        </div>

                        <!-- Détails des résultats -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Taux de réussite</h5>
                                        <div class="display-4 <?php echo $score_class; ?>">
                                            <?php echo $score; ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Réponses correctes</h5>
                                        <div class="display-4 text-success">
                                            <?php echo $correct_answers; ?>
                                            <small class="text-muted">/ <?php echo $total_questions; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <a href="student_dashboard.php" class="btn btn-primary px-4">
                                <i class="fas fa-home me-2"></i>Retour au tableau de bord
                            </a>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome pour les icônes -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>