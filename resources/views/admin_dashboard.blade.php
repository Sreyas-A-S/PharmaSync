@extends('layouts.app')

@section('main')
    <div class="container-fluid mt-4">
        <div id="alert-container"></div>
        <h2 class="mb-4">Admin Dashboard</h2>

        <!-- Analytics Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Users</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalUsers }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Departments</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalDepartments }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Total Updates</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalUpdates }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Management Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Management</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">Add New User</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="users-table" class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Department Management Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Department Management</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">Add New Department</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="departments-table" class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">All Updates</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="all-updates-table" class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Attachments</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        @include('modals.user_modals')

        @include('modals.department_modals')

        @include('modals.attachment_modals')

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            function showAlert(message, type) {
                const alertContainer = $('#alert-container');
                const alert = $(`
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);
                alertContainer.append(alert);
                setTimeout(() => alert.alert('close'), 5000);
            }

            const usersTable = $('#users-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: { url: "{{ route('admin.users') }}", dataSrc: '' },
                columns: [
                    { data: null, className: 'text-center', render: (d, t, r, m) => m.row + 1 },
                    { data: 'name' },
                    { data: 'email' },
                    { data: 'role' },
                    { data: 'department.name', defaultContent: 'N/A' },
                    {
                        data: null, orderable: false, searchable: false, className: 'text-center', render: (d, t, r) =>
                            `<div class="d-flex justify-content-center"><button class="btn btn-sm btn-outline-primary me-1 edit-user" data-id="${d.id}" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></button>` +
                            `<button class="btn btn-sm btn-outline-danger delete-user" data-id="${d.id}" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></div>`
                    }
                ],
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                dom: '<"row mb-2"<"col-sm-6"l><"col-sm-6 text-end"f>>rt<"row mt-2"<"col-sm-6"i><"col-sm-6"p>>',
            });

            const departmentsTable = $('#departments-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: { url: "{{ route('admin.departments') }}", dataSrc: '' },
                columns: [
                    { data: null, className: 'text-center', render: (d, t, r, m) => m.row + 1 },
                    { data: 'name' },
                    {
                        data: null, orderable: false, searchable: false, className: 'text-center', render: (d, t, r) =>
                            `<div class="d-flex justify-content-center"><button class="btn btn-sm btn-outline-primary me-1 edit-department" data-id="${d.id}" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></button>` +
                            `<button class="btn btn-sm btn-outline-danger delete-department" data-id="${d.id}" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></div>`
                    }
                ],
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                dom: '<"row mb-2"<"col-sm-6"l><"col-sm-6 text-end"f>>rt<"row mt-2"<"col-sm-6"i><"col-sm-6"p>>',
            });

            const allUpdatesTable = $('#all-updates-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: { url: "{{ route('admin.all-updates') }}", dataSrc: '' },
                columns: [
                    { data: null, className: 'text-center', render: (d, t, r, m) => m.row + 1 },
                    { data: 'user.name', defaultContent: 'N/A' },
                    { data: 'user.department.name', defaultContent: 'N/A' },
                    { data: 'title' },
                    {
                        data: 'description', className: 'text-wrap', render: (d, t) => {
                            const max = 80;
                            if (t === 'display' && d && d.length > max)
                                return `<span class="desc-short">${d.substr(0, max)}... </span><span class="desc-full d-none">${d}</span><a href="#" class="read-more-link ms-2">Read more</a>`;
                            return d;
                        }
                    },
                    {
                        data: 'attachments', className: 'text-center', render: (d, t, r) => {
                            if (!d || d.length === 0) return '';
                            const jsonAttachments = JSON.stringify(d).replace(/"/g, '&quot;');
                            return `<button class="btn btn-sm btn-primary view-attachments-btn" data-attachments="${jsonAttachments}">View (${d.length})</button>`;
                        }
                    },
                    { data: 'created_at', className: 'text-nowrap', render: d => d ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '' },
                ],
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                dom: '<"row mb-2"<"col-sm-6"l><"col-sm-6 text-end"f>>rt<"row mt-2"<"col-sm-6"i><"col-sm-6"p>>',
            });

            let userIdToDelete = null;
            let departmentIdToDelete = null;

            function showValidationError(element, message) {
                $(element).addClass('is-invalid');
                let feedback = $(element).next('.invalid-feedback');
                if (feedback.length === 0) {
                    feedback = $('<div class="invalid-feedback"></div>');
                    $(element).after(feedback);
                }
                feedback.text(message);
            }

            function clearValidationErrors(form) {
                $(form).find('.is-invalid').removeClass('is-invalid');
                $(form).find('.invalid-feedback').remove();
            }

            $('#createUserForm').on('submit', function (e) {
                e.preventDefault();
                clearValidationErrors(this); 

                const name = $('#createUserName').val();
                const email = $('#createUserEmail').val();
                const password = $('#createUserPassword').val();
                const role = $('#createUserRole').val();
                const departmentId = $('#createUserDepartment').val();

                let isValid = true;

                if (!name) {
                    showValidationError('#createUserName', 'Name is required.');
                    isValid = false;
                }
                if (!email) {
                    showValidationError('#createUserEmail', 'Email is required.');
                    isValid = false;
                } else if (!/\S+@\S+\.\S+/.test(email)) {
                    showValidationError('#createUserEmail', 'Please enter a valid email address.');
                    isValid = false;
                }
                if (!password) {
                    showValidationError('#createUserPassword', 'Password is required.');
                    isValid = false;
                }
                if (!role) {
                    showValidationError('#createUserRole', 'Role is required.');
                    isValid = false;
                }
                if (role !== 'admin' && !departmentId) { 
                    showValidationError('#createUserDepartment', 'Department is required for selected role.');
                    isValid = false;
                }

                if (!isValid) {
                    return; 
                }

                const formData = $(this).serialize();
                fetch("{{ route('admin.users.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message) });
                    }
                    return response.json();
                })
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('createUserModal')).hide();
                    showAlert(data.message, 'success');
                    usersTable.ajax.reload();
                    $('#createUserForm')[0].reset(); 
                    clearValidationErrors('#createUserForm'); 
                }).catch(error => showAlert(error.message, 'danger'));
            });

            $('#users-table').on('click', '.edit-user', function () {
                const userId = $(this).data('id');
                const rowData = usersTable.row($(this).closest('tr')).data();
                $('#editUserId').val(userId);
                $('#editUserName').val(rowData.name);
                $('#editUserEmail').val(rowData.email);
                $('#editUserRole').val(rowData.role);
                $('#editUserDepartment').val(rowData.department_id);
                clearValidationErrors('#editUserForm'); 
                var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                modal.show();
            });

            $('#editUserForm').on('submit', function (e) {
                e.preventDefault();
                clearValidationErrors(this); 

                const userId = $('#editUserId').val();
                const name = $('#editUserName').val();
                const email = $('#editUserEmail').val();
                const password = $('#editUserPassword').val(); 
                const role = $('#editUserRole').val();
                const departmentId = $('#editUserDepartment').val();

                let isValid = true;

                if (!name) {
                    showValidationError('#editUserName', 'Name is required.');
                    isValid = false;
                }
                if (!email) {
                    showValidationError('#editUserEmail', 'Email is required.');
                    isValid = false;
                } else if (!/\S+@\S+\.\S+/.test(email)) {
                    showValidationError('#editUserEmail', 'Please enter a valid email address.');
                    isValid = false;
                }
                if (!role) {
                    showValidationError('#editUserRole', 'Role is required.');
                    isValid = false;
                }
                if (role !== 'admin' && !departmentId) { 
                    showValidationError('#editUserDepartment', 'Department is required for selected role.');
                    isValid = false;
                }

                if (!isValid) {
                    return; 
                }

                const formData = $(this).serialize();
                fetch(`/admin/users/${userId}` , {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message) });
                    }
                    return response.json();
                })
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                    showAlert(data.message, 'success');
                    usersTable.ajax.reload();
                    $('#editUserForm')[0].reset(); 
                    clearValidationErrors('#editUserForm'); 
                }).catch(error => showAlert(error.message, 'danger'));
            });

            $('#users-table').on('click', '.delete-user', function () {
                userIdToDelete = $(this).data('id');
                var modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
                modal.show();
            });

            $('#confirmDeleteUserBtn').on('click', function () {
                fetch(`/admin/users/${userIdToDelete}` , {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message) });
                    }
                    return response.json();
                })
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();
                    showAlert(data.message, 'success');
                    usersTable.ajax.reload();
                }).catch(error => showAlert(error.message, 'danger'));
            });
 
            $('#createDepartmentForm').on('submit', function (e) {
                e.preventDefault();
                clearValidationErrors(this); 

                const departmentName = $('#createDepartmentName').val();

                let isValid = true;

                if (!departmentName) {
                    showValidationError('#createDepartmentName', 'Department Name is required.');
                    isValid = false;
                }

                if (!isValid) {
                    return; 
                }

                const formData = $(this).serialize();
                fetch("{{ route('admin.departments.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message) });
                    }
                    return response.json();
                })
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('createDepartmentModal')).hide();
                    showAlert(data.message, 'success');
                    departmentsTable.ajax.reload();
                    $('#createDepartmentForm')[0].reset(); 
                    clearValidationErrors('#createDepartmentForm'); 
                }).catch(error => showAlert(error.message, 'danger'));
            });


            $('#departments-table').on('click', '.edit-department', function () {
                const departmentId = $(this).data('id');
                const rowData = departmentsTable.row($(this).closest('tr')).data();
                $('#editDepartmentId').val(departmentId);
                $('#editDepartmentName').val(rowData.name);
                clearValidationErrors('#editDepartmentForm'); 
                var modal = new bootstrap.Modal(document.getElementById('editDepartmentModal'));
                modal.show();
            });

            $('#editDepartmentForm').on('submit', function (e) {
                e.preventDefault();
                clearValidationErrors(this); 

                const departmentId = $('#editDepartmentId').val();
                const departmentName = $('#editDepartmentName').val();

                let isValid = true;

                if (!departmentName) {
                    showValidationError('#editDepartmentName', 'Department Name is required.');
                    isValid = false;
                }

                if (!isValid) {
                    return; 
                }

                const formData = $(this).serialize();
                fetch(`/admin/departments/${departmentId}` , {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message) });
                    }
                    return response.json();
                })
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('editDepartmentModal')).hide();
                    showAlert(data.message, 'success');
                    departmentsTable.ajax.reload();
                    $('#editDepartmentForm')[0].reset(); 
                    clearValidationErrors('#editDepartmentForm');
                }).catch(error => showAlert(error.message, 'danger'));
            });

 
            $('#departments-table').on('click', '.delete-department', function () {
                departmentIdToDelete = $(this).data('id');
                var modal = new bootstrap.Modal(document.getElementById('deleteDepartmentModal'));
                modal.show();
            });

            $('#confirmDeleteDepartmentBtn').on('click', function () {
                fetch(`/admin/departments/${departmentIdToDelete}` , {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message) });
                    }
                    return response.json();
                })
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('deleteDepartmentModal')).hide();
                    showAlert(data.message, 'success');
                    departmentsTable.ajax.reload();
                }).catch(error => showAlert(error.message, 'danger'));
            });

            $('#all-updates-table')
                .on('click', '.read-more-link', function (e) {
                    e.preventDefault();
                    var $row = $(this).closest('td');
                    $row.find('.desc-short, .desc-full').toggleClass('d-none');
                    $(this).text($(this).text() === 'Read more' ? 'Show less' : 'Read more');
                })
                .on('click', '.view-attachments-btn', function () {
                    const attachments = $(this).data('attachments');
                    const attachmentsList = $('#attachmentsList');
                    attachmentsList.empty(); 

                    if (attachments && attachments.length > 0) {
                        attachments.forEach(attachment => {
                            const fullFileName = attachment.file_path.split('/').pop();
                            const parts = fullFileName.split('_');
                            let originalFileName = fullFileName;
                            if (parts.length > 1 && /^[0-9a-f]+$/.test(parts[0])) { 
                                originalFileName = parts.slice(1).join('_');
                            }
                            const fileExtension = originalFileName.split('.').pop().toLowerCase();
                            let previewHtml = '';

                            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                                previewHtml = `<img src="/storage/${attachment.file_path}" alt="${originalFileName}" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">`;
                            } else if (fileExtension === 'pdf') {
                                previewHtml = `<i class="bi bi-file-earmark-pdf fs-4 me-2"></i>`;
                            } else {
                                previewHtml = `<i class="bi bi-file-earmark fs-4 me-2"></i>`;
                            }

                            attachmentsList.append(`
                                <a href="/storage/${attachment.file_path}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center">
                                    ${previewHtml}
                                    ${originalFileName}
                                    <i class="bi bi-box-arrow-up-right ms-auto"></i>
                                </a>
                            `);
                        });
                    } else {
                        attachmentsList.append('<p class="text-muted">No attachments found.</p>');
                    }

                    $('#viewAttachmentsModal').modal('show');
                });

            function fetchDepartmentsForDropdown() {
                fetch("{{ route('admin.departments') }}")
                    .then(response => response.json())
                    .then(departments => {
                        let options = '<option value="">Select Department</option>';
                        departments.forEach(dept => {
                            options += `<option value="${dept.id}">${dept.name}</option>`;
                        });
                        $('#createUserDepartment, #editUserDepartment').html(options);
                    })
                    .catch(error => console.error('Error fetching departments:', error));
            }

            fetchDepartmentsForDropdown();
        });
    </script>
@endsection
