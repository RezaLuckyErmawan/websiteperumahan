$(document).ready(function () {
  $('#progresPembayaranTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/progres-pembayaran-rumah/json',
    columns: [
      { data: 'nama_customer' },
      { data: 'kode_rumah' },
      {
        data: 'harga_beli',
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
        data: null,
        render: function (row) {
          const harga = parseInt(row.harga_beli || 0, 10);
          const dibayar = parseInt(row.total_bayar || 0, 10);
          const percent = harga > 0 ? Math.min(Math.round((dibayar / harga) * 100), 100) : 0;

          return `
            <div class="progress-payment">
              <div class="progress-payment-track">
                <div class="progress-payment-fill" style="width:${percent}%"></div>
              </div>
              <span class="progress-payment-text">${percent}% terbayar</span>
            </div>
          `;
        },
        orderable: false,
        searchable: false
      },
      {
        data: 'status_pembelian',
        render: data => renderStatusBadge(data)
      },
      { data: 'metode_pembayaran' }
    ],
    initComplete: function () {
      $('#progresPembayaranTable_length').hide();

      $('#progresPembayaranTable_filter input')
        .attr('placeholder', 'Cari customer, rumah, status...')
        .addClass('form-control form-control-sm ms-2')
        .css({ 'display': 'inline-block', 'width': '300px', 'margin-left': '10px' });

      $('#progresPembayaranTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();
    }
  });
});

function formatRupiah(value) {
  return 'Rp ' + parseInt(value || 0, 10).toLocaleString('id-ID');
}

function renderStatusBadge(statusText) {
  const status = (statusText || '').toLowerCase();
  let color = '#6c757d';
  if (status === 'lunas') color = '#198754';
  if (status === 'cicil') color = '#0d6efd';
  if (status === 'dp') color = '#ffc107';
  if (status === 'batal') color = '#dc3545';

  return `<span class="badge text-white" style="background:${color};">${statusText || '-'}</span>`;
}
