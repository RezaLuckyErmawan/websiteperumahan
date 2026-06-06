$('#dataPembatalanTable').DataTable({
    processing: true,
    serverSide: true,
    
    lengthChange: false,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    ajax: '/pembatalan-transaksi/json',
    columns: [
        { data: 'kode_rumah'},
        { data: 'nama'},
        { data: 'harga_beli',
            render: function(data) {
                return 'Rp' + parseInt(data).toLocaleString('id-ID');
            }
        },
        { data: 'keterangan_pembatalan'},
        { data: 'tanggal_pembelian'},
        
    ],

    initComplete: function() {
        $('#dataPembatalanTable_filter input')
        .attr('placeholder', 'Cari berdasarkan kode, lokasi, tipe, status...')
        .addClass('form-control form-control-sm ms-2')
        .css({
            'display': 'inline-block',
            'width': '300px',
            'margin-left': '10px',
        });
        $('#dataPembatalanTable_filter label').contents().filter(function (){
            return this.nodeType === 3;
        }).remove();
        $('#dataPembatalanTable_filter label')
        .prepend('<i class="text-primary me-2"></i>');

    }
})