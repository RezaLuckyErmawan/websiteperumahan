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
        const list = parseGambar(data);
        if (!list.length) {
          return '<span class="text-muted">-</span>';
        }
        const first = toGambarUrl(list[0]);
        const badge = list.length > 1 ? `<span class="tabel-gambar-count">${list.length}</span>` : '';
        return `<span class="tabel-gambar" data-images="${encodeURIComponent(JSON.stringify(list))}" onclick="lihatGambar(this)" title="Lihat gambar">
          <img src="${first}" alt="Gambar">${badge}
        </span>`;
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

          <!--
          <a href="javascript:void(0);" class="btn btn-sm btn-warning" onclick="lihatBahan(${row.id})">
              <i class="fas fa-box-open"></i> Lihat Bahan
          </a>
          -->
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
    existingGambar = parseGambar(data.gambar);
    resetGambarInputs();
    renderGambarList();

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
    $('#modalForm input[name=existing_gambar]').val('[]');
    $('#modalForm input[name=existing_dokumen]').val('');
    $('#modalForm textarea[name=deskripsi]').val('');
    existingGambar = [];
    resetGambarInputs();
    renderGambarList();
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

let existingGambar = [];
let lihatGambarList = [];
let lihatGambarIndex = 0;
const MAX_GAMBAR = 8;

function gambarInputHtml() {
  return `<div class="gambar-input-row">
    <input type="file" class="form-control" name="gambar[]" accept="image/jpeg,image/jpg,image/png" onchange="previewImage()">
    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeGambarInput(this)" title="Hapus input">&times;</button>
  </div>`;
}

function resetGambarInputs() {
  if (existingGambar.length >= MAX_GAMBAR) {
    $('#gambarInputs').empty();
  } else {
    $('#gambarInputs').html(gambarInputHtml());
  }
  updateGambarAddBtn();
}

function addGambarInput() {
  if (jumlahSlotGambar() >= MAX_GAMBAR) {
    alert('Maksimal 8 foto.');
    return;
  }
  $('#gambarInputs').append(gambarInputHtml());
  updateGambarAddBtn();
}

function removeGambarInput(btn) {
  const rows = $('#gambarInputs .gambar-input-row');
  if (rows.length <= 1) {
    rows.find('input').val('');
  } else {
    $(btn).closest('.gambar-input-row').remove();
  }
  previewImage();
  updateGambarAddBtn();
}

function jumlahSlotGambar() {
  return existingGambar.length + $('#gambarInputs input[type=file]').length;
}

function adaInputGambarKosong() {
  const inputs = $('#gambarInputs input[type=file]');
  if (!inputs.length) return false;
  let kosong = false;
  inputs.each(function () {
    if (!(this.files && this.files.length)) kosong = true;
  });
  return kosong;
}

function updateGambarAddBtn() {
  const penuh = jumlahSlotGambar() >= MAX_GAMBAR;
  $('#tambahGambarBtn').toggle(!penuh && !adaInputGambarKosong());
}

function parseGambar(raw) {
  if (!raw) return [];
  if (Array.isArray(raw)) return raw.map(String).map((item) => item.trim()).filter(Boolean);
  try {
    const decoded = JSON.parse(raw);
    if (Array.isArray(decoded)) {
      return decoded.map(String).map((item) => item.trim()).filter(Boolean);
    }
  } catch (e) {}
  return [String(raw).trim()].filter(Boolean);
}

function toGambarUrl(path) {
  if (!path) return '';
  if (/^https?:\/\//i.test(path)) return path;
  return '/' + String(path).replace(/^\/+/, '');
}

function renderGambarList() {
  const wrap = $('#gambarList');
  wrap.empty();
  existingGambar.forEach((path, idx) => {
    wrap.append(`
      <div class="gambar-thumb">
        <img src="${toGambarUrl(path)}" alt="Gambar ${idx + 1}">
        <button type="button" onclick="removeExistingGambar(${idx})">&times;</button>
      </div>
    `);
  });
  $('#existingGambar').val(JSON.stringify(existingGambar));
}

function removeExistingGambar(index) {
  existingGambar.splice(index, 1);
  renderGambarList();
  previewSelectedFiles();
  updateGambarAddBtn();
}

function previewImage() {
  renderGambarList();
  previewSelectedFiles();
  updateGambarAddBtn();
}

function previewSelectedFiles() {
  $('#gambarInputs input[type=file]').each(function () {
    const file = this.files && this.files[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    $('#gambarList').append(`
      <div class="gambar-thumb">
        <img src="${url}" alt="Gambar baru">
        <span class="thumb-label">baru</span>
      </div>
    `);
  });
}

function lihatGambar(el) {
  try {
    lihatGambarList = JSON.parse(decodeURIComponent(el.getAttribute('data-images') || '%5B%5D'));
  } catch (e) {
    lihatGambarList = [];
  }
  lihatGambarIndex = 0;
  tampilkanGambarModal();
  $('#imgPreviewOverlay').addClass('open');
  document.body.style.overflow = 'hidden';
}

function tutupGambarPreview() {
  $('#imgPreviewOverlay').removeClass('open');
  document.body.style.overflow = '';
}

function tampilkanGambarModal() {
  if (!lihatGambarList.length) return;
  $('#gambarPreview').attr('src', toGambarUrl(lihatGambarList[lihatGambarIndex]));
  $('#gambarCounter').text(`${lihatGambarIndex + 1} / ${lihatGambarList.length}`);
  const banyak = lihatGambarList.length > 1;
  $('#gambarPrevBtn, #gambarNextBtn').toggle(banyak);
}

function geserGambar(step) {
  if (!lihatGambarList.length) return;
  lihatGambarIndex = (lihatGambarIndex + step + lihatGambarList.length) % lihatGambarList.length;
  tampilkanGambarModal();
}

$('#gambarCloseBtn').on('click', tutupGambarPreview);
$('#imgPreviewOverlay').on('click', function (e) {
  if (e.target === this) tutupGambarPreview();
});
$(document).on('keydown', function (e) {
  if (!$('#imgPreviewOverlay').hasClass('open')) return;
  if (e.key === 'Escape') tutupGambarPreview();
  if (e.key === 'ArrowLeft') geserGambar(-1);
  if (e.key === 'ArrowRight') geserGambar(1);
});

function removeDokumen() {
  $('#modalForm input[name=dokumen]').val('');
  $('#modalForm input[name=existing_dokumen]').val('');
  $('#dokumenInfo').hide();
  $('#dokumenName').text('');
  $('#dokumenLink').attr('href', '#');
}

