<?php
include 'configM.php';
session_start();

// Проверка, что пользователь авторизован и является администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Проверяем, передан ли ID пользователя
if (!isset($_GET['id'])) {
    die("Не указан ID пользователя.");
}

$user_id = intval($_GET['id']);

// Если форма отправлена методом POST, обновляем данные пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // если пустое — пароль не обновляется
    $role   = $_POST['role'];
    $status = $_POST['status'];

    if (empty($name) || empty($email)) {
        header("Location: edit_user.php?id=" . $user_id . "&msg=" . urlencode("Имя и email не могут быть пустыми."));
        exit;
    }
    
    if (!empty($password)) {
        // Пример хеширования (рекомендуется использовать password_hash() в продакшене)
        $hashed_password = md5(sha1($password));
        $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?");
        if (!$stmt) {
            die("Ошибка подготовки запроса: " . $mysqli->error);
        }
        $stmt->bind_param("sssssi", $name, $email, $hashed_password, $role, $status, $user_id);
    } else {
        $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?");
        if (!$stmt) {
            die("Ошибка подготовки запроса: " . $mysqli->error);
        }
        $stmt->bind_param("ssssi", $name, $email, $role, $status, $user_id);
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: admin.php?section=users&msg=" . urlencode("Пользователь успешно обновлён."));
        exit;
    } else {
        die("Ошибка обновления пользователя: " . $stmt->error);
    }
} else {
    // Метод GET: получаем данные пользователя для заполнения формы
    $stmt = $mysqli->prepare("SELECT id, name, email, role, status FROM users WHERE id = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $mysqli->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        die("Пользователь не найден.");
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя #<?= htmlspecialchars($user['id']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Редактирование пользователя #<?= htmlspecialchars($user['id']) ?></h2>
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>
    <form action="edit_user.php?id=<?= $user['id'] ?>" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Имя:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Новый пароль (оставьте пустым, если не менять):</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Роль:</label>
            <select name="role" id="role" class="form-select" required>
                <option value="user" <?= ($user['role'] === 'user' ? 'selected' : '') ?>>Пользователь</option>
                <option value="admin" <?= ($user['role'] === 'admin' ? 'selected' : '') ?>>Администратор</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Статус:</label>
            <select name="status" id="status" class="form-select" required>
                <option value="online" <?= ($user['status'] === 'online' ? 'selected' : '') ?>>Онлайн</option>
                <option value="busy" <?= ($user['status'] === 'busy' ? 'selected' : '') ?>>Занят</option>
                <option value="away" <?= ($user['status'] === 'away' ? 'selected' : '') ?>>Отошел</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <a href="admin.php?section=users" class="btn btn-secondary">Отмена</a>
    </form>
</div>
</body>
</html>
