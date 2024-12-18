$(document).ready(function() {
    $('#apply-coupon-form').on('submit', function(e) {
        e.preventDefault();
        var voucherCode = $('#voucher_code').val();
        
        $.ajax({
            url: 'apply_voucher.php',
            method: 'POST',
            data: {
                voucher_code: voucherCode,
                selected_cart: '<?= json_encode($selectedProducts) ?>'
            },
            success: function(response) {
                // Xử lý phản hồi, có thể refresh trang hoặc cập nhật giá
                location.reload();
            }
        });
    });
});