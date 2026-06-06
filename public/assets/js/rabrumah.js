$('#dataRabTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/rab-rumah/json',
    columns: [
        { data: 'kode_rab', className: 'text-center'},
        { data: 'kode_rumah', className: 'text-center'},
        { data: 'total_anggaran', className: 'text-center',
            render: function (data) {
                return 'Rp ' + parseInt(data).toLocaleString('id-ID');
            }
        },
        {
            data: 'id', className: 'text-center',
            render: function (data) {
                return `
                    <button class="btn btn-sm btn-primary" onclick="editData(${data})">
                    <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="hapusData(${data})">
                    <i class="fas fa-trash"></i> Hapus
                    </button> <br>
                    <button class="btn btn-sm btn-secondary" onclick="cetakData(${data})" style="margin-top: 5px;">
                    <i class="fas fa-print"></i> Cetak
                    </button>
                `;
            },
            orderable: false,
            searchable: false,
        }
    ],
    initComplete: function() {
              $('#dataRabTable_length')
        .html(`
          <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        `);

$('#dataRabTable_filter input')
        .attr('placeholder', 'Cari berdasarkan kode, lokasi, tipe, status...')
        .addClass('form-control form-control-sm ms-2')
        .css({
            'display': 'inline-block',
            'width': '300px',
            'margin-left': '10px'
        });
        $('#dataRabTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
        }).remove();

        $('#dataRabTable_filter label')
        .prepend('<i class=" text-primary me-2"></i>');
    }
});

function openCreateForm() {
    $('#modalForm form') [0].reset();
    $('#modalForm input[name=id]').val('');
    $('#modalFormLabel').text('Tambah Data Rab Rumah');
    $('#modalForm').modal('show');
}

function cetakData(id) {
    window.open(`/rab-rumah/cetak/${id}`, '_blank');
}


function editData(id) {
    $.get(`/rab-rumah/edit/${id}`, function (data){
        $('#modalForm input[name=id]').val(data.id);
        $('#modalForm input[name=kode_rab]').val(data.kode_rab);
        $('#modalForm select[name=perumahan_id]').val(data.perumahan_id).trigger('change');
        $('#modalForm input[name=total_anggaran]').val(data.total_anggaran);
        $('#modalFormLabel').text('Edit Data RAB');
        $('#modalForm').modal('show');
    });
}


function simpanForm() {
    let id = $('#modalForm input[name=id]').val();
    let url = id ? `/rab-rumah/update/${id}` : `/rab-rumah/store`;
    
    $.post(url, $('#modalForm form').serialize(), function(){
        $('#modalForm').modal('hide');
        $('#dataRabTable').DataTable().ajax.reload();
        if (id) {
            showSuccess('Data berhasil diperbarui');
        } else {
            showSuccess('Data berhasil di tambahkan');
        }
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
        url: `/rab-rumah/delete/${idToDelete}`,
        type: 'DELETE',
        success: function () {
            $('#confirmDeleteModal').modal('hide');
            $('#dataRabTable').DataTable().ajax.reload();
            showSuccess('Data Berhasil di hapus')
        },
        error: function () {
            alert('Gagal menghapus data');
        }
        });
    }
    });
