<?php
include 'configM.php';
session_start();

// Если пользователь не авторизован, перенаправляем или выдаем ошибку
if (!isset($_SESSION['user_id'])) {
    echo "Пожалуйста, авторизуйтесь для добавления товаров в корзину.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);

    // Проверяем, есть ли уже этот товар в корзине пользователя
    $stmt = $mysqli->prepare("SELECT id, quantity FROM basket WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Если товар уже есть, увеличиваем количество на 1
        $stmt->bind_result($basket_id, $quantity);
        $stmt->fetch();
        $stmt->close();

        $new_quantity = $quantity + 1;
        $stmt = $mysqli->prepare("UPDATE basket SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $basket_id);
        if ($stmt->execute()) {
            echo "Количество товара обновлено.";
        } else {
            echo "Ошибка обновления корзины.";
        }
    } else {
        $stmt->close();
        // Если товара нет в корзине, добавляем его
        $stmt = $mysqli->prepare("INSERT INTO basket (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->bind_param("ii", $user_id, $product_id);
        if ($stmt->execute()) {
            echo "Товар добавлен в корзину.";
        } else {
            echo "Ошибка добавления товара в корзину.";
        }
    }
    $stmt->close();
} else {
    echo "Некорректный запрос.";
}
?>
