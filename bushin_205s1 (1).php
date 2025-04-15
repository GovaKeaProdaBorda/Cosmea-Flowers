<?php
/**
 * Export to PHP Array plugin for PHPMyAdmin
 * @version 5.2.0
 */

/**
 * Database `bushin_205s1`
 */

/* `bushin_205s1`.`basket` */
$basket = array(
  array('id' => '24','user_id' => '1','product_id' => '7','quantity' => '1','added_at' => '2025-04-15 21:59:25'),
  array('id' => '25','user_id' => '1','product_id' => '6','quantity' => '1','added_at' => '2025-04-15 22:00:38'),
  array('id' => '26','user_id' => '1','product_id' => '5','quantity' => '1','added_at' => '2025-04-15 22:00:40')
);

/* `bushin_205s1`.`orders` */
$orders = array(
  array('id' => '2','user_id' => '11','order_date' => '2025-02-02 19:46:44','total_amount' => '1500.00','status' => 'completed','shipping_method' => 'pickup','delivery_address' => NULL),
  array('id' => '3','user_id' => '1','order_date' => '2025-02-04 12:52:21','total_amount' => '2900.00','status' => 'pending','shipping_method' => 'delivery','delivery_address' => 'Карисав, 12'),
  array('id' => '4','user_id' => '1','order_date' => '2025-02-04 12:53:41','total_amount' => '1500.00','status' => 'pending','shipping_method' => 'pickup','delivery_address' => NULL),
  array('id' => '6','user_id' => '11','order_date' => '2025-02-05 11:14:38','total_amount' => '4400.00','status' => 'pending','shipping_method' => 'delivery','delivery_address' => NULL),
  array('id' => '7','user_id' => '11','order_date' => '2025-04-09 11:53:06','total_amount' => '3000.00','status' => 'pending','shipping_method' => 'pickup','delivery_address' => NULL)
);

/* `bushin_205s1`.`order_items` */
$order_items = array(
  array('id' => '4','order_id' => '2','product_id' => '7','quantity' => '1','price' => '1000.00'),
  array('id' => '5','order_id' => '2','product_id' => '4','quantity' => '1','price' => '500.00'),
  array('id' => '7','order_id' => '3','product_id' => '3','quantity' => '1','price' => '400.00'),
  array('id' => '8','order_id' => '3','product_id' => '4','quantity' => '1','price' => '500.00'),
  array('id' => '9','order_id' => '4','product_id' => '2','quantity' => '1','price' => '1500.00'),
  array('id' => '11','order_id' => '6','product_id' => '5','quantity' => '1','price' => '1400.00'),
  array('id' => '12','order_id' => '6','product_id' => '7','quantity' => '1','price' => '1000.00'),
  array('id' => '13','order_id' => '6','product_id' => '6','quantity' => '1','price' => '1500.00'),
  array('id' => '14','order_id' => '7','product_id' => '6','quantity' => '1','price' => '1500.00'),
  array('id' => '15','order_id' => '7','product_id' => '4','quantity' => '1','price' => '500.00'),
  array('id' => '16','order_id' => '7','product_id' => '7','quantity' => '1','price' => '1000.00')
);

/* `bushin_205s1`.`products` */
$products = array(
  array('id' => '2','name' => 'Кактус','category' => 'Комнатные растения','subcategory' => NULL,'price' => '1500.00','image' => 'img/flower2.png','description' => 'Описание кактуса'),
  array('id' => '3','name' => 'Роза  ','category' => 'Цветы','subcategory' => 'Цветы на каждый день','price' => '400.00','image' => 'img/Flowers for every day/roza.png','description' => 'Красивые розы для вашего дома.'),
  array('id' => '4','name' => 'Тюльпан','category' => 'Цветы','subcategory' => 'Цветы на каждый день','price' => '500.00','image' => 'img/Flowers for every day/Tulip.webp','description' => 'Яркий и свежий тюльпан.'),
  array('id' => '5','name' => 'Коробочка лилий','category' => 'Цветы','subcategory' => 'Цветы на каждый день','price' => '1400.00','image' => 'img/Flowers for every day/boxlilies.webp','description' => 'Ароматные лилии для любой обстановки.'),
  array('id' => '6','name' => '9 Гербер','category' => 'Цветы','subcategory' => 'Цветы на каждый день','price' => '1500.00','image' => 'img/Flowers for every day/9 Гербер.webp','description' => 'Яркие герберы, которые поднимают настроение.'),
  array('id' => '7','name' => 'Архидея','category' => 'Цветы','subcategory' => 'Цветы на каждый день','price' => '1000.00','image' => 'img/Flowers for every day/arhid.webp','description' => 'Орхидея Живая в Горшке Цветущая – покупайте по выгодным ценам! Быстрая доставка.')
);

/* `bushin_205s1`.`users` */
$users = array(
  array('id' => '1','name' => 'admin','email' => 'admin@inbox.ru','password' => '0c7540eb7e65b553ec1ba6b20de79608','registration_date' => '2024-12-20 00:00:00','role' => 'admin','status' => 'online'),
  array('id' => '3','name' => 'Анна Смирнова','email' => 'anna.smirnova@example.com','password' => '49f526b491b15de5ec4a3a2858ef2b6e','registration_date' => '2024-12-20 00:00:00','role' => 'user','status' => 'online'),
  array('id' => '4','name' => 'Сергей Петров','email' => 'sergey.petrov@example.com','password' => '23277a20c20422ab123153b8406331a7','registration_date' => '2024-12-20 00:00:00','role' => 'user','status' => 'busy'),
  array('id' => '5','name' => 'Мария Кузнецова','email' => 'maria.kuznecova@example.com','password' => 'e933f35ad585ac6753ee607ab8fd0a4d','registration_date' => '2024-12-20 00:00:00','role' => 'user','status' => 'busy'),
  array('id' => '6','name' => 'Александр Сидоров','email' => 'alex.sidorov@example.com','password' => '8c86ff3ae60eb32e5b9e599728be7a44','registration_date' => '2024-12-20 00:00:00','role' => 'user','status' => 'busy'),
  array('id' => '7','name' => 'Олег','email' => 'Flo@inbox.ru','password' => 'c78b6663d47cfbdb4d65ea51c104044e','registration_date' => '2024-12-20 00:00:00','role' => 'user','status' => 'busy'),
  array('id' => '8','name' => 'Креол','email' => 'gre22@inbox.ru','password' => 'c78b6663d47cfbdb4d65ea51c104044e','registration_date' => '2024-12-20 00:00:00','role' => 'user','status' => 'busy'),
  array('id' => '9','name' => 'Иван','email' => 'Ivakor@inbox.ru','password' => '4d07cd0157cb9c3e5b6edb850337d493','registration_date' => '2025-01-28 00:00:00','role' => 'user','status' => 'busy'),
  array('id' => '11','name' => 'Гена','email' => 'Artemon@inbox.ru','password' => 'c72f5359811af711f29b1fac7150042f','registration_date' => '2025-02-02 00:00:00','role' => 'user','status' => 'busy')
);
