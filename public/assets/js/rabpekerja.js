$('#dataRabTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/rab-pekerja/json',
    columns: [
        {data: 'kode_rab', className: 'text-center'},
        {data: 'nama_kontraktor', className: 'text-center'},
        {data: 'biaya_kontraktor', className: 'text-center',
            render: function(data) {
                return 'Rp' + parseInt(data).toLocaleString('id-ID');
            }
        },
        {data: 'estimasi_hari', className: 'text-center'},
        {
            data: 'id',
            render: function (data) {
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


function editData(id) {
    $.get(`/rab-pekerja/edit/${id}`, function (data){
        $('#modalForm input[name=id]').val(data.id);
        $('#modalForm select[name=rab_id]').val(data.rab_id);
        $('#modalForm input[name=nama_kontraktor]').val(data.nama_kontraktor);
        $('#modalForm input[name=biaya_kontraktor]').val(data.biaya_kontraktor);
        $('#modalForm input[name=estimasi_hari]').val(data.estimasi_hari);
        $('#modalFormLabel').text('Edit Data Rab Pekerja');
        $('#modalForm').modal('show');
    });
}

function openCreateForm() {
    $('#modalForm form') [0].reset();
    $('#modalForm input[name=id]').val('');
    $('#modalFormLabel').text('Tambah Data Rab Pekerja');
    $('#modalForm').modal('show');
}

function getKodeRumah(rabId) {
    if (!rabId) {
        $('#kode_rumah').val('');
        return;
    }

    $.get(`/rab-pekerja/get-kode-rumah/${rabId}`, function(response) {
        if (response.status === 'success') {
            $('#kode_rumah').val(response.kode_rumah);
        } else {
            $('#kode_rumah').val('Tidak ditemukan');
        }
    });
}


function simpanForm() {
    let id = $('#modalForm input[name=id]').val();
    let url = id ? `/rab-pekerja/update/${id}` : `/rab-pekerja/store`;

    $.post(url, $('#modalForm form').serialize(), function(){
        $('#modalForm').modal('hide');
        $('#dataRabTable').DataTable().ajax.reload();
        
        if (id) {
            showSuccess('Data berhasil diperbarui');
        } else {
            showSuccess('Data berhasil ditambahkan');
        }
    });
}

let idToDelete =  null;

function hapusData(id) {
    idToDelete = id;
    $('#modalConfirmDelete').modal('show');
}
    $('#modalConfirmDelete').on('click', function (){
        if (idToDelete) {
            $.ajax({
                url: `/rab-pekerja/delete/${idToDelete}`,
                type: 'DELETE',
                success: function () {
                    $('#modalConfirmDelete').modal('hide');
                    $('#dataRabTable').DataTable().ajax.reload();
                    showSuccess('Data Berhasil dihapus')
                },
                error: function () {
                    alert('Gagal Menghapus Data');
                }
            });
        }
    });
