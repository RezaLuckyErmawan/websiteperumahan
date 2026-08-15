$(document).ready(function () {
  // Check if DataTables is loaded
  if (typeof $.fn.DataTable === 'undefined') {
    console.error('DataTables plugin is not loaded!');
    return;
  }

  const table = $('#pembelianRumahTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax:'/pembelian-rumah/json',
    columns: [
      { data: 'customer_nama' },
      { data: 'kode_rumah' },
      {
        data: 'tanggal_pembelian',
        render: function (data) {
            return data ? new Date(data).toLocaleDateString('id-ID') : '-';
        }
      },
      {
        data: 'harga_beli',
        render: function (data) {
            return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID');
        }
      },
      {
        data: 'total_bayar',
        render: function (data) {
            return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID');
        }
      },
      {
        data: 'sisa_bayar',
        render: function (data) {
            return 'Rp ' + parseInt(data || 0).toLocaleString('id-ID');
        }
      },
      {
        data: 'status_pembelian',
        render: function (data) {
          let style= '';
          let textColor= 'text-white';

          switch (String(data).toLowerCase()) {
            case 'lunas':
              style = 'background-color: #28a745;';
              break;
            case 'cicil':
              style = 'background-color: #17a2b8;';
              break;
            case 'dp':
              style = 'background-color: #ffc107;';
              break;
            case 'batal':
              style = 'background-color: #dc3545;';
              break;
            default:
              style = 'background-color: #6c757d';
          }
          return `<span class="badge ${textColor}" style="${style} padding: 8px 12px; font-size: 0.85rem; border-radius: 10px;">${data || '-'}</span>`;
        }
      },
      { data: 'metode_pembayaran',
        render: function (data) {
            return data || '-';
        }
      },
      {
        data: 'info_cicilan_tahun',
        defaultContent: '-',
        render: function (data) {
            return data || '-';
        }
      },
      {
        data: 'info_cicilan_ke',
        defaultContent: '-',
        render: function (data) {
            return data || '-';
        }
      },
      {
        data: 'info_cicilan_berikutnya',
        defaultContent: '-',
        render: function (data) {
            return data || '-';
        }
      },
      {
        data: 'id',
        render: (data) => `
          <button class="btn btn-sm btn-primary" onclick="editData(${data})"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger" onclick="hapusData(${data})"><i class="fas fa-trash"></i></button>
          <button class="btn btn-sm btn-secondary" onclick="detailData(${data})"><i class="fas fa-eye"></i></button>
          <button class="btn btn-sm btn-success" onclick="bukaPembayaran(${data})"><i class="fas fa-money-bill-wave"></i></button>
        `,
        orderable: false,
        searchable: false
      }
    ],
   initComplete: function () {
            $('#pembelianRumahTable_length')
        .html(`
          <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        `);

$('#pembelianRumahTable_filter input')
        .attr('placeholder', 'Cari bahan, rumah...')
        .addClass('form-control form-control-sm ms-2')
        .css({ 'width': '300px', 'margin-left': '10px' });

      $('#pembelianRumahTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();
    }
  });

  // Auto-fill harga when rumah is selected in create form
  $(document).on('change', '#rumahSelect', function() {
    const selectedOption = $(this).find('option:selected');
    const harga = selectedOption.data('harga');
    const status = selectedOption.data('status');

    if (harga) {
      $('#hargaBeli').val(harga);
    } else {
      $('#hargaBeli').val('');
    }

    // Show warning if rumah is sold
    if (status && status.toLowerCase() === 'terjual') {
      alert('Perhatian: Rumah ini sudah terjual!');
      $('#hargaBeli').prop('readonly', true);
    } else {
      $('#hargaBeli').prop('readonly', false);
    }

    toggleCicilanTahunField($('#modalForm form'));
  });

  // Auto-fill harga when rumah is selected in edit form
  $(document).on('change', '#modalEdit select[name="perumahan_id"]', function() {
    const selectedOption = $(this).find('option:selected');
    const harga = selectedOption.data('harga');

    if (harga) {
      $('#hargaBeliEdit').val(harga);
    } else {
      $('#hargaBeliEdit').val('');
    }

    toggleCicilanTahunField($('#modalEdit form'));
  });

  $(document).on('change input', 'select[name="metode_pembayaran"], input[name="lama_cicilan_tahun"], input[name="tanggal_cicilan"], input[name="tanggal_pembelian"], input[name="harga_beli"], select[name="status_pembelian"]', function() {
    const form = $(this).closest('form');
    toggleCicilanTahunField(form);
  });
});

