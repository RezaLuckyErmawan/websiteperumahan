$(document).ready(function () {
  const table = $('#pembelianRumahTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax:'/pembelian-rumah/json',
    columns: [
      { data: 'customer_nama' },
      { data: 'kode_rumah' },
      { data: 'tanggal_pembelian' },
      { data: 'harga_beli',
        render: function (data) {
            return 'Rp' + parseInt(data).toLocaleString('id-ID');
        }
      },
      { data: 'status_pembelian',
        render: function (data) {
          let style= '';
          let textColor= 'text-white';

          switch (data.toLowerCase()) {
            case 'lunas':
              style = 'background-color: #28a745;';
              break;
            case 'cicil':
              style = 'background-color: #c9d219ff; ';
              break;
            case 'dp':
              style = 'background-color: #2429b8ff;';
              break;
            case 'batal':
              style = 'background-color: #dc3545ff;';
              break;
            default:
              style = 'background-color: #6c757d';
          }
          return `<span class="badge ${textColor}" style="${style} padding: 8px 12px; font-size: 0.85rem; border-radius: 10px;">${data}</span>`;
        }
       },

      { data: 'metode_pembayaran' },
      
      {
        data: 'id',
        render: (data) => `
          <button class="btn btn-sm btn-primary" onclick="editData(${data})"><i class="fas fa-edit"></i></button>
          <button class="btn btn-sm btn-danger" onclick="hapusData(${data})"><i class="fas fa-trash"></i></button>
          <button class="btn btn-sm btn-secondary" onclick="detailData(${data})"><i class="fas fa-eye"></i></button>
          
        `,
        orderable: false,
        searchable: false
      }
    ],
   initComplete: function () {
      $('#pembelianRumahTable_filter input')
        .attr('placeholder', '🔍 Cari bahan, rumah...')
        .addClass('form-control form-control-sm ms-2')
        .css({ 'width': '300px', 'margin-left': '10px' });

      $('#pembelianRumahTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();
    }
  });
});


function openCreateForm() {
  const form = $('#modalForm form');
  form[0].reset();
  form.find('input[name="id"]').val('');
  $('#modalFormLabel').text('Form Pembelian Rumah');
  $('#btnBatal').prop('disabled', true);
  $('#textareaBatal').collapse('hide');
  $('#modalForm').modal('show');
}

function detailData(id) {
  $.ajax({
    url: `/pembelian-rumah/edit/${id}`,
    type: 'GET',
    success: function(response) {
      if (response.status) {
        const data = response.data;

        $('#detailCustomer').text(data.nama_customer);
        $('#detailKodeRumah').text(data.kode_rumah);
        $('#detailTanggal').text(data.tanggal_pembelian);
        $('#detailHarga').text('Rp' + parseInt(data.harga_beli).toLocaleString('id-ID'));
        $('#detailStatusPembelian').text(data.status_pembelian);
        $('#detailMetode').text(data.metode_pembayaran);
        $('#detailDokumen').text(data.status_dokumen);
        $('#detailRequest').text(data.request_khusus || '-');
        $('#detailCatatan').text(data.catatan_marketing || '-');

        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
      } else {
        alert('Data tidak ditemukan.');
      }
    },
    error: function() {
      alert('Terjadi kesalahan saat mengambil detail data.');
    }
  });
}


function simpanForm() {
  const form = $('#modalForm form');
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
      alert('Terjadi kesalahan pada server.');
      console.log(xhr.responseText);
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
        const form = $('#modalForm form');
        
        form.find('input[name="id"]').val(data.id);
        form.find('select[name="customer_id"]').val(data.customer_id);
        form.find('select[name="perumahan_id"]').val(data.perumahan_id);
        form.find('input[name="tanggal_pembelian"]').val(data.tanggal_pembelian);
        form.find('input[name="harga_beli"]').val(data.harga_beli);
        form.find('select[name="status_pembelian"]').val(data.status_pembelian);
        form.find('select[name="metode_pembayaran"]').val(data.metode_pembayaran);
        form.find('select[name="status_dokumen"]').val(data.status_dokumen);
        form.find('textarea[name="request_khusus"]').val(data.request_khusus);
        form.find('textarea[name="catatan_marketing"]').val(data.catatan_marketing);


        $('#modalFormLabel').text('Edit Data Pembelian Rumah');
        $('#btnBatal').prop('disabled', false);

        const modal = new bootstrap.Modal(document.getElementById('modalForm'));
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
