<?php include 'header.php'; // Подключаем шапку ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../mycss/style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-4">Вход в систему</h2>
        <form action="login.php" method="POST" class="mx-auto" style="max-width: 400px;">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>
        <p class="text-center mt-3">Нет аккаунта? <a href="register.php" class="text-decoration-none">Зарегистрироваться</a></p>
    </div>
</body>
</html>
<?php include 'footer.php'; // Подключаем подвал ?>