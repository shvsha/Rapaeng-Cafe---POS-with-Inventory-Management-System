let menusCache = [];

// debounce helper
function debounce(fn, wait = 220) {
    let t = null;
    return function(...args){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,args), wait); };
}

// Aesthetic confirmation modal helper
function showConfirmationModal(title, message, onConfirm, confirmText = 'Confirm', cancelText = 'Cancel', type = 'info') {
    // type: 'info' | 'warning' | 'danger' | 'success'
    let modal = document.getElementById('confirmationModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'confirmationModal';
        modal.className = 'modal-overlay hidden';
        modal.innerHTML = `
            <div class="modal" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
                <div class="modal-header">
                    <h3 style="color:white;" id="confirmTitle"></h3>
                    <button class="modal-close" data-close>&times;</button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage"></p>
                </div>
                <div class="modal-footer">
                    <button id="confirmCancel" class="btn-outline">Cancel</button>
                    <button id="confirmBtn" class="btn-save">Confirm</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmBtn').textContent = confirmText;
    document.getElementById('confirmCancel').textContent = cancelText;
    
    // update button styling based on type
    const confirmBtn = document.getElementById('confirmBtn');
    confirmBtn.className = 'btn-save';
    if (type === 'danger') confirmBtn.className = 'btn-danger';
    else if (type === 'warning') confirmBtn.className = 'btn-warning';
    else if (type === 'success') confirmBtn.className = 'btn-success';
    else if (type === 'add') confirmBtn.className = 'btn-add';

    // clear previous handlers
    confirmBtn.onclick = null;
    document.getElementById('confirmCancel').onclick = null;

    // set up handlers
    confirmBtn.onclick = () => {
        confirmBtn.disabled = true;
        confirmBtn.textContent = confirmText + '...';
        onConfirm(() => {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            confirmBtn.disabled = false;
            confirmBtn.textContent = confirmText;
        });
    };

    document.getElementById('confirmCancel').onclick = () => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    };

    // close on overlay click
    modal.onclick = (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
        }
    };

    // show modal
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
}

// small notification helper — injects a top-centered modal-like notification
function showNotification(type, message, duration = 3000) {
    // type: 'success' | 'error'
    let el = document.querySelector('.notification');
    if (!el) {
        el = document.createElement('div');
        el.className = 'notification hidden';
        el.setAttribute('role','status');
        document.body.appendChild(el);
    }
    el.className = `notification ${type}`;
    el.innerHTML = `
        <div class="notif-icon">${type === 'success' ? '✔' : '✖'}</div>
        <div class="notif-body">${message}</div>
    `;
    el.classList.remove('hidden');
    // auto-hide
    clearTimeout(el._hideTimeout);
    el._hideTimeout = setTimeout(()=>{
        if (el) el.classList.add('hidden');
    }, duration);
}

const getMenus = () => {
    $.ajax({
        url: '../../api/menu/get-menu.php',
        type: 'GET',
        success: (res) => {
            console.log(res);
            if (res.status) {
                menusCache = res.data;
                renderUI(res.data);
            } else {
                $('#menu-data-body').html(`<tr><td colspan="5">No data found</td></tr>`);
            }
        },
        error: () => {
            $('#menu-data-body').html(`<tr><td colspan="5">Error loading data</td></tr>`);
        }
    });
};

const renderUI = (menus) => {
    const container = $('#menu-data-body');
    container.empty();

    menus.forEach(menu => {
        const row = `
        <tr>
                            <td>${menu.name}</td>
                            <td>${
                                {1:'Coffee',2:'Non Coffee',3:'Snacks',4:'Light Bites'}[menu.category_id] || 'Unknown'
                            }</td>
                            <td>${parseFloat(menu.price).toFixed(2)}</td>
            <td>${menu.description}</td>
            <td class="action-buttons">
                <button class="edit-btn" data-id="${menu.menu_id}">Edit</button>
                <button class="delete-btn" data-id="${menu.menu_id}">Delete</button>
            </td>
        </tr>`;
        container.append(row);
    });

    // attach handlers after render
    attachRowHandlers();
};

getMenus();

// wire up client-side search (filters menusCache by name)
try {
    const searchEl = document.querySelector('.search-menu');
    if (searchEl) {
        const runSearch = (val) => {
            const q = String(val || '').trim().toLowerCase();
            if (!q) return renderUI(menusCache);
            const filtered = menusCache.filter(m => (m.name || '').toString().toLowerCase().includes(q));
            renderUI(filtered);
        };
        searchEl.addEventListener('input', debounce((e) => runSearch(e.target.value), 180));
    }
} catch (e) { console.error('search init error', e); }

// attach handlers for edit/delete on rows (delegated after render)
function attachRowHandlers(){
    $('.edit-btn').off('click').on('click', function(){
        const id = $(this).data('id');
        openEditModal(id);
    });

    $('.delete-btn').off('click').on('click', function(){
        const id = $(this).data('id');
        openDeleteModal(id);
    });
}

// helper to find menu by id
function findMenu(id){ return menusCache.find(m => Number(m.menu_id) === Number(id)); }

// modal helpers
function showModal(el){ $(el).removeClass('hidden').attr('aria-hidden','false'); }
function hideModal(el){ $(el).addClass('hidden').attr('aria-hidden','true'); }

// open edit modal and populate
function openEditModal(id){
    const item = findMenu(id);
    if (!item) return showNotification('error','Item not found');
    $('#edit_menu_id').val(item.menu_id);
    $('#edit_name').val(item.name);
    $('#edit_category_id').val(item.category_id);
    $('#edit_price').val(parseFloat(item.price));
    $('#edit_description').val(item.description);
    $('#edit_image').val(item.images || '');
    showModal('#editModal');
}

// open delete confirmation modal
function openDeleteModal(id){
    const item = findMenu(id);
    if (!item) return showNotification('error','Item not found');
    $('#delete_menu_id').val(item.menu_id);
    $('#deleteMessage').text(`Are you sure you want to delete "${item.name}"?`);
    showModal('#deleteModal');
}

// close modal when clicking close buttons
$(document).on('click','[data-close]', function(){
    const overlay = $(this).closest('.modal-overlay');
    hideModal(overlay);
});

// ensure clicking outside modal closes it
$('.modal-overlay').on('click', function(e){ if (e.target === this) hideModal(this); });

// add new menu with confirmation
$('#add-menu-form').submit((e) => {
    e.preventDefault();
    const name = $('#name').val().trim();
    const category_id = $('#category').val();
    const price = parseFloat($('#price').val());
    const description = $('#description').val().trim();
    const image = $('#image').val().trim();

    if (!name || !category_id || !price) {
        showNotification('error','Please fill in all required fields');
        return;
    }

    // Show confirmation modal before adding
    showConfirmationModal(
        'Add New Menu Item',
        `Are you sure you want to add "${name}" (₱${price.toFixed(2)})?`,
        (onDone) => {
            const formData = {
                name: name,
                category_id: category_id,
                price: price,
                description: description,
                image: image
            };

            $.ajax({
                url: '../../api/menu/add-menu.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: (res) => {
                    const ok = !!res.status || !!res.success;
                    if (ok) {
                        showNotification('success','Menu added successfully');
                        getMenus();
                        $('#add-menu-form')[0].reset();
                    } else {
                        showNotification('error','Failed to add menu: ' + (res.message || 'Unknown error'));
                    }
                    onDone();
                },
                error: () => {
                    showNotification('error','Error adding menu');
                    onDone();
                } 
            });
        },
        'Add Menu',
        'Cancel',
        'add'
    );
});

// wire edit modal save with confirmation
$('#editSave').on('click', function(){
    const id = $('#edit_menu_id').val();
    const name = $('#edit_name').val().trim();
    const price = Number($('#edit_price').val());

    if (!name || !price) {
        showNotification('error','Please fill in all required fields');
        return;
    }

    showConfirmationModal(
        'Update Menu Item',
        `Are you sure you want to update "${name}" to ₱${price.toFixed(2)}?`,
        (onDone) => {
            const payload = {
                menu_id: Number(id),
                name: name,
                category_id: Number($('#edit_category_id').val()),
                price: price,
                description: $('#edit_description').val().trim(),
                image: $('#edit_image').val().trim()
            };

            fetch('/POS-Inventory/api/menu/edit-menu.php', { 
                method: 'POST', 
                headers: {'Content-Type':'application/json'}, 
                body: JSON.stringify(payload) 
            })
            .then(res => res.json())
            .then(j => {
                const ok = j && j.success;
                if (ok) {
                    showNotification('success','Menu updated successfully');
                    hideModal('#editModal');
                    getMenus();
                } else {
                    showNotification('error','Update failed: ' + (j.message || 'Unknown'));
                }
                onDone();
            })
            .catch(e => {
                console.error(e);
                showNotification('error','Network error while updating');
                onDone();
            });
        },
        'Update Menu',
        'Cancel',
        'add'
    );
});

// delete confirm with confirmation modal
$('#deleteConfirm').on('click', function(){
    const id = Number($('#delete_menu_id').val());
    const item = findMenu(id);
    if (!id || !item) return showNotification('error','Missing item data');

    showConfirmationModal(
        'Delete Menu Item',
        `Are you sure you want to permanently delete "${item.name}"? This action cannot be undone.`,
        (onDone) => {
            fetch('/POS-Inventory/api/menu/delete-menu.php', { 
                method:'POST', 
                headers:{'Content-Type':'application/json'}, 
                body: JSON.stringify({ menu_id: id }) 
            })
            .then(res => res.json())
            .then(j => {
                if (j && j.success) {
                    showNotification('success','Menu deleted successfully');
                    hideModal('#deleteModal');
                    getMenus();
                } else {
                    showNotification('error','Delete failed: ' + (j.message || 'Unknown'));
                }
                onDone();
            })
            .catch(e => {
                console.error(e);
                showNotification('error','Network error while deleting');
                onDone();
            });
        },
        'Delete Permanently',
        'Cancel',
        'danger'
    );
});
