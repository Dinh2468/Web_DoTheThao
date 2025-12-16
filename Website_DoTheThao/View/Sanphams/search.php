<?php
// View/Sanphams/search.php  

require_once '../../config/connect.php';
require_once '../../classes/DB.class.php';
require_once '../../classes/Sanpham.class.php';

$database = new Database();
$db = $database->getConnection();
$sanphamModel = new Sanpham($db);

$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$sanphams = [];

if (!empty($search_query)) {

    $search_result = $sanphamModel->search($search_query);
    while ($row = $search_result->fetch_object()) {
        $sanphams[] = $row;
    }
}

require_once '../includes/header.php';
?>

<section class="search-results-section container">
    <div class="list-header">
        <h2 class="list-title">
            <?php if (!empty($search_query)): ?>
                Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($search_query); ?>"
            <?php else: ?>
                Vui lòng nhập từ khóa tìm kiếm.
            <?php endif; ?>
        </h2>
        <p class="result-count">
            <?php echo count($sanphams); ?> sản phẩm được tìm thấy.
        </p>
    </div>

    <div class="product-grid">

        <?php if (!empty($sanphams)): ?>
            <?php
            $image_path = BASE_URL . 'assets/images/sanphams/'; // Đường dẫn ảnh
            foreach ($sanphams as $sanpham): ?>
                <div class="product-item">
                    <a href="<?php echo BASE_URL; ?>products/detail.php?id=<?php echo $sanpham->sanpham_id; ?>" class="product-link">
                        <div class="product-image-wrapper">
                            <img
                                src="<?php echo $image_path . htmlspecialchars($sanpham->hinh_anh); ?>"
                                alt="<?php echo htmlspecialchars($sanpham->ten_sanpham); ?>">
                        </div>
                        <div class="product-info">
                            <p class="product-name"><?php echo htmlspecialchars($sanpham->ten_sanpham); ?></p>
                            <p class="product-price"><?php echo number_format($sanpham->gia, 0, ',', '.'); ?>đ</p>
                            <form action="<?php echo BASE_URL; ?>controller/GiohangController.php" method="POST" style="margin-top: 10px;">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="sanpham_id" value="<?php echo $sanpham->sanpham_id; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-add-to-cart">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($search_query)): ?>
            <p style="text-align: center; grid-column: 1 / -1; padding: 40px; font-size: 1.1em;">
                Xin lỗi, không tìm thấy sản phẩm nào khớp với từ khóa "<?php echo htmlspecialchars($search_query); ?>".
            </p>
        <?php endif; ?>

    </div>
</section>

<?php
require_once '../includes/footer.php';
?>