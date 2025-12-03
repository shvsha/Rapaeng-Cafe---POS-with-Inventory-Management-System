let currentItem = {
  name: '',
  price: '',
  description: '',
  image: '',
  categoryId: null,
  menuId: null
};

function openModal(itemName, price, description, imagePath, categoryId, menuId) {
  currentItem = { 
    name: itemName, 
    price: price, 
    description: description,
    image: imagePath,
    categoryId: categoryId,
    menuId: menuId
  };
  
  document.getElementById('modalTitle').textContent = itemName;
  document.getElementById('modalPrice').textContent = price;
  
  // Image loader
  const imageContainer = document.getElementById('modalImageContainer');
  if(imagePath && imagePath !== '') {
    imageContainer.innerHTML = `<img src="../../images/menu-pics/${imagePath}" alt="${itemName}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">`;
  } else {
    imageContainer.innerHTML = '<div class="modal-image-placeholder">☕</div>';
  }
  
  // Show/hide drink options based on category
  // Category ID 1 = Coffee, 2 = Drinks (adjust based on your actual database)
  const drinkOptions = document.getElementById('drinkOptions');
  if (categoryId === 1 || categoryId === 2) {
    drinkOptions.style.display = 'block';
  } else {
    drinkOptions.style.display = 'none';
  }
  
  document.getElementById('modalOverlay').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('active');
  document.body.style.overflow = 'auto';
  // Reset form
  document.getElementById('orderForm').reset();
}

function closeModalOnOutsideClick(event) {
  if (event.target.id === 'modalOverlay') {
    closeModal();
  }
}

function addToCart() {
  const quantity = parseInt(document.getElementById('quantity').value);
  
  // Base order data
  const orderData = {
    menu_id: currentItem.menuId,
    quantity: quantity
  };
  
  // Add drink-specific options only if it's a drink category
  if (currentItem.categoryId === 1 || currentItem.categoryId === 2) {
    const sugarLevel = document.getElementById('sugarLevel').value;
    const size = document.getElementById('size').value;
    const extraShot = document.getElementById('extraShot').checked;
    
    orderData.sugar_level = sugarLevel;
    orderData.size = size;
    orderData.extra_shot = extraShot ? 1 : 0;
  }
  
  console.log('Sending to cart:', orderData);
  
  // Send to backend
  fetch('../../api/cart/add-to-cart.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(orderData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Success message
      showNotification('✓ Added to cart successfully!', 'success');
      closeModal();
      
      // Optional: Update cart count in navbar if you have one
      updateCartCount();
    } else if (data.error === 'not_logged_in') {
      window.location.href = '../login.php';
    } else {
      showNotification('Failed to add to cart: ' + (data.message || 'Unknown error'), 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('An error occurred. Please try again.', 'error');
  });
}

function showNotification(message, type) {
  const existingNotif = document.querySelector('.notification');
  if (existingNotif) {
    existingNotif.remove();
  }
  
  const notification = document.createElement('div');
  notification.className = `notification ${type}`;
  notification.textContent = message;

  notification.style.cssText = `
    position: fixed;
    top: 100px;
    right: 20px;
    padding: 15px 25px;
    background: ${type === 'success' ? '#4CAF50' : '#f44336'};
    color: white;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 10000;
    font-family: "Lato", sans-serif;
    font-size: 14px;
    animation: slideIn 0.3s ease-out;
  `;
  
  document.body.appendChild(notification);
  
  // 3 sec
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease-out';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// css animation
if (!document.getElementById('notification-styles')) {
  const style = document.createElement('style');
  style.id = 'notification-styles';
  style.textContent = `
    @keyframes slideIn {
      from {
        transform: translateX(400px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes slideOut {
      from {
        transform: translateX(0);
        opacity: 1;
      }
      to {
        transform: translateX(400px);
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(style);
}

// Update cart count (optional - if you have a cart icon in navbar)
function updateCartCount() {
  fetch('../../api/cart/get-count-cart.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
          cartCountElement.textContent = data.count;
          if (data.count > 0) {
            cartCountElement.style.display = 'flex';
          }
        }
      }
    })
    .catch(error => console.error('Error updating cart count:', error));
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeModal();
  }
});