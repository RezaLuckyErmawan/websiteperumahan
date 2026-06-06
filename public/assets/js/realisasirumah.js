$('#dataRealisasiTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/realisasi-rumah/json',
    columns: [
        { data: 'kode_rumah', className: 'text-center'},
        { data: 'sub_total_asli', className: 'text-center',
            render: function(data) {
                return 'Rp' + parseInt(data).toLocaleString('id-ID');
            }
        },
        { data: 'tanggal_mulai', className: 'text-center'},
        { data: 'tanggal_selesai', className: 'text-center'},
        {
            data: 'id', className: 'text-center',
            render: function(data) {
                return `
                 <button class="btn btn-sm btn-primary" onclick="editData(${data})">
                    <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="hapusData(${data})">
                    <i class="fas fa-trash"></i> Hapus
                    </button>
                    <button class="btn btn-sm btn-secondary" style="margin-top: 5px;" onclick="cetakData(${data})">
                        <i class="fas fa-print"></i> Cetak
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
    $('#modalFormLabel').text('Tambah Data Realisasi Rumah');
    $('#modalForm').modal('show');
}

function editData(id) {
    $.get(`/realisasi-rumah/edit/${id}`, function (data){
        $('#modalForm input[name=id]').val(data.id);
        $('#modalForm select[name=perumahan_id]').val(data.perumahan_id);
        $('#modalForm input[name=sub_total_asli]').val(data.sub_total_asli);
        $('#modalForm input[name=tanggal_mulai]').val(data.tanggal_mulai);
        $('#modalForm input[name=tanggal_selesai').val(data.tanggal_selesai);
        $('#modalFormLabel').text('Edit Data Realisasi Rumah');
        $('#modalForm').modal('show');
    });
}

function simpanForm() {
    let id = $('#modalForm input[name=id]').val();
    let url = id ? `/realisasi-rumah/update/${id}` : `/realisasi-rumah/store`;
    $.post(url, $('#modalForm form').serialize(), function (){
        $('#modalForm').modal('hide');
        $('#dataRealisasiTable').DataTable().ajax.reload();
        if (id) {
            showSuccess('Data berhasil di perbarui');
        } else {
            showSuccess('Data berhasil di tambahkan');
        }
    })
}
function cetakData(id) {
    window.open(`/realisasi-rumah/cetak/${id}`, '_blank');
}


let idToDelete = null;

function hapusData(id) {
    idToDelete = id;
    $('#confirmDeleteModal').modal('show');
}
    $('#confirmDeleteModal').on('click', function() {
        if (idToDelete) {
            $.ajax({
                url : `/realisasi-rumah/delete/${idToDelete}`,
                type: `DELETE`,
                success: function() {
                    $('#confirmDeleteModal').modal('hide');
                    $('#dataRealisasiTable').DataTable().ajax.reload();
                    showSuccess('Data Berhasil Dihapus')
                },
            });
        }
    });
