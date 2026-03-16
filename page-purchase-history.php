<?php
/**
 * VieProxy — page-purchase-history.php
 * Purchase history sub-page fragment.
 * Loaded via AJAX into #page-content. No get_header/get_footer.
 */
?>

<div class="page-header">
    <h1>Lịch sử mua hàng</h1>
    <p class="page-subtitle">Xem lại các đơn hàng đã đặt và trạng thái thanh toán</p>
</div>

<!-- Filter bar -->
<div class="ph-filter-bar">
    <div class="ph-search-wrap">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="phSearch" class="ph-search" placeholder="Tìm theo mã đơn hàng..." />
    </div>
    <div class="ph-filter-select">
        <select id="phStatusFilter">
            <option value="">Tất cả trạng thái</option>
            <option value="completed">Hoàn thành</option>
            <option value="pending">Chờ thanh toán</option>
            <option value="cancelled">Đã hủy</option>
        </select>
        <i class="fa-solid fa-chevron-down"></i>
    </div>
</div>

<!-- Orders list -->
<div id="phOrderList">
    <div class="ac-loading">
        <i class="fa-solid fa-spinner fa-spin"></i>
        <p>Đang tải đơn hàng...</p>
    </div>
</div>

<!-- Empty state (hidden until needed) -->
<div id="phEmpty" class="ph-empty" style="display:none;">
    <i class="fa-solid fa-box-open"></i>
    <h3>Chưa có đơn hàng nào</h3>
    <p>Hãy khám phá các gói proxy của chúng tôi!</p>
    <a href="<?php echo esc_url(home_url('/proxies')); ?>" class="btn-primary"
        style="text-decoration:none; display:inline-flex; margin-top:16px;">
        <i class="fa-solid fa-shopping-bag"></i>
        Mua proxy ngay
    </a>
</div>