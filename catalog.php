<?php 
include 'header.php'; 
include 'configM.php';

// Получаем все товары из базы данных и группируем их по категориям и подкатегориям
$sql = "SELECT * FROM products ORDER BY category, subcategory, id";
$result = $mysqli->query($sql);

$categories = [];
while ($row = $result->fetch_assoc()){
    $categories[$row['category']][$row['subcategory']][] = $row;
}
?>

<div class="container py-5">
  <h2 class="text-center mb-4">Каталог товаров</h2>
  
  <?php foreach($categories as $category_name => $subcategories): ?>
    <div class="mb-5">
      <h3><?= htmlspecialchars($category_name) ?></h3>
      <?php foreach($subcategories as $subcategory_name => $products): ?>
        <h5><?= htmlspecialchars($subcategory_name) ?></h5>
        <!-- Контейнер карточек с data-атрибутами для удобного поиска -->
        <div class="row product-list" data-category="<?= htmlspecialchars($category_name) ?>" data-subcategory="<?= htmlspecialchars($subcategory_name) ?>">
          <?php foreach(array_slice($products, 0, 4) as $product): ?>
            <div class="col-md-3 mb-4">
              <div class="card product-card border-0 shadow-sm" style="transition: transform 0.3s; opacity: 0; animation: fadeIn 0.8s forwards;">
                <!-- Кнопка добавления в корзину (верхний правый угол) -->
                <div class="cart-icon position-absolute top-0 end-0 p-2">
                  <button class="btn btn-light p-2 add-to-basket" data-product-id="<?= $product['id'] ?>">
                    <img src="img/basket.png" alt="Корзина" style="width:20px; height:20px;">
                  </button>
                </div>
                <!-- Изображение товара -->
                <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" style="height:200px; object-fit:cover;">
                <div class="card-body text-center">
                  <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                  <p class="card-text text-muted"><?= number_format($product['price'], 2) ?> руб.</p>
                  <!-- Кнопка добавления в корзину (ниже) -->
                  <button class="btn btn-outline-primary btn-sm add-to-basket" data-product-id="<?= $product['id'] ?>">Добавить в корзину</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <!-- Кнопка "Показать ещё", если товаров больше 4 -->
        <?php if(count($products) > 4): ?>
          <div class="text-center mt-3">
            <button class="btn btn-primary show-more" data-category="<?= htmlspecialchars($category_name) ?>" data-subcategory="<?= htmlspecialchars($subcategory_name) ?>">Показать ещё</button>
            <script type="application/json" id="data-<?= htmlspecialchars($category_name) ?>-<?= htmlspecialchars($subcategory_name) ?>">
              <?= json_encode(array_slice($products, 4)) ?>
            </script>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</div>

<!-- Дополнительные стили для карточек (анимация и hover-эффект) -->
<style>
@keyframes fadeIn {
  to { opacity: 1; }
}
.product-card:hover {
  transform: scale(1.05);
}
</style>

<script>
// Обработчик для кнопки "Показать ещё"
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".show-more").forEach(button => {
    button.addEventListener("click", function () {
      let category = this.dataset.category;
      let subcategory = this.dataset.subcategory;
      // Находим контейнер карточек по data-атрибутам
      let container = document.querySelector(`.product-list[data-category="${category}"][data-subcategory="${subcategory}"]`);
      let dataElement = document.getElementById(`data-${category}-${subcategory}`);
      let products = JSON.parse(dataElement.textContent);
      products.forEach(product => {
        let productHtml = `
          <div class="col-md-3 mb-4">
            <div class="card product-card border-0 shadow-sm" style="transition: transform 0.3s; opacity: 0; animation: fadeIn 0.8s forwards;">
              <div class="cart-icon position-absolute top-0 end-0 p-2">
                <button class="btn btn-light p-2 add-to-basket" data-product-id="${product.id}">
                  <img src="img/basket.png" alt="Корзина" style="width:20px; height:20px;">
                </button>
              </div>
              <img src="${product.image}" class="card-img-top" alt="${product.name}" style="height:200px; object-fit:cover;">
              <div class="card-body text-center">
                <h5 class="card-title">${product.name}</h5>
                <p class="card-text text-muted">${parseFloat(product.price).toFixed(2)} руб.</p>
                <button class="btn btn-outline-primary btn-sm add-to-basket" data-product-id="${product.id}">Добавить в корзину</button>
              </div>
            </div>
          </div>
        `;
        container.insertAdjacentHTML("beforeend", productHtml);
      });
      this.remove(); // Удаляем кнопку "Показать ещё"
      attachAddToBasketHandlers();
    });
  });
  attachAddToBasketHandlers();
});

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
</script>

<?php include 'footer.php'; ?>
