<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['qcm_id'])) {
    header("Location: student_dashboard.php");
    exit();
}

$qcm_id = intval($_GET['qcm_id']);
$etudiant_id = $_SESSION['user_id'];

// Vérifier si le QCM existe
$qcm = $conn->prepare("SELECT id, titre, description FROM qcm WHERE id = ?");
$qcm->bind_param("i", $qcm_id);
$qcm->execute();
$qcm_result = $qcm->get_result();

if ($qcm_result->num_rows === 0) {
    header("Location: student_dashboard.php");
    exit();
}

$qcm_data = $qcm_result->fetch_assoc();

// Récupérer les questions
$questions = $conn->prepare("
    SELECT DISTINCT q.id, q.question, o.option1, o.option2, o.option3, o.correct_option 
    FROM questions q
    INNER JOIN options o ON q.id = o.question_id
    WHERE q.qcm_id = ?
    GROUP BY q.id
");
$questions->bind_param("i", $qcm_id);
$questions->execute();
$questions_result = $questions->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passer le QCM: <?php echo htmlspecialchars($qcm_data['titre']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .question-card {
            margin-bottom: 2rem;
            border-left: 4px solid #4e73df;
        }
        #timer {
            font-size: 1.5rem;
            font-weight: bold;
            color: red;
            text-align: right;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold text-primary">
                    <?php echo htmlspecialchars($qcm_data['titre']); ?>
                </h4>
                
            </div>
            <div class="card-body">
                <p><?php echo htmlspecialchars($qcm_data['description']); ?></p>

                <form id="qcm-form" action="submit_qcm.php" method="post">
                    <input type="hidden" name="qcm_id" value="<?php echo $qcm_id; ?>">

                    <?php $question_num = 1; ?>
                    <?php while ($question = $questions_result->fetch_assoc()): ?>
                    <div class="card question-card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Question <?php echo $question_num++; ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($question['question']); ?></p>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" 
                                    name="answers[<?php echo $question['id']; ?>]" 
                                    id="q<?php echo $question['id']; ?>_opt1" 
                                    value="1" required>
                                <label class="form-check-label" for="q<?php echo $question['id']; ?>_opt1">
                                    <?php echo htmlspecialchars($question['option1']); ?>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" 
                                    name="answers[<?php echo $question['id']; ?>]" 
                                    id="q<?php echo $question['id']; ?>_opt2" 
                                    value="2">
                                <label class="form-check-label" for="q<?php echo $question['id']; ?>_opt2">
                                    <?php echo htmlspecialchars($question['option2']); ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" 
                                    name="answers[<?php echo $question['id']; ?>]" 
                                    id="q<?php echo $question['id']; ?>_opt3" 
                                    value="3">
                                <label class="form-check-label" for="q<?php echo $question['id']; ?>_opt3">
                                    <?php echo htmlspecialchars($question['option3']); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>

                    <button type="submit" class="btn btn-primary btn-lg">Soumettre le QCM</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Timer Script -->
    

</body>
</html>
