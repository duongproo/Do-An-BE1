-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th12 05, 2024 lúc 06:05 AM
-- Phiên bản máy phục vụ: 8.0.31
-- Phiên bản PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shopfruit`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--
ALTER DATABASE shopfruit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Nước Trái Cây ', '2024-11-27 22:55:29', '2024-12-04 22:31:01'),
(2, 'Nước Ngọt', '2024-11-27 22:55:29', '2024-12-02 16:26:33'),
(3, 'Nước Lạnh', '2024-11-27 22:55:29', '2024-12-02 16:26:35'),
(4, 'Sinh Tố', '2024-11-27 22:55:29', '2024-12-02 16:26:38'),
(5, 'MeatNước Uống Thể Thao', '2024-11-27 22:55:29', '2024-12-02 16:26:41'),
(14, 'Nước soda ', '2024-12-04 23:22:04', '2024-12-04 23:22:04'),
(15, 'Nước có cồn ', '2024-12-04 23:22:04', '2024-12-04 23:22:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `expiry_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_amount`, `active`, `expiry_date`, `created_at`) VALUES
(1, 'CNUI12', 'percentage', '10.00', 1, '2024-12-11 14:43:18', '2024-12-02 14:44:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_price` double NOT NULL,
  `status` enum('pending','processing','completed','canceled') DEFAULT 'pending',
  `payment_method` enum('cod','credit_card','momo','vnpay') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cod',
  `notes` text NOT NULL,
  `address` varchar(191) NOT NULL,
  `shipping_method` enum('Standard shipping','Express shipping','Free Shipping') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `payment_method`, `notes`, `address`, `shipping_method`, `created_at`, `updated_at`) VALUES
