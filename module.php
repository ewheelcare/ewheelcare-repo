<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
$module = $_GET["param"] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "header_include.php"; ?>
</head>
<body id="page-top">

<div id="wrapper">
    <?php include "sidemenu.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include "topmenu.php"; ?>

            <div class="container-fluid">

                <div class="alert alert-info col-md-12"
                     style="text-transform:uppercase;font-weight:bold;text-align:center">
                    Master Maintenance : <?= htmlspecialchars($module) ?>
                </div>

                <div class="d-flex align-items-center mb-3 flex-wrap" style="gap:12px;">
                    <button type="button" class="btn btn-primary" id="add_opener">
                        Add <?= htmlspecialchars($module) ?>
                    </button>

                    <?php if ($module === 'item'): ?>
                    <div class="btn-group ml-auto" id="statusFilter" role="group">
                        <button type="button" class="btn btn-success btn-sm"
                                id="filterActive" onclick="setFilter('A')">
                            <i class="fas fa-check-circle mr-1"></i> Active
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm"
                                id="filterDeleted" onclick="setFilter('D')">
                            <i class="fas fa-trash-alt mr-1"></i> Deleted
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($module === 'item'): ?>
                <div class="mb-2" style="font-size:13px;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;
                                 background:#28a745;margin-right:5px;vertical-align:middle;"></span>
                    <span class="mr-3">Parent Item</span>
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;
                                 background:#007bff;margin-right:5px;vertical-align:middle;"></span>
                    <span>Sub Item</span>
                </div>
                <?php endif; ?>

                <div class="row">
                    <div class="table-responsive col-md-12">
                        <table id="myTable" class="display table table-striped"
                               style="width:100%;font-size:75%"></table>
                    </div>
                </div>

            </div>
        </div>

        <?php include "footer.php"; ?>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add <?= htmlspecialchars($module) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="add_block"></div>
            <div class="modal-footer">
                <span id="error" class="text-danger mr-auto small"></span>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="add_submit">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit <?= htmlspecialchars($module) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="edit_block"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="edit_submit">Save</button>
            </div>
        </div>
    </div>
</div>

<?php include "modals.php"; ?>
<?php include "footer_include.php"; ?>

<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script>
var module    = "<?= addslashes($module) ?>";
var addModal  = new bootstrap.Modal(document.getElementById('addModal'));
var editModal = new bootstrap.Modal(document.getElementById('editModal'));
var currentStatus = 'A';   // default filter: Active
var dtInstance = null;     // DataTable instance reference

function initializeDatePickers() {
    $(".date").datepicker({ dateFormat: "yy-mm-dd" });
}

// ── Status filter (item only) ─────────────────────────────────────────────────
function setFilter(status) {
    currentStatus = status;

    if (status === 'A') {
        $('#filterActive').removeClass('btn-outline-success').addClass('btn-success');
        $('#filterDeleted').removeClass('btn-danger').addClass('btn-outline-danger');
    } else {
        $('#filterDeleted').removeClass('btn-outline-danger').addClass('btn-danger');
        $('#filterActive').removeClass('btn-success').addClass('btn-outline-success');
    }

    // Destroy and reload table with new status
    if (dtInstance) {
        dtInstance.destroy();
        $('#myTable').empty();
        dtInstance = null;
    }
    loadItemTable(status);
}

// ── Add Button ────────────────────────────────────────────────────────────────
$('#add_opener').on('click', function () {
    if (module === "item") {
        window.location.href = "item_create.php";
        return;
    }
    $.post("generic_form.php", { module: module })
        .done(function (data) {
            $("#add_block").html($.trim(data));
            addModal.show();
            initializeDatePickers();
        });
});

$('#add_submit').on('click', function (e) {
    e.preventDefault();
    var isValid = true;
    $('#generic_form').find('input, select, textarea').each(function () {
        if ($(this).prop('disabled')) return;
        if ($.trim($(this).val()) === '') {
            isValid = false;
            $(this).focus();
            $("#error").text("Please fill up all required fields.");
            return false;
        }
    });
    if (!isValid) return;
    $("#error").text('');
    var form = $("#generic_form");
    $.post(form.attr('action'), form.serialize())
        .done(function (response) { alert(response); location.reload(); })
        .fail(function () { alert('Something went wrong.'); });
});

$(document).on('click', '#edit_submit', function (e) {
    e.preventDefault();
    var form = $("#generic_form_update");
    if (!form.length || !form.attr('action')) { alert("Form not found or action missing!"); return; }
    $.post(form.attr('action'), form.serialize())
        .done(function (response) { alert(response); location.reload(); })
        .fail(function () { alert('Something went wrong.'); });
});

$(document).ready(function () {
    if (module === 'item') {
        loadItemTable('A');
    } else {
        loadGenericTable();
    }
});

