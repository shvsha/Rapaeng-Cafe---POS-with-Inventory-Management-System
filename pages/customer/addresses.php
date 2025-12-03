<?php
include('../../api/fetch-address.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>My Addresses</title>
  <link rel="stylesheet" href="../../css/nav-bar.css">
  <link rel="stylesheet" href="../../css/footer.css">
  <link rel="stylesheet" href="../../css/customer/account.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
</head>
<body>
  <?php include('../nav.bar.php'); ?>
  <div style="padding: 30px;"></div>
  <main class="account-wrapper">
    <div class="account-grid">
      <aside class="account-side">
        <div class="profile-block account-card">
          <div class="profile-avatar"><img src="../../images/rapaeng-logo.png" alt="avatar"></div>
          <div>
            <div class="profile-name">Your Name</div>
            <div style="font-size:13px;color:#666;">Edit Profile</div>
          </div>
        </div>

        <nav class="side-nav">
          <ul>
            <?php $self = basename($_SERVER['PHP_SELF']); ?>
            <li class="<?php echo $self === 'profile.php' ? 'active' : ''; ?>"><a href="profile.php"><span class="side-icon">🏠</span> Profile</a></li>
            <li class="<?php echo $self === 'addresses.php' ? 'active' : ''; ?>"><a href="addresses.php"><span class="side-icon">📍</span> Addresses</a></li>
            <li class="<?php echo $self === 'change-password.php' ? 'active' : ''; ?>"><a href="change-password.php"><span class="side-icon">🔑</span> Change Password</a></li>
            <li class="<?php echo $self === 'notifications.php' ? 'active' : ''; ?>"><a href="notifications.php"><span class="side-icon">🔔</span> Notification Settings</a></li>
            <li class="<?php echo $self === 'orders.php' ? 'active' : ''; ?>"><a href="orders.php"><span class="side-icon">🛒</span> My Orders</a></li>
          </ul>
        </nav>
      </aside>

      <section class="account-main">
        <div class="top-controls">
          <div class="account-title">My Addresses</div>
          <button id="open-add-address" class="top-action" type="button">+ Add New Address</button>
        </div>

        <div class="account-content">
          <?php if (!empty($msg)): ?>
            <div style="color:green;margin-bottom:10px"><?php echo htmlspecialchars($msg); ?></div>
          <?php endif; ?>

          <?php if (!empty($errors)): ?>
            <div style="color:#a94442;margin-bottom:10px">
              <?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
            </div>
          <?php endif; ?>

          <!-- Add Address Modal (form is progressive-enhancement friendly: falls back to page POST) -->
          <div id="addAddressModal" class="modal" style="display:none;">
            <div class="modal-overlay"></div>
            <div class="modal-content">
              <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <div style="font-weight:700;">Add New Address</div>
                <button id="close-add-modal" class="btn-secondary" type="button">✕</button>
              </div>
              <form id="add-address-form" method="POST">
                <input type="hidden" name="action" value="add_address">
                <div class="form-field"><label>Address</label><textarea name="address_line" rows="3" required></textarea></div>
                <div class="form-row">
                  <div style="flex:1;" class="form-field"><label>City</label><input type="text" name="city" required></div>
                  <div style="flex:1;" class="form-field"><label>Province/State</label><input type="text" name="province" required></div>
                </div>
                <div class="form-row">
                  <div style="flex:1;" class="form-field"><label>Postal Code</label><input type="text" name="postal_code"></div>
                </div>
                <div class="form-actions" style="margin-top:14px;">
                  <button type="button" id="cancel-add" class="btn-secondary">Cancel</button>
                  <button type="submit" class="btn-primary">Add Address</button>
                </div>
              </form>
            </div>
          </div>

          <div id="addresses-list">

          <?php if (count($addresses) === 0): ?>
            <div class="section-empty">
              <img src="../../images/home-sample-pics/pic-3.jpg" alt="empty" />
              <p>You don't have addresses yet.</p>
            </div>
          <?php else: ?>
            <?php foreach ($addresses as $addr): ?>
              <div class="account-card" style="padding:12px; margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                  <div>
                    <div style="font-weight:700; color:#333;"><?php echo htmlspecialchars($addr['label']); ?> <?php echo $addr['is_default'] ? '<span style="color:#8B4513;font-weight:700;margin-left:8px">(Default)</span>' : ''; ?></div>
                    <div style="color:#555;margin-top:6px;">
                      <?php echo nl2br(htmlspecialchars($addr['address_line'])); ?>
                      <div style="margin-top:6px; font-size:13px; color:#666;">
                        <?php echo htmlspecialchars($addr['city']) . ', ' . htmlspecialchars($addr['province']) . ' ' . htmlspecialchars($addr['postal_code']); ?>
                        <?php if (!empty($addr['phone_number'])) { echo ' • ' . htmlspecialchars($addr['phone_number']); } ?>
                      </div>
                    </div>
                  </div>
                  <div style="text-align:right;">
                    <form method="POST" style="display:inline-block;">
                      <input type="hidden" name="action" value="delete_address">
                      <button class="btn-secondary" type="submit" onclick="return confirm('Remove this address?')">Remove</button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

      </section>
    </div>
  </main>
  <script>
    (function(){
      const openBtn = document.getElementById('open-add-address');
      const modal = document.getElementById('addAddressModal');
      const closeBtn = document.getElementById('close-add-modal');
      const cancelBtn = document.getElementById('cancel-add');
      const form = document.getElementById('add-address-form');
      const addressesList = document.getElementById('addresses-list');

      function openModal(){
        modal.style.display = 'flex';
      }
      function closeModal(){
        modal.style.display = 'none';
        // reset form
        form.reset();
        const err = form.querySelector('.form-error'); if(err) err.remove();
      }

      openBtn && openBtn.addEventListener('click', openModal);
      closeBtn && closeBtn.addEventListener('click', closeModal);
      cancelBtn && cancelBtn.addEventListener('click', closeModal);

      // close modal when clicking overlay
      modal && modal.addEventListener('click', function(e){
        if (e.target.classList && e.target.classList.contains('modal-overlay')) closeModal();
      });

      // handle AJAX add
      form && form.addEventListener('submit', async function(ev){
        // progressive enhancement: if JS disabled, form posts to page. With JS we do AJAX to API.
        ev.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        // remove prior errors
        const prevErr = form.querySelector('.form-error'); if(prevErr) prevErr.remove();

        const data = new FormData(form);
        try {
          const res = await fetch('../../api/add-address.php', { method: 'POST', body: data, credentials: 'same-origin' });
          const json = await res.json();
          if (!json.success) {
            const eDiv = document.createElement('div');
            eDiv.className = 'form-error';
            eDiv.style.color = '#a94442';
            eDiv.style.marginBottom = '10px';
            eDiv.textContent = (json.errors && json.errors.join ? json.errors.join('; ') : 'Failed to add address');
            form.prepend(eDiv);
            return;
          }

          const a = json.address;

          // if is_default, clear previous default labels
          if (a.is_default == 1) {
            const defaults = addressesList.querySelectorAll('.account-card .default-badge');
            defaults.forEach(d => d.remove());
          }

          // remove empty state
          const empty = addressesList.querySelector('.section-empty');
          if (empty) empty.remove();

          // build new card
          const card = document.createElement('div');
          card.className = 'account-card';
          card.style.padding = '12px';
          card.style.marginBottom = '12px';

          const inner = document.createElement('div');
          inner.style.display = 'flex';
          inner.style.justifyContent = 'space-between';
          inner.style.alignItems = 'center';

          const left = document.createElement('div');
          const title = document.createElement('div');
          title.style.fontWeight = '700';
          title.style.color = '#333';
          title.textContent = a.label || 'Address';

          if (a.is_default == 1) {
            const badge = document.createElement('span');
            badge.className = 'default-badge';
            badge.style.color = '#8B4513';
            badge.style.fontWeight = '700';
            badge.style.marginLeft = '8px';
            badge.textContent = ' (Default)';
            title.appendChild(badge);
          }

          const details = document.createElement('div');
          details.style.color = '#555';
          details.style.marginTop = '6px';
          details.innerHTML = (a.address_line ? a.address_line.replace(/\n/g, '<br>') : '');

          const meta = document.createElement('div');
          meta.style.marginTop = '6px';
          meta.style.fontSize = '13px';
          meta.style.color = '#666';
          const cityprov = (a.city || '') + (a.city && a.province ? ', ' : '') + (a.province || '');
          meta.textContent = cityprov + (a.postal_code ? ' ' + a.postal_code : '');
          if (a.phone_number) meta.textContent += ' • ' + a.phone_number;

          details.appendChild(meta);
          left.appendChild(title);
          left.appendChild(details);

          const right = document.createElement('div');
          right.style.textAlign = 'right';

          // create delete form (falls back to full page post)
          const delForm = document.createElement('form');
          delForm.method = 'POST';
          delForm.style.display = 'inline-block';
          const act = document.createElement('input'); act.type='hidden'; act.name='action'; act.value='delete_address';
          const aid = document.createElement('input'); aid.type='hidden'; aid.name='address_id'; aid.value = a.address_id;
          const btn = document.createElement('button'); btn.className = 'btn-secondary'; btn.type='submit'; btn.textContent='Remove';
          delForm.appendChild(act); delForm.appendChild(aid); delForm.appendChild(btn);

          right.appendChild(delForm);

          inner.appendChild(left);
          inner.appendChild(right);
          card.appendChild(inner);

          // insert at top
          addressesList.insertBefore(card, addressesList.firstChild);

          // close modal and reset
          closeModal();

        } catch(err) {
          const eDiv = document.createElement('div');
          eDiv.className = 'form-error';
          eDiv.style.color = '#a94442';
          eDiv.style.marginBottom = '10px';
          eDiv.textContent = 'Network or server error';
          form.prepend(eDiv);
          console.error(err);
        } finally {
          const submitBtn = form.querySelector('button[type="submit"]');
          submitBtn.disabled = false;
        }
      });
    })();
  </script>
  <?php include('../footer.php'); ?>
</body>
</html>