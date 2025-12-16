<?php
// Vị trí: controller/TaikhoanController.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$root_dir = dirname(dirname(__FILE__));

require_once $root_dir . '/config/connect.php';
require_once $root_dir . '/classes/Taikhoan.class.php';

// 2. Khởi tạo CSDL và Model
$database = new Database();
$db = $database->getConnection();
$taikhoan = new Taikhoan($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {



    // LOGIC XỬ LÝ THÊM TÀI KHOẢN (CREATE)

    if (isset($_POST['action']) && $_POST['action'] == 'add_taikhoan') {

        // Gán dữ liệu từ form
        $taikhoan->ten_dangnhap = $_POST['ten_dangnhap'];
        $taikhoan->mat_khau     = $_POST['mat_khau'];
        $taikhoan->vai_tro      = $_POST['vai_tro'];
        $taikhoan->trang_thai   = $_POST['trang_thai'];

        if ($taikhoan->create()) {
            $_SESSION['message'] = "Thêm tài khoản thành công! ID: " . $taikhoan->taikhoan_id;
        } else {
            $_SESSION['error'] = "Lỗi: Không thể thêm tài khoản. (Kiểm tra trùng Tên đăng nhập)";
        }

        header("Location: ../View/Taikhoans/index.php");
        exit();
    }


    // LOGIC XỬ LÝ CẬP NHẬT TÀI KHOẢN (UPDATE)

    if (isset($_POST['action']) && $_POST['action'] == 'update_taikhoan') {

        $taikhoan->taikhoan_id = $_POST['taikhoan_id']; // ID cần cập nhật
        $taikhoan->ten_dangnhap = $_POST['ten_dangnhap'];
        $taikhoan->mat_khau     = $_POST['mat_khau']; // Có thể rỗng nếu không đổi
        $taikhoan->vai_tro      = $_POST['vai_tro'];
        $taikhoan->trang_thai   = $_POST['trang_thai'];

        if ($taikhoan->update()) {
            $_SESSION['message'] = "Cập nhật tài khoản thành công!";
        } else {
            $_SESSION['error'] = "Lỗi: Không thể cập nhật tài khoản.";
        }

        header("Location: ../View/Taikhoans/index.php");
        exit();
    }


    // LOGIC XỬ LÝ XÓA TÀI KHOẢN (DELETE)

    if (isset($_POST['action']) && $_POST['action'] == 'delete_taikhoan') {

        if (isset($_POST['taikhoan_id'])) {
            $taikhoan->taikhoan_id = $_POST['taikhoan_id'];

            // LƯU Ý: Phải đảm bảo Khách hàng/Nhân viên liên kết đã được xử lý (CASCADE)
            if ($taikhoan->delete()) {
                $_SESSION['message'] = "Xóa tài khoản thành công!";
            } else {
                $_SESSION['error'] = "Lỗi: Không thể xóa tài khoản. (Kiểm tra ràng buộc khóa ngoại)";
            }
        }
        header("Location: ../View/Taikhoans/index.php");
        exit();
    }
    // LOGIC XỬ LÝ ĐĂNG NHẬP TÀI KHOẢN
    if (isset($_POST['action']) && $_POST['action'] == 'login') {

        // 1. Gán dữ liệu từ form đăng nhập


        $taikhoan->ten_dangnhap = $_POST['username'];
        $taikhoan->mat_khau     = $_POST['password'];



        if ($taikhoan->login()) {



            if ($taikhoan->trang_thai == 1) { // Kiểm tra tài khoản có hoạt động không

                // 2. Đăng nhập thành công: Thiết lập SESSION
                $_SESSION['taikhoan_id']  = $taikhoan->taikhoan_id;
                $_SESSION['ten_dangnhap'] = $taikhoan->ten_dangnhap;
                $_SESSION['vai_tro']      = $taikhoan->vai_tro;
                $_SESSION['logged_in']    = true;

                // 3. Chuyển hướng người dùng về trang chủ
                $_SESSION['message'] = "Đăng nhập thành công!";
                header("Location: ../index.php"); // Quay về trang chủ (hoặc trang dashboard)
                exit();
            } else {


                $_SESSION['error'] = "Tài khoản của bạn đã bị khóa.";

                header("Location: ../View/account/login.php");
                exit();
            }
        } else {

            //debug

            $_SESSION['error'] = "Tên đăng hoặc mật khẩu không chính xác.";
            header("Location: ../View/account/login.php");
            exit();
        }
    }
    // ======================================================
    // LOGIC XỬ LÝ ĐĂNG KÝ TÀI KHOẢN MỚI (REGISTER)
    // ======================================================
    if (isset($_POST['action']) && $_POST['action'] == 'register') {

        $ten_dangnhap = $_POST['ten_dangnhap'];
        $mat_khau = $_POST['mat_khau'];
        $confirm_mat_khau = $_POST['confirm_mat_khau'];

        // 1. KIỂM TRA DỮ LIỆU ĐẦU VÀO
        if (empty($ten_dangnhap) || empty($mat_khau) || empty($confirm_mat_khau)) {
            $_SESSION['error'] = "Vui lòng điền đầy đủ các trường.";
            header("Location: ../View/account/register.php");
            exit();
        }

        // 2. KIỂM TRA KHỚP MẬT KHẨU
        if ($mat_khau !== $confirm_mat_khau) {
            $_SESSION['error'] = "Mật khẩu và Nhập lại mật khẩu không khớp.";
            header("Location: ../View/account/register.php");
            exit();
        }

        // 3. GÁN DỮ LIỆU VÀ GỌI CREATE
        $taikhoan->ten_dangnhap = $ten_dangnhap;
        $taikhoan->mat_khau = $mat_khau; // Mật khẩu sẽ được mã hóa trong model
        $taikhoan->vai_tro = 'khachhang'; // Mặc định là khách hàng
        $taikhoan->trang_thai = 1; // Mặc định là hoạt động

        if ($taikhoan->create()) {
            $_SESSION['message'] = "Đăng ký thành công! Vui lòng đăng nhập.";
            // Chuyển hướng đến trang Đăng nhập sau khi đăng ký thành công
            header("Location: ../View/account/login.php");
            exit();
        } else {
            // Lỗi có thể do trùng Tên đăng nhập (Unique Constraint)
            $_SESSION['error'] = "Đăng ký thất bại. Tên đăng nhập (Email) có thể đã tồn tại.";
            header("Location: ../View/account/register.php");
            exit();
        }
    }
}
// ======================================================
// LOGIC XỬ LÝ ĐĂNG XUẤT (LOGOUT) - Dùng GET request
// ======================================================
if (isset($_GET['action']) && $_GET['action'] == 'logout') {


    // 1. Xóa tất cả các biến session
    $_SESSION = array();

    // 2. Hủy session (Loại bỏ tệp session trên server)
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    // 3. Chuyển hướng người dùng về trang chủ
    header("Location: ../index.php");
    exit();
}