(1, 1, 10.5, 'pending', 'cod', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(2, 2, 6, 'completed', '', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(3, 3, 15, 'processing', 'momo', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(4, 4, 25, 'completed', 'credit_card', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(5, 5, 8, 'canceled', 'cod', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(6, 6, 12.5, 'pending', 'credit_card', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(7, 7, 20, 'completed', 'momo', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(8, 8, 18, 'pending', '', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(9, 9, 30, 'completed', 'cod', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(10, 10, 22, 'processing', 'credit_card', '', '', '', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(44, 16, 90000, 'pending', '', 'SÔSOOSOSO ', '228 duong so 8 thu duc viet nam', 'Free Shipping', '2024-12-04 01:06:45', '2024-12-04 01:06:45'),
(45, 16, 3500000, 'pending', '', 'SÁOOOO', '228 duong so 8 thu duc viet nam', 'Free Shipping', '2024-12-04 01:14:42', '2024-12-04 01:14:42'),
(47, 16, 3590000, 'pending', 'credit_card', 'ádsdasd', '228 duong so 8 thu duc viet nam', 'Free Shipping', '2024-12-04 01:16:30', '2024-12-04 01:16:30'),
(46, 16, 3500000, 'pending', 'cod', 'ádsadd', '228 duong so 8 thu duc viet nam', 'Express shipping', '2024-12-04 01:15:47', '2024-12-04 01:15:47'),
(48, 16, 90000, 'pending', '', 'eeeee', '228 duong so 8 thu duc viet nam', 'Standard shipping', '2024-12-04 01:25:05', '2024-12-04 01:25:05'),
(49, 16, 90000, 'pending', 'cod', 'ddddddd', '228 duong so 8 thu duc viet nam', 'Free Shipping', '2024-12-04 01:26:46', '2024-12-04 01:26:46'),
(50, 16, 45000, 'pending', 'credit_card', 'pppppppppp', '228 duong so 8 thu duc viet nam', 'Standard shipping', '2024-12-04 01:27:35', '2024-12-04 01:27:35'),
(51, 16, 45000, 'pending', '', 'oppp ', '228 duong so 8 thu duc viet nam', 'Express shipping', '2024-12-04 01:28:55', '2024-12-04 01:28:55'),
(52, 16, 45000, 'pending', 'vnpay', 'ádddddddddddddddddddddddddddd ', '', 'Express shipping', '2024-12-04 01:30:29', '2024-12-04 01:30:29'),
(53, 16, 45000, 'completed', 'cod', 'VOX CASNc ', '228 duong so 8 thu duc viet nam', 'Free Shipping', '2024-12-04 01:38:36', '2024-12-04 01:48:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 3, '3.00'),
(2, 1, 2, 2, '1.20'),
(3, 2, 3, 3, '2.00'),
(4, 3, 4, 10, '1.50'),
(5, 4, 5, 8, '3.00'),
(6, 5, 6, 4, '2.50'),
(7, 6, 7, 15, '0.80'),
(8, 7, 8, 3, '5.00'),
(9, 8, 9, 2, '12.00'),
(10, 9, 10, 6, '1.00'),
(51, 39, 1, 1, '3500000.00'),
(50, 38, 2, 1, '45000.00'),
(49, 36, 1, 1, '3500000.00'),
(66, 53, 2, 1, '45000.00'),
(65, 52, 2, 1, '45000.00'),
(64, 51, 2, 1, '45000.00'),
(63, 50, 2, 1, '45000.00'),
(62, 49, 2, 2, '45000.00'),
(61, 48, 2, 2, '45000.00'),
(45, 32, 2, 1, '45000.00'),
(44, 31, 2, 1, '45000.00'),
(43, 30, 2, 1, '45000.00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `payment_method` enum('cod','credit_card','momo','paypal') DEFAULT 'cod',
  `payment_status` enum('pending','success','failed') DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `transaction_id`, `payment_method`, `payment_status`, `amount`, `created_at`) VALUES
(1, 1, 'TXN123456', 'cod', 'success', '10.50', '2024-11-27 22:55:29'),
(2, 2, 'TXN654321', 'paypal', 'success', '6.00', '2024-11-27 22:55:29'),
(3, 3, 'TXN789012', 'momo', 'success', '15.00', '2024-11-27 22:55:29'),
(4, 4, 'TXN345678', 'credit_card', 'success', '25.00', '2024-11-27 22:55:29'),
(5, 5, 'TXN901234', 'cod', 'failed', '8.00', '2024-11-27 22:55:29'),
(6, 6, 'TXN567890', 'credit_card', 'pending', '12.50', '2024-11-27 22:55:29'),
(7, 7, 'TXN098765', 'momo', 'success', '20.00', '2024-11-27 22:55:29'),
(8, 8, 'TXN432109', 'paypal', 'pending', '18.00', '2024-11-27 22:55:29'),
(9, 9, 'TXN876543', 'cod', 'success', '30.00', '2024-11-27 22:55:29'),
(10, 10, 'TXN210987', 'credit_card', '', '22.00', '2024-11-27 22:55:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text,
  `price` double(10,2) NOT NULL,
  `discount` int DEFAULT '0',
  `category_id` int DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `rating` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `discount`, `category_id`, `image`, `rating`, `created_at`, `updated_at`) VALUES
(1, 'Apple', 'Fresh Red Apples là lựa chọn hoàn hảo cho những ai yêu thích hương vị tươi mới và ngọt ngào của trái cây tự nhiên. Được chọn lọc từ những vườn táo hữu cơ chất lượng cao, mỗi trái táo đỏ đều mang trong mình sự kết hợp hài hòa giữa vẻ đẹp tự nhiên, hương vị thơm ngon và giá trị dinh dưỡng tuyệt vời.', 3500000.00, 0, 1, 'chai1j.pg.jpg', 5, '2024-11-27 22:55:29', '2024-12-01 17:05:30'),
(2, 'Banana', 'Organic Bananas là những quả chuối được trồng tự nhiên, không sử dụng hóa chất hay thuốc trừ sâu, đảm bảo an toàn và giàu dinh dưỡng. Với lớp vỏ vàng óng đẹp mắt và hương vị ngọt thanh, mềm mịn, chúng là nguồn cung cấp dồi dào kali, vitamin B6, và chất xơ, giúp hỗ trợ tiêu hóa, tăng năng lượng, và cải thiện sức khỏe tim mạch. Hoàn hảo để ăn trực tiếp, làm sinh tố, hoặc dùng trong các món tráng miệng yêu thích của bạn.', 45000.00, 0, 1, 'chai2.jpg', 4, '2024-11-27 22:55:29', '2024-12-01 17:05:34'),
(3, 'Carrot', 'Crunchy Carrots là những củ cà rốt tươi ngon, giòn tan và đầy màu sắc, được trồng tự nhiên mà không sử dụng hóa chất. Với vị ngọt nhẹ và kết cấu giòn, chúng không chỉ là món ăn vặt lý tưởng mà còn là nguồn cung cấp dồi dào vitamin A, beta-carotene và chất xơ. Crunchy Carrots giúp cải thiện thị lực, tăng cường sức khỏe da và hỗ trợ hệ tiêu hóa. Chúng rất phù hợp để ăn sống, làm salad, hoặc chế biến trong các món ăn hàng ngày.', 50000.00, 0, 1, 'chai3.jpg', 3, '2024-11-27 22:55:29', '2024-12-01 17:05:37'),
(4, 'Milk', 'Pure Cow Milk là sữa bò nguyên chất, tươi mới, không pha tạp chất, mang đến hương vị thơm ngon và tự nhiên. Sản phẩm được lấy từ những con bò được nuôi dưỡng trong điều kiện an toàn và vệ sinh, đảm bảo chất lượng và dinh dưỡng vượt trội. Với hàm lượng canxi, protein, và vitamin D cao, Pure Cow Milk giúp hỗ trợ phát triển xương, tăng cường hệ miễn dịch và cung cấp năng lượng cho cơ thể. Thích hợp để uống trực tiếp, làm nguyên liệu trong các món ăn và đồ uống hàng ngày.', 600000.00, 0, 1, 'sua.jpg', 5, '2024-11-27 22:55:29', '2024-12-01 17:05:39'),
(5, 'Orange Juice', 'Freshly Squeezed Orange Juice là nước cam tươi nguyên chất, được ép từ những quả cam chín mọng, giàu vitamin C và chất chống oxy hóa. Vị ngọt tự nhiên và hơi chua nhẹ mang đến cảm giác sảng khoái, giúp giải khát tuyệt vời. Đây là nguồn cung cấp dồi dào vitamin và khoáng chất, hỗ trợ tăng cường hệ miễn dịch, cải thiện sức khỏe tim mạch và làn da. Hoàn hảo để thưởng thức vào mỗi buổi sáng hoặc khi cần một thức uống bổ dưỡng trong ngày.', 750000.00, 0, 1, 'chai3.jpg', 4, '2024-11-27 22:55:29', '2024-12-01 17:05:41'),
(6, 'Chips', 'Salted Potato Chips là những lát khoai tây giòn rụm, được chế biến tươi ngon và rắc một lớp muối nhẹ để tạo hương vị đậm đà. Sản phẩm này là món ăn vặt lý tưởng, với vị mặn vừa phải, mang đến sự thỏa mãn cho những ai yêu thích sự giòn tan và hương vị tự nhiên của khoai tây. Salted Potato Chips thích hợp cho mọi lứa tuổi và là lựa chọn tuyệt vời để nhâm nhi trong các bữa tiệc, xem phim, hay ăn kèm với đồ uống yêu thích.', 120000.00, 0, 3, 'chai4.jpg', 5, '2024-11-27 22:55:29', '2024-12-02 15:43:13'),
(7, 'Rice', 'White Jasmine Rice là loại gạo thơm trắng tinh, nổi bật với hương thơm nhẹ nhàng và vị ngọt tự nhiên. Được trồng ở những vùng đất màu mỡ, gạo Jasmine mang lại chất lượng cao, mềm dẻo khi nấu, giúp món ăn thêm phần hấp dẫn. Đây là sự lựa chọn lý tưởng cho các món cơm châu Á, từ cơm chiên đến các món hầm, nấu canh, hoặc làm sushi. Với độ dẻo mịn và hương thơm đặc trưng, White Jasmine Rice là một phần không thể thiếu trong bữa ăn hàng ngày của bạn.', 154000.00, 0, 3, 'chai8.jpg', 5, '2024-11-27 22:55:29', '2024-12-02 15:43:17'),
(8, 'Chicken Breast', 'Fresh Chicken Breast là ức gà tươi ngon, không xương, ít mỡ và giàu protein, là lựa chọn lý tưởng cho các bữa ăn lành mạnh. Với kết cấu thịt mềm mịn và dễ chế biến, Fresh Chicken Breast có thể được áp dụng trong nhiều món ăn khác nhau, từ nướng, xào, hấp đến làm salad. Đây là nguồn cung cấp dồi dào dinh dưỡng, giúp xây dựng cơ bắp, duy trì sức khỏe tim mạch và hỗ trợ giảm cân hiệu quả. Thích hợp cho mọi chế độ ăn kiêng hoặc ăn uống lành mạnh.', 4521000.00, 1, 4, 'chai5.jpg', 4, '2024-11-27 22:55:29', '2024-12-02 15:43:20'),
(9, 'Orange Juice New', 'Fresh Atlantic Salmon là cá hồi Đại Tây Dương tươi ngon, giàu omega-3, vitamin D và protein, giúp tăng cường sức khỏe tim mạch và cải thiện làn da. Với màu cam tự nhiên, thịt cá mềm mịn và hương vị thơm ngon, Fresh Atlantic Salmon lý tưởng để nướng, hấp, làm sushi, hoặc chế biến các món ăn nhẹ nhàng như salad. Đây là sự lựa chọn hoàn hảo cho những ai yêu thích các món ăn bổ dưỡng và lành mạnh, mang lại nhiều lợi ích cho cơ thể.\n\n\n\n\n\n\nFresh Atlantic Salmon là cá hồi Đại Tây Dương tươi ngon, giàu omega-3, vitamin D và protein, giúp tăng cường sức khỏe tim mạch và cải thiện làn da. Với màu cam tự nhiên, thịt cá mềm mịn và hương vị thơm ngon, Fresh Atlantic Salmon lý tưởng để nướng, hấp, làm sushi, hoặc chế biến các món ăn nhẹ nhàng như salad. Đây là sự lựa chọn hoàn hảo cho những ai yêu thích các món ăn bổ dưỡng và lành mạnh, mang lại nhiều lợi ích cho cơ thể.\n\n\n\n\n\nFresh Atlantic Salmon là cá hồi Đại Tây Dương tươi ngon, giàu omega-3, vitamin D và protein, giúp tăng cường sức khỏe tim mạch và cải thiện làn da. Với màu cam tự nhiên, thịt cá mềm mịn và hương vị thơm ngon, Fresh Atlantic Salmon lý tưởng để nướng, hấp, làm sushi, hoặc chế biến các món ăn nhẹ nhàng như salad. Đây là sự lựa chọn hoàn hảo cho những ai yêu thích các món ăn bổ dưỡng và lành mạnh, mang lại nhiều lợi ích cho cơ thể.\n\n\n\n\n\n\n', 250000.00, 2, 4, 'chai6.jpg', 4, '2024-11-27 22:55:29', '2024-12-02 15:43:22'),
(10, 'Ketchup', 'Tomato Ketchup là sốt cà chua đậm đà, được chế biến từ cà chua tươi, gia vị và một chút đường, tạo nên hương vị ngọt ngào và chua nhẹ, hoàn hảo để kết hợp với các món ăn như khoai tây chiên, burger, hotdog hay các món chiên xào khác. Tomato Ketchup là món gia vị quen thuộc trong bữa ăn hàng ngày, mang lại sự tiện lợi và thêm phần hấp dẫn cho mọi bữa tiệc hoặc bữa ăn gia đình.', 410000.00, 0, 4, 'chai8.jpg', 4, '2024-11-27 22:55:29', '2024-12-01 17:05:55'),
(11, 'Nước Cam Tươi ', 'ÁDSADASDASD', 20000.00, NULL, 3, 'avatar4.jpg', 1, '2024-12-02 16:14:40', '2024-12-04 21:54:20'),
(12, 'Nước Dứa Tươi', 'Nước dứa thơm ngon, thanh mát, bổ dưỡng.', 18.30, 0, 1, 'nuocdua.jpg', 5, '2024-12-02 16:14:40', '2024-12-02 16:22:05'),
(13, 'Nước Táo', 'Nước ép táo ngọt ngào, thơm mát.', 22.00, 0, 1, 'nuoctao.jpg', 3, '2024-12-02 16:14:40', '2024-12-02 16:22:32'),
(14, 'Nước Lựu', 'Nước lựu tươi, giúp tăng cường sức khỏe.', 25.00, 5, 1, 'nuocluu.jpg', 4, '2024-12-02 16:14:40', '2024-12-02 16:22:42'),
(15, 'Nước Dưa Hấu', 'Nước dưa hấu tươi mát, giải khát mùa hè.', 15.00, 0, 1, 'nuocduahau.jpg', 5, '2024-12-02 16:14:40', '2024-12-02 16:23:03'),
(16, 'Nước Nho', 'Nước nho tươi, ngọt ngào và bổ dưỡng.', 21.00, 0, 1, 'nuocnho.jpg', 4, '2024-12-02 16:14:40', '2024-12-02 16:23:13'),
(17, 'Nước Quýt', 'Nước quýt tự nhiên, vị chua ngọt hài hòa.', 19.50, 0, 1, 'nuocquyt.jpg', 3, '2024-12-02 16:14:40', '2024-12-02 16:23:24'),
(18, 'Nước Kiwi', 'Nước kiwi tươi ngon, giàu vitamin và chất xơ.', 28.00, 10, 1, 'nuockiwi.jpg', 4, '2024-12-02 16:14:40', '2024-12-02 16:23:32'),
(19, 'Nước Dâu', 'Nước dâu tây thơm ngon, ngọt mát.', 23.00, 0, 1, 'nuocdau.png', 5, '2024-12-02 16:14:40', '2024-12-02 16:23:42'),
(20, 'Nước Bưởi', 'Nước bưởi tươi, thanh mát, tốt cho sức khỏe.', 18.00, 0, 1, 'nuocboi.jpg', 4, '2024-12-02 16:14:40', '2024-12-02 16:23:50'),
(26, 'admin', 'ádasddasdasdad', 123123.00, NULL, 2, 'Ảnh chụp màn hình 2024-11-27 232045.png', 2, '2024-12-04 22:38:21', '2024-12-04 23:39:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, 5, 'Delicious apples!', '2024-11-27 22:55:29'),
(2, 2, 3, 4, 'Fresh and crispy carrots.', '2024-11-27 22:55:29'),
(3, 3, 4, 5, 'Best milk ever.', '2024-11-27 22:55:29'),
(4, 4, 5, 4, 'Tasty juice but a bit sweet.', '2024-11-27 22:55:29'),
(5, 5, 6, 3, 'Good chips but too salty.', '2024-11-27 22:55:29'),
(6, 6, 7, 5, 'Very high quality rice.', '2024-11-27 22:55:29'),
(7, 7, 8, 4, 'Chicken is fresh and tender.', '2024-11-27 22:55:29'),
(8, 8, 9, 5, 'Amazing salmon quality.', '2024-11-27 22:55:29'),
(9, 9, 10, 4, 'Ketchup tastes great with fries.', '2024-11-27 22:55:29'),
(10, 10, 2, 5, 'Bananas are perfectly ripe!', '2024-11-27 22:55:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone`, `role`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'john@example.com', 'password123', '1234567890', 'customer', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(2, 'Jane Smith', 'jane@example.com', 'password123', '0987654321', 'customer', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(3, 'Alice Johnson', 'alice@example.com', 'password123', '1112223333', 'customer', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(4, 'Bob Brown', 'bob@example.com', 'password123', '2223334444', 'customer', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(5, 'Charlie Davis', 'charlie@example.com', 'password123', '3334445555', 'customer', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(6, 'Diana Moore', 'diana@example.com', 'password123', '4445556666', 'customer', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(7, 'Ethan White', 'ethan@example.com', 'password123', '5556667777', 'customer', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(8, 'Fiona Black', 'fiona@example.com', 'password123', '6667778888', 'customer', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(9, 'George Hall', 'george@example.com', 'password123', '7778889999', 'admin', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(10, 'Hannah Scott', 'hannah@example.com', 'password123', '8889990000', 'customer', '2024-11-27 22:55:29', '2024-11-27 22:55:29'),
(13, 'nguyen van a', 'nguyen@gmail.com', '054875123', '$2y$10$ln0Oeo7ZTUG5/', 'customer', '2024-11-29 23:28:13', '2024-11-29 23:28:13'),
(15, 'vo van so', 'vovanso@gmail.com', '0923123323', '$2y$10$L9wxmOE4BgSrY', 'customer', '2024-12-01 16:14:38', '2024-12-01 16:14:38'),
(16, 'vo van son', 'voa@gmail.com', '$2y$10$E1nkOWZVf5REILh4WPen7.hx/waFrCnRAO4zO7DLb/VVLawLXDBkG', '0875454455', 'admin', '2024-12-01 16:26:10', '2024-12-04 23:03:37');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
