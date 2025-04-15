<?php 
session_start();
// Проверяем, что пользователь авторизован
if (!isset($_SESSION['user_id'])) {
    header("Location: autorization.php");
    exit;
}
include 'header.php'; 

$user_id = $_SESSION['user_id'];

// Если пользователь является администратором, показываем все заказы,
// иначе – только заказы данного пользователя.
if ($_SESSION['role'] === 'admin') {
    $sql = "SELECT * FROM orders ORDER BY order_date DESC";
    $stmt = $mysqli->prepare($sql);
} else {
    $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container py-5">
    <h2 class="text-center mb-4">Оформленные заказы</h2>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Номер заказа</th>
                    <th>Дата оформления</th>
                    <th>Способ получения</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Заказов не найдено</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <?php 
                        // Для уникальности идентификатора collapse используем ID заказа
                        $collapseId = "orderDetails" . $order['id'];
                        // Определяем текст для способа получения
                        $shippingText = ($order['shipping_method'] === 'delivery') ? 'Доставка' : 'Самовывоз';
                        ?>
                        <tr>
                            <td>#<?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars(date("d M Y", strtotime($order['order_date']))) ?></td>
                            <td><?= $shippingText ?></td>
                            <td>
                                <?php 
                                    $status = $order['status'];
                                    // Определяем класс для бейджа статуса
                                    if ($status == 'Завершен' || $status == 'completed') {
                                        $badgeClass = 'bg-success';
                                    } elseif ($status == 'В обработке' || $status == 'pending') {
                                        $badgeClass = 'bg-warning';
                                    } else {
                                        $badgeClass = 'bg-secondary';
                                    }
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                            <td><?= number_format($order['total_amount'], 2) ?> руб.</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>">Подробнее</button>
                            </td>
                        </tr>
                        <?php 
                        // Получаем список товаров для текущего заказа
                        $stmtItems = $mysqli->prepare("SELECT oi.*, p.name AS product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $stmtItems->bind_param("i", $order['id']);
                        $stmtItems->execute();
                        $resultItems = $stmtItems->get_result();
                        $orderItems = $resultItems->fetch_all(MYSQLI_ASSOC);
                        $stmtItems->close();

                        // Если доставка выбрана, стоимость доставки фиксирована - 500 руб., иначе 0.
                        $deliveryCost = ($order['shipping_method'] === 'delivery') ? 500 : 0;
                        ?>
                        <tr id="<?= $collapseId ?>" class="collapse">
                            <td colspan="6">
                                <h5>Список товаров в заказе:</h5>
                                <?php if (!empty($orderItems)): ?>
                                    <ul>
                                        <?php foreach ($orderItems as $item): ?>
                                            <li><?= htmlspecialchars($item['product_name']) ?> (<?= $item['quantity'] ?> шт.) - <?= number_format($item['price'], 2) ?> руб.</li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>Товары не найдены.</p>
                                <?php endif; ?>
                                <p><strong>Способ получения:</strong> <?= $shippingText ?></p>
                                <?php if ($order['shipping_method'] === 'delivery'): ?>
                                    <p><strong>Адрес доставки:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                                    <p><strong>Стоимость доставки:</strong> 500.00 руб.</p>
                                <?php endif; ?>
                                <p><strong>Итого:</strong> <?= number_format($order['total_amount'], 2) ?> руб.</p>
                                <p><strong>Статус заказа:</strong> <?= htmlspecialchars($order['status']) ?></p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
