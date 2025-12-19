-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 14, 2025 at 06:05 AM
-- Server version: 9.2.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_dothethao`
--

-- --------------------------------------------------------

--
-- Table structure for table `chitiet_donhang`
--

DROP TABLE IF EXISTS `chitiet_donhang`;
CREATE TABLE IF NOT EXISTS `chitiet_donhang` (
  `chitiet_id` int NOT NULL AUTO_INCREMENT,
  `donhang_id` int NOT NULL,
  `sanpham_id` int NOT NULL,
  `so_luong` int NOT NULL,
  `gia_ban` decimal(10,2) NOT NULL COMMENT 'Gia ban tai thoi diem dat hang',
  PRIMARY KEY (`chitiet_id`),
  UNIQUE KEY `unique_order_product` (`donhang_id`,`sanpham_id`),
  KEY `sanpham_id` (`sanpham_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chitiet_donhang`
--

INSERT INTO `chitiet_donhang` (`chitiet_id`, `donhang_id`, `sanpham_id`, `so_luong`, `gia_ban`) VALUES
(1, 1, 1, 1, 2500000.00),
(2, 2, 2, 1, 3200000.00),
(3, 3, 3, 1, 490000.00),
(4, 3, 4, 1, 300000.00),
(5, 4, 4, 2, 750000.00),
(6, 5, 5, 1, 890000.00),
(7, 6, 6, 1, 350000.00),
(8, 7, 7, 2, 300000.00),
(9, 8, 8, 1, 550000.00),
(10, 9, 9, 1, 1800000.00);

-- --------------------------------------------------------

--
-- Table structure for table `danhgia`
--

DROP TABLE IF EXISTS `danhgia`;
CREATE TABLE IF NOT EXISTS `danhgia` (
  `danhgia_id` int NOT NULL AUTO_INCREMENT,
  `sanpham_id` int NOT NULL,
  `khachhang_id` int NOT NULL,
  `diem_sao` tinyint NOT NULL COMMENT 'Tu 1 den 5 sao',
  `noi_dung` text COLLATE utf8mb4_unicode_ci,
  `ngay_danh_gia` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`danhgia_id`),
  KEY `sanpham_id` (`sanpham_id`),
  KEY `khachhang_id` (`khachhang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danhgia`
--

INSERT INTO `danhgia` (`danhgia_id`, `sanpham_id`, `khachhang_id`, `diem_sao`, `noi_dung`, `ngay_danh_gia`) VALUES
(1, 1, 1, 5, 'Giay Nike rat em chan, dang tien.', '2025-12-01 14:38:17'),
(2, 2, 2, 4, 'Da san co nhan tao rat bam, nhung mau hoi toi.', '2025-12-01 14:38:17'),
(3, 3, 3, 5, 'Ao tham hut mo hoi tot, mac di tap thoai mai.', '2025-12-01 14:38:17'),
(4, 4, 4, 3, 'Quan hoi ngan so voi mau, nhung chat lieu tot.', '2025-12-01 14:38:17'),
(5, 5, 5, 5, 'Tham yoga chong truot tuyet voi, day dan.', '2025-12-01 14:38:17'),
(6, 6, 6, 4, 'Day khang luc ben, nhung ta hoi nhe.', '2025-12-01 14:38:17'),
(7, 7, 7, 5, 'Kinh boi khong bi mo, tam nhin rong.', '2025-12-01 14:38:17'),
(8, 8, 8, 5, 'Bong ro cam rat chac tay, dang mua.', '2025-12-01 14:38:17'),
(9, 9, 9, 4, 'Vot cau long tro luc tot, danh nhe nhang.', '2025-12-01 14:38:17'),
(10, 10, 10, 5, 'Bao ho goi ho tro tot khi tap squat.', '2025-12-01 14:38:17');

-- --------------------------------------------------------

--
-- Table structure for table `donhang`
--

DROP TABLE IF EXISTS `donhang`;
CREATE TABLE IF NOT EXISTS `donhang` (
  `donhang_id` int NOT NULL AUTO_INCREMENT,
  `khachhang_id` int NOT NULL,
  `ngay_dat` datetime DEFAULT CURRENT_TIMESTAMP,
  `tong_tien` decimal(10,2) NOT NULL,
  `trang_thai` tinyint NOT NULL COMMENT '1: Moi, 2: Dang xu ly, 3: Da giao, 4: Hoan tat, 5: Huy',
  `dia_chi_giao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`donhang_id`),
  KEY `khachhang_id` (`khachhang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donhang`
--

INSERT INTO `donhang` (`donhang_id`, `khachhang_id`, `ngay_dat`, `tong_tien`, `trang_thai`, `dia_chi_giao`) VALUES
(1, 1, '2025-12-01 14:38:17', 2500000.00, 4, '10 Vo Van Ngan, TP. Thu Duc'),
(2, 2, '2025-12-01 14:38:17', 3200000.00, 2, '22 Nguyen Hue, Quan 1'),
(3, 3, '2025-11-26 14:38:17', 790000.00, 4, '33 Le Loi, Quan Go Vap'),
(4, 4, '2025-11-27 14:38:17', 1500000.00, 1, '44 Tran Hung Dao, Quan 5'),
(5, 5, '2025-11-28 14:38:17', 890000.00, 3, '55 Hai Ba Trung, Quan 3'),
(6, 6, '2025-11-29 14:38:17', 350000.00, 5, '66 Phan Dang Luu, Quan Binh Thanh'),
(7, 7, '2025-11-30 14:38:17', 600000.00, 2, '77 Cong Hoa, Quan Tan Binh'),
(8, 8, '2025-12-01 14:38:17', 550000.00, 1, '88 Au Co, Quan 11'),
(9, 9, '2025-12-01 14:38:17', 1800000.00, 2, '99 Ly Thai To, Quan 10'),
(10, 10, '2025-12-01 14:38:17', 400000.00, 1, '100 Nguyen Trai, Quan 5');

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

DROP TABLE IF EXISTS `khachhang`;
CREATE TABLE IF NOT EXISTS `khachhang` (
  `khachhang_id` int NOT NULL AUTO_INCREMENT,
  `taikhoan_id` int NOT NULL,
  `ho_ten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dien_thoai` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`khachhang_id`),
  UNIQUE KEY `taikhoan_id` (`taikhoan_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`khachhang_id`, `taikhoan_id`, `ho_ten`, `email`, `dien_thoai`, `dia_chi`) VALUES
(1, 11, 'Nguyễn Vân An', 'an@gmail.com', '0901000001', '10 Võ Văn Ngân, TP. Thủ Đức'),
(2, 12, 'Tran Thi Binh', 'binh@gmail.com', '0902000002', '22 Nguyen Hue, Quan 1'),
(3, 13, 'Le Van Cuong', 'cuong@gmail.com', '0903000003', '33 Le Loi, Quan Go Vap'),
(4, 14, 'Pham Thi Dung', 'dung@gmail.com', '0904000004', '44 Tran Hung Dao, Quan 5'),
(5, 15, 'Hoang Minh Hieu', 'hieu@gmail.com', '0905000005', '55 Hai Ba Trung, Quan 3'),
(6, 16, 'Do Trong Khoa', 'khoa@gmail.com', '0906000006', '66 Phan Dang Luu, Quan Binh Thanh'),
(7, 17, 'Vu Duc Minh', 'minh@gmail.com', '0907000007', '77 Cong Hoa, Quan Tan Binh'),
(8, 18, 'Ly Tan Nhan', 'nhan@gmail.com', '0908000008', '88 Au Co, Quan 11'),
(9, 19, 'Hoang Van Hung', 'hung@gmail.com', '0908000009', '99 Ly Thai To, Quan 10'),
(10, 20, 'Nguyen Duc Phong', 'phong@gmail.com', '0908000010', '100 Nguyen Trai, Quan 5'),
(11, 10, 'teo', 'teo@gmail.com', '123', '123'),
(16, 21, 'a', 'a@yahoo.com', '11111', '1');

-- --------------------------------------------------------

--
-- Table structure for table `loaisanpham`
--

DROP TABLE IF EXISTS `loaisanpham`;
CREATE TABLE IF NOT EXISTS `loaisanpham` (
  `loai_id` int NOT NULL AUTO_INCREMENT,
  `ten_loai` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`loai_id`),
  UNIQUE KEY `ten_loai` (`ten_loai`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loaisanpham`
--

INSERT INTO `loaisanpham` (`loai_id`, `ten_loai`) VALUES
(3, 'Ao thun the thao'),
(8, 'Bong ro'),
(5, 'Dung cu yoga'),
(2, 'Giay bong da'),
(1, 'Giay chay bo'),
(10, 'Phu kien bao ho'),
(7, 'Phu kien boi loi'),
(4, 'Quan short'),
(6, 'Ta va day khang luc'),
(9, 'Vot cau long');

-- --------------------------------------------------------

--
-- Table structure for table `nhacungcap`
--

DROP TABLE IF EXISTS `nhacungcap`;
CREATE TABLE IF NOT EXISTS `nhacungcap` (
  `ncc_id` int NOT NULL AUTO_INCREMENT,
  `ten_ncc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dia_chi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dien_thoai` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ncc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nhacungcap`
--

INSERT INTO `nhacungcap` (`ncc_id`, `ten_ncc`, `dia_chi`, `dien_thoai`) VALUES
(1, 'NCC Nike Viet Nam', '45 Nguyen Hue, Q.1, TPHCM', '028111222'),
(2, 'NCC Adidas Miền Nam', '10 Phan Dang Luu, Binh Thanh, TPHCM', '028333444'),
(3, 'NCC Puma Ha Noi', '20 Tran Duy Hung, Ha Noi', '024555666'),
(4, 'NCC Đa năng A', '200 Cong Hoa, Tan Binh, TPHCM', '028777888'),
(5, 'NCC The Thao Quoc Te', '111 Le Loi, Q.3, TPHCM', '028999000'),
(6, 'NCC Dụng cụ Gym', '50 Dien Bien Phu, Q.1, TPHCM', '028123456'),
(7, 'NCC Bơi lội Hải Âu', '123 Truong Chinh, Tan Binh, TPHCM', '028789012'),
(8, 'NCC Bong Quoc Dan', '456 Su Van Hanh, Q.10, TPHCM', '028345678'),
(9, 'NCC Cau long VN', '789 Hoang Van Thu, Phu Nhuan, TPHCM', '028901234'),
(10, 'NCC Bao ho Dai Loan', '100 Cach Mang Thang 8, Q.3, TPHCM', '028567890');

-- --------------------------------------------------------

--
-- Table structure for table `nhanvien`
--

DROP TABLE IF EXISTS `nhanvien`;
CREATE TABLE IF NOT EXISTS `nhanvien` (
  `nhanvien_id` int NOT NULL AUTO_INCREMENT,
  `taikhoan_id` int NOT NULL,
  `ho_ten` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chuc_vu` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_vao_lam` date DEFAULT NULL,
  `sdt` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`nhanvien_id`),
  UNIQUE KEY `taikhoan_id` (`taikhoan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nhanvien`
--

INSERT INTO `nhanvien` (`nhanvien_id`, `taikhoan_id`, `ho_ten`, `chuc_vu`, `ngay_vao_lam`, `sdt`, `email`) VALUES
(1, 1, 'Truong Ngoc Dinh', 'Admin', '2023-01-15', '0981112223', 'truongdinh@store.com'),
(2, 2, 'Nguyen Thi Phuong', 'Nhan vien kho', '2024-05-20', '0982223334', 'phuongnv@store.com'),
(3, 3, 'Pham Van C', 'Sale', '2024-01-01', '0983334445', 'phamvanc@store.com'),
(4, 4, 'Dao Thi D', 'Marketing', '2024-02-01', '0984445556', 'daothid@store.com'),
(5, 5, 'Hoang Van E', 'Kiem kho', '2024-03-01', '0985556667', 'hoangvane@store.com'),
(6, 6, 'Le Van F', 'Giao hang', '2024-04-01', '0986667778', 'levanf@store.com'),
(7, 7, 'Tran Thi G', 'CSKH', '2024-05-01', '0987778889', 'tranthig@store.com'),
(8, 8, 'Vu Van H', 'Sale', '2024-06-01', '0988889990', 'vuvah@store.com'),
(9, 9, 'Nguyen Van I', 'Ke toan', '2024-07-01', '0989990001', 'nguyenvani@store.com'),
(10, 10, 'Dinh Thi K', 'Nhan su', '2024-08-01', '0990001112', 'dinhtk@store.com');

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

DROP TABLE IF EXISTS `sanpham`;
CREATE TABLE IF NOT EXISTS `sanpham` (
  `sanpham_id` int NOT NULL AUTO_INCREMENT,
  `loai_id` int NOT NULL,
  `ncc_id` int NOT NULL,
  `thuong_hieu` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ten_sanpham` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia` decimal(10,2) NOT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `so_luong_ton` int NOT NULL DEFAULT '0',
  `hinh_anh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`sanpham_id`),
  KEY `loai_id` (`loai_id`),
  KEY `ncc_id` (`ncc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`sanpham_id`, `loai_id`, `ncc_id`, `thuong_hieu`, `ten_sanpham`, `gia`, `mo_ta`, `so_luong_ton`, `hinh_anh`) VALUES
(1, 1, 1, 'Nike', 'Giay chay bo Pegasus 40', 2500000.00, 'Giay chay bo chuyen nghiep.', 40, 'img_1.jpg'),
(2, 2, 2, 'Adidas', 'Giay bong da Predator', 3200000.00, 'Giay bong da san co nhan tao.', 25, 'img_2.jpg'),
(3, 3, 3, 'Puma', 'Ao Thun DryCELL', 490000.00, 'Ao thun tham hut mo hoi.', 150, 'img_3.jpg'),
(4, 4, 4, 'Reebok', 'Quan Short Crossfit', 750000.00, 'Quan short tap luyen cuong do cao.', 80, 'img_4.jpg'),
(5, 5, 5, 'Under Armour', 'Tham Yoga Pro', 890000.00, 'Tham yoga chong truot tuyet voi.', 60, 'img_5.jpg'),
(6, 6, 6, 'Domyos', 'Bo Day Khang Luc', 350000.00, 'Bo 5 day khang luc tap gym.', 120, 'img_6.jpg'),
(7, 7, 7, 'Mizuno', 'Kinh Boi Chuyen Dung', 300000.00, 'Kinh boi chong suong mu.', 90, 'img_7.jpg'),
(8, 8, 8, 'Yonex', 'Bong Ro Tieu Chuan', 550000.00, 'Bong ro da PU tieu chuan.', 35, 'img_8.jpg'),
(9, 9, 9, 'Li-Ning', 'Vot Cau Long Carbon', 1800000.00, 'Vot cau long carbon nhe.', 20, 'img_9.jpg'),
(10, 10, 10, 'GymShark', 'Bao Ho Goi', 400000.00, 'Dai bao ve goi thoang khi.', 70, 'img_10.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan`
--

DROP TABLE IF EXISTS `taikhoan`;
CREATE TABLE IF NOT EXISTS `taikhoan` (
  `taikhoan_id` int NOT NULL AUTO_INCREMENT,
  `ten_dangnhap` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vai_tro` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'khachhang, nhanvien, admin',
  `trang_thai` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`taikhoan_id`),
  UNIQUE KEY `ten_dangnhap` (`ten_dangnhap`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taikhoan`
--

INSERT INTO `taikhoan` (`taikhoan_id`, `ten_dangnhap`, `mat_khau`, `vai_tro`, `trang_thai`) VALUES
(1, 'admin_dinh', '123456', 'admin', '1'),
(2, 'nv_phuong', '123456', 'nhanvien', 'active'),
(3, 'nv_c', '123456', 'nhanvien', 'active'),
(4, 'nv_d', '123456', 'nhanvien', 'active'),
(5, 'nv_e', '123456', 'nhanvien', 'active'),
(6, 'nv_f', '123456', 'nhanvien', 'active'),
(7, 'nv_g', '123456', 'nhanvien', 'active'),
(8, 'nv_h', '123456', 'nhanvien', 'active'),
(9, 'nv_i', '123456', 'nhanvien', 'active'),
(10, 'nv_k', '123456', 'nhanvien', 'active'),
(11, 'kh_anv', '123456', 'khachhang', '1'),
(12, 'kh_binh', '123456', 'khachhang', 'active'),
(13, 'kh_cuong', '123456', 'khachhang', 'active'),
(14, 'kh_dung', '123456', 'khachhang', 'active'),
(15, 'kh_hieu', '123456', 'khachhang', 'active'),
(16, 'kh_khoa', '123456', 'khachhang', 'active'),
(17, 'kh_minh', '123456', 'khachhang', 'active'),
(18, 'kh_nhan', '123456', 'khachhang', 'active'),
(19, 'kh_hung', '123456', 'khachhang', 'active'),
(20, 'kh_phong', '123456', 'khachhang', 'active'),
(21, 'a4', '0cc175b9c0f1b6a831c399e269772661', 'nhanvien', '1'),
(22, 'abcd@yahoo.com', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '0'),
(23, 'a', 'e10adc3949ba59abbe56e057f20f883e', 'khachhang', '1');

-- --------------------------------------------------------

--
-- Table structure for table `thanhtoan`
--

DROP TABLE IF EXISTS `thanhtoan`;
CREATE TABLE IF NOT EXISTS `thanhtoan` (
  `thanhtoan_id` int NOT NULL AUTO_INCREMENT,
  `donhang_id` int NOT NULL,
  `phuong_thuc_tt` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'COD, Chuyen khoan, VNPAY,...',
  `ma_gd` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `so_tien` decimal(10,2) NOT NULL,
  `trang_thai_tt` tinyint NOT NULL COMMENT '1: Thanh cong, 0: That bai, 2: Dang cho',
  `thoi_gian_tt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`thanhtoan_id`),
  UNIQUE KEY `donhang_id` (`donhang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thanhtoan`
--

INSERT INTO `thanhtoan` (`thanhtoan_id`, `donhang_id`, `phuong_thuc_tt`, `ma_gd`, `so_tien`, `trang_thai_tt`, `thoi_gian_tt`) VALUES
(1, 1, 'VNPAY', 'VNPAY1001', 2500000.00, 1, '2025-12-01 14:38:17'),
(2, 2, 'COD', NULL, 3200000.00, 2, '2025-12-01 14:38:17'),
(3, 3, 'COD', NULL, 790000.00, 1, '2025-12-01 14:38:17'),
(4, 4, 'Chuyen khoan', 'CK1004', 1500000.00, 2, '2025-12-01 14:38:17'),
(5, 5, 'VNPAY', 'VNPAY1005', 890000.00, 1, '2025-12-01 14:38:17'),
(6, 6, 'VNPAY', 'VNPAY1006', 350000.00, 0, '2025-12-01 14:38:17'),
(7, 7, 'COD', NULL, 600000.00, 2, '2025-12-01 14:38:17'),
(8, 8, 'Chuyen khoan', 'CK1008', 550000.00, 2, '2025-12-01 14:38:17'),
(9, 9, 'VNPAY', 'VNPAY1009', 1800000.00, 2, '2025-12-01 14:38:17'),
(10, 10, 'COD', NULL, 400000.00, 2, '2025-12-01 14:38:17');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_customer_list`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_customer_list`;
CREATE TABLE IF NOT EXISTS `vw_customer_list` (
`dia_chi` varchar(255)
,`dien_thoai` varchar(15)
,`email` varchar(100)
,`ho_ten` varchar(100)
,`khachhang_id` int
,`ten_dangnhap` varchar(50)
,`trang_thai` varchar(20)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_customer_list`
--
DROP TABLE IF EXISTS `vw_customer_list`;

DROP VIEW IF EXISTS `vw_customer_list`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_customer_list`  AS SELECT `kh`.`khachhang_id` AS `khachhang_id`, `kh`.`ho_ten` AS `ho_ten`, `kh`.`dien_thoai` AS `dien_thoai`, `kh`.`dia_chi` AS `dia_chi`, `kh`.`email` AS `email`, `tk`.`ten_dangnhap` AS `ten_dangnhap`, `tk`.`trang_thai` AS `trang_thai` FROM (`khachhang` `kh` join `taikhoan` `tk` on((`kh`.`taikhoan_id` = `tk`.`taikhoan_id`))) WHERE (`tk`.`vai_tro` = 'khachhang') ORDER BY `kh`.`khachhang_id` ASC ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chitiet_donhang`
--
ALTER TABLE `chitiet_donhang`
  ADD CONSTRAINT `chitiet_donhang_ibfk_1` FOREIGN KEY (`donhang_id`) REFERENCES `donhang` (`donhang_id`),
  ADD CONSTRAINT `chitiet_donhang_ibfk_2` FOREIGN KEY (`sanpham_id`) REFERENCES `sanpham` (`sanpham_id`);

--
-- Constraints for table `danhgia`
--
ALTER TABLE `danhgia`
  ADD CONSTRAINT `danhgia_ibfk_1` FOREIGN KEY (`sanpham_id`) REFERENCES `sanpham` (`sanpham_id`),
  ADD CONSTRAINT `danhgia_ibfk_2` FOREIGN KEY (`khachhang_id`) REFERENCES `khachhang` (`khachhang_id`);

--
-- Constraints for table `donhang`
--
ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`khachhang_id`) REFERENCES `khachhang` (`khachhang_id`);

--
-- Constraints for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD CONSTRAINT `khachhang_ibfk_1` FOREIGN KEY (`taikhoan_id`) REFERENCES `taikhoan` (`taikhoan_id`);

--
-- Constraints for table `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`taikhoan_id`) REFERENCES `taikhoan` (`taikhoan_id`);

--
-- Constraints for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`loai_id`) REFERENCES `loaisanpham` (`loai_id`),
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`ncc_id`) REFERENCES `nhacungcap` (`ncc_id`);

--
-- Constraints for table `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD CONSTRAINT `thanhtoan_ibfk_1` FOREIGN KEY (`donhang_id`) REFERENCES `donhang` (`donhang_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
