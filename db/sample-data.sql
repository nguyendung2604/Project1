/*
SQLyog Ultimate v9.51 
MySQL - 5.5.5-10.4.32-MariaDB : Database - project
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`project` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `project`;

/*Data for the table `brands` */

insert  into `brands`(`brand_id`,`name`,`created_at`,`updated_at`) values (1,'Apple','2025-06-12 15:39:20','2025-06-12 15:39:20'),(2,'Samsung','2025-06-12 15:39:20','2025-06-12 15:39:20'),(3,'Xiaomi','2025-06-12 15:39:20','2025-06-12 15:39:20'),(4,'Huawei','2025-06-12 15:39:20','2025-06-12 15:39:20'),(5,'Oppo','2025-06-12 15:39:20','2025-06-12 15:39:20'),(6,'Vivo','2025-06-12 15:39:20','2025-06-12 15:39:20'),(7,'Sony','2025-06-12 15:39:20','2025-06-12 15:39:20'),(8,'Nokia','2025-06-12 15:39:20','2025-06-12 15:39:20'),(9,'Realme','2025-06-12 15:39:20','2025-06-12 15:39:20'),(10,'OnePlus','2025-06-12 15:39:20','2025-06-12 15:39:20');

/*Data for the table `cart_items` */

insert  into `cart_items`(`cart_item_id`,`cart_id`,`product_id`,`quantity`,`price`,`created_at`,`updated_at`) values (35,11,2,1,29990000,'2025-06-14 23:23:43','2025-06-14 23:23:43'),(36,11,3,1,19990000,'2025-06-14 23:23:44','2025-06-14 23:23:44');

/*Data for the table `carts` */

insert  into `carts`(`cart_id`,`user_id`,`total_price`,`created_at`,`updated_at`) values (11,3,49980000,'2025-06-14 23:23:43','2025-06-14 23:23:44');

/*Data for the table `categories` */

insert  into `categories`(`category_id`,`name`,`created_at`,`updated_at`) values (1,'Điện thoại thông minh','2025-06-12 15:40:38','2025-06-12 15:40:38'),(2,'Phụ kiện điện thoại','2025-06-12 15:40:38','2025-06-12 15:40:38'),(3,'Ốp lưng & Bao da','2025-06-12 15:40:38','2025-06-12 15:40:38'),(4,'Cáp sạc & Bộ sạc','2025-06-12 15:40:38','2025-06-12 15:40:38'),(5,'Tai nghe & Loa Bluetooth','2025-06-12 15:40:38','2025-06-12 15:40:38'),(6,'Kính cường lực','2025-06-12 15:40:38','2025-06-12 15:40:38'),(7,'Pin dự phòng','2025-06-12 15:40:38','2025-06-12 15:40:38'),(8,'Thẻ nhớ & USB OTG','2025-06-12 15:40:38','2025-06-12 15:40:38'),(9,'Sim & Gói cước','2025-06-12 15:40:38','2025-06-12 15:40:38'),(10,'Điện thoại phổ thông','2025-06-12 15:40:38','2025-06-12 15:40:38');

/*Data for the table `coupons` */

insert  into `coupons`(`coupon_id`,`code`,`discount_type`,`discount_value`,`expires_at`,`usage_limit`,`used_count`,`created_at`) values (1,'GIAM10','percentage',10,'2025-12-31 23:59:59',NULL,3,'2025-06-14 15:47:00'),(2,'GIAM100K','fixed',100000,'2025-12-31 23:59:59',NULL,0,'2025-06-14 15:47:00');

/*Data for the table `customers` */

insert  into `customers`(`customer_id`,`user_id`,`name`,`phone`,`email`,`address`,`created_at`,`updated_at`,`consignee`) values (1,NULL,'Ly Mí Và','0353132121','lymivagtvt57@gmail.com','57 Pham Hy Luong, Thanh My Loi Ward','2025-06-13 10:03:19','2025-06-13 10:03:19','Ly Mí Và'),(2,3,'Kế toán','0353132121','admin@gmail.com','57 Pham Hy Luong, Thanh My Loi Ward','2025-06-13 10:07:18','2025-06-13 10:53:26','Ly Mí Và');

/*Data for the table `favorites` */

