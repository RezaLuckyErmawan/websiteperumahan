$(document).ready(function () {
    const table = $('#dataRabTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        ajax: '/rab-bahan/json',
        columns: [
            { data: 'kode_rab'},
            { data: 'nama_bahan' },
            { data: 'jumlah_rencana' },
            { 
                data: 'harga_satuan',
                render: function (data) {
                    return 'Rp' + parseInt(data).toLocaleString('id-ID');
                }
            },
            { 
                data: 'sub_total',
                render: function (data) {
                    return 'Rp' + parseInt(data).toLocaleString('id-ID');
                }
            },
            { 
                data: 'id',
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
        initComplete: function () {
          $('#dataRabTable_length')
            .html(`
              <button type="button" onclick="openCreateForm()" class="add-btn1">
                <i class="fas fa-plus"></i> Tambah Data
              </button>
            `);

        }
  });
    
    window.openCreateForm = function () {
        $('#modalForm form')[0].reset();
        $('#modalForm input[name=id]').val('');
        $('#modalFormLabel').text('Tambah Data RAB Bahan');
        $('#modalForm').modal('show');
    };

    // Klik Edit
    $('#dataRabTable').on('click', '.editBtn', function () {
        let id = $(this).data('id');

        $.ajax({
            url: `/rab-bahan/edit/${id}`,
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                let data = res.rab_bahan;
                let bahan = res.bahan;

                $('#modalForm input[name=id]').val(data.id);
                $('#modalForm select[name=rab_id]').val(data.rab_id);

                $('#bahanTable tbody').empty();

                let options = `<option value="">-- Bahan Bangunan --</option>`;
                bahan.forEach(b => {
                    options += `<option value="${b.id}" data-stok="${b.stok}" data-satuan="${b.satuan}">
                        ${b.nama_bahan} (Stok: ${b.stok})
                    </option>`;
                });


                let row = `
                <tr>
                    <td>
                        <select name="bahanbangunan_id" class="form-control bahanSelect">
                            ${options}
                        </select>
                    </td>
                    <td><input type="text" name="stok" class="form-control stokField" readonly></td>
                    <td><input type="number" name="jumlah_rencana" class="form-control" value="${data.jumlah_rencana}" required></td>
                    <td><input type="number" name="harga_satuan" class="form-control" value="${data.harga_satuan}" required></td>
                    <td><input type="text" name="satuan" class="form-control satuanField" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
                </tr>
                `;

                $('#bahanTable tbody').append(row);

                // set bahan terpilih + trigger change agar isi satuan
                $('#bahanTable tbody select[name="bahanbangunan_id"]').val(data.bahanbangunan_id).trigger('change');

                $('#modalFormLabel').text('Edit Data RAB Bahan');
                $('#modalForm').modal('show');
            },
            error: function () {
                alert('Gagal mengambil data');
            }
        });
    });

    // Submit form untuk store atau update
   $('#formRabBahan').on('submit', function (e) {
    e.preventDefault();

    let id = $('input[name=id]').val();
    let url = id ? `/rab-bahan/update/${id}` : `/rab-bahan/store`;

    $.ajax({
        url: url,
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                // bedakan pesan sukses
                let msg = id ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.';
                
                $('#successMessage').text(msg);
                $('#successmodal').modal('show');
                $('#modalForm').modal('hide');
                table.ajax.reload();
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


    // Tambah baris baru di modal
    $(document).on('click', '#addRow', function () {
    let newRow = `
    <tr>
      <td>
        <select name="bahanbangunan_id[]" class="form-control bahanSelect">
          ${bahanOptions}
        </select>
      </td>
      <td><input type="text" class="form-control stokField" name="stok[]" readonly></td>
      <td><input type="number" name="jumlah_rencana[]" class="form-control" required></td>
      <td><input type="number" name="harga_satuan[]" class="form-control" required></td>
      <td><input type="text" class="form-control satuanField" name="satuan[]" readonly></td>
      <td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
    </tr>
    `;
    $('#bahanTable tbody').append(newRow);
});


    // Hapus baris di modal
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
    });

    // Auto isi satuan saat pilih bahan
   $(document).on('change', '.bahanSelect', function () {
    let selected = $(this).find(':selected');
    let stok = selected.data('stok') || '';
    let satuan = selected.data('satuan') || '';

    let row = $(this).closest('tr');
    row.find('.stokField').val(stok);
    row.find('.satuanField').val(satuan);
});

    // Hapus data
    let deleteId = null;
    $('#dataRabTable').on('click', '.deleteBtn', function () {
        deleteId = $(this).data('id');
        $('#modalConfirmDelete').modal('show');
    });

    $('#btnDeleteConfirm').on('click', function () {
        if (!deleteId) return;

        $.ajax({
            url: `/rab-bahan/delete/${deleteId}`,
            method: 'DELETE',
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    $('#modalConfirmDelete').modal('hide');

                    $('#successMessage').text('Data berhasil dihapus.');
                    $('#successmodal').modal('show');

                    $('#dataRabTable').DataTable().ajax.reload();
                } else {
                    alert(res.message || 'Gagal menghapus data');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Terjadi kesalahan pada server');
            }
        });
    });
});
