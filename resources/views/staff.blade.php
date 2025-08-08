@extends('layouts.app')

@section('main')
    <div class="container mt-4">
        <div id="alert-container"></div>
        @if ($existingUpdate)
            <div class="alert alert-info" role="alert">
                You have already created an update. You can only have one active update at a time.
            </div>
        @endif
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Weekly Updates</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUpdateModal" @if ($existingUpdate) disabled title="You can only create one update." @endif>Create New Update</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="updates-table" class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Attachments</th>
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
        <div class="modal-dialog modal-dialog-centered">
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
                        <div class="mb-3">
                            <label class="form-label">Current Attachments</label>
                            <div id="currentAttachments" class="border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                                <!-- Attachments will be loaded here by JS -->
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editUpdateAttachment" class="form-label">Add New Attachments (Image or PDF)</label>
                            <input type="file" class="form-control" id="editUpdateAttachment" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf">
                            <div id="editAttachmentsPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
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
        <div class="modal-dialog modal-dialog-centered">
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

    <div class="modal fade" id="createUpdateModal" tabindex="-1" aria-labelledby="createUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="createUpdateForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUpdateModalLabel">Create New Update</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="createUpdateTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="createUpdateTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="createUpdateDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="createUpdateDescription" name="description" rows="4"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="createUpdateAttachment" class="form-label">Attachments (Image or PDF)</label>
                            <input type="file" class="form-control" id="createUpdateAttachment" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf">
                            <div id="createAttachmentsPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
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
                    {
                        data: 'attachments', className: 'text-center', render: (d, t, r) => {
                            if (!d || d.length === 0) return '';
                            let html = '';
                            d.forEach(attachment => {
                                html += `<a href="/storage/${attachment.file_path}" target="_blank" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-file-earmark"></i></a>`;
                            });
                            return html;
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

                    // Populate current attachments
                    const currentAttachmentsDiv = $('#currentAttachments');
                    currentAttachmentsDiv.empty();
                    if (data.attachments && data.attachments.length > 0) {
                        data.attachments.forEach(attachment => {
                            const fileName = attachment.file_path.split('/').pop();
                            const fileType = fileName.split('.').pop().toLowerCase() === 'pdf' ? 'application/pdf' : 'image';
                            let previewHtml = '';

                            if (fileType === 'image') {
                                previewHtml = `
                                    <div class="attachment-item position-relative border p-1 rounded" data-id="${attachment.id}">
                                        <img src="/storage/${attachment.file_path}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;" alt="${fileName}">
                                        <button type="button" class="btn-close position-absolute top-0 end-0 m-1" aria-label="Remove"></button>
                                    </div>
                                `;
                            } else if (fileType === 'application/pdf') {
                                previewHtml = `
                                    <div class="attachment-item position-relative border p-1 rounded" data-id="${attachment.id}">
                                        <div class="d-flex flex-column align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="bi bi-file-earmark-pdf fs-3"></i>
                                            <span class="text-truncate" style="max-width: 70px;">${fileName}</span>
                                        </div>
                                        <button type="button" class="btn-close position-absolute top-0 end-0 m-1" aria-label="Remove"></button>
                                    </div>
                                `;
                            }
                            currentAttachmentsDiv.append(previewHtml);
                        });
                    } else {
                        currentAttachmentsDiv.append('<p>No attachments.</p>');
                    }

                    // Clear new attachments input
                    $('#editUpdateAttachment').val('');
                    $('#editAttachmentsPreview').empty();

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
                const formData = new FormData();

                formData.append('title', $('#editUpdateTitle').val());
                formData.append('description', $('#editUpdateDescription').val());

                // Append attachments marked for deletion
                $('#currentAttachments .attachment-item.deleted').each(function() {
                    formData.append('delete_attachments[]', $(this).data('id'));
                });

                const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                let isValid = true;

                // Add new attachments
                const newAttachments = $('#editUpdateAttachment')[0].files;
                for (let i = 0; i < newAttachments.length; i++) {
                    const fileName = newAttachments[i].name;
                    const fileExtension = fileName.split('.').pop().toLowerCase();
                    if (!allowedExtensions.includes(fileExtension)) {
                        showAlert(`Invalid file type: ${fileName}. Only JPG, JPEG, PNG, and PDF files are allowed.`, 'danger');
                        isValid = false;
                        break;
                    }
                    formData.append('attachments[]', newAttachments[i]);
                }

                // Since FormData is used, we need to manually set the method to PUT
                formData.append('_method', 'PUT');

                fetch(`/updates/${id}` , {
                    method: 'POST', // Use POST for FormData with _method PUT
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
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

            $('#createUpdateForm').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData();

                formData.append('title', $('#createUpdateTitle').val());
                formData.append('description', $('#createUpdateDescription').val());

                const files = $('#createUpdateAttachment')[0].files;
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                let isValid = true;

                for (let i = 0; i < files.length; i++) {
                    const fileName = files[i].name;
                    const fileExtension = fileName.split('.').pop().toLowerCase();
                    if (!allowedExtensions.includes(fileExtension)) {
                        showAlert(`Invalid file type: ${fileName}. Only JPG, JPEG, PNG, and PDF files are allowed.`, 'danger');
                        isValid = false;
                        break;
                    }
                    formData.append('attachments[]', files[i]);
                }

                if (!isValid) {
                    return;
                }

                fetch("{{ route('updates.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                }).then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message) });
                    }
                    return response.json();
                })
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('createUpdateModal')).hide();
                    showAlert(data.message, 'success');
                    table.ajax.reload();
                }).catch(error => showAlert(error.message, 'danger'));
            });

            // Function to render attachment previews
            function renderAttachmentPreviews(files, previewContainer, isNew = true) {
                previewContainer.empty();
                if (files.length === 0) {
                    previewContainer.append('<p>No attachments selected.</p>');
                    return;
                }

                Array.from(files).forEach((file, index) => {
                    const fileName = file.name || file.file_path.split('/').pop();
                    const fileType = file.type || (fileName.split('.').pop().toLowerCase() === 'pdf' ? 'application/pdf' : 'image');
                    let previewHtml = '';

                    if (fileType.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const img = `<img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;" alt="${fileName}">`;
                            const item = $(`
                                <div class="attachment-item position-relative border p-1 rounded" data-file-index="${index}" data-id="${file.id || ''}">
                                    ${img}
                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-1" aria-label="Remove"></button>
                                </div>
                            `);
                            previewContainer.append(item);
                        };
                        reader.readAsDataURL(file);
                    } else if (fileType === 'application/pdf') {
                        previewHtml = `
                            <div class="attachment-item position-relative border p-1 rounded" data-file-index="${index}" data-id="${file.id || ''}">
                                <div class="d-flex flex-column align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="bi bi-file-earmark-pdf fs-3"></i>
                                    <span class="text-truncate" style="max-width: 70px;">${fileName}</span>
                                </div>
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-1" aria-label="Remove"></button>
                            </div>
                        `;
                        previewContainer.append(previewHtml);
                    } else {
                        previewHtml = `
                            <div class="attachment-item position-relative border p-1 rounded" data-file-index="${index}" data-id="${file.id || ''}">
                                <div class="d-flex flex-column align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="bi bi-file-earmark fs-3"></i>
                                    <span class="text-truncate" style="max-width: 70px;">${fileName}</span>
                                </div>
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-1" aria-label="Remove"></button>
                            </div>
                        `;
                        previewContainer.append(previewHtml);
                    }
                });
            }

            // Handle file input change for create modal
            $('#createUpdateAttachment').on('change', function() {
                const previewContainer = $('#createAttachmentsPreview');
                renderAttachmentPreviews(this.files, previewContainer);
            });

            // Handle removal of newly selected files for create modal
            $('#createAttachmentsPreview').on('click', '.btn-close', function() {
                const itemToRemove = $(this).closest('.attachment-item');
                const fileIndex = itemToRemove.data('file-index');
                const dataTransfer = new DataTransfer();
                const files = $('#createUpdateAttachment')[0].files;

                for (let i = 0; i < files.length; i++) {
                    if (i !== fileIndex) {
                        dataTransfer.items.add(files[i]);
                    }
                }
                $('#createUpdateAttachment')[0].files = dataTransfer.files;
                itemToRemove.remove();
                // Re-render previews to update data-file-index
                renderAttachmentPreviews($('#createUpdateAttachment')[0].files, $('#createAttachmentsPreview'));
            });

            // Handle file input change for edit modal (new attachments)
            $('#editUpdateAttachment').on('change', function() {
                const previewContainer = $('#editAttachmentsPreview');
                renderAttachmentPreviews(this.files, previewContainer);
            });

            // Handle removal of newly selected files for edit modal
            $('#editAttachmentsPreview').on('click', '.btn-close', function() {
                const itemToRemove = $(this).closest('.attachment-item');
                const fileIndex = itemToRemove.data('file-index');
                const dataTransfer = new DataTransfer();
                const files = $('#editUpdateAttachment')[0].files;

                for (let i = 0; i < files.length; i++) {
                    if (i !== fileIndex) {
                        dataTransfer.items.add(files[i]);
                    }
                }
                $('#editUpdateAttachment')[0].files = dataTransfer.files;
                itemToRemove.remove();
                // Re-render previews to update data-file-index
                renderAttachmentPreviews($('#editUpdateAttachment')[0].files, $('#editAttachmentsPreview'));
            });

            // Handle removal of existing attachments for edit modal
            $('#currentAttachments').on('click', '.btn-close', function() {
                const itemToRemove = $(this).closest('.attachment-item');
                itemToRemove.toggleClass('deleted'); // Mark for deletion
                if (itemToRemove.hasClass('deleted')) {
                    itemToRemove.css('opacity', '0.5'); // Visual cue for deletion
                } else {
                    itemToRemove.css('opacity', '1');
                }
            });

            // Clear attachments preview on modal close
            $('#createUpdateModal').on('hidden.bs.modal', function () {
                $('#createAttachmentsPreview').empty();
                $('#createUpdateAttachment').val('');
            });

            $('#editUpdateModal').on('hidden.bs.modal', function () {
                $('#editAttachmentsPreview').empty();
                $('#editUpdateAttachment').val('');
            });
        });
    </script>
@endsection