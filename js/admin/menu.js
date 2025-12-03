let menusCache = [];

// debounce helper
function debounce(fn, wait = 220) {
    let t = null;
    return function(...args){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,args), wait); };
}

const getMenus = () => {
    $.ajax({
        url: '../../api/menu/get-menu.php', // change to correct path
        type: 'GET',
        success: (res) => {
            console.log(res); // DEBUG
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
    // item.price might be integer — use as float for display
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
    $('#deleteMessage').text(`Are you sure you want to delete "${item.name}" (ID: ${item.menu_id})?`);
    showModal('#deleteModal');
}

// close modal when clicking close buttons
$(document).on('click','[data-close]', function(){
    const overlay = $(this).closest('.modal-overlay');
    hideModal(overlay);
});

// ensure clicking outside modal closes it
$('.modal-overlay').on('click', function(e){ if (e.target === this) hideModal(this); });

// add new mwnu
$('#add-menu-form').submit((e) => {
    e.preventDefault();
    const name = $('#name').val().trim();
    const category_id = $('#category').val();
    const price = parseFloat($('#price').val());
    const description = $('#description').val().trim();
    const image = $('#image').val().trim();

    const formData = {
        name: name,
        category_id: category_id,
        price: price,
        description: description,
        image: image
    }

    $.ajax({
        url: '../../api/menu/add-menu.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(formData),
        success: (res) => {
            console.log(res);
            const ok = !!res.status || !!res.success;
            if (ok) {
                showNotification('success','Menu added successfully');
                getMenus();
                $('#add-menu-form')[0].reset();
            } else {
                showNotification('error','Failed to add menu: ' + (res.message || 'Unknown error'));
            }  
        },
        error: () => {
            showNotification('error','Error adding menu');
        } 
    })
})

// wire edit modal save
$('#editSave').on('click', async function(){
    const id = $('#edit_menu_id').val();
    const payload = {
        menu_id: Number(id),
        name: $('#edit_name').val().trim(),
        category_id: Number($('#edit_category_id').val()),
        price: Number($('#edit_price').val()),
        description: $('#edit_description').val().trim(),
        image: $('#edit_image').val().trim()
    };
    $('#editSave').prop('disabled', true).text('Saving...');
    try{
        const res = await fetch('/POS-Inventory/api/menu/edit-menu.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
        const j = await res.json();
        const ok = j && j.success;
        if (ok) {
            showNotification('success','Menu updated');
            hideModal('#editModal');
            getMenus();
        } else {
            showNotification('error','Update failed: ' + (j.message || 'Unknown'));
        }
    } catch(e){ console.error(e); showNotification('error','Network error while updating'); }
    $('#editSave').prop('disabled', false).text('Save changes');
});

// delete confirm
$('#deleteConfirm').on('click', async function(){
    const id = Number($('#delete_menu_id').val());
    if (!id) return showNotification('error','Missing id');
    $('#deleteConfirm').prop('disabled', true).text('Deleting...');
    try{
        const res = await fetch('/POS-Inventory/api/menu/delete-menu.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ menu_id: id }) });
        const j = await res.json();
        if (j && j.success) {
            showNotification('success','Menu deleted');
            hideModal('#deleteModal');
            getMenus();
        } else {
            showNotification('error','Delete failed: ' + (j.message || 'Unknown'));
        }
    } catch(e){ console.error(e); showNotification('error','Network error while deleting'); }
    $('#deleteConfirm').prop('disabled', false).text('Delete');
});
