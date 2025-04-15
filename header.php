<?php
include 'configM.php';
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Магазин цветов</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../cve/mycss/styleS.css" rel="stylesheet">
</head>
<body>
  <!-- Header -->
  <header>
    <!-- Top Header -->
    <div class="top-header">
      <div class="container d-flex justify-content-between">
        <div>
          <a href="#">О нас</a>
          <span>|</span>
          <a href="#">Оплата</a>
          <span>|</span>
          <a href="#">Доставка</a>
          <span>|</span>
          <a href="#">Гарантии</a>
          <span>|</span>
          <a href="#">Контакты</a>
        </div>
        <div>
          <span>+7 (123) 456-78-90</span>
          <span>|</span>
          <a href="#">Телеграм</a>
          <span>|</span>
          <a href="#">ВК</a>
          <span>|</span>
          <a href="#">Одноклассники</a>
        </div>
      </div>
    </div>
    <!-- Middle Header -->
    <div class="header-middle">
      <div class="container">
        <!-- Логотип с ссылкой на index.php -->
        <div class="logo">
          <a href="index.php"><img src="img/logo.png" alt="Логотип"></a>
        </div>
        <!-- Информация о городе и времени доставки -->
        <div class="city-info">
          <p>Барнаул</p>
          <p>Доставка с 9:00 до 21:00</p>
        </div>
        <!-- Поле поиска -->
        <div class="search flex-grow-1 mx-4">
          <input type="text" class="form-control" placeholder="Поиск...">
        </div>
        <!-- Иконки -->
        <div class="icons">
          <a href="basket.php" class="text-center">
            <img src="img/basket.png" alt="Корзина">
            <div>Корзина</div>
          </a>
          <a href="orders.php" class="text-center">
            <img src="img/orders.png" alt="Заказы">
            <div>Заказы</div>
          </a>
          <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="autorization.php" class="text-center">
              <img src="img/personal.png" alt="Вход">
              <div>Вход</div>
            </a>
          <?php else: ?>
            <a href="<?= ($_SESSION['role'] === 'admin') ? 'admin.php' : 'profile.php' ?>" class="text-center">
              <img src="img/personal.png" alt="Личный кабинет">
              <div>Ваш кабинет</div>
            </a>
            <a href="logout.php" class="text-center">
              <img src="img/exit.png" alt="Выход">
              <div>Выход</div>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <!-- Bottom Header -->
    <div class="header-bottom">
      <div class="container">
        <nav>
          <ul>
            <li><a href="index.php">Главная</a></li>
            <li>
              <a href="catalog.php">Цветы</a>
              <ul>
                <li><a href="catalog.php">Цветы на каждый день</a></li>
                <li><a href="catalog.php">Комнатные растения</a></li>
              </ul>
            </li>
            <li>
              <a href="catalog.php">Букеты</a>
              <ul>
                <li><a href="catalog.php">Ссылка на каталог</a></li>
              </ul>
            </li>
            <li>
              <a href="catalog.php">Композиции</a>
              <ul>
                <li><a href="catalog.php">Ссылка на каталог</a></li>
              </ul>
            </li>
            <li>
              <a href="catalog.php">Праздники</a>
              <ul>
                <li><a href="catalog.php">8 марта</a></li>
                <li><a href="catalog.php">1 сентября</a></li>
              </ul>
            </li>
            <li>
              <a href="catalog.php">Всё для растений</a>
              <ul>
                <li><a href="catalog.php">Грунт</a></li>
              </ul>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </header>
  <main>
