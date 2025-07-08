<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$qcm_id = intval($_POST['qcm_id']);
$etudiant_id = $_SESSION['user_id'];
$answers = $_POST['answers'];
$score = 0;
$total_questions = 0;

// Compter le nombre total de questions
$count = $conn->prepare("SELECT COUNT(*) FROM questions WHERE qcm_id = ?");
$count->bind_param("i", $qcm_id);
$count->execute();
$count_result = $count->get_result();
$total_questions = $count_result->fetch_row()[0];
$count->close();

// Vérifier chaque réponse
foreach ($answers as $question_id => $selected_option) {
    $question_id = intval($question_id);
    $selected_option = $conn->real_escape_string($selected_option);
    
    $check = $conn->prepare("SELECT correct_option FROM options WHERE question_id = ?");
    $check->bind_param("i", $question_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $correct = $result->fetch_assoc();
        if ($correct['correct_option'] == $selected_option) {
            $score++;
        }
    }
    $check->close();
}

// Calculer le pourcentage
$percentage = ($total_questions > 0) ? round(($score / $total_questions) * 100) : 0;

// Enregistrer le résultat
$insert = $conn->prepare("INSERT INTO results (etudiant_id, qcm_id, score) VALUES (?, ?, ?)");
$insert->bind_param("iii", $etudiant_id, $qcm_id, $percentage);
$insert->execute();
$insert->close();

$conn->close();

// Rediriger vers la page de résultats
header("Location: view_result.php?qcm_id=$qcm_id&score=$percentage&total=$total_questions");
exit();
?>