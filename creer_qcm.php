<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'qcm_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Traitement du formulaire si des données sont soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $questions = $_POST['questions'];

    // Insertion du QCM dans la table `qcm`
    $sql = "INSERT INTO qcm (titre, description, professeur_id, date_creation) VALUES (:titre, :description, 1, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['titre' => $titre, 'description' => $description]);
    $qcmId = $pdo->lastInsertId(); // Récupère l'ID du QCM créé
          
    // Insertion des questions et des options
    foreach ($questions as $question) {
        $texteQuestion = $question['texte'];
        $options = $question['options'];
        $correctOption = $question['correct_option'];

        // Insertion de la question
        $sql = "INSERT INTO questions (qcm_id, question) VALUES (:qcm_id, :question)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['qcm_id' => $qcmId, 'question' => $texteQuestion]);
        $questionId = $pdo->lastInsertId(); // Récupère l'ID de la question créée
         
        // Insertion des options
        foreach ($options as $index => $option) {
            $estCorrect = ($index + 1 == $correctOption) ? 1 : 0;
            $sql = "INSERT INTO options (question_id, option1, option2, option3, correct_option) VALUES (:question_id, :option1, :option2, :option3, :correct_option)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'question_id' => $questionId,
                'option1' => $options[0],
                'option2' => $options[1],
                'option3' => $options[2],
                'correct_option' => $correctOption
            ]);
        }
    }

    // Redirection vers la page teacher_dashboard.php après la création du QCM
    header("Location: teacher_dashboard.php");
    exit(); // Arrêter l'exécution du script après la redirection
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un QCM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .question {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .button-container {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Créer un QCM</h1>
        <form action="creer_qcm.php" method="post">
            <!-- Titre et description du QCM -->
            <label for="titre">Titre du QCM :</label>
            <input type="text" id="titre" name="titre" required>

            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <!-- Section pour ajouter des questions -->
            <div id="questions">
                <div class="question">
                    <label>Question 1 :</label>
                    <input type="text" name="questions[0][texte]" placeholder="Texte de la question" required>

                    <label>Option 1 :</label>
                    <input type="text" name="questions[0][options][0]" placeholder="Option 1" required>

                    <label>Option 2 :</label>
                    <input type="text" name="questions[0][options][1]" placeholder="Option 2" required>

                    <label>Option 3 :</label>
                    <input type="text" name="questions[0][options][2]" placeholder="Option 3" required>

                    <label>Réponse correcte :</label>
                    <select name="questions[0][correct_option]" required>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                    </select>
                </div>
            </div>

            <!-- Boutons pour ajouter une question et soumettre le formulaire -->
            <div class="button-container">
                <button type="button" onclick="ajouterQuestion()">Ajouter une question</button>
                <button type="submit">Créer le QCM</button>
            </div>
        </form>
    </div>

    <script>
        let questionCount = 1;

        function ajouterQuestion() {
            const questionsDiv = document.getElementById('questions');

            const newQuestion = document.createElement('div');
            newQuestion.className = 'question';
            newQuestion.innerHTML = `
                <label>Question ${questionCount + 1} :</label>
                <input type="text" name="questions[${questionCount}][texte]" placeholder="Texte de la question" required>

                <label>Option 1 :</label>
                <input type="text" name="questions[${questionCount}][options][0]" placeholder="Option 1" required>

                <label>Option 2 :</label>
                <input type="text" name="questions[${questionCount}][options][1]" placeholder="Option 2" required>

                <label>Option 3 :</label>
                <input type="text" name="questions[${questionCount}][options][2]" placeholder="Option 3" required>

                <label>Réponse correcte :</label>
                <select name="questions[${questionCount}][correct_option]" required>
                    <option value="1">Option 1</option>
                    <option value="2">Option 2</option>
                    <option value="3">Option 3</option>
                </select>
            `;

            questionsDiv.appendChild(newQuestion);
            questionCount++;
        }
    </script>
</body>
</html>