<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    #mandorTable thead th {
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
<table id="mandorTable" class="display table table-striped table-bordered w-100">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Username</th>
            <th>Email</th>
            <th>No. Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Modal Tambah Mandor -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">Tambah Mandor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); simpanForm();">
                <div class="modal-body">
                    <input type="hidden" name="id">

                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" class="form-control" name="no_telepon">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3"></textarea>
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

<!-- Modal Edit Mandor -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit Mandor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="event.preventDefault(); updateForm();">
                <div class="modal-body">
                    <input type="hidden" name="id">

                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control" name="password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" class="form-control" name="no_telepon">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3"></textarea>
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
                Apakah kamu yakin ingin menghapus data mandor ini?
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
    $('#mandorTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        ajax: '/data-mandor/json',
        columns: [
            { data: 'nama' },
            { data: 'username' },
            { data: 'email' },
            { data: 'no_telepon' },
            { data: 'alamat' },
            {
                data: 'user_id',
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

    $('#mandorTable_length').html(`
        <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Mandor
        </button>
    `);
});

function openCreateForm() {
    $('#modalForm form')[0].reset();
    $('#modalForm input[name=id]').val('');
    $('#modalFormLabel').text('Tambah Mandor');
    $('#modalForm').modal('show');
}

function editData(id) {
    $.get(`/data-mandor/edit/${id}`, function (data) {
        $('#modalEdit input[name=id]').val(data.user_id);
        $('#modalEdit input[name=nama]').val(data.nama);
        $('#modalEdit input[name=username]').val(data.username);
        $('#modalEdit input[name=email]').val(data.email || '');
        $('#modalEdit input[name=no_telepon]').val(data.no_telepon || '');
        $('#modalEdit textarea[name=alamat]').val(data.alamat || '');
        $('#modalEditLabel').text('Edit Mandor');
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
            url: `/data-mandor/delete/${idToDelete}`,
            type: 'DELETE',
            success: function () {
                $('#confirmDeleteModal').modal('hide');
                $('#mandorTable').DataTable().ajax.reload();
                showSuccess('Data mandor berhasil dihapus');
            },
            error: function () {
                alert('Gagal menghapus data mandor');
            }
        });
    }
});

function simpanForm() {
    let formData = new FormData($('#modalForm form')[0]);

    $.ajax({
        url: '/data-mandor/store',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
            $('#modalForm').modal('hide');
            $('#mandorTable').DataTable().ajax.reload();
            showSuccess('Data mandor berhasil ditambahkan');
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
        url: `/data-mandor/update/${id}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function () {
            $('#modalEdit').modal('hide');
            $('#mandorTable').DataTable().ajax.reload();
            showSuccess('Data mandor berhasil diperbarui');
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
