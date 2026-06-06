// Inisialisasi DataTable
$('#dataInsidentilTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/pekerjaan-insidentil/json',
    columns: [
        { data: 'kode_rumah', className: 'text-center' },
        { data: 'mandor_nama', className: 'text-center' },
        { data: 'nama_pekerjaan', className: 'text-center' },
        { 
            data: 'total_biaya', 
            className: 'text-center',
            render: function(data) {
                return 'Rp ' + parseInt(data).toLocaleString('id-ID');
            }
        },
        { 
            data: 'status', 
            className: 'text-center',
            render: function (data) {
                let style = '';
                let textColor = 'text-white';

                switch (data.toLowerCase()) {
                    case 'pending':
                        style = 'background-color: rgba(207, 200, 15, 1); color: white;';
                        textColor = '';
                        break;
                    case 'approved':
                        style = 'background-color: rgba(1, 220, 34, 1); color: white;';
                        textColor = '';
                        break;
                    case 'rejected':
                        style = 'background-color: rgba(220, 1, 1, 1); color: white;';
                        textColor = '';
                        break;
                    default:
                        style = 'background-color: #6c757d;';
                }

                return `<span class="badge ${textColor}" style="${style} padding: 8px 12px; font-size: 0.85rem; border-radius: 10px;">${data}</span>`;
            }
        },
        { data: 'tanggal', className: 'text-center' },
        {
            data: 'id', className: 'text-center',
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
              $('#dataInsidentilTable_length')
        .html(`
          <button type="button" onclick="openCreateForm()" class="add-btn1">
            <i class="fas fa-plus"></i> Tambah Data
          </button>
        `);

$('#dataInsidentilTable_filter input')
            .attr('placeholder', 'Cari berdasarkan kode, total, nama, tanggal...')
            .addClass('form-control form-control-sm ms-2')
            .css({
                'display': 'inline-block',
                'width': '300px',
                'margin-left': '10px'
            });

        $('#dataInsidentilTable_filter label').contents().filter(function () {
            return this.nodeType === 3;
        }).remove();

        $('#dataInsidentilTable_filter label')
            .prepend('<i class="text-primary me-2"></i>');
    }
});

// Buka form create
function openCreateForm() {
    $('#modalForm form')[0].reset();
    $('#modalForm input[name=id]').val('');
    $('#modalFormLabel').text('Tambah Data Pekerjaan Insidentil');
    $('#statusField').hide(); // default sembunyiin status
    $('#modalForm').modal('show');
}

// Buka form edit
function editData(id) {
    fetch(`/pekerjaan-insidentil/edit/${id}`)
        .then(res => res.json())
        .then(data => {
            $('#modalForm input[name=id]').val(data.id);
            $('#modalForm select[name=perumahan_id]').val(data.perumahan_id);
            $('#modalForm select[name=mandor_id]').val(data.mandor_id);
            $('#modalForm input[name=tanggal]').val(data.tanggal);
            $('#modalForm input[name=nama_pekerjaan]').val(data.nama_pekerjaan);
            $('#modalForm textarea[name=keterangan]').val(data.keterangan);
            $('#modalForm input[name=total_biaya]').val(data.total_biaya);

            // kalau role = admin, tampilkan status
            if (typeof userRole !== 'undefined' && userRole === 'admin') {
                $('#statusField').show();
                $('#modalForm select[name=status]').val(data.status.toLowerCase());
            }

            $('#modalFormLabel').text('Edit Data Pekerjaan Insidentil');
            $('#modalForm').modal('show');
        })
        .catch(err => console.error(err));
}

// Simpan form
function simpanForm() {
   let id = $('#modalForm input[name=id]').val();
   let url = id ? `/pekerjaan-insidentil/update/${id}` : `/pekerjaan-insidentil/store`;
   $.post(url, $('#modalForm form').serialize(), function (){
    $('#modalForm').modal('hide');
    $('#dataInsidentilTable').DataTable().ajax.reload();
    if (id) {
        showSuccess('Data Berhasil di perbarui');
    } else {
        showSuccess('Data Berhasil ditambahkan');
    }
   })
}

// Hapus data
function hapusData(id) {
    $('#modalConfirmDelete').modal('show');
    $('#btnDeleteConfirm').off('click').on('click', function() {
        fetch(`/pekerjaan-insidentil/delete/${id}`, { method: 'DELETE' })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showSuccess(res.message);
                    $('#modalConfirmDelete').modal('hide');
                    $('#dataInsidentilTable').DataTable().ajax.reload();
                } else {
                    alert(res.message);
                }
            })
            .catch(err => console.error(err));
    });
}
