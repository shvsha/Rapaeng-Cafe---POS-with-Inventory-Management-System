// toggle select all checkboxes
function toggleSelectAll(checkbox) {
  const itemCheckboxes = document.querySelectorAll('.item-select');
  itemCheckboxes.forEach(cb => cb.checked = checkbox.checked);
  updateTotal();
}

// update total price when checkboxes change
function updateTotal() {
  const checkboxes = document.querySelectorAll('.item-select:checked');
  let subtotal = 0;
  let selectedCount = 0;

  checkboxes.forEach(checkbox => {
    const price = parseFloat(checkbox.dataset.price);
    const quantity = parseInt(checkbox.dataset.quantity);
    subtotal += price * quantity;
    selectedCount++;
  });

  // update display
  document.getElementById('selectedCount').textContent = selectedCount;
  document.getElementById('subtotalAmount').textContent = '₱' + subtotal.toFixed(2);

  const shippingFee = subtotal > 500 ? 0 : 50;
  document.getElementById('shippingFee').textContent = '₱' + shippingFee.toFixed(2);

  const total = subtotal + shippingFee;
  document.getElementById('totalAmount').textContent = '₱' + total.toFixed(2);

  // enable/disable checkout button
  document.getElementById('checkoutBtn').disabled = selectedCount === 0;
}

// update quantity via buttons
function updateQuantity(cartId, change) {
  const input = document.querySelector(`input[data-cart-id="${cartId}"]`);
  let newValue = parseInt(input.value) + change;

  if (newValue < 1) newValue = 1;
  if (newValue > 99) newValue = 99;

  input.value = newValue;
  updateQuantityInput(cartId, newValue);
}

// update quantity via input
function updateQuantityInput(cartId, quantity) {
  quantity = parseInt(quantity);
  if (quantity < 1 || quantity > 99) return;

  fetch('../../api/cart/update-quantity.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      cart_id: cartId,
      quantity: quantity
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`).closest('.cart-item');
      const price = parseFloat(cartItem.querySelector('.item-price').textContent.replace('₱', ''));
      const totalPrice = price * quantity;
      cartItem.querySelector('.item-total').textContent = '₱' + totalPrice.toFixed(2);
      updateTotal();
    } else {
      alert('Failed to update quantity');
      location.reload();
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred');
  });
}

// delete single item
function deleteItem(cartId) {
  if (confirm('Are you sure you want to remove this item?')) {
    fetch('../../api/cart/delete-item.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        cart_id: cartId
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.querySelector(`[data-cart-id="${cartId}"]`).closest('.cart-item').remove();
        updateTotal();

        const cartItems = document.querySelectorAll('.cart-item');
        if (cartItems.length === 0) {
          location.reload();
        }
      } else {
        alert('Failed to delete item');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred');
    });
  }
}

// delete selected items
function deleteSelected() {
  const checkboxes = document.querySelectorAll('.item-select:checked');
  if (checkboxes.length === 0) {
    alert('Please select items to delete');
    return;
  }

  if (confirm(`Delete ${checkboxes.length} item(s)?`)) {
    const cartIds = Array.from(checkboxes).map(cb => {
      return parseInt(cb.closest('.cart-item').dataset.cartId);
    });

    fetch('../../api/cart/delete-selected.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        cart_ids: cartIds
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Failed to delete items');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred');
    });
  }
}

// proceed to checkout
function proceedToCheckout() {
  const checkboxes = document.querySelectorAll('.item-select:checked');
  if (checkboxes.length === 0) {
    alert('Please select items to checkout');
    return;
  }

  // collect cart ids selected and send to server so checkout shows only those
  const cartIds = Array.from(checkboxes).map(cb => parseInt(cb.closest('.cart-item').dataset.cartId));

  fetch('../../api/cart/select-for-checkout.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ cart_ids: cartIds })
  })
  .then(r => r.json())
  .then(resp => {
    if (resp.success) {
      // proceed to checkout page which will load selected items from session
      window.location.href = 'checkout.php';
    } else if (resp.error === 'not_logged_in') {
      window.location.href = 'login.php';
    } else {
      alert('Unable to proceed to checkout');
    }
  })
  .catch(err => { console.error(err); alert('An error occurred'); });
}

// initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  updateTotal();
});
