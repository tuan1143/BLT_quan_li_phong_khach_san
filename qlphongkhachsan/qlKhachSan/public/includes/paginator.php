<?php
function renderPagination($total_pages, $current_page, $base_url) {
    if ($total_pages <= 1) return; // Ít hơn 1 trang thì không hiện

    // Giữ lại các tham số URL hiện tại (search, filter...) trừ 'page'
    $params = $_GET;
    unset($params['page']);
    
    // Hàm tạo link
    $createLink = function($page) use ($base_url, $params) {
        $query = http_build_query(array_merge($params, ['page' => $page]));
        return "$base_url?$query";
    };

    echo '<div class="pagination-container">';
    echo '<div class="pagination-info">Trang ' . $current_page . ' / ' . $total_pages . '</div>';
    echo '<div class="pagination">';

    // Nút Previous
    if ($current_page > 1) {
        echo '<a href="' . $createLink($current_page - 1) . '" class="page-link">«</a>';
    } else {
        echo '<span class="page-link disabled">«</span>';
    }

    // Các trang số
    for ($i = 1; $i <= $total_pages; $i++) {
        // Logic hiển thị thông minh (chỉ hiện trang gần trang hiện tại)
        if ($i == 1 || $i == $total_pages || ($i >= $current_page - 2 && $i <= $current_page + 2)) {
            $active = ($i == $current_page) ? 'active' : '';
            echo '<a href="' . $createLink($i) . '" class="page-link ' . $active . '">' . $i . '</a>';
        } elseif ($i == $current_page - 3 || $i == $current_page + 3) {
            echo '<span class="page-link disabled">...</span>';
        }
    }

    // Nút Next
    if ($current_page < $total_pages) {
        echo '<a href="' . $createLink($current_page + 1) . '" class="page-link">»</a>';
    } else {
        echo '<span class="page-link disabled">»</span>';
    }

    echo '</div>'; // End .pagination
    echo '</div>'; // End .pagination-container
}
?>