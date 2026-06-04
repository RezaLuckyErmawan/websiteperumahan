$(document).ready(function() {
  $('#dataUserTable').DataTable({
    processing: true,
    serverSide: true,
    lengthMenu: [5, 10, 20, 25],
    ajax: '/data-user/json',
    columns: [
      { data: 'nama' },
      { data: 'username' },
      { data: 'role',
        render: function (data) {
        let style = '';
        switch (data.toLowerCase()) {
        case 'admin':
            style = 'background-color: #198754; color: white;';
            break;
        case 'karyawan':
            style = 'background-color: #0d6efd; color: white;';
            break;
        case 'mandor':
            style = 'background-color: #ffc107; color: black;';
            break;
          case 'spv':
            style = 'background-color: #ff4d07ff; color: white;';
            break;
        default:
            style = 'background-color: #6c757d; color: white;';
        }

        return `<span style="${style} padding: 6px 10px; font-size: 0.85rem; border-radius: 10px;">${data}</span>`;
    }
    },
      { data: 'status' },
      {
        data: 'id',
        render: function (data) {
          return `
            <button class="btn btn-sm btn-primary" onclick="editUser(${data})">
            <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn btn-sm btn-danger" onclick="hapusUser(${data})">
            <i class="fas fa-trash"></i> Hapus
            </button>
          `;
        },
        orderable: false,
        searchable: false
      }
    ]
  });
});

function openCreateForm() {
  $('#modalUserForm form')[0].reset();
  $('#modalUserForm input[name=id]').val('');
  $('#modalUserFormLabel').text('Tambah data User');
   $('.hint-password').hide();
  $('#modalUserForm').modal('show');
}

function simpanUser() {
  let id = $('#modalUserForm input[name=id]').val();
  let url = id ? `/user/update/${id}` : `/user/store`;
  let message = id ? 'Data user berhasil diperbarui!' : 'Data user berhasil ditambahkan!';

  $.post(url, $('#modalUserForm form').serialize(), function (res) {
    $('#modalUserForm').modal('hide');
    $('#dataUserTable').DataTable().ajax.reload();
    $('#successMessage').text(message);
    $('#successModal').modal('show');
    setTimeout(() => {
      $('#successModal').modal('hide');
    }, 2000);
  }).fail(function (xhr) {
    alert('Gagal menyimpan data user. Pastikan semua isian sudah benar.');
    console.error(xhr.responseText);
  });
}

function editUser(id) {
  $.get(`/ user/edit/${id}`, function (data) {
    $('#modalUserForm input[name=id]').val(data.id);
    $('#modalUserForm input[name=nama]').val(data.nama);
    $('#modalUserForm input[name=username]').val(data.username);
    $('#modalUserForm select[name=role]').val(data.role);
    $('#modalUserForm select[name=status]').val(data.status);
    $('#modalUserForm input[name=password]').val('');
    $('#modalUserFormLabel').text('Edit data User')
    $('.hint-password').show();
    $('#modalUserForm').modal('show');
  });
}


function hapusUser(id) {
  idToDelete = id;
  $('#confirmDeleteModal').modal('show');
}

$('#confirmDeleteBtn').click(function () {
  if (!idToDelete) return;

  $.ajax({
    url: `/user/delete/${idToDelete}`,
    type: 'DELETE',
    success: function () {
      $('#confirmDeleteModal').modal('hide');
      $('#dataUserTable').DataTable().ajax.reload();
      $('#successModal').modal('show');

      idToDelete = null;

      setTimeout(() => {
        $('#successModal').modal('hide');
      }, 2000);
    },
    error: function (xhr) {
      alert('Gagal menghapus data!');
      console.error(xhr.responseText);
    }
  });
});

