function showEditModal(user) {
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_user_type').value = user.user_type;

    const form = document.getElementById('editForm');
    form.action = `/users/${user.id}`;

    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

function showDeleteModal(userId) {
    const form = document.getElementById('deleteForm');
    form.action = `/users/${userId}`;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
