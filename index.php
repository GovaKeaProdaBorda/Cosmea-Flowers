<?php 
include 'header.php'; 
include 'configM.php';

// Получаем популярные товары (например, последние 8 добавленных)
$sql = "SELECT * FROM products ORDER BY id DESC LIMIT 32";
$result = $mysqli->query($sql);
$featuredProducts = [];
while ($row = $result->fetch_assoc()){
    $featuredProducts[] = $row;
}
?>
<main>
  <div class="container py-5">
    <!-- Приветственный блок -->
    <div class="jumbotron text-center mb-5" style="background:rgba(202, 255, 167, 0.34); padding: 50px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
      <h1 class="display-4">Добро пожаловать в наш магазин!</h1>
      <p class="lead">Хотите чего-то конкретного? Переходите в наш каталог товаров!</p>
      <a class="btn btn-outline-danger btn-lg" href="catalog.php" role="button">Перейти в каталог товаров</a>
    </div>
    
    <!-- Секция популярных товаров -->
    <h2 class="text-center mb-4">Популярные товары</h2>
    <div class="row">
      <?php foreach($featuredProducts as $product): ?>
        <div class="col-md-3 mb-4">
          <div class="card product-card border-0 shadow-sm" style="transition: transform 0.3s; opacity: 0; animation: fadeIn 0.8s forwards;">
            <!-- Кнопка добавления в корзину (иконка в верхнем правом углу) -->
            <div class="position-absolute top-0 end-0 p-2">
              <button class="btn btn-light p-2 add-to-basket" data-product-id="<?= $product['id'] ?>">
                <img src="img/basket.png" alt="Корзина" style="width:20px; height:20px;">
              </button>
            </div>
            <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" style="height:200px; object-fit:cover;">
            <div class="card-body text-center">
              <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
              <p class="card-text text-muted"><?= number_format($product['price'], 2) ?> руб.</p>
              <!-- Кнопка "Добавить в корзину" внизу карточки -->
              <button class="btn btn-outline-primary btn-sm add-to-basket" data-product-id="<?= $product['id'] ?>">Добавить в корзину</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  
  <style>
    @keyframes fadeIn {
      to { opacity: 1; }
    }
    .product-card:hover {
      transform: scale(1.05);
    }
  </style>
  
  <script>
    // Функция для навешивания обработчика на кнопки "Добавить в корзину"
    function attachAddToBasketHandlers() {
      document.querySelectorAll(".add-to-basket").forEach(button => {
        button.onclick = function() {
          let productId = this.dataset.productId;
          fetch('add_to_basket.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + encodeURIComponent(productId)
          })
          .then(response => response.text())
          .then(data => { alert(data); })
          .catch(error => { console.error('Ошибка:', error); });
        }
      });
    }
    
    document.addEventListener("DOMContentLoaded", function () {
      attachAddToBasketHandlers();
    });
  </script>
</main>
  
<?php include 'footer.php'; ?>
