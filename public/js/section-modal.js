// Toggle Students List
function toggleStudentsList(sectionId) {
    const list = document.getElementById(`students-list-${sectionId}`);
    if (list.style.display === 'none' || list.style.display === '') {
        list.style.display = 'table-row';
    } else {
        list.style.display = 'none';
    }
}

// Show Edit Modal
function showEditModal(section) {
    // Populate the edit form with current section data
    document.getElementById('edit_name').value = section.name;
    
    // Create the description in the format: "BSIT - 1A"
    const yearLevel = section.name.split(' ')[0]; // Extract year level number (e.g., '1')
    const sectionLetter = section.description.split(' - ')[1]; // Extract section letter (e.g., 'A')
    const description = `BSIT - ${yearLevel}${sectionLetter}`; // Full description (e.g., "BSIT - 1A")

    // Populate the description in the modal
    document.getElementById('edit_description').value = description;

    // Set form action for editing
    const form = document.getElementById('editForm');
    form.action = `/sections/${section.id}`;

    // Show the edit modal
    const editModal = new bootstrap.Modal(document.getElementById('editSectionModal'));
    editModal.show();
}

// Show Delete Modal
function showDeleteModal(sectionId) {
    // Set form action for delete
    const form = document.getElementById('deleteForm');
    form.action = `/sections/${sectionId}`;

    // Show the delete modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteSectionModal'));
    deleteModal.show();
}