function toggleCicilanTahunField(form) {
  const metode = form.find('select[name="metode_pembayaran"]').val();
  const field = form.find('.cicilan-tahun-field');
  const input = form.find('input[name="lama_cicilan_tahun"]');
  const tanggalCicilan = form.find('input[name="tanggal_cicilan"]');
  const isCicilan = metode === 'Cicilan Internal';

  field.toggle(isCicilan);
  input.prop('required', isCicilan);
  tanggalCicilan.prop('required', isCicilan);
  if (!isCicilan) {
    input.val('');
    tanggalCicilan.val('');
    form.find('input[name="info_jumlah_cicilan"]').val('');
    form.find('.cicilan-jatuh-tempo-hint').text('Pilih tanggal, misalnya 15. Cicilan jatuh tempo setiap tanggal 15 tiap bulan.');
    return;
  }

  updateCicilanInfoPreview(form);
}

function updateCicilanInfoPreview(form) {
  const tahun = parseInt(form.find('input[name="lama_cicilan_tahun"]').val(), 10) || 0;
  const harga = parseInt(form.find('input[name="harga_beli"]').val() || 0, 10);
  const tanggalCicilan = form.find('input[name="tanggal_cicilan"]').val();
  const totalCicilan = tahun > 0 ? tahun * 12 : 0;

  if (totalCicilan > 0 && harga > 0) {
    const nominal = Math.ceil(harga / totalCicilan);
    form.find('input[name="info_jumlah_cicilan"]').val('Rp ' + nominal.toLocaleString('id-ID') + ' / bulan');
  } else {
    form.find('input[name="info_jumlah_cicilan"]').val('');
  }

  let hint = 'Pilih tanggal, misalnya 15. Cicilan jatuh tempo setiap tanggal 15 tiap bulan.';
  if (tanggalCicilan) {
    const tgl = parseInt(String(tanggalCicilan).split('-')[2], 10);
    hint = `Cicilan jatuh tempo setiap tanggal ${tgl} tiap bulan.`;
  }
  form.find('.cicilan-jatuh-tempo-hint').text(hint);
}


function openCreateForm() {
  const form = $('#modalForm form');
  form[0].reset();
  form.find('input[name="id"]').val('');

  // Set default date to today
  const today = new Date().toISOString().split('T')[0];
  form.find('input[name="tanggal_pembelian"]').val(today);

  restoreSoldHouseOptions();
  toggleCicilanTahunField(form);
  $('#modalFormLabel').text('Form Pembelian Rumah');
  $('#modalForm').modal('show');
}

function bukaPembayaran(id) {
  window.location.href = `/pembayaran-rumah?pembelian_id=${id}`;
}

function detailData(id) {
  // Since there's no detail modal, redirect to detail page instead
  window.location.href = `/detail-pembelian-rumah/${id}`;
}


function simpanForm() {
  const form = $('#modalForm form');

  // Validasi required fields
  const customerId = form.find('select[name="customer_id"]').val();
  const perumahanId = form.find('select[name="perumahan_id"]').val();
  const tanggalPembelian = form.find('input[name="tanggal_pembelian"]').val();
  const metodePembayaran = form.find('select[name="metode_pembayaran"]').val();
  const statusDokumen = form.find('select[name="status_dokumen"]').val();
  const statusPembelian = form.find('select[name="status_pembelian"]').val();
  const lamaCicilan = form.find('input[name="lama_cicilan_tahun"]').val();

  if (!customerId || !perumahanId || !tanggalPembelian || !metodePembayaran || !statusDokumen || !statusPembelian) {
    alert('Mohon lengkapi semua field yang wajib diisi!');
    return;
  }

  if (metodePembayaran === 'Cicilan Internal') {
    const tahun = parseInt(lamaCicilan, 10);
    const tanggalCicilan = form.find('input[name="tanggal_cicilan"]').val();
    if (!tahun || tahun < 1 || tahun > 30) {
      alert('Lama cicilan wajib diisi 1-30 tahun untuk Cicilan Internal.');
      return;
    }
    if (!tanggalCicilan) {
      alert('Tanggal cicilan wajib diisi untuk Cicilan Internal.');
      return;
    }
  }

  const formData = form.serialize();
  const id = form.find('input[name="id"]').val();
  const isEdit = id !== '';

  const url = isEdit ? `/pembelian-rumah/update/${id}` : '/pembelian-rumah/store';

  $.ajax({
    url: url,
    method: 'POST',
    data: formData,
    success: function (res) {
      if (res.status === 'success') {
        $('#modalForm').modal('hide');
        $('#pembelianRumahTable').DataTable().ajax.reload();
        showSuccess(isEdit ? 'Data berhasil diperbarui!' : 'Data berhasil disimpan!');
        form[0].reset();
        form.find('input[name="id"]').val('');
        $('#modalFormLabel').text('Form Pembelian Rumah');
      } else {
        alert(res.message || 'Gagal menyimpan data.');
      }
    },
    error: function (xhr) {
      alert(xhr.responseJSON?.message || 'Terjadi kesalahan pada server.');
      console.log(xhr.responseText);
    }
  });
}

