$(document).ready(function () {
  // Check if DataTables is loaded
  if (typeof $.fn.DataTable === 'undefined') {
    console.error('DataTables plugin is not loaded!');
    return;
  }

  const table = $('#pembelianRumahTable').DataTable({
    processing: true,
    serverSide: true,
    scrollX: true,
    autoWidth: false,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/pembelian-rumah/json',
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
          let style = '';
          let textColor = 'text-white';

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
            case 'booking':
              style = 'background-color: #6f42c1;';
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
      {
        data: 'sumber',
        defaultContent: 'admin',
        render: function (data) {
          return data === 'customer' ? 'Customer' : 'Admin';
        }
      },
      {
        data: 'status_verifikasi',
        defaultContent: 'tidak_perlu',
        render: function (data, type, row) {
          if ((row.sumber || 'admin') !== 'customer') {
            return '<span class="text-muted">-</span>';
          }
          const label = data || 'pending';
          let style = 'background-color:#6c757d;';
          if (label === 'disetujui') style = 'background-color:#28a745;';
          if (label === 'pending') style = 'background-color:#17a2b8;';
          if (label === 'ditolak') style = 'background-color:#dc3545;';
          return `<span class="badge text-white" style="${style} padding:8px 12px;border-radius:10px;">${label}</span>`;
        }
      },
      {
        data: 'metode_pembayaran',
        render: function (data) {
          if (data === 'Cicilan Internal') return 'KPR';
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
        render: (data, type, row) => {
          const perluVerifikasi = row.sumber === 'customer' && (row.status_verifikasi || 'pending') === 'pending';
          const verifikasiBtn = perluVerifikasi
            ? `<button class="btn btn-sm btn-success" onclick="bukaVerifikasiBooking(${data})"><i class="fas fa-check"></i></button>`
            : '';
          return `
          ${verifikasiBtn}
          <button class="btn btn-sm btn-primary" onclick="editData(${data})"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger" onclick="hapusData(${data})"><i class="fas fa-trash"></i></button>
          <button class="btn btn-sm btn-secondary" onclick="detailData(${data})"><i class="fas fa-eye"></i></button>
          <button class="btn btn-sm btn-success" onclick="bukaPembayaran(${data})"><i class="fas fa-money-bill-wave"></i></button>
        `;
        },
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
  $(document).on('change', '#rumahSelect', function () {
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
  $(document).on('change', '#modalEdit select[name="perumahan_id"]', function () {
    const selectedOption = $(this).find('option:selected');
    const harga = selectedOption.data('harga');

    if (harga) {
      $('#hargaBeliEdit').val(harga);
    } else {
      $('#hargaBeliEdit').val('');
    }

    toggleCicilanTahunField($('#modalEdit form'));
  });

  $(document).on('change input', 'select[name="metode_pembayaran"], input[name="lama_cicilan_tahun"], input[name="tanggal_cicilan"], input[name="tanggal_pembelian"], input[name="harga_beli"], select[name="status_pembelian"]', function () {
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
  $('#rumahSelect option, #modalEdit select[name="perumahan_id"] option').each(function () {
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
    success: function (response) {
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
    error: function () {
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

$('#confirmDeleteBtn').on('click', function () {
  if (!idToDelete) return;

  $.ajax({
    url: `/pembelian-rumah/delete/${idToDelete}`,
    method: 'DELETE',
    success: function () {
      $('#pembelianRumahTable').DataTable().ajax.reload();
      const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
      confirmModal.hide();

      showSuccess('Data Berhasil Dihapus !');
    },
    error: function () {
      alert('Terjadi Kesalahan Saat Menghapus')
    }
  })
})

function getCsrfData() {
  const el = document.querySelector('input[name^="csrf"]');
  return el ? { [el.name]: el.value } : {};
}

function bukaVerifikasiBooking(id) {
  $('#bookingVerifikasiId').val(id);
  $('#catatanVerifikasi').val('');
  $('#bookingDetailInfo').html('Memuat...');
  const form = $('#formVerifikasiPembayaran');
  $.getJSON('/pembelian-rumah/booking/' + id, function (res) {
    if (!res.status) {
      $('#bookingDetailInfo').html('Data tidak ditemukan.');
      return;
    }
    const d = res.data || {};
    const info = res.info_berkas || {};
    let html = `
      <p><strong>${d.nama || '-'}</strong> · ${d.telepon || '-'} · ${d.email || '-'}</p>
      <p>${d.kode_rumah || '-'} · ${d.tipe || '-'} · ${d.lokasi || '-'}</p>
      <p>Alamat: ${d.alamat || '-'} · Harga: <strong>Rp ${parseInt(d.harga || 0).toLocaleString('id-ID')}</strong></p>
      <p>Status berkas: <strong>${info.status || '-'}</strong> · Verifikasi: <strong>${d.status_verifikasi || 'pending'}</strong></p>
      <div class="mb-3"><strong>Berkas</strong></div>
    `;
    (res.berkas || []).forEach(function (item) {
      const file = item.file
        ? `<a href="/${item.file}" target="_blank">Lihat file</a>`
        : '<span class="text-danger">Belum diunggah</span>';
      html += `<div class="mb-2">${item.label} ${item.wajib ? '(wajib)' : '(opsional)'}: ${file}</div>`;
    });
    $('#bookingDetailInfo').html(html);

    $('#verifikasiHargaBeli').val(d.harga || 0);
    $('#verifikasiMetode').val(d.metode_pembayaran || 'Cicilan Internal');
    $('#verifikasiLamaCicilan').val(d.lama_cicilan_tahun || 5);
    $('#verifikasiStatusPembelian').val(d.status_pembelian && d.status_pembelian !== 'Booking' ? d.status_pembelian : 'DP');
    const today = new Date().toISOString().split('T')[0];
    $('#verifikasiTanggalCicilan').val(d.tanggal_cicilan || today);
    toggleCicilanTahunField(form);

    const pending = (d.status_verifikasi || 'pending') === 'pending';
    $('#modalVerifikasiBooking .btn-success, #modalVerifikasiBooking .btn-danger').toggle(pending);
    form.toggle(pending);
  });
  new bootstrap.Modal(document.getElementById('modalVerifikasiBooking')).show();
}

function verifikasiBooking(aksi) {
  const id = $('#bookingVerifikasiId').val();
  if (!id) return;

  const payload = Object.assign(getCsrfData(), {
    aksi: aksi,
    catatan_verifikasi: $('#catatanVerifikasi').val()
  });

  if (aksi === 'setujui') {
    const metode = $('#verifikasiMetode').val();
    const statusPembelian = $('#verifikasiStatusPembelian').val();
    const lama = $('#verifikasiLamaCicilan').val();
    const tanggal = $('#verifikasiTanggalCicilan').val();

    if (!metode || !statusPembelian) {
      alert('Metode pembayaran dan status pembelian wajib diisi.');
      return;
    }
    if (metode === 'Cicilan Internal') {
      const tahun = parseInt(lama, 10);
      if (!tahun || tahun < 1 || tahun > 30) {
        alert('Lama cicilan wajib diisi 1-30 tahun untuk Cicilan Internal.');
        return;
      }
      if (!tanggal) {
        alert('Tanggal cicilan wajib diisi untuk Cicilan Internal.');
        return;
      }
    }

    payload.metode_pembayaran = metode;
    payload.status_pembelian = statusPembelian;
    payload.lama_cicilan_tahun = lama;
    payload.tanggal_cicilan = tanggal;
  }

  pendingVerifikasi = { payload: payload, id: id };

  const isSetujui = aksi === 'setujui';
  $('#confirmVerifikasiTitle').text(isSetujui ? 'Konfirmasi Setujui' : 'Konfirmasi Tolak');
  $('#confirmVerifikasiMessage').text(isSetujui ? 'Setujui booking ini?' : 'Tolak booking ini?');
  $('#confirmVerifikasiBtn')
    .prop('disabled', false)
    .text(isSetujui ? 'Setujui' : 'Tolak')
    .removeClass('btn-success btn-danger')
    .addClass(isSetujui ? 'btn-success' : 'btn-danger');

  bootstrap.Modal.getOrCreateInstance(document.getElementById('modalConfirmVerifikasi')).show();
}

let pendingVerifikasi = null;

function kirimVerifikasiBooking() {
  if (!pendingVerifikasi) return;
  const { payload, id } = pendingVerifikasi;
  if (!id) return;

  const btn = $('#confirmVerifikasiBtn');
  btn.prop('disabled', true);

  $.ajax({
    url: '/pembelian-rumah/booking/' + id + '/verifikasi',
    method: 'POST',
    data: payload,
    success: function (res) {
      if (res.status === 'success') {
        pendingVerifikasi = null;
        bootstrap.Modal.getInstance(document.getElementById('modalConfirmVerifikasi'))?.hide();
        bootstrap.Modal.getInstance(document.getElementById('modalVerifikasiBooking'))?.hide();
        $('#pembelianRumahTable').DataTable().ajax.reload();
        showSuccess(res.message);
      } else {
        btn.prop('disabled', false);
        alert(res.message || 'Verifikasi gagal.');
      }
    },
    error: function (xhr) {
      btn.prop('disabled', false);
      const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Verifikasi gagal.';
      alert(msg);
    }
  });
}

$('#confirmVerifikasiBtn').on('click', function () {
  kirimVerifikasiBooking();
});

document.getElementById('modalConfirmVerifikasi')?.addEventListener('shown.bs.modal', function () {
  const backdrops = document.querySelectorAll('.modal-backdrop');
  if (backdrops.length) {
    backdrops[backdrops.length - 1].style.zIndex = '1060';
  }
});
