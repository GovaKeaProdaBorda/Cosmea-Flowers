<?php
session_start();

// Если пользователь не авторизован, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: autorization.php");
    exit;
}

include 'header.php';

include 'configM.php';
$user_id = $_SESSION['user_id'];

// Извлекаем данные о пользователе из таблицы users
$stmt = $mysqli->prepare("SELECT id, name, email, registration_date, role, status FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<div class="container py-5">
    <h2 class="text-center mb-4">Мой профиль</h2>
    
    <!-- Вывод сообщения (например, об успешном обновлении профиля) -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <!-- Карточка с информацией о пользователе -->
    <div class="card mb-4">
        <div class="card-header">
            Информация о пользователе
        </div>
        <div class="card-body">
            <p><strong>Имя:</strong> <?= htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
            <p><strong>Дата регистрации:</strong> <?= htmlspecialchars($user['registration_date']); ?></p>
            <p><strong>Роль:</strong> <?= htmlspecialchars($user['role']); ?></p>
            <p><strong>Статус:</strong> <?= htmlspecialchars($user['status']); ?></p>
        </div>
    </div>

    <!-- Форма редактирования профиля -->
    <h3>Редактировать профиль</h3>
    <form action="update_profile.php" method="POST">
        <!-- Рекомендуется также реализовать проверку и защиту (например, CSRF-токены) -->
        <div class="mb-3">
            <label for="name" class="form-label">Имя:</label>
            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Новый пароль (оставьте пустым, если не хотите менять):</label>
            <input type="password" class="form-control" name="password" id="password">
        </div>
        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
    </form>

    <!-- Другой функционал: ссылка на заказы пользователя -->
    <h3 class="mt-5">Мои заказы</h3>
    <p>
        <a href="orders.php" class="btn btn-secondary">Просмотреть заказы</a>
    </p>
</div>

<?php include 'footer.php'; ?>
