<?php
include 'configM.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: autorization.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $basket_id = intval($_GET['id']);
    $stmt = $mysqli->prepare("DELETE FROM basket WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $basket_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: basket.php");
exit;
?>
