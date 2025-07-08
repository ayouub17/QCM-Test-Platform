<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

if (!isset($_GET['qcm_id']) || empty($_GET['qcm_id'])) {
    echo "Aucun QCM sélectionné.";
    exit;
}

$qcm_id = intval($_GET['qcm_id']);

// Récupérer le titre du QCM
$stmt_qcm = $conn->prepare('SELECT titre FROM qcm WHERE id = ?');
$stmt_qcm->bind_param('i', $qcm_id);
$stmt_qcm->execute();
$stmt_qcm->bind_result($titre_qcm);
$stmt_qcm->fetch();
$stmt_qcm->close();

if (!$titre_qcm) {
    echo "QCM introuvable.";
    exit;
}

// Récupérer les questions liées à ce QCM
$stmt_questions = $conn->prepare('SELECT id, question FROM questions WHERE qcm_id = ?');
$stmt_questions->bind_param('i', $qcm_id);
$stmt_questions->execute();
$stmt_questions->bind_result($qid, $question_text);
$questions = [];

while ($stmt_questions->fetch()) {
    $questions[] = ['id' => $qid, 'question' => $question_text];
}
$stmt_questions->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Questions du QCM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .question {
            margin-bottom: 30px;
        }
        .question h3 {
            margin-bottom: 10px;
            color: #007BFF;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 8px;
            background: #f9f9f9;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        .correct {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
        }
        .back-link a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Questions du QCM : <?php echo htmlspecialchars($titre_qcm ?? ''); ?></h1>

    <?php if (count($questions) > 0): ?>
        <?php foreach ($questions as $question): ?>
            <div class="question">
                <h3><?php echo htmlspecialchars($question['question']); ?></h3>

                <ul>
                <?php
                    $stmt_answers = $conn->prepare('SELECT reponse, est_correct FROM answers WHERE question_id = ?');
                    $stmt_answers->bind_param('i', $question['id']);
                    $stmt_answers->execute();
                    $stmt_answers->bind_result($reponse, $est_correct);

                    while ($stmt_answers->fetch()):
                        $class = ((int)$est_correct === 1) ? 'correct' : '';
                        $prefix = ((int)$est_correct === 1) ? '✅ ' : '';
                ?>
                    <li class="<?php echo $class; ?>"><?php echo $prefix . htmlspecialchars($reponse); ?></li>
                <?php endwhile; $stmt_answers->close(); ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune question trouvée pour ce QCM.</p>
    <?php endif; ?>

    <div class="back-link">
        <a href="visualiser_qcm.php">← Retour à la liste des QCM</a>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>
