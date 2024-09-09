-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 08, 2024 at 02:27 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_dien_thoai` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `first_name`, `last_name`, `username`, `password`, `email`, `avatar`, `so_dien_thoai`) VALUES
('A00001', 'John', 'Due', 'admin', '111', 'johndoe@example.com', 'avatar_admin/admin.png', '0339156809');

--
-- Triggers `admin`
--
DROP TRIGGER IF EXISTS `before_admin_insert`;
DELIMITER $$
CREATE TRIGGER `before_admin_insert` BEFORE INSERT ON `admin` FOR EACH ROW BEGIN
    DECLARE last_id INT;
    DECLARE new_id VARCHAR(6);

    -- Lấy số ID cuối cùng
    SELECT SUBSTRING(admin_id, 2, 5) INTO last_id
    FROM admin
    ORDER BY admin_id DESC
    LIMIT 1;

    IF last_id IS NULL THEN
        SET last_id = 0;
    END IF;

    -- Tăng số ID lên 1 và tạo ID mới
    SET new_id = CONCAT('A', LPAD(last_id + 1, 5, '0'));
    SET NEW.admin_id = new_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `id` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `release_date` date NOT NULL,
  `so_luong` int DEFAULT '0',
  `tomtat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Day` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `theloai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `image_path`, `release_date`, `so_luong`, `tomtat`, `Day`, `theloai`, `publisher`) VALUES
('vrm371', 'kim', 'kim đong', 'sachvuaramat/7-ball.jpg', '2024-09-23', 7, ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'B', 'Truyện cổ tích', 'kim đong');

