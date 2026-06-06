let idToDelete = null;

$(document).ready(function () {
  $('#pembelianTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 15, 20, 25],
    ajax: '/data-pembelian-bahan/json',
    columns: [
      { data: 'nomor_nota' },
      { data: 'tanggal' },
      { data: 'supplier' },
      {
        data: 'total_harga',
        render: data => 'Rp ' + parseInt(data).toLocaleString('id-ID')
      },
      {
        data: 'id',
        render: (data) => `
        
          <button class="btn btn-primary btn-sm" onclick="editData(${data})">
          <i class="fas fa-edit"></i> Edit</button>
          <button class="btn btn-danger btn-sm" onclick="hapusData(${data})">
          <i class="fas fa-trash"></i> Hapus</button>
        `,
        orderable: false,
        searchable: false
      }
    ],
    initComplete: function () {
            $('#pembelianTable_length')
        .html(`
          <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        `);

$('#pembelianTable_filter input')
        .attr('placeholder', 'Cari nomor nota, supplier, atau harga...')
        .addClass('form-control form-control-sm ms-2')
        .css({
          'display': 'inline-block',
          'width': '300px',
          'margin-left': '10px'
        });

      $('#pembelianTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();

      $('#pembelianTable_filter label')
        .prepend('<i class="text-primary me-2"></i>');
    }
  });
});

function openCreateForm() {
  $('#modalForm form')[0].reset();
  $('#modalForm input[name=id]').val('');
  $('#modalFormLabel').text('Tambah Pembelian');
  new bootstrap.Modal(document.getElementById('modalForm')).show();
}

function editData(id) {
  $.get(`/data-pembelian-bahan/edit/${id}`, function (data) {
    $('#modalForm input[name=id]').val(data.id);
    $('#modalForm input[name=nomor_nota]').val(data.nomor_nota);
    $('#modalForm input[name=tanggal]').val(data.tanggal);
    $('#modalForm input[name=supplier]').val(data.supplier);
    $('#modalForm input[name=total_harga]').val(data.total_harga);
    $('#modalFormLabel').text('Edit Pembelian');
    new bootstrap.Modal(document.getElementById('modalForm')).show();
  });
}

function simpanForm() {
  let id = $('#modalForm input[name=id]').val();
  let url = id ? `/data-pembelian-bahan/update/${id}` : `/data-pembelian-bahan/store`;

  $.post(url, $('#modalForm form').serialize(), function () {
    $('#modalForm').modal('hide');
    $('#pembelianTable').DataTable().ajax.reload();
    showSuccess(id ? 'Data berhasil di perbarui !' : 'Data Berhasil ditambahkan!');
  });
}

function hapusData(id) {
  idToDelete = id;
  new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
}

$('#confirmDeleteBtn').on('click', function () {
  if (idToDelete) {
    $.ajax({
      url: `/data-pembelian-bahan/delete/${idToDelete}`,
      type: 'DELETE',
      success: function () {
        $('#confirmDeleteModal').modal('hide');
        $('#pembelianTable').DataTable().ajax.reload();
        showSuccess('Data berhasil dihapus')
      },
      error: function () {
        alert('Gagal menghapus data');
      }
    });
  }
});
