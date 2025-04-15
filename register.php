<?php include 'header.php'; // Подключаем шапку ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>

</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-4">Регистрация</h2>
        <form action="register_handler.php" method="post" class="mx-auto" style="max-width: 400px;">
            <div class="mb-3">
                <label for="name" class="form-label">Имя:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
        </form>
        <p class="text-center mt-3">Есть аккаунт? <a href="autorization.php" class="text-decoration-none">Войти</a></p>
    </div>
</body>
</html>
<?php include 'footer.php'; // Подключаем подвал ?>