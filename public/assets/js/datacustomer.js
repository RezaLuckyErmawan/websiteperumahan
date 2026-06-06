let idToDelete = null;
$(document).ready(function () {
  const table = $('#customerTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/data-customer/json',
    columns: [
      { data: 'nama' },
      { data: 'email' },
      { data: 'telepon' },
      { data: 'alamat' },
      
      { data: 'tanggal_pembelian' },
      {
        data: 'id',
        render: (data) => `
          <button class="btn btn-sm btn-primary" onclick="editData(${data})"><i class="fas fa-edit"></i> Edit</button>
    <button class="btn btn-sm btn-danger" onclick="hapusData(${data})"><i class="fas fa-trash"></i> Hapus</button>
        `,
        orderable: false,
        searchable: false
      }
    ],
    initComplete: function () {
      $('#customerTable_length')
        .html(`
          <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        `);

      $('#customerTable_filter input')
        .attr('placeholder', 'Cari nama, email, alamat...')
        .addClass('form-control form-control-sm ms-2')
        .css({ width: '300px', display: 'inline-block', marginLeft: '10px' });

      $('#customerTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();
    }
  });

  $('#btnDeleteConfirm').click(function () {
    if (idToDelete) {
      $.ajax({
        url: `/customer/delete/${idToDelete}`,
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
  $('#formCustomer')[0].reset();
  $('#customer_id').val('');
  $('#modalCustomerLabel').text('Tambah Data Customer');
  $('#formCustomer').attr('action', '/customer/store');
  $('#modalCustomerForm').modal('show');
}

function editData(id) {
  $.ajax({
    url: `/customer/edit/${id}`,
    method: 'GET',
    success: function (response) {
      const customer = response.customer;
      $('#customer_id').val(customer.id);
      $('#nama').val(customer.nama);
      $('#email').val(customer.email);
      $('#telepon').val(customer.telepon);
      $('#alamat').val(customer.alamat);
      $('#tanggal_pembelian').val(customer.tanggal_pembelian);

      $('#modalCustomerLabel').text('Edit Data Customer');
      $('#formCustomer').attr('action', `/customer/update/${customer.id}`);
      $('#modalCustomerForm').modal('show');
    },
    error: function () {
      alert('Gagal memuat data customer.');
    }
  });
}

$('#formCustomer').submit(function (e) {
  e.preventDefault();

  const id = $('#customer_id').val();
  const url = id ? `/customer/update/${id}` : '/customer/store';

  $.ajax({
    url: url,
    method: 'POST',
    data: $(this).serialize(),
    success: function () {
      $('#modalCustomerForm').modal('hide');
      $('#customerTable').DataTable().ajax.reload();
      showSuccess(id ? 'Data customer berhasil diperbarui!' :'Data Customer Berhasil ditambahkan');
    },
    error: function () {
      alert('Terjadi kesalahan saat menyimpan data.');
    }
  });
});


function hapusData(id) {
  idToDelete = id;
  $('#modalConfirmDelete').modal('show');
}

function showSuccess(msg) {
  $('#successMessage').text(msg);
  $('#successmodal').modal('show');
  
  // Auto close modal setelah 2.5 detik (opsional)
  setTimeout(() => {
    $('#successmodal').modal('hide');
  }, 2500);
}

