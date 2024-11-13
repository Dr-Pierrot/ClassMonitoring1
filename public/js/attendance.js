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
