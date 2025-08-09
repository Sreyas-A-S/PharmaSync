@extends('layouts.app')

@section('main')
    <div class="container mt-4">
        <h2 class="mb-4">Department Head Dashboard</h2>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Employee Updates Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($users as $user)
                        <div class="col-md-4 mb-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $user->name }}</h5>
                                    <p class="card-text">Updates Posted: {{ $updatesCount[$user->id] ?? 0 }}</p>
                                    <button class="btn btn-light view-employee-updates" data-user-id="{{ $user->id }}">View Updates</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Department Updates</h5>
                <select id="employeeFilter" class="form-select w-auto">
                    <option value="all">All Employees</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="department-updates-table" class="table table-bordered table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
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
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            const departmentUpdatesTable = $('#department-updates-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('department.updates') }}",
                    data: function (d) {
                        d.user_id = $('#employeeFilter').val();
                    },
                    dataSrc: ''
                },
                columns: [
                    { data: null, className: 'text-center', render: (d, t, r, m) => m.row + 1 },
                    { data: 'user.name', defaultContent: 'N/A' },
                    { data: 'title' },
                    {
                        data: 'description', className: 'text-wrap', render: (d, t) => {
                            const max = 80;
                            if (t === 'display' && d && d.length > max)
                                return `<span class="desc-short">${d.substr(0, max)}... </span><span class="desc-full d-none">${d}</span><a href="#" class="read-more-link ms-2">Read more</a>`;
                            return d;
                        }
                    },
                    { data: 'attachments', className: 'text-center', render: (d, t, r) => {
                            if (!d || d.length === 0) return '';
                            return `<button class="btn btn-sm btn-primary view-attachments-btn" data-attachments='${JSON.stringify(d)}'>View (${d.length})</button>`;
                        }
                    },
                    { data: 'created_at', className: 'text-nowrap', render: d => d ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '' },
                ],
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                dom: '<"row mb-2"f>rt<"row mt-2"i<"col-sm-6"p>>', 
            });

            $('#employeeFilter').on('change', function () {
                departmentUpdatesTable.ajax.reload();
            });

            $('.view-employee-updates').on('click', function() {
                const userId = $(this).data('user-id');
                $('#employeeFilter').val(userId).trigger('change');
            });

            $('#department-updates-table').on('click', '.read-more-link', function (e) {
                e.preventDefault();
                var $row = $(this).closest('td');
                $row.find('.desc-short, .desc-full').toggleClass('d-none');
                $(this).text($(this).text() === 'Read more' ? 'Show less' : 'Read more');
            });
        $('#department-updates-table').on('click', '.view-attachments-btn', function () {
                const attachments = $(this).data('attachments');
                const attachmentsList = $('#attachmentsList');
                attachmentsList.empty(); // Clear previous attachments

                if (attachments && attachments.length > 0) {
                    attachments.forEach(attachment => {
                        const fullFileName = attachment.file_path.split('/').pop();
                        const parts = fullFileName.split('_');
                        let originalFileName = fullFileName;
                        if (parts.length > 1 && /^[0-9a-f]+$/.test(parts[0])) { // Check if the first part is a unique ID
                            originalFileName = parts.slice(1).join('_'); // Reconstruct original name
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
        });
    </script>
    @include('modals.attachment_modals')
@endsection