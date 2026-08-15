$('#dataRumahTable').DataTable({
  processing: true,
  serverSide: true,
  pageLength: 5,
  lengthMenu: [5, 10, 25, 50],
  ajax: '/data-rumah/json',
  columns: [
    { data: 'kode_rumah' },
    {
      data: 'gambar',
      render: function (data) {
        if (data) {
          return `<img src="/${data}" alt="Gambar" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onclick="lihatGambar('${data}')">`;
        }
        return '<span class="text-muted">-</span>';
      },
      orderable: false,
      searchable: false
    },
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
    {
      data: 'dokumen',
      render: function (data) {
        if (data) {
          return `
            <a href="/${data}" target="_blank" class="btn btn-sm btn-info" title="Download Dokumen">
              <i class="fas fa-file-alt"></i> Download
            </a>`;
        }
        return '<span class="text-muted">-</span>';
      },
      orderable: false,
      searchable: false
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
    $('#modalForm textarea[name=deskripsi]').val(data.deskripsi || '');

    // Handle existing image
    $('#modalForm input[name=existing_gambar]').val(data.gambar || '');
    if (data.gambar) {
      $('#previewImg').attr('src', '/' + data.gambar);
      $('#imagePreview').show();
    } else {
      $('#imagePreview').hide();
    }

    // Handle existing document
    $('#modalForm input[name=existing_dokumen]').val(data.dokumen || '');
    if (data.dokumen) {
      const fileName = data.dokumen.split('/').pop();
      $('#dokumenName').text(fileName);
      $('#dokumenLink').attr('href', '/' + data.dokumen);
      $('#dokumenInfo').show();
    } else {
      $('#dokumenInfo').hide();
    }

    $('#modalFormLabel').text('Edit Data Rumah');
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
    $('#modalForm input[name=existing_gambar]').val('');
    $('#modalForm input[name=existing_dokumen]').val('');
    $('#modalForm textarea[name=deskripsi]').val('');
    $('#imagePreview').hide();
    $('#previewImg').attr('src', '');
    $('#dokumenInfo').hide();
    $('#dokumenName').text('');
    $('#dokumenLink').attr('href', '#');
    $('#modalFormLabel').text('Tambah Data Rumah');
    $('#modalForm').modal('show');
}


function simpanForm() {
  let id = $('#modalForm input[name=id]').val();
  let url = id ? `/data-rumah/update/${id}` : `/data-rumah/store`;

  // Use FormData for file upload
  let formData = new FormData($('#modalForm form')[0]);

  $.ajax({
    url: url,
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      $('#modalForm').modal('hide');
      $('#dataRumahTable').DataTable().ajax.reload();
      showSuccess(id ? 'Data berhasil diperbarui!' : 'Data berhasil ditambahkan!');
    },
    error: function (xhr) {
      let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = xhr.responseJSON.message;
      }
      alert(errorMessage);
    }
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

function previewImage(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      $('#previewImg').attr('src', e.target.result);
      $('#imagePreview').show();
    };
    reader.readAsDataURL(file);
  } else {
    $('#imagePreview').hide();
  }
}

function removeImage() {
  $('#modalForm input[name=gambar]').val('');
  $('#modalForm input[name=existing_gambar]').val('');
  $('#imagePreview').hide();
  $('#previewImg').attr('src', '');
}

function removeDokumen() {
  $('#modalForm input[name=dokumen]').val('');
  $('#modalForm input[name=existing_dokumen]').val('');
  $('#dokumenInfo').hide();
  $('#dokumenName').text('');
  $('#dokumenLink').attr('href', '#');
}

function lihatGambar(gambarPath) {
  $('#gambarPreview').attr('src', '/' + gambarPath);
  const modal = new bootstrap.Modal(document.getElementById('lihatGambarModal'));
  modal.show();
}