function updateForm() {
  const form = $('#modalEdit form');

  // Validasi required fields
  const customerId = form.find('select[name="customer_id"]').val();
  const perumahanId = form.find('select[name="perumahan_id"]').val();
  const tanggalPembelian = form.find('input[name="tanggal_pembelian"]').val();
  const metodePembayaran = form.find('select[name="metode_pembayaran"]').val();
  const statusDokumen = form.find('select[name="status_dokumen"]').val();
  const statusPembelian = form.find('select[name="status_pembelian"]').val();
  const lamaCicilan = form.find('input[name="lama_cicilan_tahun"]').val();

  if (!customerId || !perumahanId || !tanggalPembelian || !metodePembayaran || !statusDokumen || !statusPembelian) {
    alert('Mohon lengkapi semua field yang wajib diisi!');
    return;
  }

  if (metodePembayaran === 'Cicilan Internal') {
    const tahun = parseInt(lamaCicilan, 10);
    const tanggalCicilan = form.find('input[name="tanggal_cicilan"]').val();
    if (!tahun || tahun < 1 || tahun > 30) {
      alert('Lama cicilan wajib diisi 1-30 tahun untuk Cicilan Internal.');
      return;
    }
    if (!tanggalCicilan) {
      alert('Tanggal cicilan wajib diisi untuk Cicilan Internal.');
      return;
    }
  }

  const formData = form.serialize();
  const id = form.find('input[name="id"]').val();

  if (!id) {
    alert('ID tidak ditemukan!');
    return;
  }

  $.ajax({
    url: `/pembelian-rumah/update/${id}`,
    method: 'POST',
    data: formData,
    success: function (res) {
      if (res.status === 'success') {
        $('#modalEdit').modal('hide');
        $('#pembelianRumahTable').DataTable().ajax.reload();
        showSuccess('Data berhasil diperbarui!');
        form[0].reset();
        form.find('input[name="id"]').val('');
      } else {
        alert(res.message || 'Gagal menyimpan data.');
      }
    },
    error: function (xhr) {
      alert(xhr.responseJSON?.message || 'Terjadi kesalahan pada server.');
      console.log(xhr.responseText);
    }
  });
}

function restoreSoldHouseOptions() {
  // Disable options that are already sold
  $('#rumahSelect option, #modalEdit select[name="perumahan_id"] option').each(function() {
    const status = $(this).data('status');
    if (status && status.toLowerCase() === 'terjual') {
      $(this).prop('disabled', true);
    }
  });
}

function editData(id) {
  $.ajax({
    url: `/pembelian-rumah/edit/${id}`,
    type: 'GET',
    success: function(response) {
      if (response.status) {
        const data = response.data;
        const form = $('#modalEdit form'); // Changed from modalForm to modalEdit
        form[0].reset();
        restoreSoldHouseOptions();

        form.find('input[name="id"]').val(data.id);
        form.find('select[name="customer_id"]').val(data.customer_id);
        form.find(`select[name="perumahan_id"] option[value="${data.perumahan_id}"]`).prop('disabled', false);
        form.find('select[name="perumahan_id"]').val(data.perumahan_id);
        form.find('input[name="tanggal_pembelian"]').val(data.tanggal_pembelian);
        form.find('#hargaBeliEdit').val(data.harga_beli);
        form.find('select[name="metode_pembayaran"]').val(data.metode_pembayaran);
        form.find('input[name="lama_cicilan_tahun"]').val(data.lama_cicilan_tahun || '');
        form.find('input[name="tanggal_cicilan"]').val(data.tanggal_cicilan || '');
        form.find('select[name="status_dokumen"]').val(data.status_dokumen);
        form.find('select[name="status_pembelian"]').val(data.status_pembelian);
        form.find('textarea[name="request_khusus"]').val(data.request_khusus);
        form.find('textarea[name="catatan_marketing"]').val(data.catatan_marketing);
        toggleCicilanTahunField(form);


        $('#modalEditLabel').text('Edit Data Pembelian Rumah');

        const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        modal.show();
      } else {
        alert('Data tidak ditemukan.');
      }
    },
    error: function() {
      alert('Terjadi kesalahan saat mengambil data.');
    }
  });
}

let idToDelete = null;

function hapusData(id) {
  idToDelete = id;
  const confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
  confirmModal.show();
}

$('#confirmDeleteBtn').on('click', function() {
  if (!idToDelete) return;

  $.ajax({
    url: `/pembelian-rumah/delete/${idToDelete}`,
    method: 'DELETE',
    success: function() {
      $('#pembelianRumahTable').DataTable().ajax.reload();
      const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
      confirmModal.hide();

      showSuccess('Data Berhasil Dihapus !');
    },
    error: function() {
      alert('Terjadi Kesalahan Saat Menghapus')
    }
  })
})
