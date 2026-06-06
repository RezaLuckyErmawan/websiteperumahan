let idToDelete = null;

$(document).ready(function () {
  const table = $('#detailPembelianTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/detail-pembelian/json',
    columns: [
      { data: 'nomor_nota' },
      { data: 'nama_bahan' },
      { data: 'jumlah' },
      {
        data: 'harga_satuan',
        render: data => 'Rp ' + parseFloat(data).toLocaleString('id-ID')
      },
      {
        data: 'subtotal',
        render: data => 'Rp ' + parseFloat(data).toLocaleString('id-ID')
      },
      {
        data: 'id',
        render: (data) => `
          <button class="btn btn-sm btn-primary" onclick="editData(${data})">
          <i class="fas fa-edit"></i> Edit</button>
          <button class="btn btn-sm btn-danger" onclick="hapusData(${data})">
          <i class="fas fa-trash"></i> Hapus</button>
        `,
        orderable: false,
        searchable: false
      }
    ],
    initComplete: function () {
            $('#detailPembelianTable_length')
        .html(`
          <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        `);

$('#detailPembelianTable_filter input')
        .attr('placeholder', 'Cari nota, bahan, jumlah...')
        .addClass('form-control form-control-sm ms-2')
        .css({
          'display': 'inline-block',
          'width': '300px',
          'margin-left': '10px'
        });

      $('#detailPembelianTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();

      $('#detailPembelianTable_filter label')
        .prepend('<i class="text-primary me-2"></i>');
    }
  });
  

  $('#formDetailPembelian').submit(function (e) {
    const id = $('#id').val();
    const url = id ? `/detail-pembelian-bahan/update/${id}` : `/detail-pembelian-bahan/store`;

    $.post(url, $(this).serialize(), function () {
      $('#modalDetailForm').modal('hide');
      table.ajax.reload();
      showSuccess(id ? 'Data berhasil diperbarui!' : 'Data berhasil ditambahkan!');
    });
  });

  $('#btnDeleteConfirm').click(function () {
    if (idToDelete) {
      $.ajax({
        url: `/detail-pembelian-bahan/delete/${idToDelete}`,
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
  // Reset form
  $('#formDetailPembelian')[0].reset();
  $('#formDetailPembelian input[name="id"]').val('');

  // Reset select dropdown ke default
  $('#pembelian_id').val('');
  $('#bahan_bangunan_id').val('');

  // Ubah judul modal
  $('#modalDetailLabel').text('Tambah Detail Pembelian');

  // Tampilkan modal
  const modal = new bootstrap.Modal(document.getElementById('modalDetailForm'));
  modal.show();
}

$(document).ready(function () {
  $('#formDetailPembelian').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const id = $('#id').val();
    const url = id
      ? `/detail-pembelian/update/${id}`
      : `/detail-pembelian/store`;

    $.ajax({
      url: url,
      method: 'POST',
      data: form.serialize(),
      success: function (res) {
        $('#modalDetailForm').modal('hide');
        $('#detailPembelianTable').DataTable().ajax.reload();
        showSuccess(id ? 'Data berhasil diperbarui!' : 'Data berhasil ditambahkan!');
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        alert('Gagal menyimpan data!');
      }
    });
  });
});

function editData(id) {
  $.get(`/detail-pembelian/edit/${id}`, function (data) {
    $('#id').val(data.detail.id);
    $('#pembelian_id').val(data.detail.pembelian_id);
    $('#bahan_bangunan_id').val(data.detail.bahan_bangunan_id);
    $('#jumlah').val(data.detail.jumlah);
    $('#harga_satuan').val(data.detail.harga_satuan);
    $('#modalDetailLabel').text('Edit Detail Pembelian');
    $('#modalDetailForm').modal('show');
  });
}

function hapusData(id) {
  idToDelete = id;
  $('#modalConfirmDelete').modal('show');
}
