<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion QCM</title>
    <style>
        /* Styles généraux */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Header */
        header {
            background-color:rgb(78, 122, 179);
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .nav-container h1 {
            color: #fff;
            margin: 0;
            font-size: 24px;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-buttons button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: transparent;
            border: 2px solid #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-buttons button:hover {
            background-color: #fff;
            color: #007bff;
        }

        /* Contenu principal */
        main {
            padding: 80px 20px 20px; /* Espace pour le header fixe */
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-buttons {
                margin-top: 10px;
                flex-wrap: wrap;
            }

            .nav-buttons button {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="nav-container">
            <h1>Gestion des QCM</h1>
            <div class="nav-buttons">
                <button onclick="creerQCM()">Créer un QCM</button>
                <button onclick="ajouterEtudiant()">Ajouter un étudiant</button>
                <button onclick="visualiserEtudiants()">Visualiser la liste des étudiants</button>
                <button onclick="visualiserQCM()">Visualiser le QCM</button>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <main>
        <h2>Bienvenue sur la plateforme de gestion des QCM</h2>
        <p>Utilisez les boutons ci-dessus pour naviguer entre les différentes fonctionnalités.</p>
    </main>

    <script>
        function creerQCM() {
            alert("Redirection vers la page de création de QCM...");
            window.location.href = "creer_qcm.php"; // Redirection vers une page de création de QCM
        }

        function ajouterEtudiant() {
            window.location.href = "ajouter_etudiant.php"; // Redirection vers une page d'ajout d'étudiant
        }

        function visualiserEtudiants() {
            window.location.href = "liste_etudiants.php"; // Redirection vers une page de liste des étudiants
        }

        function visualiserQCM() {
            window.location.href = "visualiser_qcm.php"; // Redirection vers une page de visualisation du QCM
        }
    </script>
</body>
</html>