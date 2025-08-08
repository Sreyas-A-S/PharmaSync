@extends('layouts.app')

@section('main')
    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Staff Update Summary</h5>
            </div>
            <div class="card-body">
                <div class="row" id="employee-summary">
                    @forelse ($users as $user)
                        <div class="col-md-3 mb-3">
                            <div class="card text-center h-100 shadow-sm employee-card" data-user-id="{{ $user->id }}" style="cursor: pointer;">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $user->name }}</h6>
                                    <p class="card-text">Updates: <span class="badge bg-primary">{{ $updatesCount[$user->id] ?? 0 }}</span></p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p>No employees found in your department.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Department Updates</h5>
                <select class="form-select w-auto" id="employeeFilter">
                    <option value="all">All Employees</option>
                    @foreach ($users as $user)
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
                    { data: 'user.name', className: 'fw-semibold' },
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

            $('#employeeFilter').on('change', function() {
                departmentUpdatesTable.ajax.reload();
            });

            $('#employee-summary').on('click', '.employee-card', function() {
                const userId = $(this).data('user-id');
                $('#employeeFilter').val(userId).trigger('change');
            });

            $('#department-updates-table').on('click', '.read-more-link', function (e) {
                e.preventDefault();
                var $row = $(this).closest('td');
                $row.find('.desc-short, .desc-full').toggleClass('d-none');
                $(this).text($(this).text() === 'Read more' ? 'Show less' : 'Read more');
            });

            @if ($selectedUserId)
                $('#employeeFilter').val('{{ $selectedUserId }}').trigger('change');
            @endif
        });
    </script>
@endsection
