$(document).ready(function () {
  const table = $('#dataBahanTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/data-bahan/json',
    columns: [

      { data: 'nama_bahan' },
      { data: 'stok' },
      { data: 'satuan' },
      {
        data: 'id',
        render: function (data, type, row) {
          return `
           
            <button class="btn btn-sm btn-primary" onclick="editData(${data})">
            <i class="fas fa-edit"></i> Edit
            </button>
             <button class="btn btn-sm btn-danger" onclick="hapusData(${data})">
            <i class="fas fa-trash"></i> Hapus
          </button>
          `;
        },
        orderable: false,
        searchable: false
      }
    ],
    initComplete: function () {
            $('#dataBahanTable_length')
        .html(`
          <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        `);

$('#dataBahanTable_filter input')
        .attr('placeholder', 'Cari nama, satuan, atau stok...')
        .addClass('form-control form-control-sm ms-2')
        .css({
          'display': 'inline-block',
          'width': '300px',
          'margin-left': '10px'
        });

      $('#dataBahanTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
      }).remove();

      $('#dataBahanTable_filter label')
        .prepend('<i class="text-primary me-2"></i>');
    }
  });
});

  
  
  
  function openCreateForm() {
    $('#modalForm form')[0].reset();
    $('#modalForm input[name=id]').val('');
    $('#modalFormLabel').text('Tambah Data Bahan');
    new bootstrap.Modal(document.getElementById('modalForm')).show();
  }

  function editData(id) {
    $.get(`/data-bahan/edit/${id}`, function (data) {
      $('#modalForm input[name=id]').val(data.id);
      $('#modalForm input[name=nama_bahan]').val(data.nama_bahan);
      $('#modalForm input[name=stok]').val(data.stok);
      $('#modalForm input[name=satuan]').val(data.satuan);
      $('#modalFormLabel').text('Edit Data Bahan');
      new bootstrap.Modal(document.getElementById('modalForm')).show();
    });
  }

  function simpanForm() {
    let id = $('#modalForm input[name=id]').val();
    let url = id ? `/data-bahan/update/${id}` : `/data-bahan/store`;

    $.post(url, $('#modalForm form').serialize(), function () {
      $('#modalForm').modal('hide');
      $('#dataBahanTable').DataTable().ajax.reload();
      showSuccess(id ? 'Data berhasil diperbarui!' : 'Data berhasil ditambahkan!');
    });
  }

    let idToDelete = null;
    function hapusData(id) {
        idToDelete = id;
        const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        modal.show();
    }
   $('#confirmDeleteModal').on('click', function() {
    if (idToDelete) {
        $.ajax ({
            url: `/data-bahan/delete/${idToDelete}`,
            type: 'DELETE',
            success: function() {
                $('#confirmDeleteModal').modal('hide');
                $('#dataBahanTable').DataTable().ajax.reload();
                showSuccess('Data berhasil dihapus!');
            },
            error: function() {
                alert('Gagal Menghapus data');
            }
        });
    }
   });  
  

