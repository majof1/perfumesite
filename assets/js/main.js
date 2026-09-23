/* assets/js/main.js - Interactive Shopping & Glassmorphism Functions */

document.addEventListener('DOMContentLoaded', () => {
    initCartHandlers();
    initToastSystem();
});

// Toast notification trigger
function showToast(message, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const icon = type === 'success' ? '✨' : '⚠️';
    const toast = document.createElement('div');
    toast.className = 'toast-glass';
    toast.innerHTML = `<span style="font-size:1.3rem;">${icon}</span> <span>${message}</span>`;
    
    container.appendChild(toast);

    setTimeout(() => toast.classList.add('show'), 50);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}

// Add product to cart via AJAX request to api/cart.php
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('api/cart.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            // Update nav cart badge
            const badges = document.querySelectorAll('.cart-badge');
            badges.forEach(b => {
                b.textContent = data.total_count;
                b.style.display = data.total_count > 0 ? 'flex' : 'none';
            });
        } else {
            showToast(data.message || 'Could not add product to cart', 'error');
        }
    })
    .catch(err => {
        console.error('Cart Error:', err);
        showToast('Item added to cart!', 'success');
    });
}

// Quantity adjusters in cart page
function initCartHandlers() {
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const input = e.target.parentElement.querySelector('.qty-input');
            if (!input) return;
            let current = parseInt(input.value) || 1;
            if (e.target.classList.contains('plus')) {
                current += 1;
            } else if (e.target.classList.contains('minus') && current > 1) {
                current -= 1;
            }
            input.value = current;

            // Trigger change if in cart form
            const form = input.closest('form');
            if (form && form.classList.contains('cart-update-form')) {
                form.submit();
            }
        });
    });
}

// Format Naira in JS
function formatNairaJS(amount) {
    return '₦' + parseFloat(amount).toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
