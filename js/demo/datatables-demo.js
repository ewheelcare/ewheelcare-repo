// Call the dataTables jQuery plugin
/*$(document).ready(function() {
  $('#dataTable').DataTable();
});*/
$(document).ready(function() {
  $('#dataTable').DataTable({
    order: [[0, 'desc']]   // first column (index starts at 0)
  });
});
