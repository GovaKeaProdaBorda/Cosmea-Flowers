<?php
include 'configM.php';
session_start();

// Проверка, что пользователь авторизован и является администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Проверяем, передан ли ID товара
if (!isset($_GET['id'])) {
    die("Не указан ID товара.");
}

$product_id = intval($_GET['id']);

// Если форма отправлена методом POST, обновляем данные товара
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Получаем данные из формы
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $subcategory = trim($_POST['subcategory']); // может быть пустым
    $price = floatval($_POST['price']);
    $image = trim($_POST['image']);
    $description = trim($_POST['description']);

    // Обновляем данные товара
    $stmt = $mysqli->prepare("UPDATE products SET name = ?, category = ?, subcategory = ?, price = ?, image = ?, description = ? WHERE id = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $mysqli->error);
    }
    // Формат: s (name), s (category), s (subcategory), d (price), s (image), s (description), i (id)
    $stmt->bind_param("sssdssi", $name, $category, $subcategory, $price, $image, $description, $product_id);
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: admin.php?section=products&msg=" . urlencode("Товар успешно обновлён."));
        exit;
    } else {
        die("Ошибка обновления товара: " . $stmt->error);
    }
} else {
    // Метод GET: получаем данные товара для заполнения формы
    $stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        die("Товар не найден.");
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование товара #<?= htmlspecialchars($product_id) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Редактирование товара #<?= htmlspecialchars($product_id) ?></h2>
    <form method="POST" action="edit_product.php?id=<?= $product_id ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Название:</label>
            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Категория:</label>
            <input type="text" class="form-control" name="category" id="category" value="<?= htmlspecialchars($product['category']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="subcategory" class="form-label">Подкатегория:</label>
            <input type="text" class="form-control" name="subcategory" id="subcategory" value="<?= htmlspecialchars($product['subcategory']) ?>">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Цена:</label>
            <input type="number" step="0.01" class="form-control" name="price" id="price" value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Путь к изображению:</label>
            <input type="text" class="form-control" name="image" id="image" value="<?= htmlspecialchars($product['image']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание:</label>
            <textarea class="form-control" name="description" id="description"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <a href="admin.php?section=products" class="btn btn-secondary">Отмена</a>
    </form>
</div>
</body>
</html>
