@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('sub-title', 'Manage officer accounts')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card-modern bg-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-2">
                        <i class="fas fa-users-cog me-2"></i> Officer Accounts
                    </h4>
                    <p class="text-muted mb-0">Manage officer accounts (Review Officers, Finance Officers, IT Officers)</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.admin.users.create') }}" class="btn btn-gradient me-2">
                        <i class="fas fa-plus-circle"></i> Add New
                    </a>
                    <button type="button" class="btn btn-gradient-success" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import"></i> Import CSV
                    </button>
                    <a href="{{ route('dashboard.admin.users.template') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-download"></i> Template
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-modern bg-white">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $user->name }}</span>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $roleColors = [
                                            'review_officer' => 'warning',
                                            'finance_officer' => 'info',
                                            'it_officer' => 'danger'
                                        ];
                                        $roleIcons = [
                                            'review_officer' => 'fa-clipboard-check',
                                            'finance_officer' => 'fa-coins',
                                            'it_officer' => 'fa-shield-alt'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">
                                        <i class="fas {{ $roleIcons[$user->role] ?? 'fa-user' }} me-1"></i>
                                        {{ str_replace('_', ' ', ucfirst($user->role)) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dashboard.admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id != auth()->id())
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('dashboard.admin.users.destroy', $user->id) }}" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('dashboard.admin.users.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-file-import me-2"></i>Import Users from CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Format:</strong> name, email, password, role
                        <br>
                        <small>Roles: review_officer, finance_officer, it_officer</small>
                        <br>
                        <a href="{{ route('dashboard.admin.users.template') }}" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                    <div class="mb-3">
                        <label for="csv_file" class="form-label fw-bold">CSV File</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,.txt" required>
                        <small class="text-muted">Max file size: 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
                <p><strong>User:</strong> <span id="deleteUserName"></span></p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete User</button>
            </div>
        </div>
    </div>
</div>

<style>
.btn-group .btn {
    margin: 0 2px;
    border-radius: 6px !important;
}
</style>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        pageLength: 10,
        language: {
            search: "<i class='fas fa-search'></i> Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            infoEmpty: "No users found",
            infoFiltered: "(filtered from _MAX_ total users)",
            zeroRecords: "No matching users found"
        }
    });
});

var deleteUserId = null;

function confirmDelete(userId, userName) {
    deleteUserId = userId;
    document.getElementById('deleteUserName').textContent = userName;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteUserId) {
        document.getElementById('delete-form-' + deleteUserId).submit();
    }
});
</script>
@endpush