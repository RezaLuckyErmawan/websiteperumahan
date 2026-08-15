<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    #userTable thead th {
        background-color: #eef6f8;
        color: #203246;
        text-align: center;
    }

    .user-popover {
        min-width: 180px;
        padding: 2px 0;
    }

    .user-popover-row {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-bottom: 10px;
    }

    .user-popover-row span {
        color: #647084;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .user-popover-row strong {
        color: #172033;
        font-size: 13px;
        font-weight: 700;
    }

    .user-popover-logout {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
        padding-top: 10px;
        border-top: 1px solid #e4e8ef;
        color: #dc2626;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
    }

    .user-popover-logout:hover {
        color: #b91c1c;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<table id="userTable" class="display table table-striped table-bordered w-100">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Modal Tambah User -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); simpanForm();">
                <div class="modal-body">
                    <input type="hidden" name="id">

                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-control" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="mandor">Mandor</option>
                            <option value="spv">SPV</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); updateForm();">
                <div class="modal-body">
                    <input type="hidden" name="id">

                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control" name="password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-control" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="mandor">Mandor</option>
                            <option value="spv">SPV</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content bg-success text-black">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="successModalLabel">✔️ Berhasil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p id="successMessage">Data berhasil disimpan!</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah kamu yakin ingin menghapus user ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        ajax: '/data-user/json',
        columns: [
            { data: 'id' },
            { data: 'username' },
            { data: 'nama' },
            {
                data: 'role',
                render: function (data) {
                    const roleLabels = {
                        'admin': 'Admin',
                        'mandor': 'Mandor',
                        'spv': 'SPV',
                        'customer': 'Customer',
                        'owner': 'Owner'
                    };
                    return roleLabels[data] || data;
                }
            },
            {
                data: 'id',
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-primary" onclick="editData(${data})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="hapusData(${data})">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    `;
                },
                orderable: false,
                searchable: false
            }
        ]
    });

    $('#userTable_length').html(`
        <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah User
        </button>
    `);
});

function openCreateForm() {
    $('#modalForm form')[0].reset();
    $('#modalForm input[name=id]').val('');
    $('#modalFormLabel').text('Tambah User');
    $('#modalForm').modal('show');
}

function editData(id) {
    $.get(`/data-user/edit/${id}`, function (data) {
        $('#modalEdit input[name=id]').val(data.id);
        $('#modalEdit input[name=username]').val(data.username);
        $('#modalEdit input[name=nama]').val(data.nama);
        $('#modalEdit select[name=role]').val(data.role);
        $('#modalEditLabel').text('Edit User');
        $('#modalEdit').modal('show');
    });
}

let idToDelete = null;

function hapusData(id) {
    idToDelete = id;
    $('#confirmDeleteModal').modal('show');
}

$('#confirmDeleteBtn').on('click', function () {
    if (idToDelete) {
        $.ajax({
            url: `/data-user/delete/${idToDelete}`,
            type: 'DELETE',
            success: function () {
                $('#confirmDeleteModal').modal('hide');
                $('#userTable').DataTable().ajax.reload();
                showSuccess('User berhasil dihapus');
            },
            error: function () {
                alert('Gagal menghapus user');
            }
        });
    }
});

function simpanForm() {
    let formData = new FormData($('#modalForm form')[0]);

    $.ajax({
        url: '/data-user/store',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
            $('#modalForm').modal('hide');
            $('#userTable').DataTable().ajax.reload();
            showSuccess('User berhasil ditambahkan');
        },
        error: function (xhr) {
            let errorMessage = 'Terjadi kesalahan';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
}

function updateForm() {
    let id = $('#modalEdit input[name=id]').val();
    let formData = new FormData($('#modalEdit form')[0]);

    $.ajax({
        url: `/data-user/update/${id}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
            $('#modalEdit').modal('hide');
            $('#userTable').DataTable().ajax.reload();
            showSuccess('User berhasil diperbarui');
        },
        error: function (xhr) {
            let errorMessage = 'Terjadi kesalahan';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
}
</script>
<?= $this->endSection() ?>
