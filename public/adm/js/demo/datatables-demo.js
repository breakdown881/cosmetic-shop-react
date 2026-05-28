// Call the dataTables jQuery plugin
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [],
        "columnDefs": [
            { "orderable": false, "targets": "no-sort" }  // Tắt sort cho cột có class "no-sort"
        ]
    });
});