insert  into `favorites`(`id`,`user_id`,`product_id`,`created_at`) values (1,3,1,'2025-06-13 16:10:38'),(2,3,2,'2025-06-13 16:26:43'),(3,3,3,'2025-06-13 16:26:44'),(4,3,4,'2025-06-13 16:26:46'),(5,3,7,'2025-06-13 16:26:47'),(6,3,6,'2025-06-13 16:26:48'),(7,3,5,'2025-06-13 16:26:49'),(8,3,9,'2025-06-13 16:26:50');

/*Data for the table `order_items` */

insert  into `order_items`(`order_item_id`,`order_id`,`product_id`,`quantity`,`price`,`created_at`,`updated_at`) values (1,1,1,1,32990000,'2025-06-13 10:03:19','2025-06-13 10:03:19'),(2,1,6,1,18990000,'2025-06-13 10:03:19','2025-06-13 10:03:19'),(3,2,7,1,25990000,'2025-06-13 10:07:18','2025-06-13 10:07:18'),(4,2,5,1,17990000,'2025-06-13 10:07:18','2025-06-13 10:07:18'),(6,4,1,1,32990000,'2025-06-14 16:18:14','2025-06-14 16:18:14'),(7,4,3,1,19990000,'2025-06-14 16:18:14','2025-06-14 16:18:14'),(8,5,7,1,25990000,'2025-06-14 16:20:46','2025-06-14 16:20:46'),(9,5,9,1,9990000,'2025-06-14 16:20:46','2025-06-14 16:20:46'),(10,6,1,1,32990000,'2025-06-14 16:25:20','2025-06-14 16:25:20'),(11,6,5,1,17990000,'2025-06-14 16:25:20','2025-06-14 16:25:20'),(12,7,1,2,32990000,'2025-06-14 22:40:27','2025-06-14 22:40:27'),(13,7,3,1,19990000,'2025-06-14 22:40:27','2025-06-14 22:40:27'),(14,8,1,1,32990000,'2025-06-14 22:42:34','2025-06-14 22:42:34'),(15,8,2,1,29990000,'2025-06-14 22:42:34','2025-06-14 22:42:34'),(16,9,2,1,29990000,'2025-06-14 22:51:44','2025-06-14 22:51:44'),(17,9,3,1,19990000,'2025-06-14 22:51:44','2025-06-14 22:51:44'),(18,10,1,1,32990000,'2025-06-14 22:52:42','2025-06-14 22:52:42'),(19,10,2,1,29990000,'2025-06-14 22:52:42','2025-06-14 22:52:42'),(20,11,2,1,29990000,'2025-06-14 22:57:02','2025-06-14 22:57:02'),(21,11,6,1,18990000,'2025-06-14 22:57:02','2025-06-14 22:57:02'),(22,11,7,1,25990000,'2025-06-14 22:57:02','2025-06-14 22:57:02'),(23,12,2,1,29990000,'2025-06-14 23:03:20','2025-06-14 23:03:20'),(24,12,3,1,19990000,'2025-06-14 23:03:20','2025-06-14 23:03:20');

/*Data for the table `orders` */

insert  into `orders`(`order_id`,`user_id`,`total_price`,`status`,`created_at`,`updated_at`,`customer_id`,`note`,`payment_method`,`coupon_code`,`discount_amount`) values (1,NULL,51980000,'pending','2025-06-13 10:03:19','2025-06-13 10:03:19',1,NULL,'cod',NULL,0),(2,NULL,43980000,'cancelled','2025-06-13 10:07:18','2025-06-14 14:14:41',2,NULL,'cod',NULL,0),(4,3,47682000,'pending','2025-06-14 16:18:14','2025-06-14 23:22:14',2,NULL,'cod','GIAM10',5298000),(5,3,32382000,'completed','2025-06-14 16:20:46','2025-06-14 23:22:15',2,NULL,'cod','GIAM10',3598000),(6,3,45882000,'pending','2025-06-14 16:25:20','2025-06-14 23:22:15',2,NULL,'cod','GIAM10',5098000),(7,3,85970000,'pending','2025-06-14 22:40:27','2025-06-14 23:22:16',2,NULL,'vnpay',NULL,0),(8,3,62980000,'pending','2025-06-14 22:42:34','2025-06-14 23:22:17',2,NULL,'',NULL,0),(9,3,49980000,'pending','2025-06-14 22:51:44','2025-06-14 23:22:18',2,NULL,'bank_transfer',NULL,0),(10,3,62980000,'completed','2025-06-14 22:52:42','2025-06-14 23:43:36',2,NULL,'vnpay',NULL,0),(11,3,74970000,'pending','2025-06-14 22:57:02','2025-06-14 23:22:19',2,NULL,'vnpay',NULL,0),(12,3,49980000,'pending','2025-06-14 23:03:20','2025-06-14 23:22:21',2,NULL,'',NULL,0);

