<?php 
session_start();
// Если сессия ещё не запущена, запускаем её
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Если пользователь не авторизован или не администратор, перенаправляем его
    header("Location: index.php");
    exit;
}
include 'header.php';

// Определяем, какой раздел админ-панели показывать
$section = isset($_GET['section']) ? $_GET['section'] : 'products';
?>

<h2 class="mt-4 text-center">Панель администратора</h2>

<!-- Навигация по разделам админ-панели -->
<nav class="mt-4">
    <ul class="nav nav-tabs justify-content-center">
       <li class="nav-item">
           <a class="nav-link <?php if($section=='products') echo 'active'; ?>" href="admin.php?section=products">Лоты товаров</a>
       </li>
       <li class="nav-item">
           <a class="nav-link <?php if($section=='orders') echo 'active'; ?>" href="admin.php?section=orders">Заказы</a>
       </li>
       <li class="nav-item">
           <a class="nav-link <?php if($section=='users') echo 'active'; ?>" href="admin.php?section=users">Пользователи</a>
       </li>
    </ul>
</nav>

<div class="container mt-4">
<?php if($section=='products'): ?>
    <!-- Раздел: Лоты товаров -->
    <h3>Добавить новый лот</h3>
    <?php
    // Если форма добавления лота отправлена
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $subcategory = $_POST['subcategory'];
        $price = $_POST['price'];
        $image = $_POST['image'];
        $description = $_POST['description'];

        $stmt = $mysqli->prepare("INSERT INTO products (name, category, subcategory, price, image, description) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            // Обратите внимание: для поля price используем тип "d" (double)
            $stmt->bind_param("sssiss", $name, $category, $subcategory, $price, $image, $description);
            if($stmt->execute()){
                echo "<div class='alert alert-success'>Лот успешно добавлен</div>";
            } else {
                echo "<div class='alert alert-danger'>Ошибка: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Ошибка подготовки запроса: " . $mysqli->error . "</div>";
        }
    }
    ?>
    <form method="POST" action="admin.php?section=products">
      <input type="hidden" name="add_product" value="1">
      <div class="mb-3">
          <label for="name" class="form-label">Название:</label>
          <input type="text" class="form-control" name="name" required>
      </div>
      <div class="mb-3">
          <label for="category" class="form-label">Категория:</label>
          <input type="text" class="form-control" name="category" required>
      </div>
      <div class="mb-3">
          <label for="subcategory" class="form-label">Подкатегория:</label>
          <input type="text" class="form-control" name="subcategory">
      </div>
      <div class="mb-3">
          <label for="price" class="form-label">Цена:</label>
          <input type="number" step="0.01" class="form-control" name="price" required>
      </div>
      <div class="mb-3">
          <label for="image" class="form-label">Путь к изображению:</label>
          <input type="text" class="form-control" name="image" required>
      </div>
      <div class="mb-3">
          <label for="description" class="form-label">Описание:</label>
          <textarea class="form-control" name="description"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Добавить лот</button>
    </form>

    <h3 class="mt-5">Управление лотами</h3>
    <?php
    $res = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
    ?>
    <table class="table table-bordered">
       <thead>
         <tr>
           <th>ID</th>
           <th>Название</th>
           <th>Категория</th>
           <th>Подкатегория</th>
           <th>Цена</th>
           <th>Действия</th>
         </tr>
       </thead>
       <tbody>
         <?php while($product = $res->fetch_assoc()): ?>
         <tr>
           <td><?= $product['id'] ?></td>
           <td><?= htmlspecialchars($product['name']) ?></td>
           <td><?= htmlspecialchars($product['category']) ?></td>
           <td><?= htmlspecialchars($product['subcategory']) ?></td>
           <td><?= htmlspecialchars($product['price']) ?></td>
           <td>
             <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Изменить</a>
             <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить лот?');">Удалить</a>
           </td>
         </tr>
         <?php endwhile; ?>
       </tbody>
    </table>

