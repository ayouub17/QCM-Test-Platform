<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'étudiant
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qcm_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

// Récupérer le QCM sélectionné (ici, on prend le premier pour l'exemple)
$qcm_id = isset($_GET['qcm_id']) ? intval($_GET['qcm_id']) : 0;

// Récupérer les informations du QCM
$qcm_info = $conn->prepare("SELECT id, titre, description FROM qcm WHERE id = ?");
$qcm_info->bind_param("i", $qcm_id);
$qcm_info->execute();
$qcm_result = $qcm_info->get_result();
$qcm = $qcm_result->fetch_assoc();
$qcm_info->close();

if (!$qcm) {
    die("QCM non trouvé");
}

// Récupérer les questions du QCM
$questions = $conn->prepare("SELECT id, question FROM questions WHERE qcm_id = ?");
$questions->bind_param("i", $qcm_id);
$questions->execute();
$questions_result = $questions->get_result();
$questions->close();

// Récupérer les options pour chaque question
$all_questions = [];
while ($question = $questions_result->fetch_assoc()) {
    $options = $conn->prepare("SELECT id, option1, option2, option3, correct_option FROM options WHERE question_id = ?");
    $options->bind_param("i", $question['id']);
    $options->execute();
    $options_result = $options->get_result();
    $options_data = $options_result->fetch_assoc();
    $options->close();
    
    $all_questions[] = [
        'id' => $question['id'],
        'question' => $question['question'],
        'options' => [
            '1' => $options_data['option1'],
            '2' => $options_data['option2'],
            '3' => $options_data['option3']
        ],
        'correct_option' => $options_data['correct_option']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test QCM - <?php echo htmlspecialchars($qcm['titre']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .qcm-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .question {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .question-text {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .options label {
            display: block;
            margin: 8px 0;
            cursor: pointer;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        .submit-btn:hover {
            background: #45a049;
        }
        .timer {
            text-align: center;
            font-size: 18px;
            margin: 20px 0;
            color: #d9534f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($qcm['titre']); ?></h1>
        
        <div class="qcm-info">
            <p><strong>Description :</strong> <?php echo htmlspecialchars($qcm['description']); ?></p>
        </div>
        
        <!-- Timer (optionnel) -->
        <div class="timer" id="timer">Temps restant : 30:00</div>
        
        <form action="submit_qcm.php" method="post" id="qcm-form">
            <input type="hidden" name="qcm_id" value="<?php echo $qcm['id']; ?>">
            
            <?php foreach ($all_questions as $index => $question): ?>
            <div class="question">
                <div class="question-text">
                    <?php echo ($index + 1) . ". " . htmlspecialchars($question['question']); ?>
                </div>
                
                <div class="options">
                    <?php foreach ($question['options'] as $key => $option): ?>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo $key; ?>" required>
                        <?php echo htmlspecialchars($option); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <button type="submit" class="submit-btn">Soumettre le QCM</button>
        </form>
    </div>

    <script>
        // Timer JavaScript (30 minutes)
        let timeLeft = 30 * 60; // 30 minutes en secondes
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            document.getElementById('timer').textContent = `Temps restant : ${minutes}:${seconds}`;
            
            if (timeLeft <= 0) {
                document.getElementById('qcm-form').submit();
            } else {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            }
        }
        
        // Démarrer le timer
        updateTimer();
        
        // Empêcher le rafraîchissement de la page
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = 'Vos réponses seront perdues si vous quittez cette page. Êtes-vous sûr?';
        });
    </script>
</body>
</html>