/*Data for the table `payment_methods` */

insert  into `payment_methods`(`payment_method_id`,`method_code`,`method_name`,`created_at`,`updated_at`) values (1,'vnpay','Thanh toán qua VNPay','2025-06-14 23:30:09','2025-06-14 23:30:09'),(2,'bank_transfer','Chuyển khoản ngân hàng','2025-06-14 23:30:09','2025-06-14 23:30:09'),(3,'card_expiry','Thẻ tín dụng / Thẻ ghi nợ','2025-06-14 23:30:09','2025-06-14 23:30:09'),(4,'momo','Thanh toán qua MoMo','2025-06-14 23:30:09','2025-06-14 23:30:09'),(7,'ưerwertew','rểtr','2025-06-14 23:36:12','2025-06-14 23:36:12');

/*Data for the table `product_attributes` */

/*Data for the table `product_images` */

insert  into `product_images`(`product_image_id`,`product_id`,`image_url`,`created_at`,`updated_at`) values (1,1,'https://cdn.tgdd.vn/Products/Images/42/329149/iphone-16-pro-max-sa-mac-thumb-1-600x600.jpg','2025-06-12 15:46:08','2025-06-12 15:46:08'),(2,2,'https://img.websosanh.vn/v2/users/review/images/0smhaniff09ui.jpg?compress=85','2025-06-12 15:46:08','2025-06-12 15:46:08'),(3,3,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQiu548I2psCHbdoRxVrsgJvz-A-2r9wXZMtQ&s','2025-06-12 15:46:08','2025-06-12 15:46:08'),(4,4,'https://cdn-images.vtv.vn/2019/10/10/photo-1-15706463929181755249740.jpg','2025-06-12 15:46:08','2025-06-12 15:46:08'),(5,5,'https://cdn.tgdd.vn/Products/Images/42/329149/iphone-16-pro-max-sa-mac-thumb-1-600x600.jpg','2025-06-12 15:46:08','2025-06-12 15:46:08'),(6,6,'https://img.websosanh.vn/v2/users/review/images/0smhaniff09ui.jpg?compress=85','2025-06-12 15:46:08','2025-06-12 15:46:08'),(7,7,'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQiu548I2psCHbdoRxVrsgJvz-A-2r9wXZMtQ&s','2025-06-12 15:46:08','2025-06-12 15:46:08'),(8,8,'https://cdn-images.vtv.vn/2019/10/10/photo-1-15706463929181755249740.jpg','2025-06-12 15:46:08','2025-06-12 15:46:08'),(9,9,'https://img.websosanh.vn/v2/users/review/images/0smhaniff09ui.jpg?compress=85','2025-06-12 15:46:08','2025-06-12 15:46:08'),(10,10,'https://cdn.tgdd.vn/Products/Images/42/329149/iphone-16-pro-max-sa-mac-thumb-1-600x600.jpg','2025-06-12 15:46:08','2025-06-12 15:46:08');

/*Data for the table `products` */

insert  into `products`(`product_id`,`name`,`description`,`price`,`category_id`,`brand_id`,`quantity`,`created_at`,`updated_at`,`old_price`,`review`) values (1,'iPhone 14 Pro Max','Điện thoại cao cấp của Apple',32990000,1,1,100,'2025-06-12 15:42:23','2025-06-12 22:34:14',34990000,1),(2,'Samsung Galaxy S23 Ultra','Flagship mới nhất của Samsung',29990000,1,2,80,'2025-06-12 15:42:23','2025-06-12 22:34:15',NULL,2),(3,'Xiaomi 13 Pro','Hiệu năng mạnh, giá tốt',19990000,1,3,120,'2025-06-12 15:42:23','2025-06-12 22:34:16',NULL,3),(4,'Huawei P60 Pro','Camera đỉnh cao, thiết kế sang trọng',23990000,1,4,90,'2025-06-12 15:42:23','2025-06-12 22:34:17',NULL,4),(5,'Oppo Find X6','Thiết kế đẹp, sạc nhanh 80W',17990000,1,5,110,'2025-06-12 15:42:23','2025-06-12 22:34:18',NULL,5),(6,'Vivo X90','Chụp ảnh ban đêm tốt',18990000,1,6,95,'2025-06-12 15:42:23','2025-06-12 22:34:20',NULL,5),(7,'Sony Xperia 1 V','Màn hình 4K OLED, âm thanh tốt',25990000,1,7,70,'2025-06-12 15:42:23','2025-06-12 22:34:21',NULL,4),(8,'Nokia G21','Giá rẻ, pin trâu',3990000,10,8,150,'2025-06-12 15:42:23','2025-06-12 22:34:23',NULL,3),(9,'Realme GT Neo 5','Hiệu năng cao, giá tầm trung',9990000,1,9,130,'2025-06-12 15:42:23','2025-06-12 22:34:24',NULL,2),(10,'OnePlus 11','Cấu hình mạnh, OxygenOS mượt',17990000,1,10,85,'2025-06-12 15:42:23','2025-06-12 22:34:27',NULL,1);

/*Data for the table `return_requests` */

insert  into `return_requests`(`return_id`,`order_id`,`reason`,`status`,`created_at`,`updated_at`) values (1,2,'3242 445','rejected','2025-06-14 14:03:34','2025-06-14 14:07:13'),(2,5,'Ko nhu cầu','pending','2025-06-14 23:42:24','2025-06-14 23:42:24');

/*Data for the table `reviews` */

insert  into `reviews`(`review_id`,`order_id`,`product_id`,`customer_id`,`rating`,`comment`,`created_at`,`updated_at`) values (1,2,5,2,3,'rwett','2025-06-13 13:49:29','2025-06-13 13:49:29'),(2,2,5,2,3,'rwett','2025-06-13 13:49:40','2025-06-13 13:49:40'),(3,2,5,2,3,'rteyye ỷey','2025-06-13 13:52:11','2025-06-13 13:52:11'),(4,2,7,2,4,'45 436','2025-06-13 13:56:33','2025-06-13 13:56:33'),(5,2,7,2,4,'êtrte','2025-06-13 13:57:30','2025-06-13 13:57:30'),(6,NULL,1,2,NULL,'ểtt','2025-06-13 15:31:37','2025-06-13 15:31:37'),(7,NULL,2,2,NULL,'rẻtrey','2025-06-13 15:32:44','2025-06-13 15:32:44'),(8,NULL,2,2,NULL,'Xin chào','2025-06-13 15:45:54','2025-06-13 15:45:54'),(9,NULL,2,2,NULL,'Cảm ơn','2025-06-13 15:46:14','2025-06-13 15:46:14'),(10,NULL,1,2,NULL,'trt','2025-06-13 18:04:04','2025-06-13 18:04:04');

/*Data for the table `shipping_addresses` */

insert  into `shipping_addresses`(`address_id`,`customer_id`,`recipient_name`,`phone`,`address`,`is_default`,`created_at`,`updated_at`) values (3,2,'Ly Mí Và 54','0353132121','Hà Giang 123',0,'2025-06-14 15:12:56','2025-06-14 15:20:24'),(4,2,'Ly Mí Và','0353132122','wetwtt ewrtr',1,'2025-06-14 15:20:24','2025-06-14 15:20:24');

/*Data for the table `users` */

insert  into `users`(`user_id`,`username`,`password`,`email`,`fullname`,`role`,`status`,`created_at`,`updated_at`) values (3,'admin123','$2y$10$9AnsEC1E4bcnUdevwB/Wr.C12To55E6VfmHM6N2uI4PembJaRuN7i','admin@gmail.com','ABC','user','actived','2025-06-12 13:49:39','2025-06-13 10:48:44'),(4,'duykhanh','$2y$10$SUJpzGmalqwNv6JE6Fu92uLUtVk8ZPSpQ6jfU3nl768sB.Yz14j3W','duykhanh@gmail.com','Duy Khánh','admin','actived','2025-06-15 21:51:41','2025-06-15 21:52:17');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