<?php elseif($section=='orders'): ?>
    <!-- Раздел: Заказы -->
    <h3>Управление заказами</h3>
    <?php
    // Выбираем все заказы
    $res = $mysqli->query("SELECT * FROM orders ORDER BY order_date DESC");
    ?>
    <table class="table table-bordered">
       <thead class="table-light">
         <tr>
           <th>ID</th>
           <th>Пользователь</th>
           <th>Дата заказа</th>
           <th>Способ получения</th>
           <th>Сумма</th>
           <th>Статус</th>
           <th>Действия</th>
         </tr>
       </thead>
       <tbody>
         <?php while($order = $res->fetch_assoc()): ?>
         <?php 
            // Формируем уникальный ID для collapse
            $collapseId = "orderDetails" . $order['id'];
            $shippingText = ($order['shipping_method'] === 'delivery') ? 'Доставка' : 'Самовывоз';
         ?>
         <tr>
           <td><?= $order['id'] ?></td>
           <td><?= $order['user_id'] ?></td>
           <td><?= $order['order_date'] ?></td>
           <td><?= $shippingText ?></td>
           <td><?= number_format($order['total_amount'], 2) ?> руб.</td>
           <td><?= htmlspecialchars($order['status']) ?></td>
           <td>
             <a href="edit_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-warning">Изменить</a>
             <a href="delete_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить заказ?');">Удалить</a>
             <button type="button" class="btn btn-info btn-sm" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>">Подробнее</button>
           </td>
         </tr>
         <tr id="<?= $collapseId ?>" class="collapse">
           <td colspan="7">
             <h5>Список товаров в заказе:</h5>
             <?php
             // Получаем список товаров для данного заказа
             $stmtItems = $mysqli->prepare("SELECT oi.*, p.name AS product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
             $stmtItems->bind_param("i", $order['id']);
             $stmtItems->execute();
             $resultItems = $stmtItems->get_result();
             $orderItems = $resultItems->fetch_all(MYSQLI_ASSOC);
             $stmtItems->close();
             ?>
             <?php if (!empty($orderItems)): ?>
                <ul>
                   <?php foreach ($orderItems as $item): ?>
                      <li><?= htmlspecialchars($item['product_name']) ?> (<?= $item['quantity'] ?> шт.) - <?= number_format($item['price'], 2) ?> руб.</li>
                   <?php endforeach; ?>
                </ul>
             <?php else: ?>
                <p>Товары не найдены.</p>
             <?php endif; ?>
             <?php if ($order['shipping_method'] === 'delivery'): ?>
                <p><strong>Адрес доставки:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
                <p><strong>Стоимость доставки:</strong> 500.00 руб.</p>
             <?php endif; ?>
             <p><strong>Итого:</strong> <?= number_format($order['total_amount'], 2) ?> руб.</p>
             <p><strong>Статус заказа:</strong> <?= htmlspecialchars($order['status']) ?></p>
           </td>
         </tr>
         <?php endwhile; ?>
       </tbody>
    </table>

<?php elseif($section=='users'): ?>
    <!-- Раздел: Пользователи -->
    <h3>Управление аккаунтами пользователей</h3>
    <?php
    $res = $mysqli->query("SELECT * FROM users ORDER BY id DESC");
    ?>
    <table class="table table-bordered">
       <thead>
         <tr>
           <th>ID</th>
           <th>Имя</th>
           <th>Email</th>
           <th>Дата регистрации</th>
           <th>Роль</th>
           <th>Статус</th>
           <th>Действия</th>
         </tr>
       </thead>
       <tbody>
         <?php while($user = $res->fetch_assoc()): ?>
         <tr>
           <td><?= $user['id'] ?></td>
           <td><?= htmlspecialchars($user['name']) ?></td>
           <td><?= htmlspecialchars($user['email']) ?></td>
           <td><?= $user['registration_date'] ?></td>
           <td><?= $user['role'] ?></td>
           <td><?= $user['status'] ?></td>
           <td>
             <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Изменить</a>
             <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить аккаунт?');">Удалить</a>
           </td>
         </tr>
         <?php endwhile; ?>
       </tbody>
    </table>

<?php endif; ?>
</div>

<?php include 'footer.php'; ?>
