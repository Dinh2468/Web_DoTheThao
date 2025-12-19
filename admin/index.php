<?php
// admin/index.php


require_once 'includes/admin_header.php';


$stats = [
    ['value' => '150', 'label' => 'Đơn hàng mới', 'icon_class' => 'fa-shopping-cart'],
    ['value' => '530', 'label' => 'Sản phẩm', 'icon_class' => 'fa-box'],
    ['value' => '45', 'label' => 'Khách hàng mới', 'icon_class' => 'fa-users'],
    ['value' => '12M', 'label' => 'Doanh thu tháng', 'icon_class' => 'fa-dollar-sign'],
];
?>
<h1>Tổng quan</h1>
<div class="stats-cards">
    <?php foreach ($stats as $stat): ?>
        <div class="stat-card">
            <div>
                <div class="value"><?php echo $stat['value']; ?></div>
                <div class="label"><?php echo $stat['label']; ?></div>
            </div>
            <i class="fa <?php echo $stat['icon_class']; ?> icon"></i>
        </div>
    <?php endforeach; ?>
</div>

<div class="recent-orders">
    <div class="recent-orders-header">
        <h3>Đơn hàng gần đây</h3>
        <a href="#" class="export-btn">+ Xuất báo cáo</a>
    </div>

    <h1>nội dung</h1>
</div>

<?php
require_once 'includes/admin_footer.php';
?>