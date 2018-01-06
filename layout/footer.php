<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    
    <script src="http://getbootstrap.com/assets/js/vendor/popper.min.js"></script>
    <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
    <script src="js/bootbox.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

    <script>
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $(this).toggleClass('active');
    });
   	$(document).ready(function() {
        $('.table.report').DataTable({
        dom: 'Bfrtip',
        "paging":   false,
        "info":     false,
        buttons: [
            {
                extend: 'csv',
                exportOptions: {
                    columns: '0,1,2,3,4'
                }
            },

            {
            text: 'Send Reminders',
            action: function ( e, dt, node, config ) {
               alert(1);
            }
        }
        ],
        "aaSorting": []
        });
	} );
   	</script>
</body>
</html>