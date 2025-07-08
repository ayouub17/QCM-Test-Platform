<?php
include('db.php');

// Vérifier si l'ID du QCM est bien passé en POST
if (isset($_POST['qcm_id'])) {
    $qcm_id = $_POST['qcm_id'];

    // Requête SQL pour supprimer le QCM
    $query = "DELETE FROM qcm WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $qcm_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Le QCM a été supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression du QCM.";
    }

    // Rediriger vers la page de visualisation des QCM
    header("Location: visualiser_qcm.php");
} else {
    echo "Aucun QCM spécifié.";
}

mysqli_close($conn);
?>
