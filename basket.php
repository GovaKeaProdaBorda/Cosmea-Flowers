<?php 
session_start();

// Если пользователь не авторизован, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: autorization.php");
    exit;
}

include 'header.php';

$user_id = $_SESSION['user_id'];

// Получаем товары корзины для данного пользователя
$sql = "SELECT b.id AS basket_id, b.quantity, p.* 
        FROM basket b 
        JOIN products p ON b.product_id = p.id 
        WHERE b.user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$basket_items = [];
while ($row = $result->fetch_assoc()) {
    $basket_items[] = $row;
}
$stmt->close();
?>

<div class="container py-5">
    <h2 class="text-center mb-4">Ваша корзина</h2>
    
    <?php if (empty($basket_items)): ?>
        <p class="text-center">Ваша корзина пуста.</p>
    <?php else: ?>
    <!-- Форма для обновления количеств товаров -->
    <form method="POST" action="update_basket.php">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Товар</th>
                        <th>Описание</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Итого</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($basket_items as $item): ?>
                        <?php 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid" style="max-width: 100px;">
                            </td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= number_format($item['price'], 2) ?> руб.</td>
                            <td>
                                <!-- Передаем идентификатор записи и новое количество -->
                                <input type="hidden" name="basket_ids[]" value="<?= $item['basket_id'] ?>">
                                <input type="number" name="quantities[]" class="form-control" value="<?= $item['quantity'] ?>" min="1">
                            </td>
                            <td><?= number_format($subtotal, 2) ?> руб.</td>
                            <td>
                                <a href="delete_basket.php?id=<?= $item['basket_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить товар?');">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end">Итого:</td>
                        <td colspan="2" class="text-start"><?= number_format($total, 2) ?> руб.</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-secondary">Обновить корзину</button>
            <a href="checkout.php" class="btn btn-primary">Оформить заказ</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
