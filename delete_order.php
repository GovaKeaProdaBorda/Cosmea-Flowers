<?php
include 'configM.php';
session_start();

// Проверяем, что пользователь авторизован и является администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Проверяем, передан ли ID заказа
if (!isset($_GET['id'])) {
    die("Не указан ID заказа.");
}

$order_id = intval($_GET['id']);

// Удаляем заказ. Если настроены внешние ключи с ON DELETE CASCADE, связанные записи в order_items будут удалены автоматически.
$stmt = $mysqli->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
if ($stmt->execute()) {
    $stmt->close();
    header("Location: admin.php?section=orders&msg=" . urlencode("Заказ успешно удалён."));
    exit;
} else {
    die("Ошибка удаления заказа: " . $stmt->error);
}
?>
