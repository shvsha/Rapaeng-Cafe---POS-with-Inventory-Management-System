(function(){
    const apiBase = '../../api/users';

    // Aesthetic confirmation modal helper
    function showConfirmationModal(title, message, onConfirm, confirmText = 'Confirm', cancelText = 'Cancel', type = 'info') {
        let modal = document.getElementById('userConfirmationModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'userConfirmationModal';
            modal.className = 'modal-overlay hidden';
            modal.innerHTML = `
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
                    <div class="modal-header">
                        <h3 id="confirmTitle"></h3>
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
        else if (type === 'success') confirmBtn.className = 'btn-add';

        // clear previous handlers
        confirmBtn.onclick = null;
        document.getElementById('confirmCancel').onclick = null;

        // set up handlers
        confirmBtn.onclick = () => {
            confirmBtn.disabled = true;
            confirmBtn.textContent = confirmText + '...';
            onConfirm(() => {
                modal.classList.add('hidden');
                confirmBtn.disabled = false;
                confirmBtn.textContent = confirmText;
            });
        };

        document.getElementById('confirmCancel').onclick = () => {
            modal.classList.add('hidden');
        };

        // close button handler
        const closeBtn = modal.querySelector('[data-close]');
        if (closeBtn) {
            closeBtn.onclick = () => {
                modal.classList.add('hidden');
            };
        }

        // close on overlay click
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        };

        // show modal
        modal.classList.remove('hidden');
    }

    // Notification helper
    function showNotification(type, message, duration = 3000) {
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
        clearTimeout(el._hideTimeout);
        el._hideTimeout = setTimeout(()=>{
            if (el) el.classList.add('hidden');
        }, duration);
    }


    // Load users into the table (for user-management.php)
    async function loadUsers(){
        try{
            const res = await fetch(`${apiBase}/get-users.php`);
            const j = await res.json();
            const tbody = document.getElementById('user-data-body');
            if(!tbody) return; // not on user management page
            tbody.innerHTML = '';
            if(!j || !j.status || !j.data || j.data.length === 0){
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No users found</td></tr>';
                return;
            }
            j.data.forEach(user => {
                const tr = document.createElement('tr');
                const cashierId = user.cashier_id || '';
                const fullName = user.full_name || '';
                const username = user.username || '';
                tr.innerHTML = `
                    <td>${cashierId}</td>
                    <td>${escapeHtml(fullName)}</td>
                    <td>${escapeHtml(username)}</td>
                    <td>
                        <button class="btn-edit" data-id="${cashierId}" data-name="${escapeHtml(fullName)}" data-user="${escapeHtml(username)}">Edit</button>
                        <button class="btn-delete" data-id="${cashierId}" data-name="${escapeHtml(fullName)}">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            setupEditDeleteHandlers();
        }catch(err){
            const tbody = document.getElementById('user-data-body');
            if(tbody) tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:red;">Error loading users</td></tr>';
            console.error('Load users error', err);
        }
    }

    function escapeHtml(text){
        if(!text) return '';
        return String(text).replace(/[&<>"']/g, function(m){ 
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m]; 
        });
    }

    function setupEditDeleteHandlers(){
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function(){
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const user = this.getAttribute('data-user');
                document.getElementById('edit_cashier_id').value = id;
                document.getElementById('edit_full_name').value = name;
                document.getElementById('edit_username').value = user;
                document.getElementById('edit_password').value = '';
                document.getElementById('editUserModal').classList.remove('hidden');
                document.getElementById('editUserModal').removeAttribute('hidden');
            });
        });
        
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(){
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                document.getElementById('deleteUserMessage').textContent = `Are you sure you want to delete "${name}"?`;
                document.getElementById('delete_cashier_id').value = id;
                document.getElementById('deleteUserModal').classList.remove('hidden');
                document.getElementById('deleteUserModal').removeAttribute('hidden');
            });
        });
    }

    // Modal close handlers
    function setupModalHandlers(){
        document.querySelectorAll('[data-close]').forEach(btn => {
            btn.addEventListener('click', function(){
                const modal = this.closest('.modal-overlay');
                if(modal){
                    modal.classList.add('hidden');
                    modal.setAttribute('hidden', '');
                }
            });
        });
    }

    // Save edited user
    async function saveEditedUser(){
        const cashierId = document.getElementById('edit_cashier_id').value;
        const fullName = document.getElementById('edit_full_name').value;
        const username = document.getElementById('edit_username').value;
        const password = document.getElementById('edit_password').value;
        
        if(!fullName || !username){
            showNotification('error', 'Full name and username are required');
            return;
        }

        showConfirmationModal(
            'Update User',
            `Are you sure you want to update "${fullName}"?`,
            async (onDone) => {
                try{
                    const res = await fetch(`${apiBase}/update-user.php`, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            cashier_id: cashierId,
                            full_name: fullName,
                            username: username,
                            password: password || undefined
                        })
                    });
                    const j = await res.json();
                    if(j.status){
                        showNotification('success', 'User updated successfully');
                        document.getElementById('editUserModal').classList.add('hidden');
                        document.getElementById('editUserModal').setAttribute('hidden', '');
                        loadUsers();
                    } else {
                        showNotification('error', 'Error: ' + (j.message || 'Failed to update user'));
                    }
                }catch(err){
                    showNotification('error', 'Error updating user: ' + err.message);
                }
                onDone();
            },
            'Update User',
            'Cancel',
            'warning'
        );
    }

    // Confirm delete user
    async function confirmDeleteUser(){
        const cashierId = document.getElementById('delete_cashier_id').value;
        const username = document.getElementById('delete_username')?.value || 'this user';
        
        showConfirmationModal(
            'Delete User',
            `Are you sure you want to permanently delete "${username}"? This action cannot be undone.`,
            async (onDone) => {
                try{
                    const res = await fetch(`${apiBase}/delete-user.php`, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({cashier_id: cashierId})
                    });
                    const j = await res.json();
                    if(j.status){
                        showNotification('success', 'User deleted successfully');
                        document.getElementById('deleteUserModal').classList.add('hidden');
                        document.getElementById('deleteUserModal').setAttribute('hidden', '');
                        loadUsers();
                    } else {
                        showNotification('error', 'Error: ' + (j.message || 'Failed to delete user'));
                    }
                }catch(err){
                    showNotification('error', 'Error deleting user: ' + err.message);
                }
                onDone();
            },
            'Delete User',
            'Cancel',
            'danger'
        );
    }

    // Add new user (from add-user.php form)
    async function handleAddUserSubmit(e){
        e.preventDefault();
        const fullName = document.getElementById('full_name')?.value || '';
        const username = document.getElementById('username')?.value || '';
        const password = document.getElementById('password')?.value || '';
        
        if(!fullName || !username || !password){
            showNotification('error', 'All fields are required');
            return;
        }

        showConfirmationModal(
            'Register New User',
            `Register cashier "${fullName}" with username "${username}"?`,
            async (onDone) => {
                try{
                    const res = await fetch(`${apiBase}/add-user.php`, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            full_name: fullName,
                            username: username,
                            password: password
                        })
                    });
                    const j = await res.json();
                    if(j.status){
                        showNotification('success', 'User registered successfully');
                        document.getElementById('addUserForm').reset();
                        // redirect to user management after a short delay
                        setTimeout(() => {
                            window.location.href = 'user-management.php';
                        }, 1000);
                    } else {
                        showNotification('error', 'Error: ' + (j.message || 'Failed to register user'));
                    }
                }catch(err){
                    showNotification('error', 'Error registering user: ' + err.message);
                }
                onDone();
            },
            'Register User',
            'Cancel',
            'success'
        );
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function(){
        // If on user-management page, load users
        if(document.getElementById('user-data-body')){
            loadUsers();
            setupModalHandlers();
            
            // Save edited user
            const editSaveBtn = document.getElementById('editUserSave');
            if(editSaveBtn) editSaveBtn.addEventListener('click', saveEditedUser);
            
            // Confirm delete
            const deleteConfirmBtn = document.getElementById('deleteUserConfirm');
            if(deleteConfirmBtn) deleteConfirmBtn.addEventListener('click', confirmDeleteUser);
        }
        
        // If on add-user page, handle form submission
        const addUserForm = document.getElementById('addUserForm');
        if(addUserForm){
            addUserForm.addEventListener('submit', handleAddUserSubmit);
        }
    });
})();
