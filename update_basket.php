<?php
include 'configM.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: autorization.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['basket_ids'], $_POST['quantities'])) {
    $basket_ids = $_POST['basket_ids'];
    $quantities = $_POST['quantities'];

    for ($i = 0; $i < count($basket_ids); $i++) {
        $basket_id = intval($basket_ids[$i]);
        $quantity = max(1, intval($quantities[$i])); // Минимум 1

        $stmt = $mysqli->prepare("UPDATE basket SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $basket_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: basket.php");
exit;
?>
