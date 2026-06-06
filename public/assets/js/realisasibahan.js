$(document).ready(function () {
    const table = $('#dataRealisasiTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        ajax: '/realisasi-bahan/json',
        columns: [
            { data: 'kode_rumah', className: 'text-center'},
            { data: 'nama_bahan', className: 'text-center'},
            { 
                data: 'harga_satuan', className: 'text-center',
                render: function (data) {
                    return 'Rp' + parseInt(data).toLocaleString('id-ID');
                }
            },
            { data: 'jumlah', className: 'text-center'},
            { 
                data: 'sub_total', className: 'text-center',
                render: function (data) {
                    return 'Rp' + parseInt(data).toLocaleString('id-ID');
                }
            },
            {
                data: 'id', className: 'text-center',
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-primary editBtn" data-id="${data}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${data}">
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
            .attr('placeholder', 'Cari berdasarkan kode, bahan, harga...')
            .addClass('form-control form-control-sm ms-2')
            .css({ 'display': 'inline-block', 'width': '300px', 'margin-left': '10px' });

            $('#dataRealisasiTable_filter label').contents().filter(function () {
                return this.nodeType === 3;
            }).remove();
        }
    });

    let baseRow = $('#bahanTable tbody tr:first').clone();
   window.openCreateForm = function () {
    $('#realisasiForm')[0].reset();
    $('#editId').val('');

    // kembalikan name jadi array lagi
    $('select[name="bahanbangunan_id"]').attr('name', 'bahanbangunan_id[]');
    $('input[name="jumlah"]').attr('name', 'jumlah[]');
    $('input[name="harga_satuan"]').attr('name', 'harga_satuan[]');
    $('input[name="satuan"]').attr('name', 'satuan[]');
    $('input[name="stok"]').attr('name', 'stok[]');

    $('#modalFormLabel').text('Tambah Data Realisasi Bahan');
    $('#modalFormModal').modal('show');
};


    $(document).ready(function() {
    // update stok dan satuan otomatis saat pilih bahan
    $(document).on('change', '.bahanSelect', function() {
        const selected = $(this).find(':selected');
        const row = $(this).closest('tr');

        row.find('.stokField').val(selected.data('stok') || '');
        row.find('.satuanField').val(selected.data('satuan') || '');
    });

    // tambah baris baru
    $('#addRow').click(function() {
        let newRow = $('#bahanTable tbody tr:first').clone();
        newRow.find('input').val('');
        newRow.find('select').val('');
        $('#bahanTable tbody').append(newRow);
    });

    // hapus baris
    $(document).on('click', '.removeRow', function() {
    if ($('#bahanTable tbody tr').length > 1) {
      $(this).closest('tr').remove();
    }
  });

    // klik tombol Edit
    $(document).on('click', '.editBtn', function () {
    let id = $(this).data('id');

    $.ajax({
        url: `/realisasi-bahan/edit/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                let data = res.data;
                // hidden id
                $('#editId').val(data.realisasi_bahan_id);

                // isi kode rumah (select)
                $('select[name="realisasi_id"]').val(data.realisasi_id).trigger('change');

                // isi field lain langsung
                $('select[name="bahanbangunan_id[]"]').attr('name', 'bahanbangunan_id').val(data.bahanbangunan_id).trigger('change');
                $('input[name="jumlah[]"]').attr('name', 'jumlah').val(data.jumlah);
                $('input[name="harga_satuan[]"]').attr('name', 'harga_satuan').val(data.harga_satuan);
                $('input[name="satuan[]"]').attr('name', 'satuan').val(data.satuan);
                $('input[name="stok[]"]').attr('name', 'stok').val(data.stok);


                // set judul modal
                $('#modalFormLabel').text('Edit Data Realisasi Bahan');
                $('#modalFormModal').modal('show');
            } else {
                alert(res.message || 'Data tidak ditemukan');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Gagal mengambil data');
        }
    });
});

    // submit form
   $('#realisasiForm').on('submit', function (e) {
    e.preventDefault();
    let id = $('#editId').val();
    let url = id ? `/realisasi-bahan/update/${id}` : `/realisasi-bahan/store`;
    let formData = new FormData(this);
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                let msg = id ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.';

                $('#realisasiForm')[0].reset();
                $('#successMessage').text(msg);
                $('#successModal').modal('show');
                $('#modalFormModal').modal('hide');
                $('#dataRealisasiTable').DataTable().ajax.reload();
                
            } else {
                alert(res.message || 'Terjadi kesalahan');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Gagal menyimpan data');
        }
        });
    });
    let deleted = null;
    $(document).on('click', '.deleteBtn', function() {
        deleteId = $(this).data('id');
        $('#confirmDeleteModal').modal('show');
    });
    $('#confirmDeleteBtn').on('click', function(){
        if (!deleteId) return;
        $.ajax({
            url: `/realisasi-bahan/delete/${deleteId}`,
            method: 'DELETE',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#confirmDeleteModal').modal('hide');
                    $('#successMessage').text('Data Berhasil di hapus.')
                    $('#successModal').modal('show');
                    $('#dataRealisasiTable').DataTable().ajax.reload();
                } else {
                    alert(res.message || 'Gagal Menghapus data');
                }
            }
        })
    })
    });

});
