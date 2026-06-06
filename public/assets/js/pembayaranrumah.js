let idToDelete = null;

$(document).ready(function () {
  $('#pembayaranRumahTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/pembayaran-rumah/json',
    columns: [
      { data: 'nama_customer' },
      { data: 'kode_rumah' },
      { data: 'tanggal_bayar' },
      {
        data: 'jenis_pembayaran',
        render: function (data) {
          const label = {
            booking_fee: 'Booking Fee',
            dp: 'DP',
            cicilan: 'Cicilan',
            pelunasan: 'Pelunasan'
          };
          return label[data] || data;
        }
      },
      {
        data: 'jumlah_bayar',
        render: data => formatRupiah(data)
      },
      {
        data: 'total_bayar',
        render: data => formatRupiah(data)
      },
      {
        data: 'sisa_bayar',
        render: data => formatRupiah(data)
      },
      {
        data: 'status_pembelian',
        render: function (data) {
          const status = (data || '').toLowerCase();
          let color = '#6c757d';
          if (status === 'lunas') color = '#198754';
          if (status === 'cicil') color = '#0d6efd';
          if (status === 'dp') color = '#ffc107';
          if (status === 'batal') color = '#dc3545';
          return `<span class="badge text-white" style="background:${color};">${data}</span>`;
        }
      },
      {
        data: 'bukti_bayar',
        render: function (data) {
          if (!data) return '<span class="text-muted">Belum ada</span>';
          return `<a class="btn btn-sm btn-outline-secondary" href="/${data}" target="_blank"><i class="fas fa-file-invoice"></i></a>`;
        },
        orderable: false,
        searchable: false
      },
      {
        data: 'id',
        render: data => `
          <button class="btn btn-sm btn-primary" onclick="editData(${data})"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger" onclick="hapusData(${data})"><i class="fas fa-trash"></i></button>
        `,
        orderable: false,
        searchable: false
      }
    ],
    initComplete: function () {
      $('#pembayaranRumahTable_filter input')
        .attr('placeholder', 'Cari customer, rumah, metode...')
        .addClass('form-control form-control-sm ms-2')
        .css({ 'display': 'inline-block', 'width': '300px', 'margin-left': '10px' });

      $('#pembayaranRumahTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();
    }
  });

  const selectedId = window.selectedPembelianId || '';
  if (selectedId) {
    openCreateForm(selectedId);
  }
});

function formatRupiah(value) {
  return 'Rp ' + parseInt(value || 0).toLocaleString('id-ID');
}

function openCreateForm(pembelianId = '') {
  const form = $('#modalForm form');
  form[0].reset();
  form.find('input[name=id]').val('');
  form.find('select[name=pembelian_rumah_id]').prop('disabled', false);
  $('#buktiSaatIni').html('');
  $('#modalFormLabel').text('Tambah Pembayaran Rumah');

  if (pembelianId) {
    form.find('select[name=pembelian_rumah_id]').val(pembelianId);
  }

  updateRingkasan();
  new bootstrap.Modal(document.getElementById('modalForm')).show();
}

function editData(id) {
  $.get(`/pembayaran-rumah/edit/${id}`, function (response) {
    if (response.status !== 'success') {
      alert(response.message || 'Data tidak ditemukan');
      return;
    }

    const data = response.data;
    const form = $('#modalForm form');
    form[0].reset();
    form.find('input[name=id]').val(data.id);
    form.find('select[name=pembelian_rumah_id]').val(data.pembelian_rumah_id).prop('disabled', true);
    form.find('input[name=tanggal_bayar]').val(data.tanggal_bayar);
    form.find('input[name=jumlah_bayar]').val(data.jumlah_bayar);
    form.find('select[name=jenis_pembayaran]').val(data.jenis_pembayaran);
    form.find('select[name=metode_bayar]').val(data.metode_bayar);
    form.find('textarea[name=keterangan]').val(data.keterangan);
    form.find('input[name=bukti_bayar]').val('');
    $('#buktiSaatIni').html(
      data.bukti_bayar
        ? `<a href="/${data.bukti_bayar}" target="_blank">Lihat bukti pembayaran saat ini</a>`
        : '<span class="text-muted">Belum ada bukti pembayaran.</span>'
    );

    $('#modalFormLabel').text('Edit Pembayaran Rumah');
    updateRingkasan(data.id);
    new bootstrap.Modal(document.getElementById('modalForm')).show();
  });
}

function simpanForm() {
  const form = $('#modalForm form');
  const id = form.find('input[name=id]').val();
  const url = id ? `/pembayaran-rumah/update/${id}` : '/pembayaran-rumah/store';
  const disabled = form.find(':disabled').prop('disabled', false);
  const formData = new FormData(form[0]);

  $.ajax({
    url: url,
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      disabled.prop('disabled', true);
      if (res.status === 'success') {
        $('#modalForm').modal('hide');
        $('#pembayaranRumahTable').DataTable().ajax.reload(null, false);
        showSuccess(id ? 'Pembayaran berhasil diperbarui!' : 'Pembayaran berhasil ditambahkan!');
      } else {
        alert(res.message || 'Gagal menyimpan data');
      }
    },
    error: function (xhr) {
      disabled.prop('disabled', true);
      const message = xhr.responseJSON?.message || 'Terjadi kesalahan pada server';
      alert(message);
    }
  });
}

function hapusData(id) {
  idToDelete = id;
  new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
}

$('#confirmDeleteBtn').on('click', function () {
  if (!idToDelete) return;

  $.ajax({
    url: `/pembayaran-rumah/delete/${idToDelete}`,
    type: 'DELETE',
    success: function () {
      $('#confirmDeleteModal').modal('hide');
      $('#pembayaranRumahTable').DataTable().ajax.reload(null, false);
      showSuccess('Pembayaran berhasil dihapus!');
      idToDelete = null;
    },
    error: function (xhr) {
      alert(xhr.responseJSON?.message || 'Gagal menghapus pembayaran');
    }
  });
});

function updateRingkasan(excludePaymentId = '') {
  const pembelianId = $('#modalForm select[name=pembelian_rumah_id]').val();
  const box = $('#ringkasanPembayaran');

  if (!pembelianId) {
    box.html('<span class="text-muted">Pilih transaksi rumah untuk melihat sisa tagihan.</span>');
    $('#modalForm input[name=jumlah_bayar]').attr('placeholder', 'Contoh: 5000000');
    return;
  }

  $.get(`/pembayaran-rumah/ringkasan/${pembelianId}`, function (response) {
    if (response.status !== 'success') return;

    const data = response.data;
    const currentValue = parseInt($('#modalForm input[name=jumlah_bayar]').val() || 0);
    const sisaUntukEdit = parseInt(data.sisa_bayar || 0) + (excludePaymentId ? currentValue : 0);

    box.html(`
      <div><strong>${data.nama_customer}</strong> - ${data.kode_rumah}</div>
      <div>Harga: ${formatRupiah(data.harga_beli)}</div>
      <div>Sudah dibayar: ${formatRupiah(data.total_bayar)}</div>
      <div>Sisa tagihan: ${formatRupiah(sisaUntukEdit)}</div>
    `);
    $('#modalForm input[name=jumlah_bayar]').attr('placeholder', `Maksimal ${formatRupiah(sisaUntukEdit)}`);
  });
}
