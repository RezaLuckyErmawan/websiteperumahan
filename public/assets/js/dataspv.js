$(document).ready(function(){
    $('#dataUserTable').DataTable({
        processing: true,
        serverSide: true,
        
    lengthChange: false,
    lengthMenu: [5, 10,20,25],
        ajax: '/data-spv/json',
        columns: [
            {data : 'nama'},
            {data: 'username'},
            {data: 'role',
                 render: function (data) {
                let style = '';
                switch (data.toLowerCase()) {
                case 'admin':
                    style = 'background-color: #198754; color: white;';
                    break;
                case 'karyawan':
                    style = 'background-color: #0d6efd; color: white;';
                    break;
                case 'mandor':
                    style = 'background-color: #ffc107; color: black;';
                    break;
                case 'spv':
                    style = 'background-color: #ff4d07ff; color: white;';
                    break;
                default:
                    style = 'background-color: #6c757d; color: white;';
                }
                return `<span style="${style} padding: 6px 10px; font-size: 0.85rem; border-radius: 10px;">${data}</span>`;
        }
            },
            
            {data: 'status'},
        ]
    });
});