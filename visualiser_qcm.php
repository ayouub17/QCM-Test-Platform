<?php
session_start();
require_once 'db.php';
include('teacher_dashboard.php');

// Requête SQL pour récupérer tous les QCM
$query = "SELECT id, titre, description FROM qcm";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Erreur lors de la récupération des QCM : " . mysqli_error($conn));
}
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualiser les QCM</title>
    <style>
        /* TON STYLE EXISTANT */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .L {
            text-align: center;
            color: #333;
            padding: 20px;
            background-color: #007bff;
        }
        h1 {
            text-align: center;
            color: #333;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        table.qcm-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
        }
        td {
            background-color: #f9f9f9;
        }
        tbody tr:nth-child(odd) {
            background-color: #f1f1f1;
        }
        tbody tr:hover {
            background-color: #eaeaea;
        }
        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        button {
            padding: 8px 15px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            text-align: center;
        }
        .btn-view {
            background-color: #007BFF;
            color: white;
        }
        .btn-view:hover {
            background-color: #0056b3;
        }
        .btn-delete {
            background-color: #ff5733;
            color: white;
        }
        .btn-delete:hover {
            background-color: #cc3300;
        }
        .btn-edit {
            background-color: #28a745;
            color: white;
        }
        .btn-edit:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="L">Liste des QCM</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="qcm-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['titre']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td class="actions">
                            <form action="visualiser_questions.php" method="get">
                                <input type="hidden" name="qcm_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <button type="submit" class="btn-view">Voir les questions</button>
                            </form>

                            <form action="supprimer_qcm.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce QCM ?');">
                                <input type="hidden" name="qcm_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <button type="submit" class="btn-delete">Supprimer</button>
                            </form>

                            <form action="modifier_qcm.php" method="get">
                                <input type="hidden" name="qcm_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <button type="submit" class="btn-edit">Modifier</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun QCM trouvé.</p>
    <?php endif; ?>

    <?php mysqli_close($conn); ?>
</div>

</body>
</html>
