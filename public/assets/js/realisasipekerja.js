$('#dataRealisasiTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/realisasi-pekerja/json',
    columns: [
        { data: 'kode_rumah', className: 'text-center'},
        { data: 'nama_kontraktor', className: 'text-center'},
        { data: 'biaya_kontraktor', className: 'text-center',
            render: function(data) {
                return 'Rp' + parseInt(data).toLocaleString('id-ID');
            }
        },
        { data: 'jumlah_hari', className: 'text-center'},
        {
            data: 'id', className: 'text-center',
            render: function (data) {
                return `
                 <button class="btn btn-sm btn-primary" onclick="editData(${data})" >
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
              $('#dataRealisasiTable_length')
        .html(`
          <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        `);

$('#dataRealisasiTable_filter input')
        .attr('placeholder', 'Cari berdasarkan kode, total, tanggal...')
        .addClass('form-control form-control-sm ms-2')
        .css({
            'display': 'inline-block',
            'width': '300px',
            'margin-left': '10px'
        });
        $('#dataRealisasiTable_filter label').contents().filter(function () {
        return this.nodeType === 3;
        }).remove();

        $('#dataRealisasiTable_filter label')
        .prepend('<i class=" text-primary me-2"></i>');
    }
});

function openCreateForm() {
    $('#modalForm form') [0].reset();
    $('#modalForm input[name=id]').val('');
    $('#modalFormLabel').text('Tambbah data Realisasi Pekerja');
    $('#modalForm').modal('show');
}

function editData(id) {
    $.get(`/realisasi-pekerja/edit/${id}`, function (data){
        $('#modalForm input[name=id]').val(data.id);
        $('#modalForm select[name=realisasi_id]').val(data.realisasi_id);
        $('#modalForm input[name=nama_kontraktor]').val(data.nama_kontraktor);
        $('#modalForm input[name=biaya_kontraktor]').val(data.biaya_kontraktor);
        $('modalForm input[name=jumlah_hari]').val(data.jumlah_hari);
        $('#modalFormLabel').text('Edit Data Realisasi Pekerja');
        $('#modalForm').modal('show');
    });
}

function simpanForm() {
    let id = $('#modalForm input[name=id]').val();
    let url = id ? `/realisasi-pekerja/update/${id}` : `/realisasi-pekerja/store`;
    $.post(url, $('#modalForm form').serialize(), function () {
        $('#modalForm').modal('hide');
        $('#dataRealisasiTable').DataTable().ajax.reload();
        if (id) {
            showSuccess('Data Berhasil di perbarui');
        } else {
            showSuccess('Data Berhasil di tambahkan');
        }
    })
}

let idToDelete = null;
function hapusData(id) {
    idToDelete = id;
    $('#confirmDeleteModal').modal('show');
}

$('#confirmDeleteModal').on('click', function() {
    if (idToDelete) {
        $.ajax({
            url : `/realisasi-pekerja/delete/${idToDelete}`,
            type : `DELETE`,
            success: function() {
                $('#confirmDeleteModal').modal('hide');
                $('#dataRealisasiTable').DataTable().ajax.reload();
                showSuccess('Data Berhasil dihapus')
            },
        });
    }
});