function loadItemTable(status) {
    $.post("item_fetch.php", { status: status }, function (jsonData) {

        if (!jsonData || jsonData.length === 0) {
            $('#myTable').html("<p class='p-3 text-muted'>No records found.</p>");
            return;
        }

        var isDeleted = (status === 'D');

        var columns = [
            { title: 'Item ID', data: 'item_id' },
            {
                title: 'Name',
                data: 'item_name',
                render: function (val, type, row) {
                    if (type !== 'display') return val;
                    var dot = row._role === 'sub'
                        ? '<span title="Sub Item" style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#007bff;margin-right:6px;vertical-align:middle;"></span>'
                        : '<span title="Parent Item" style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#28a745;margin-right:6px;vertical-align:middle;"></span>';
                    var indent = row._role === 'sub'
                        ? '<span style="display:inline-block;width:16px;"></span>' : '';
                    return indent + dot + val;
                }
            },
            { title: 'Description',         data: 'item_description' },
            { title: 'Cost',                data: 'cost' },
            { title: 'Tax CGST %',          data: 'tax_pc' },
            { title: 'Tax SGST %',          data: 'tax_pc_sgst' },
            { title: 'HSN',                 data: 'hsn' },
            { title: 'Tyre Type',           data: 'tyre_type_name' },
            { title: 'Price Edit',          data: 'price_edit' },
            { title: 'Purchase Tax CGST %', data: 'purchase_tax_cgst' },
            { title: 'Purchase Tax IGST %', data: 'purchase_tax_igst' },
            {
                title: 'Actions',
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    var id = row.item_id;

                    if (isDeleted) {
                        if (row._role === 'sub') return '—';
                        return '<div style="white-space:nowrap;">'
                             + '<button class="btn btn-sm" onclick="restoreFunction(' + id + ')" '
                             + 'title="Restore to Active" style="color:#28a745;background:none;border:none;font-size:16px;padding:2px 6px;">'
                             + '<i class="fas fa-undo-alt"></i></button>'
                             + '</div>';
                    }

                    if (row._role === 'sub') {
                        return '<div style="white-space:nowrap;">'
                             + '<button class="btn btn-sm" onclick="editFunction(' + row._parent_id + ')" '
                             + 'title="Edit Parent" style="color:#f6a800;background:none;border:none;font-size:16px;padding:2px 6px;">'
                             + '<i class="fas fa-pencil-alt"></i></button>'
                             + '</div>';
                    }
                    return '<div style="white-space:nowrap;">'
                         + '<button class="btn btn-sm" onclick="editFunction(' + id + ')" '
                         + 'title="Edit" style="color:#f6a800;background:none;border:none;font-size:16px;padding:2px 6px;">'
                         + '<i class="fas fa-pencil-alt"></i></button>'
                         + '<button class="btn btn-sm" onclick="delFunction(' + id + ')" '
                         + 'title="Delete" style="color:#e74c3c;background:none;border:none;font-size:16px;padding:2px 6px;">'
                         + '<i class="fas fa-trash-alt"></i></button>'
                         + '</div>';
                }
            }
        ];

        dtInstance = $('#myTable').DataTable({
            data: jsonData,
            columns: columns,
            order: [[0, 'asc']],
            createdRow: function (row, data) {
                if (data._role === 'sub') {
                    $(row).css('background-color', '#f0f4ff');
                }
                if (data._status === 'D') {
                    $(row).css('opacity', '0.65');
                }
            }
        });

    }, 'json');
}

function loadGenericTable() {
    $.post("generic_fetch.php", { module: module }, function (jsonData) {
        if (!jsonData || jsonData.length === 0) {
            $('#myTable').html("<p class='p-3'>No data available</p>");
            return;
        }
        var columns = Object.keys(jsonData[0])
            .filter(function (key) { return key.toUpperCase() !== 'ACTION_BUTTON'; })
            .map(function (key) { return { title: key, data: key }; });

        columns.push({
            title: 'Actions',
            data: null,
            orderable: false,
            render: function (data, type, row) {
                var id = row[Object.keys(row)[0]];
                return '<div style="white-space:nowrap;">'
                     + '<button class="btn btn-sm" onclick="editFunction(' + id + ')" '
                     + 'title="Edit" style="color:#f6a800;background:none;border:none;font-size:16px;padding:2px 6px;">'
                     + '<i class="fas fa-pencil-alt"></i></button>'
                     + '<button class="btn btn-sm" onclick="delFunction(' + id + ')" '
                     + 'title="Delete" style="color:#e74c3c;background:none;border:none;font-size:16px;padding:2px 6px;">'
                     + '<i class="fas fa-trash-alt"></i></button>'
                     + '</div>';
            }
        });
        dtInstance = $('#myTable').DataTable({ data: jsonData, columns: columns });
    }, 'json');
}

function editFunction(id) {
    if (module === "item") {
        window.location.href = "item_create.php?item_id=" + id;
        return;
    }
    $.post("generic_form_update.php", { module: module, id: id })
        .done(function (data) {
            $("#edit_block").html($.trim(data));
            editModal.show();
            initializeDatePickers();
        });
}

function delFunction(id) {
    if (!confirm("Are you sure you want to delete?")) return;
    if (module === "item") {
        $.post("item_delete.php", { item_id: id })
            .done(function (response) { alert(response); setFilter('A'); })
            .fail(function () { alert('Something went wrong.'); });
        return;
    }
    $.post("generic_form_delete.php", { module: module, id: id })
        .done(function () { alert("Entry deleted successfully"); location.reload(); });
}

function restoreFunction(id) {
    if (!confirm("Restore this item and its sub items to Active?")) return;
    $.post("item_restore.php", { item_id: id })
        .done(function (response) { alert(response); setFilter('D'); })
        .fail(function () { alert('Something went wrong.'); });
}
</script>

</body>
</html>