
function filterStudents() {
    var subjectId = $('#subject_id').val();
    var sectionId = $('#section_id').val();

    // Construct the URL dynamically
    var url = '/class-card/filter-students?subject_id=' + subjectId + '&section_id=' + sectionId;

    // Perform the AJAX request
    $.ajax({
        url: url,  // Use the dynamically constructed URL
        type: 'GET',
        success: function(data) {
            $('#student_id').empty().append('<option value="">All Students</option>');
            $.each(data, function(key, student) {
                $('#student_id').append('<option value="' + student.id + '">' + 
                    student.first_name + ' ' + student.middle_name + ' ' + student.last_name + '</option>');
            });
        }
    });
}


function openPerformanceModal(classCardId, studentId, term, type_activity) {
    $('#selected_exam_type').val(term);
    $('#class_card_id').val(classCardId);
    $('#student_id_performance').val(studentId); // Populate the student_id
    $('#term').val(term); // Set the term input field value
    $('#type_activity').val(type_activity)
    $('#performanceModal').modal('show');
}

function showEditModal(student) {
    document.getElementById('edit_student_number').value = student.student_number;
    document.getElementById('edit_first_name').value = student.first_name;
    document.getElementById('edit_last_name').value = student.last_name;
    document.getElementById('edit_middle_name').value = student.middle_name;
    document.getElementById('edit_date_of_birth').value = student.date_of_birth;
    document.getElementById('edit_gender').value = student.gender;
    document.getElementById('edit_course').value = student.course;
    document.getElementById('edit_section_id').value = student.section_id; // Set section_id
    document.getElementById('edit_subject_id').value = student.subject_id; // Set subject_id

    const form = document.getElementById('editForm');
    form.action = `/students/${student.id}`;

    const editModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
    editModal.show();
}

function openEditPerformanceModal(score_id, score, over_score) {
    // Set the score and over_score values in the input fields
    $('#edit_performanceScore').val(score);
    $('#edit_performanceOverScore').val(over_score); // Set the over_score

    // Set the form action dynamically to use the correct score ID
    const form = document.getElementById('editperformanceForm');
    form.action = `/class-card/performance-task/update/${score_id}`;

    // Show the modal
    $('#editperformanceModal').modal('show');
}


function openRemovePerformanceModal(){
    $('#removeperformanceModal').modal('show');
}


function openRemoveQuizModal(){
    $('#removequizModal').modal('show');
}

function deletePerformance(type, term, item, csrf) {
    if (confirm('Are you sure you want to remove all performance tasks for this item?')) {
        // Get the CSRF token
        // const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Send an AJAX DELETE request
        fetch(`/class-card/performance-task/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf, // Add CSRF token in headers
            },
            body: JSON.stringify({
                type: type,
                term: term,
                item: item,
            })
        })
        .then(response => {
            if (response.ok) {
                location.reload(); // Reload the page after successful deletion
            } else {
                alert('Failed to delete performance tasks.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

