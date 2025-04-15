<?php
include 'configM.php';
session_start();

// Если пользователь не авторизован, перенаправляем его на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: autorization.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем данные из формы
$name  = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Простая валидация (при необходимости можно добавить более строгую проверку)
if (empty($name) || empty($email)) {
    header("Location: profile.php?msg=" . urlencode("Имя и email не могут быть пустыми."));
    exit;
}

// Если пользователь указал новый пароль, обновляем и его
if (!empty($password)) {
    // Пример хеширования пароля (не рекомендуется использовать md5/sha1, лучше password_hash)
    $hashed_password = md5(sha1($password));
    $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $mysqli->error);
    }
    $stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);
} else {
    // Если новый пароль не указан, обновляем только имя и email
    $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $mysqli->error);
    }
    $stmt->bind_param("ssi", $name, $email, $user_id);
}

if ($stmt->execute()) {
    $stmt->close();
    header("Location: profile.php?msg=" . urlencode("Профиль успешно обновлён."));
    exit;
} else {
    echo "Ошибка обновления профиля: " . $stmt->error;
}
?>
