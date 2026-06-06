$('#dataRumahTable').DataTable({
  processing: true,
  serverSide: true,
  pageLength: 5,
  lengthMenu: [5, 10, 25, 50],
  ajax: '/data-rumah/json',
  columns: [
    { data: 'kode_rumah' },
    { data: 'lokasi' },
    { data: 'tipe' },
    { data: 'luas_tanah' },
    { data: 'luas_bangunan' },
    { 
      data: 'harga',
      render: function (data) {
        return 'Rp ' + parseInt(data).toLocaleString('id-ID');
      }
    },
    { data: 'status',
        render: function (data) {
        const className = (data || '').toLowerCase().replace(/\s+/g, '-');
        return `<span class="status-label ${className}">${data}</span>`;
  }
},

    { data: 'id',
      render: function (data, type, row) {
        return `
          <button class="btn btn-sm btn-primary" onclick="editData(${data})">
            <i class="fas fa-edit"></i> Edit
          </button>
          <button class="btn btn-sm btn-danger" onclick="hapusData(${data})">
            <i class="fas fa-trash"></i> Hapus
          </button>
         
            <a href="javascript:void(0);" class="btn btn-sm btn-warning" onclick="lihatBahan(${row.id})">
            <i class="fas fa-box-open"></i> Lihat Bahan
            </a>
        `;
      },
      orderable: false,
      searchable: false
    }
  ],

  initComplete: function() {
     $('#dataRumahTable_length')
    .html(`
      <button type="button" onclick="openCreateForm()" class="add-btn1">
        <i class="fas fa-plus"></i> Tambah Data
      </button>
    `);

     $('#dataRumahTable_filter input')
    .attr('placeholder', 'Cari berdasarkan kode, lokasi, tipe, status...')
    .addClass('form-control form-control-sm ms-2')
    .css({
        'display': 'inline-block',
        'width': '300px',
        'margin-left': '10px'
    });
    $('#dataRumahTable_filter label').contents().filter(function () {
    return this.nodeType === 3;
    }).remove();

    $('#dataRumahTable_filter label')
    .prepend('<i class=" text-primary me-2"></i>');
  }
});
   


function editData(id) {
  $.get(`/data-rumah/edit/${id}`, function (data) {
    $('#modalForm input[name=id]').val(data.id);
    $('#modalForm input[name=kode_rumah]').val(data.kode_rumah);
    $('#modalForm textarea[name=lokasi]').val(data.lokasi);
    $('#modalForm input[name=tipe]').val(data.tipe);
    $('#modalForm input[name=luas_tanah]').val(data.luas_tanah);
    $('#modalForm input[name=luas_bangunan]').val(data.luas_bangunan);
    $('#modalForm input[name=harga]').val(data.harga);
    $('#modalForm select[name=status]').val(data.status);
    // Lanjutkan untuk field lain...
    $('#modalForm').modal('show');
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
        url: `/data-rumah/delete/${idToDelete}`,
        type: 'DELETE',
        success: function () {
            $('#confirmDeleteModal').modal('hide');
            $('#dataRumahTable').DataTable().ajax.reload();
            showSuccess('Data Berhasil di hapus')
        },
        error: function () {
            alert('Gagal menghapus data');
        }
        });
    }
    });


function openCreateForm() {
    $('#modalForm form')[0].reset(); // perbaikan di sini
    $('#modalForm input[name=id]').val('');
    $('#modalFormLabel').text('Tambah Data Rumah');
    $('#modalForm').modal('show');
}


function simpanForm() {
  let id = $('#modalForm input[name=id]').val();
  let url = id ? `/data-rumah/update/${id}` : `/data-rumah/store`;

  $.post(url, $('#modalForm form').serialize(), function () {
    $('#modalForm').modal('hide');
    $('#dataRumahTable').DataTable().ajax.reload();
    showSuccess(id ? 'Data berhasil diperbarui!' : 'Data berhasil ditambahkan!');
  });
}


function lihatBahan(id) {
  $('#lihatBahanContent').html('<p>Loading Data...</p>');
  $.get(`/bahan-pembangunan/rumah-detail/${id}`, function (html) {
    $('#lihatBahanContent').html(html);
    const modal = new bootstrap.Modal(document.getElementById('lihatBahanModal'));
    modal.show();
  }).fail(function () {
    $('#lihatBahanContent').html('<div class="alert alert-danger">Gagal Memuat Data.</div>');
  });
}