--
-- Triggers `books`
--
DROP TRIGGER IF EXISTS `before_insert_books`;
DELIMITER $$
CREATE TRIGGER `before_insert_books` BEFORE INSERT ON `books` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'vrm';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('sachvuaramat/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `danhgia`
--

DROP TABLE IF EXISTS `danhgia`;
CREATE TABLE IF NOT EXISTS `danhgia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rating` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `book_id` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_book` (`book_id`),
  KEY `fk_user` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danhgia`
--

INSERT INTO `danhgia` (`id`, `rating`, `comment`, `created_at`, `book_id`, `user_id`) VALUES
(8, '3', 'Sách này hay tuyệt vời', '2024-09-18 19:49:34', 'CNK465', '116675'),
(9, '1', 'sách quá dở luôn, đừng mượn', '2024-09-26 20:53:28', 'XNY130', '158868'),
(10, '1', 'fsdfsdf', '2024-09-06 02:35:01', 'XNY130', '116675'),
(11, '1', 'Sách quá thuyệt vời', '2024-09-06 02:37:48', 'XNY130', '116675'),
(12, '1', 'Sách đọc cũng được', '2024-09-06 02:42:20', 'WMW797', '116675'),
(13, '1', 'dsf', '2024-09-06 02:42:37', 'WMW797', '116675'),
(14, '2', 'haha', '2024-09-06 02:44:07', 'WMW797', '116675'),
(15, '3', 'fsdfdsf', '2024-09-06 02:46:15', 'ZPC560', '116675'),
(16, '3', 'ds', '2024-09-06 02:48:41', 'ZPC560', '116675'),
(17, '5', 'sdfds', '2024-09-06 02:50:02', 'ZPC560', '116675'),
(18, '1', 'ddsf', '2024-09-06 03:02:03', 'ZPC560', '116675'),
(19, '4', 'dfs', '2024-09-06 03:02:14', 'ZPC560', '116675'),
(20, '1', 'dsf', '2024-09-06 03:05:26', 'WMW797', '116675'),
(21, '3', 'fsdf', '2024-09-06 03:20:42', 'WMW797', '116675'),
(22, '5', 'Sách cũng hay', '2024-09-06 03:21:23', 'WMW797', '584276'),
(23, '3', 'fsdfds999', '2024-09-06 03:58:19', 'WMW797', '116675'),
(24, '4', 'hahahaha', '2024-09-06 04:03:09', 'WMW797', '116675'),
(25, '3', 'hihi haha', '2024-09-06 04:08:39', 'MSD586', '116675'),
(26, '2', 'Truyện cũng ok!', '2024-09-06 04:09:09', 'AHL291', '116675'),
(27, '5', 'Truyện Doraemon này quá hay luôn', '2024-09-06 04:13:55', 'tht309', '116675'),
(28, '3', 'Hay quá đi', '2024-09-06 04:16:13', 'tht309', '158868'),
(29, '3', 'Thủ thư cho em xin \\nđặt trước sách này!', '2024-09-06 04:18:47', 'tht309', '158868'),
(30, '5', 'Cảm ơn đã cho em mượn sách', '2024-09-06 04:21:51', 'DEN051', '158868'),
(31, '2', 'Ghê quá!!!', '2024-09-06 04:25:33', '1', '158868'),
(32, '5', 'Ghê quá!!!', '2024-09-06 04:26:37', '', '158868'),
(33, '3', 'Cũng tạm cho 3 sao nha!', '2024-09-06 04:30:41', 'stt748', '158868'),
(34, '3', 'Tạm', '2024-09-06 04:35:59', 'scn044', '158868'),
(35, '3', 'Cũng hay:)', '2024-09-06 04:36:44', 'scn302', '584276'),
(36, '3', 'Hay quá@@@', '2024-09-06 04:41:37', 'tch171', '584276'),
(37, '5', 'Qúa tuyệt vời', '2024-09-06 04:44:33', 'sxh833', '584276'),
(38, '4', 'Đọc tạm????', '2024-09-06 04:47:10', 'pba892', '584276'),
(39, '3', 'huhu sách hay quá huhu', '2024-09-06 04:48:09', 'WMW797', '584276'),
(40, '3', 'Qúa tuyệt vời', '2024-09-06 05:22:46', 'QKB025', '887448'),
(41, '4', 'Okok', '2024-09-06 06:06:21', 'CNK465', '887448'),
(42, '3', 'haya qua', '2024-09-07 03:10:38', 'pba654', '158868'),
(43, '5', 'học là hay', '2024-09-07 03:14:31', 'scn274', '158868'),
(44, '4', 'sach hay', '2024-09-08 02:34:23', 'spb368', '158868');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `image_path`) VALUES
(1, '01.jpg'),
(2, '02.jpg'),
(3, '03.jpg'),
(4, '04.jpg'),
(5, '01.jpg'),
(8, '02.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `position` int DEFAULT '0',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `title`, `parent_id`, `position`, `url`) VALUES
(1, 'Trang Chủ', NULL, 1, 'index.php'),
(2, 'Phân Loại', NULL, 2, NULL),
(3, 'Khu Truyện', 2, 0, NULL),
(4, 'Khu Sách', 2, 1, NULL),
(5, 'Truyện Tranh', 3, 0, 'truyentranh.php\r\n'),
(6, 'Cổ Tích', 3, 1, 'truyencotich.php\r\n'),
(7, 'kinh Dị', 3, 2, 'truyenkinhdi.php'),
(8, 'Tiểu Thuyết', 3, 3, 'tieuthuyet.php'),
(9, 'Chuyên Ngành', 4, 0, 'sachchuyennganh.php'),
(10, 'Truyền Cảm Hứng', 4, 1, 'sachtruyencamhung.php'),
(11, 'Văn Hóa- Xã Hội ', 4, 2, 'sachvanhoaxahoi.php'),
(12, ' Khám Phá Bí Ẩn', 4, 3, 'sachkhamphabian.php'),
(13, 'Sách Đã Mượn', NULL, 3, 'sach_da_muon.php'),
(14, 'Thông Tin Cá Nhân', NULL, 4, NULL),
(15, 'Xem Thông Tin', 14, 0, 'profile.php'),
(16, 'Đăng Xuất', 14, 1, 'logout.php');

-- --------------------------------------------------------

--
-- Table structure for table `menus_admin`
--

DROP TABLE IF EXISTS `menus_admin`;
CREATE TABLE IF NOT EXISTS `menus_admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `position` int DEFAULT '0',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus_admin`
--

INSERT INTO `menus_admin` (`id`, `title`, `parent_id`, `position`, `url`) VALUES
(1, 'Trang Chủ', NULL, 1, 'home_admin.php'),
(2, 'Phân Loại', NULL, 2, NULL),
(3, 'Khu Truyện', 2, 0, NULL),
(4, 'Khu Sách', 2, 1, NULL),
(5, 'Truyện Tranh', 3, 0, 'truyentranh/dulieutrangchu-truyentranh.php\r\n'),
(6, 'Cổ Tích', 3, 1, 'truyencotich/dulieutrangchu-truyencotich.php\r\n'),
(7, 'kinh Dị', 3, 2, 'truyenkinhdi/dulieutrangchu-truyenkinhdi.php'),
(8, 'Tiểu Thuyết', 3, 3, 'tieuthuyet/dulieutrangchu-tieuthuyet.php'),
(9, 'Chuyên Ngành', 4, 0, 'chuyennganh/dulieutrangchu-sachchuyennganh.php'),
(10, 'Truyền Cảm Hứng', 4, 1, 'truyencamhung/dulieutrangchu-sachtruyencamhung.php'),
(11, 'Văn Hóa- Xã Hội ', 4, 2, 'vanhoaxahoi/dulieutrangchu-sachvanhoaxahoi.php'),
(12, ' Khám Phá Bí Ẩn', 4, 3, 'khamphabian/dulieutrangchu-sachkhamphabian.php'),
(13, 'Sách Đã Mượn', NULL, 3, 'quan_ly_muon_sach.php'),
(14, 'Thông Tin Cá Nhân', NULL, 7, NULL),
(15, 'Xem Thông Tin', 14, 0, 'profile.php'),
(16, 'Đăng Xuất', 14, 1, 'logout.php'),
(18, 'Quản lý người dùng', NULL, 4, 'danhsach_users.php'),
(19, 'Cập nhật sách', NULL, 5, NULL),
(20, 'Sách vừa ra mắt', 19, 0, 'sachvuaramat/dulieutrangchu-sachvuaramat.php'),
(21, 'Sách vừa phổ biến', 19, 1, 'sachphobien/dulieutrangchu-sachphobien.php'),
(22, 'Thống kê sách', NULL, 6, NULL),
(23, 'Số lượng đọc giả', 22, 0, 'thongkedocgia/thongkesldocgia_cot.php'),
(24, 'Số kê số lượng sách', 22, 1, 'thongkesach/thongkeslsach_cot.php');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_muon`
--

DROP TABLE IF EXISTS `phieu_muon`;
CREATE TABLE IF NOT EXISTS `phieu_muon` (
  `id_phieu` int NOT NULL AUTO_INCREMENT,
  `id` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ho_va_ten` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_dien_thoai` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_phieu`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `popular_books`
--

DROP TABLE IF EXISTS `popular_books`;
CREATE TABLE IF NOT EXISTS `popular_books` (
  `id` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `release_date` date NOT NULL,
  `so_luong` int DEFAULT '0',
  `tomtat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Day` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `theloai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `popular_books`
--

INSERT INTO `popular_books` (`id`, `title`, `author`, `image_path`, `release_date`, `so_luong`, `tomtat`, `Day`, `theloai`, `publisher`) VALUES
('spb368', 'kim', 'kim đong', 'sachphobien/cotich6.jpg', '2024-09-23', 17, ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'B', 'Truyện cổ tích', 'kim đong');

--
-- Triggers `popular_books`
--
DROP TRIGGER IF EXISTS `before_insert_popular_books`;
DELIMITER $$
CREATE TRIGGER `before_insert_popular_books` BEFORE INSERT ON `popular_books` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'spb';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('sachphobien/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reset_pass`
--

DROP TABLE IF EXISTS `reset_pass`;
CREATE TABLE IF NOT EXISTS `reset_pass` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reset_pass`
--

INSERT INTO `reset_pass` (`id`, `user_id`, `email`, `verification_code`, `created_at`) VALUES
(7, '887448', 'khanhan2k4@gmail.com', '234792', '2024-09-06 16:36:15');

-- --------------------------------------------------------

--
-- Table structure for table `sachchuyennganh`
--

DROP TABLE IF EXISTS `sachchuyennganh`;
CREATE TABLE IF NOT EXISTS `sachchuyennganh` (
  `id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tomtat` mediumtext COLLATE utf8mb4_unicode_ci,
  `theloai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `Day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sachchuyennganh`
--

INSERT INTO `sachchuyennganh` (`id`, `title`, `author`, `image_path`, `tomtat`, `theloai`, `publisher`, `release_date`, `Day`, `so_luong`) VALUES
('scn306', 'kim', 'kim đong', 'sachchuyennganh/anh2.jpg', ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'Chuyên ngành', 'kim đong', '2024-09-07', 'B', 20);

--
-- Triggers `sachchuyennganh`
--
DROP TRIGGER IF EXISTS `before_insert_sachchuyennganh`;
DELIMITER $$
CREATE TRIGGER `before_insert_sachchuyennganh` BEFORE INSERT ON `sachchuyennganh` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'scn';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('sachchuyennganh/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sachkhamphabian`
--

DROP TABLE IF EXISTS `sachkhamphabian`;
CREATE TABLE IF NOT EXISTS `sachkhamphabian` (
  `id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tomtat` mediumtext COLLATE utf8mb4_unicode_ci,
  `theloai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `Day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sachkhamphabian`
--

INSERT INTO `sachkhamphabian` (`id`, `title`, `author`, `image_path`, `tomtat`, `theloai`, `publisher`, `release_date`, `Day`, `so_luong`) VALUES
('khp831', 'kim', 'kim đong', 'sachkhamphabian/kpba1.jpg', ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'Khám phá bí ẩn', 'kim đong', '2024-09-07', 'B', 19);

--
-- Triggers `sachkhamphabian`
--
DROP TRIGGER IF EXISTS `before_insert_sachkhamphabian`;
DELIMITER $$
CREATE TRIGGER `before_insert_sachkhamphabian` BEFORE INSERT ON `sachkhamphabian` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'khp';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('sachkhamphabian/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sachtruyencamhung`
--

DROP TABLE IF EXISTS `sachtruyencamhung`;
CREATE TABLE IF NOT EXISTS `sachtruyencamhung` (
  `id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tomtat` mediumtext COLLATE utf8mb4_unicode_ci,
  `theloai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `Day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sachtruyencamhung`
--

INSERT INTO `sachtruyencamhung` (`id`, `title`, `author`, `image_path`, `tomtat`, `theloai`, `publisher`, `release_date`, `Day`, `so_luong`) VALUES
('tch424', 'khong gục ngã', 'kim đong', 'sachtruyencamhung/stch1.jpg', ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'Truyền cảm hứng', 'kim đong', '2024-09-07', 'A', 9),
('tch232', 'kim', 'kim đong', 'sachtruyencamhung/stch1.jpg', ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'Truyền cảm hứng', 'kim đong', '2024-09-07', 'B', 10);

--
-- Triggers `sachtruyencamhung`
--
DROP TRIGGER IF EXISTS `before_insert_sachtruyencamhung`;
DELIMITER $$
CREATE TRIGGER `before_insert_sachtruyencamhung` BEFORE INSERT ON `sachtruyencamhung` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'tch';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('sachtruyencamhung/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sachvanhoaxahoi`
--

DROP TABLE IF EXISTS `sachvanhoaxahoi`;
CREATE TABLE IF NOT EXISTS `sachvanhoaxahoi` (
  `id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tomtat` mediumtext COLLATE utf8mb4_unicode_ci,
  `theloai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `Day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sachvanhoaxahoi`
--

INSERT INTO `sachvanhoaxahoi` (`id`, `title`, `author`, `image_path`, `tomtat`, `theloai`, `publisher`, `release_date`, `Day`, `so_luong`) VALUES
('vhx357', 'kim', 'kim đong', 'sachvanhoaxahoi/vhxh1.jpg', ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'Truyện tranh', 'kim đong', '2024-09-14', 'B', 20);

--
-- Triggers `sachvanhoaxahoi`
--
DROP TRIGGER IF EXISTS `before_insert_sachvanhoaxahoi`;
DELIMITER $$
CREATE TRIGGER `before_insert_sachvanhoaxahoi` BEFORE INSERT ON `sachvanhoaxahoi` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'vhx';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('sachvanhoaxahoi/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sach_da_muon`
--

DROP TABLE IF EXISTS `sach_da_muon`;
CREATE TABLE IF NOT EXISTS `sach_da_muon` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ten_sach` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_muon` date NOT NULL,
  `ngay_tra` date DEFAULT NULL,
  `ten_nguoi_muon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `sach_da_muon`
--
DROP TRIGGER IF EXISTS `set_ngay_tra`;
DELIMITER $$
CREATE TRIGGER `set_ngay_tra` BEFORE INSERT ON `sach_da_muon` FOR EACH ROW BEGIN
    -- Tính toán ngày trả sách cách ngày mượn 15 ngày
    SET NEW.ngay_tra = DATE_ADD(NEW.ngay_muon, INTERVAL 15 DAY);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sach_da_tra`
--

DROP TABLE IF EXISTS `sach_da_tra`;
CREATE TABLE IF NOT EXISTS `sach_da_tra` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `book_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thong_bao` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_tra` datetime NOT NULL,
  `admin_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sach_da_tra`
--

INSERT INTO `sach_da_tra` (`id`, `user_id`, `book_id`, `thong_bao`, `ngay_tra`, `admin_name`, `read_status`) VALUES
(46, '158868', 'spb368', 'kim đã trả thành công', '2024-09-08 20:02:21', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `the_loai`
--

DROP TABLE IF EXISTS `the_loai`;
CREATE TABLE IF NOT EXISTS `the_loai` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten_the_loai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `the_loai`
--

INSERT INTO `the_loai` (`id`, `ten_the_loai`) VALUES
(1, 'Truyện tranh'),
(2, 'Truyện cổ tích'),
(3, 'Truyện kinh dị'),
(4, 'Tiểu thuyết'),
(5, 'Chuyên ngành'),
(6, 'Truyền cảm hứng'),
(7, 'Văn hóa - Xã hội'),
(8, 'Khám phá bí ẩn');

-- --------------------------------------------------------

--
-- Table structure for table `tieuthuyet`
--

DROP TABLE IF EXISTS `tieuthuyet`;
CREATE TABLE IF NOT EXISTS `tieuthuyet` (
  `id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tomtat` mediumtext COLLATE utf8mb4_unicode_ci,
  `theloai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `Day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tieuthuyet`
--

INSERT INTO `tieuthuyet` (`id`, `title`, `author`, `image_path`, `tomtat`, `theloai`, `publisher`, `release_date`, `Day`, `so_luong`) VALUES
('tth746', 'ki', 'kim đong', 'tieuthuyet/tieuthuyet1.jpg', ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'Tiểu thuyết', 'kim đong', '2024-09-07', 'B', 90);

--
-- Triggers `tieuthuyet`
--
DROP TRIGGER IF EXISTS `before_insert_tieuthuyet`;
DELIMITER $$
CREATE TRIGGER `before_insert_tieuthuyet` BEFORE INSERT ON `tieuthuyet` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'tth';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('tieuthuyet/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `truyencotich`
--

DROP TABLE IF EXISTS `truyencotich`;
CREATE TABLE IF NOT EXISTS `truyencotich` (
  `id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tomtat` mediumtext COLLATE utf8mb4_unicode_ci,
  `theloai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `Day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `truyencotich`
--

INSERT INTO `truyencotich` (`id`, `title`, `author`, `image_path`, `tomtat`, `theloai`, `publisher`, `release_date`, `Day`, `so_luong`) VALUES
('tct291', 'kim', 'kim đong', 'truyencotich/cotich2.jpg', ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'Truyện cổ tích', 'kim đong', '2024-09-23', 'B', 20);

--
-- Triggers `truyencotich`
--
DROP TRIGGER IF EXISTS `before_insert_truyencotich`;
DELIMITER $$
CREATE TRIGGER `before_insert_truyencotich` BEFORE INSERT ON `truyencotich` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'tct';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('truyencotich/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `truyenkinhdi`
--

DROP TABLE IF EXISTS `truyenkinhdi`;
CREATE TABLE IF NOT EXISTS `truyenkinhdi` (
  `id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tomtat` mediumtext COLLATE utf8mb4_unicode_ci,
  `theloai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `Day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `truyenkinhdi`
--

INSERT INTO `truyenkinhdi` (`id`, `title`, `author`, `image_path`, `tomtat`, `theloai`, `publisher`, `release_date`, `Day`, `so_luong`) VALUES
('kdi979', 'kin', 'kim đong', 'truyenkinhdi/kinhdi6.jpg', ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'Truyện kinh dị', 'kim đong', '2024-09-16', 'A', 19);

--
-- Triggers `truyenkinhdi`
--
DROP TRIGGER IF EXISTS `before_insert_truyenkinhdi`;
DELIMITER $$
CREATE TRIGGER `before_insert_truyenkinhdi` BEFORE INSERT ON `truyenkinhdi` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'kdi';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('truyenkinhdi/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `truyentranh`
--

DROP TABLE IF EXISTS `truyentranh`;
CREATE TABLE IF NOT EXISTS `truyentranh` (
  `id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tomtat` mediumtext COLLATE utf8mb4_unicode_ci,
  `theloai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `Day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_luong` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `truyentranh`
--

INSERT INTO `truyentranh` (`id`, `title`, `author`, `image_path`, `tomtat`, `theloai`, `publisher`, `release_date`, `Day`, `so_luong`) VALUES
('ttr959', 'doraemon', 'kim đong', 'truyentranh/conan95.jpg', ' nhiều điều kì lại xung quanh ta chưa được giải mã', 'Truyện tranh', 'kim đong', '2024-09-09', 'B', 8);

--
-- Triggers `truyentranh`
--
DROP TRIGGER IF EXISTS `before_insert_truyentranh`;
DELIMITER $$
CREATE TRIGGER `before_insert_truyentranh` BEFORE INSERT ON `truyentranh` FOR EACH ROW BEGIN
    -- Tạo mã ID mới nếu chưa có giá trị
    DECLARE new_id VARCHAR(6);
    DECLARE letter_part CHAR(3) DEFAULT 'ttr';
    DECLARE number_part CHAR(3);
    
    -- Sinh phần số ngẫu nhiên
    SET number_part = LPAD(FLOOR(RAND() * 1000), 3, '0');

    -- Tạo mã ID với 3 chữ cái "cnt" + 3 số ngẫu nhiên
    SET new_id = CONCAT(letter_part, number_part);

    -- Gán giá trị cho cột id nếu chưa có giá trị
    IF NEW.id IS NULL OR TRIM(NEW.id) = '' THEN
        SET NEW.id = new_id;
    END IF;

    -- Đảm bảo giá trị cho cột image_path
    IF NEW.image_path IS NOT NULL AND TRIM(NEW.image_path) != '' THEN
        SET NEW.image_path = CONCAT('truyentranh/', NEW.image_path);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png',
  `so_dien_thoai` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `ThongBaoResetMK` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `avatar`, `so_dien_thoai`, `ThongBaoResetMK`) VALUES
('116675', 'anvo123', '123456', 'Võ', 'Khánh', 'khanhan2k00@gmail.com', '1.png', '0559021390', NULL),
('158868', 'buian11', '12345@', 'Bùi', 'An', 'anbui@gmail.com', 'default.png', '0978645123', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
