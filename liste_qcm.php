<?php
session_start();
require_once 'db.php';

// Vérification des droits
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Récupérer les QCMs de l'enseignant
$stmt = $conn->prepare("SELECT id, titre, description FROM qcm WHERE enseignant_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$qcms = [];
while ($row = $result->fetch_assoc()) {
    $qcms[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des QCMs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Mes QCMs</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (empty($qcms)): ?>
        <div class="alert alert-info">Aucun QCM trouvé.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($qcms as $qcm): ?>
                    <tr>
                        <td><?= htmlspecialchars($qcm['titre']) ?></td>
                        <td><?= htmlspecialchars($qcm['description']) ?></td>
                        <td>
                            <a href="modifier_qcm.php?qcm_id=<?= $qcm['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <a href="supprimer_qcm.php?qcm_id=<?= $qcm['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce QCM ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="creer_qcm.php" class="btn btn-primary">Créer un nouveau QCM</a>
</div>
</body>
</html>
