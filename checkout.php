<?php 
session_start();
// Проверяем, что пользователь авторизован
if (!isset($_SESSION['user_id'])) {
    header("Location: autorization.php");
    exit;
}
include 'header.php'; 
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем товары корзины для данного пользователя
    $sql = "SELECT b.id AS basket_id, b.quantity, p.id AS product_id, p.price 
            FROM basket b 
            JOIN products p ON b.product_id = p.id 
            WHERE b.user_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $basket_items = [];
    $items_total = 0;
    while ($row = $result->fetch_assoc()) {
        $basket_items[] = $row;
        $items_total += $row['price'] * $row['quantity'];
    }
    $stmt->close();

    if (empty($basket_items)) {
        echo "<div class='container py-5'><h2>Ваша корзина пуста.</h2></div>";
        exit;
    }

    // Получаем способ доставки и адрес (если применимо) из формы
    $shipping_method = (isset($_POST['shipping_method']) && $_POST['shipping_method'] === 'delivery') ? 'delivery' : 'pickup';
    $delivery_address = ($shipping_method === 'delivery' && !empty(trim($_POST['delivery_address']))) ? trim($_POST['delivery_address']) : NULL;

    // Если доставка выбрана, добавляем фиксированную стоимость доставки (500 руб.)
    $delivery_fee = ($shipping_method === 'delivery') ? 500 : 0;
    $total = $items_total + $delivery_fee;

    // Создаём заказ с указанием способа доставки и адреса доставки (если указан)
    $stmt = $mysqli->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_method, delivery_address) VALUES (?, ?, 'pending', ?, ?)");
    $stmt->bind_param("idss", $user_id, $total, $shipping_method, $delivery_address);
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Добавляем позиции заказа
        $stmt_items = $mysqli->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($basket_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $stmt_items->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt_items->execute();
        }
        $stmt_items->close();

        // Очищаем корзину пользователя
        $stmt_clear = $mysqli->prepare("DELETE FROM basket WHERE user_id = ?");
        $stmt_clear->bind_param("i", $user_id);
        $stmt_clear->execute();
        $stmt_clear->close();
        ?>
        <div class="container py-5">
            <h2 class="text-center mb-4">Заказ успешно оформлен!</h2>
            <p class="text-center">Номер вашего заказа: <strong><?= $order_id ?></strong></p>
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">На главную</a>
            </div>
        </div>
        <?php
    } else {
        echo "<div class='container py-5'><h2>Ошибка оформления заказа: " . $stmt->error . "</h2></div>";
    }
    exit;
} else {
    // Метод GET - отображаем сводку корзины и форму выбора способа доставки
    $sql = "SELECT b.id AS basket_id, b.quantity, p.* 
            FROM basket b 
            JOIN products p ON b.product_id = p.id 
            WHERE b.user_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $basket_items = [];
    $items_total = 0;
    while ($row = $result->fetch_assoc()) {
        $basket_items[] = $row;
        $items_total += $row['price'] * $row['quantity'];
    }
    $stmt->close();
    ?>
    <div class="container py-5">
        <h2 class="text-center mb-4">Оформление заказа</h2>
        <?php if (empty($basket_items)): ?>
            <p class="text-center">Ваша корзина пуста.</p>
        <?php else: ?>
        <form method="POST" action="checkout.php" id="checkout-form">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Товар</th>
                            <th>Описание</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Итого</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($basket_items as $item): ?>
                            <?php $subtotal = $item['price'] * $item['quantity']; ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid" style="max-width: 100px;">
                                </td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= number_format($item['price'], 2) ?> руб.</td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($subtotal, 2) ?> руб.</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">Итого по товарам:</td>
                            <td class="text-start" id="items-total"><?= number_format($items_total, 2) ?> руб.</td>
                        </tr>
                        <tr id="delivery-row" style="display: none;">
                            <td colspan="4" class="text-end">Стоимость доставки:</td>
                            <td class="text-start" id="delivery-fee">500.00 руб.</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Общая сумма:</td>
                            <td class="text-start" id="order-total"><?= number_format($items_total, 2) ?> руб.</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Форма выбора способа доставки -->
            <div class="mb-4">
                <h4>Способ получения заказа</h4>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="shipping_method" id="pickup" value="pickup" checked>
                    <label class="form-check-label" for="pickup">
                        Самовывоз (бесплатно)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="shipping_method" id="delivery" value="delivery">
                    <label class="form-check-label" for="delivery">
                        Доставка (500 руб.)
                    </label>
                </div>
            </div>

            <!-- Поле для ввода адреса доставки (показывается только при выборе доставки) -->
            <div class="mb-4" id="delivery-address-group" style="display: none;">
                <label for="delivery_address" class="form-label">Адрес доставки:</label>
                <input type="text" class="form-control" id="delivery_address" name="delivery_address" placeholder="Укажите адрес доставки">
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary">Оформить заказ</button>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <script>
    // Функция для преобразования строки с суммой в число (удаляются все символы, кроме цифр и точки)
    function parseAmount(text) {
        return parseFloat(text.replace(/[^0-9.]/g, ''));
    }

    document.addEventListener("DOMContentLoaded", function () {
        const pickupRadio = document.getElementById("pickup");
        const deliveryRadio = document.getElementById("delivery");
        const addressGroup = document.getElementById("delivery-address-group");
        const deliveryRow = document.getElementById("delivery-row");
        
        // Получаем текст итоговой суммы по товарам и парсим его в число
        const itemsTotalText = document.getElementById("items-total").textContent;
        const itemsTotal = parseAmount(itemsTotalText);
        const orderTotalElem = document.getElementById("order-total");

        function updateTotals() {
            if (deliveryRadio.checked) {
                addressGroup.style.display = "block";
                deliveryRow.style.display = "";
                orderTotalElem.textContent = (itemsTotal + 500).toFixed(2) + " руб.";
            } else {
                addressGroup.style.display = "none";
                deliveryRow.style.display = "none";
                orderTotalElem.textContent = itemsTotal.toFixed(2) + " руб.";
            }
        }

        pickupRadio.addEventListener("change", updateTotals);
        deliveryRadio.addEventListener("change", updateTotals);
    });
    </script>
    <?php
}

include 'footer.php';
?>
