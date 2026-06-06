let idToDelete = null;

$(document).ready(function () {
  const table = $('#bahanPembangunanTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25 , 50],
    ajax: '/data-bahan-pembangunan/json',
    columns: [
      { data: 'kode_rumah' },
      { data: 'nama_bahan' },
      { data: 'jumlah_pemakaian' },
      { data: 'tanggal_penggunaan' },
      { data: 'keterangan' },
      {
        data: 'id',
        render: (data) => `
          <button class="btn btn-sm btn-primary" onclick="editData(${data})">
          <i class="fas fa-edit"></i> Edit
          </button>
          <button class="btn btn-sm btn-danger" onclick="hapusData(${data})"> 
          <i class="fas fa-trash"></i> Hapus</button>
        `,
        orderable: false,
        searchable: false
      }
    ],
    initComplete: function () {
            $('#bahanPembangunanTable_length')
        .html(`
          <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        `);

$('#bahanPembangunanTable_filter input')
        .attr('placeholder', 'Cari bahan, rumah...')
        .addClass('form-control form-control-sm ms-2')
        .css({ 'width': '300px', 'margin-left': '10px' });

      $('#bahanPembangunanTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();
    }
  });

  $('#formBahanPembangunan').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const id = form.find('input[name="id"]').val();
    const url = id
      ? `/bahan-pembangunan/update/${id}`
      : `/bahan-pembangunan/store`;

    $.ajax({
      url: url,
      method: 'POST',
      data: form.serialize(),
      success: function () {
        $('#modalForm').modal('hide');
        table.ajax.reload();
        showSuccess(id ? 'Data berhasil diperbarui!' : 'Data berhasil ditambahkan!');
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        alert('Terjadi kesalahan saat menyimpan data!');
      }
    });
  });

  $('#btnDeleteConfirm').click(function () {
    if (idToDelete) {
      $.ajax({
        url: `/bahan-pembangunan/rumah/delete/${idToDelete}`,
        type: 'GET',
        success: function () {
          $('#modalConfirmDelete').modal('hide');
          table.ajax.reload();
          showSuccess('Data berhasil dihapus!');
        }
      });
    }
  });
});

function openCreateForm() {
  $('#formBahanPembangunan')[0].reset();
  $('#formBahanPembangunan input[name="id"]').val('');
  $('#modalFormLabel').text('Form Tambah Bahan Pembangunan Rumah');
  new bootstrap.Modal(document.getElementById('modalForm')).show();
}

function editData(id) {
  $.get(`/bahan-pembangunan/edit/${id}`, function (res) {
    if (!res.bahan) {
      alert('Data tidak ditemukan!');
      return;
    }
    const bahan = res.bahan;
    $('#formBahanPembangunan input[name="id"]').val(bahan.id);
    $('#formBahanPembangunan select[name="perumahan_id"]').val(bahan.perumahan_id);
    $('#formBahanPembangunan select[name="bahan_bangunan_id"]').val(bahan.bahan_bangunan_id);
    $('#formBahanPembangunan input[name="jumlah_pemakaian"]').val(bahan.jumlah_pemakaian);
    $('#formBahanPembangunan input[name="tanggal_penggunaan"]').val(bahan.tanggal_penggunaan);
    $('#formBahanPembangunan input[name="keterangan"]').val(bahan.keterangan);

    $('#modalFormLabel').text('Form Edit Bahan Pembangunan Rumah');
    $('#modalForm').modal('show');
  });
}

function hapusData(id) {
  idToDelete = id;
  $('#modalConfirmDelete').modal('show');
}


function showSuccess(message) {
  $('#successMessage').text(message); 
  const modal = new bootstrap.Modal(document.getElementById('successmodal'));
  modal.show();
}


// function showSuccess(message) {
//   const alert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
//       ${message}
//       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//     </div>`;
//   $('.content1').prepend(alert);
// }
