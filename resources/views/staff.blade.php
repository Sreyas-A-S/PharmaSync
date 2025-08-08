@extends('layouts.app')

@section('main')
    <div class="container mt-4">
        <div id="alert-container"></div>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Weekly Updates</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="updates-table" class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUpdateModal" tabindex="-1" aria-labelledby="editUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editUpdateForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUpdateModalLabel">Edit Update</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editUpdateId" name="id">
                        <div class="mb-3">
                            <label for="editUpdateTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editUpdateTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUpdateDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editUpdateDescription" name="description" rows="4"
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteUpdateModal" tabindex="-1" aria-labelledby="deleteUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUpdateModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this update?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            let deleteUpdateId = null;

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

            console.log('DataTable initializing');
            const table = $('#updates-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: { url: "{{ route('updates') }}", dataSrc: '' },
                columns: [
                    { data: null, className: 'text-center', render: (d, t, r, m) => m.row + 1 },
                    { data: 'title', className: 'fw-semibold' },
                    {
                        data: 'description', className: 'text-wrap', render: (d, t) => {
                            const max = 80;
                            if (t === 'display' && d && d.length > max)
                                return `<span class="desc-short">${d.substr(0, max)}... </span><span class="desc-full d-none">${d}</span><a href="#" class="read-more-link ms-2">Read more</a>`;
                            return d;
                        }
                    },
                    { data: 'created_at', className: 'text-nowrap', render: d => d ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '' },
                    {
                        data: null, orderable: false, searchable: false, className: 'text-center', render: (d, t, r) =>
                            `<div class="d-flex justify-content-center"><button class="btn btn-sm btn-outline-primary me-1 edit-update" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></button>` +
                            `<button class="btn btn-sm btn-outline-danger delete-update" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></div>`
                    }
                ],
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                language: {
                    search: "<span class='me-2'>🔍</span>Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ updates",
                    infoEmpty: "No updates available",
                    zeroRecords: "No matching updates found",
                    paginate: { previous: "<", next: ">" }
                },
                dom: '<"row mb-2"<"col-sm-6"l><"col-sm-6 text-end"f>>rt<"row mt-2"<"col-sm-6"i><"col-sm-6"p>>',
                drawCallback: function () {
                    if (window.bootstrap && bootstrap.Tooltip) {
                        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (el) {
                            return new bootstrap.Tooltip(el);
                        });
                    }
                }
            });

            $('#updates-table')
                .on('click', '.read-more-link', function (e) {
                    e.preventDefault();
                    var $row = $(this).closest('td');
                    $row.find('.desc-short, .desc-full').toggleClass('d-none');
                    $(this).text($(this).text() === 'Read more' ? 'Show less' : 'Read more');
                })
                .on('click', '.edit-update', function () {
                    const row = $(this).closest('tr');
                    const data = table.row(row).data();
                    // Populate and show modal
                    $('#editUpdateId').val(data.id);
                    $('#editUpdateTitle').val(data.title);
                    $('#editUpdateDescription').val(data.description);
                    var modal = new bootstrap.Modal(document.getElementById('editUpdateModal'));
                    modal.show();
                })
                .on('click', '.delete-update', function () {
                    const row = $(this).closest('tr');
                    const data = table.row(row).data();
                    deleteUpdateId = data.id;
                    var modal = new bootstrap.Modal(document.getElementById('deleteUpdateModal'));
                    modal.show();
                });

            $('#confirmDeleteBtn').on('click', function () {
                fetch(`/updates/${deleteUpdateId}` , {
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
                    bootstrap.Modal.getInstance(document.getElementById('deleteUpdateModal')).hide();
                    showAlert(data.message, 'success');
                    table.ajax.reload();
                }).catch(error => showAlert(error.message, 'danger'));
            });


            $('#editUpdateForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editUpdateId').val();
                fetch(`/updates/${id}` , {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: $('#editUpdateTitle').val(),
                        description: $('#editUpdateDescription').val()
                    })
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message) });
                    }
                    return response.json();
                })
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('editUpdateModal')).hide();
                    showAlert(data.message, 'success');
                    table.ajax.reload();
                }).catch(error => showAlert(error.message, 'danger'));
            });
        });
    </script>
@endsection