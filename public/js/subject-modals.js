// Function to show edit modal for subject
function showEditModal(subject) {
    document.getElementById('edit_course_code').value = subject.course_code;
    document.getElementById('edit_name').value = subject.name;
    document.getElementById('edit_description').value = subject.description;

    const form = document.getElementById('editForm');
    form.action = `/subjects/${subject.id}`;

    const editModal = new bootstrap.Modal(document.getElementById('editSubjectModal'));
    editModal.show();
}

// Function to show delete modal for subject
function showDeleteModal(subjectId) {
    const form = document.getElementById('deleteForm');
    form.action = `/subjects/${subjectId}`;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteSubjectModal'));
    deleteModal.show();
}
