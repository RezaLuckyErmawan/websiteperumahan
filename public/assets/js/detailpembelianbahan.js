let idToDelete = null;

$(document).ready(function () {
  const table = $('#detailPembelianTable').DataTable({
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
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

  $('#btnDeleteConfirm').click(function () {
    if (idToDelete) {
      $.ajax({
        url: `/detail-pembelian-bahan/delete/${idToDelete}`,
        type: 'GET',
        success: function () {
          bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmDeleteModal')).hide();
          showSuccess('Data berhasil dihapus!');
          setTimeout(() => window.location.reload(), 1200);
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
      success: function () {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetailForm')).hide();
        showSuccess(id ? 'Data berhasil diperbarui!' : 'Data berhasil ditambahkan!');
        setTimeout(() => window.location.reload(), 1200);
      },
      error: function (xhr) {
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
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetailForm')).show();
  });
}

function hapusData(id) {
  idToDelete = id;
  bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmDeleteModal')).show();
}
