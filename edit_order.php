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

// Если форма отправлена методом POST, обновляем данные заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $shipping_method = isset($_POST['shipping_method']) && $_POST['shipping_method'] === 'delivery' ? 'delivery' : 'pickup';
    $delivery_address = ($shipping_method === 'delivery' && !empty(trim($_POST['delivery_address']))) ? trim($_POST['delivery_address']) : NULL;

    // Обновляем заказ в базе данных
    $stmt = $mysqli->prepare("UPDATE orders SET status = ?, shipping_method = ?, delivery_address = ? WHERE id = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $mysqli->error);
    }
    $stmt->bind_param("sssi", $status, $shipping_method, $delivery_address, $order_id);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: admin.php?section=orders&msg=" . urlencode("Заказ обновлён."));
        exit;
    } else {
        die("Ошибка обновления заказа: " . $stmt->error);
    }
}

// Если метод GET, получаем данные заказа для заполнения формы
$stmt = $mysqli->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Заказ не найден.");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование заказа #<?= htmlspecialchars($order['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Редактирование заказа #<?= htmlspecialchars($order['id']) ?></h2>
    <form method="POST" action="edit_order.php?id=<?= $order['id'] ?>">
        <div class="mb-3">
            <label for="status" class="form-label">Статус заказа:</label>
            <select class="form-select" name="status" id="status" required>
                <?php
                // Пример возможных статусов. Измените список по необходимости.
                $statuses = ['pending' => 'В обработке', 'completed' => 'Завершён', 'cancelled' => 'Отменён'];
                foreach ($statuses as $key => $value):
                ?>
                    <option value="<?= $key ?>" <?= ($order['status'] === $key ? 'selected' : '') ?>><?= htmlspecialchars($value) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Способ получения:</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="shipping_method" id="pickup" value="pickup" <?= ($order['shipping_method'] === 'pickup' ? 'checked' : '') ?>>
                <label class="form-check-label" for="pickup">
                    Самовывоз (бесплатно)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="shipping_method" id="delivery" value="delivery" <?= ($order['shipping_method'] === 'delivery' ? 'checked' : '') ?>>
                <label class="form-check-label" for="delivery">
                    Доставка (500 руб.)
                </label>
            </div>
        </div>
        <div class="mb-3" id="delivery-address-group" style="display: <?= ($order['shipping_method'] === 'delivery' ? 'block' : 'none') ?>;">
            <label for="delivery_address" class="form-label">Адрес доставки:</label>
            <input type="text" class="form-control" id="delivery_address" name="delivery_address" value="<?= htmlspecialchars($order['delivery_address'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <a href="admin.php?section=orders" class="btn btn-secondary">Отмена</a>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const pickupRadio = document.getElementById("pickup");
    const deliveryRadio = document.getElementById("delivery");
    const addressGroup = document.getElementById("delivery-address-group");

    function updateAddressVisibility() {
        if (deliveryRadio.checked) {
            addressGroup.style.display = "block";
        } else {
            addressGroup.style.display = "none";
        }
    }

    pickupRadio.addEventListener("change", updateAddressVisibility);
    deliveryRadio.addEventListener("change", updateAddressVisibility);
});
</script>
</body>
</html>
