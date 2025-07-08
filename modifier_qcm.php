<?php
session_start();
require_once 'db.php';

// Vérification des droits
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    die('Accès non autorisé.');
}

if (!isset($_GET['qcm_id'])) {
    die('QCM non spécifié.');
}

$qcm_id = intval($_GET['qcm_id']);

// TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mettre à jour le titre et la description du QCM
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare('UPDATE qcm SET titre = ?, description = ? WHERE id = ?');
    $stmt->bind_param('ssi', $titre, $description, $qcm_id);
    $stmt->execute();
    $stmt->close();

    // Traitement des questions existantes
    if (isset($_POST['questions'])) {
        foreach ($_POST['questions'] as $question_id => $data) {
            // Mise à jour du texte de la question
            $question_text = trim($data['text']);
            $stmt = $conn->prepare('UPDATE questions SET question = ? WHERE id = ?');
            $stmt->bind_param('si', $question_text, $question_id);
            $stmt->execute();
            $stmt->close();

            // Mise à jour des options
            if (isset($data['options'])) {
                $option1 = trim($data['options']['option1']);
                $option2 = trim($data['options']['option2']);
                $option3 = trim($data['options']['option3']);
                $correct_option = $data['options']['correct_option'];

                $stmt = $conn->prepare('UPDATE options SET option1 = ?, option2 = ?, option3 = ?, correct_option = ? WHERE question_id = ?');
                $stmt->bind_param('ssssi', $option1, $option2, $option3, $correct_option, $question_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    $_SESSION['success_message'] = 'QCM mis à jour avec succès.';
    header("Location: modifier_qcm.php?qcm_id=" . $qcm_id);
    exit;
}

// Récupération des données du QCM
$stmt = $conn->prepare('SELECT id, titre, description FROM qcm WHERE id = ?');
$stmt->bind_param('i', $qcm_id);
$stmt->execute();
$qcm = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Récupération des questions avec leurs options
$questions = [];
$stmt = $conn->prepare('SELECT q.id, q.question, o.option1, o.option2, o.option3, o.correct_option 
                       FROM questions q 
                       LEFT JOIN options o ON q.id = o.question_id 
                       WHERE q.qcm_id = ?');
$stmt->bind_param('i', $qcm_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier QCM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .question-card {
            margin-bottom: 2rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
        }
        .question-header {
            background-color: #f8f9fa;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #dee2e6;
        }
        .question-body {
            padding: 1.25rem;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <h1 class="mb-4">Modifier le QCM</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="qcm_id" value="<?= htmlspecialchars($qcm_id) ?>">

        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" class="form-control" name="titre" value="<?= htmlspecialchars($qcm['titre']) ?>" required>
        </div>

        <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($qcm['description']) ?></textarea>
        </div>

        <h3 class="mt-5 mb-3">Questions</h3>

        <?php if (empty($questions)): ?>
            <div class="alert alert-info">Aucune question pour ce QCM.</div>
        <?php else: ?>
            <?php foreach ($questions as $question): ?>
                <div class="question-card">
                    <div class="question-header">Question</div>
                    <div class="question-body">
                        <div class="mb-3">
                            <label class="form-label">Texte de la question</label>
                            <input type="text" class="form-control" 
                                   name="questions[<?= $question['id'] ?>][text]" 
                                   value="<?= htmlspecialchars($question['question']) ?>" required>
                        </div>

                        <h5 class="mt-3">Options</h5>

                        <div class="mb-3">
                            <label class="form-label">Option 1</label>
                            <input type="text" class="form-control" 
                                   name="questions[<?= $question['id'] ?>][options][option1]" 
                                   value="<?= htmlspecialchars($question['option1']) ?>" required>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" 
                                       name="questions[<?= $question['id'] ?>][options][correct_option]" 
                                       value="1" <?= $question['correct_option'] == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label">Correcte</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Option 2</label>
                            <input type="text" class="form-control" 
                                   name="questions[<?= $question['id'] ?>][options][option2]" 
                                   value="<?= htmlspecialchars($question['option2']) ?>" required>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" 
                                       name="questions[<?= $question['id'] ?>][options][correct_option]" 
                                       value="2" <?= $question['correct_option'] == '2' ? 'checked' : '' ?>>
                                <label class="form-check-label">Correcte</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Option 3</label>
                            <input type="text" class="form-control" 
                                   name="questions[<?= $question['id'] ?>][options][option3]" 
                                   value="<?= htmlspecialchars($question['option3']) ?>" required>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" 
                                       name="questions[<?= $question['id'] ?>][options][correct_option]" 
                                       value="3" <?= $question['correct_option'] == '3' ? 'checked' : '' ?>>
                                <label class="form-check-label">Correcte</label>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="mt-4">
            <a href="visualiser_qcm.php" type="submit" class="btn btn-primary" >Enregistrer</a>
            
            <a href="visualiser_qcm.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
