// Function to update quantity and total price
// Function to update quantity and total price
function updateQuantity(button, change) {
    const quantityInput = button.closest('.input-group').querySelector('.quantity-input');
    let currentQuantity = parseInt(quantityInput.value) || 1; // Đảm bảo giá trị hiện tại không âm

    // Cập nhật số lượng
    currentQuantity += change;

    // Ngăn số lượng âm
    if (currentQuantity < 1) {
        currentQuantity = 1;
    }

    // Cập nhật giá trị mới
    quantityInput.value = currentQuantity;

    // Cập nhật tổng tiền của dòng sản phẩm
    updateTotal(quantityInput);

    // Cập nhật lại tổng tiền khi thay đổi số lượng
    updateTotalPrice();
}




// Function to update total price dynamically
function updateTotal(input) {
    const price = parseFloat(input.dataset.price);
    const quantity = parseInt(input.value);
    const totalPriceElement = input.closest('tr').querySelector('.total-price');
    const cartTotalElement = document.querySelector('.cart-total');
    const subtotalElement = document.querySelector('.subtotal');
    
    // Update row total
    const rowTotal = (price * quantity);
    totalPriceElement.textContent = `${rowTotal} $`;
    
    // Calculate overall cart total
    let cartTotal = 0;
    const totalPrices = document.querySelectorAll('.total-price');
    totalPrices.forEach(el => { 
        cartTotal += parseFloat(el.textContent);
    });
    
    // Update cart total and subtotal
    cartTotalElement.textContent = `${cartTotal} $`;
    subtotalElement.textContent = `${cartTotal}`;
    
    // Send AJAX request to update server-side cart
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(`productId=${input.dataset.productId}&quantity=${quantity}`);
}

// Function to update total price based on selected products
function updateTotalPrice() {
    let cartTotal = 0;
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked'); // Lấy tất cả checkbox được chọn

    // Tính toán tổng tiền cho các sản phẩm đã chọn
    selectedCheckboxes.forEach(checkbox => {
        const price = parseFloat(checkbox.dataset.price);
        const quantity = parseInt(checkbox.dataset.quantity);
        const rowTotal = price * quantity;

        cartTotal += rowTotal; // Cộng vào tổng giỏ hàng
    });

    // Cập nhật tổng tiền giỏ hàng
    const cartTotalElement = document.querySelector('.cart-total');
    cartTotalElement.textContent = `${cartTotal} $`; // Cập nhật vào phần tử tổng giá

    // Kiểm tra nếu có sản phẩm được chọn thì bật nút thanh toán
    const checkoutButton = document.getElementById('checkoutButton');
    if (cartTotal > 0) {
        checkoutButton.disabled = false; // Kích hoạt nút thanh toán
    } else {
        checkoutButton.disabled = true; // Tắt nút thanh toán nếu không có sản phẩm nào được chọn
    }
}

// Function to handle the "Proceed Checkout" click event
function proceedToCheckout() {
    const selectedProducts = [];
    const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');

    selectedCheckboxes.forEach(checkbox => {
        const productId = checkbox.dataset.productId;
        const price = parseFloat(checkbox.dataset.price);
        const quantity = parseInt(checkbox.dataset.quantity);
        const image = checkbox.closest('tr').querySelector('img').src.split('/').pop(); // Get image filename
        const name = checkbox.closest('tr').querySelector('td:nth-child(3)').textContent; // Get product name

        selectedProducts.push({
            productId: productId,
            price: price,
            quantity: quantity,
            image: image,
            name: name,
            total: price * quantity
        });
    });

    if (selectedProducts.length > 0) {
        const form = document.createElement('form');                     
        form.method = 'POST';
        form.action = 'chackout.php'; // Corrected typo in filename

        // Add selected products to form
        selectedProducts.forEach(product => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_cart[]';  // Changed name for clarity
            input.value = JSON.stringify(product);
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    } else {
        alert("Please select products to checkout.");
    }
}
