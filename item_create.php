<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
include "db_config.php";

$item_id = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;

$item = [
    'item_name'         => '', 'item_description'  => '',
    'cost'              => '', 'tax_pc'            => '',
    'tax_pc_sgst'       => '', 'hsn'               => '',
    'tyre_type_name'    => '',
    'price_edit'        => '', 'purchase_tax_cgst' => '',
    'purchase_tax_igst' => ''
];

$subItems = [];

if ($item_id > 0) {
    $r = $conn->query("SELECT * FROM item WHERE item_id=" . $item_id);
    if ($r && $r->num_rows) {
        $item = $r->fetch_assoc();
    }

    // Load sub items — excludes parent-to-self groupassociation row
    $r2 = $conn->query(
        "SELECT g.perc, i.*
         FROM groupassociation g
         INNER JOIN item i ON g.item_id = i.item_id
         WHERE g.itemgroup_id = " . $item_id . "
           AND g.item_id <> g.itemgroup_id"
    );
    while ($r2 && $m = $r2->fetch_assoc()) {
        $subItems[] = $m;
    }
}

$hasSubItems = count($subItems) > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "header_include.php"; ?>
<style>
label { color: #000 !important; font-weight: 600; }

.sub-item-card {
    border: 1px solid #d1d3e2;
    border-radius: 8px;
    padding: 18px 20px 10px;
    margin-bottom: 16px;
    background: #f8f9fc;
    position: relative;
}
.sub-item-card .card-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .05em;
    color: #5a5c69;
    text-transform: uppercase;
    margin-bottom: 12px;
}
.sub-item-card .btn-remove-sub {
    position: absolute;
    top: 12px;
    right: 14px;
}
#percentageSummary { font-size: 14px; }
#percentageSummary .badge { font-size: 13px; padding: 5px 10px; }
.warning-box { display: none; font-size: 13px; }
.is-invalid-custom { border-color: #e74a3b !important; }
</style>
</head>
<body id="page-top">
<div id="wrapper">
<?php include "sidemenu.php"; ?>
<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<?php include "topmenu.php"; ?>

<div class="container-fluid">

<div class="alert alert-secondary text-center font-weight-bold mb-3">
    <?= $item_id ? 'EDIT ITEM' : 'CREATE ITEM' ?>
</div>

<input type="hidden" id="item_id" value="<?= $item_id ?>">

<!-- ===== PARENT ITEM CARD ===== -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Parent Item Details</h6>
    </div>
    <div class="card-body">
        <div class="row">

            <div class="col-md-6 mb-3">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="item_name"
                       value="<?= htmlspecialchars($item['item_name']) ?>"
                       placeholder="Item name">
                <div class="text-warning small mt-1" id="nameWarn" style="display:none;">
                    ⚠ Item name already exists. A new record will still be created.
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label>Description <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="item_description"
                       value="<?= htmlspecialchars($item['item_description']) ?>"
                       placeholder="Item description">
            </div>

            <div class="col-md-6 mb-3">
                <label>Cost <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="cost"
                       value="<?= htmlspecialchars($item['cost']) ?>"
                       placeholder="0.00">
            </div>

            <div class="col-md-6 mb-3">
                <label>Tax CGST (%) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="tax_pc"
                       value="<?= htmlspecialchars($item['tax_pc']) ?>"
                       placeholder="0">
            </div>

            <div class="col-md-6 mb-3">
                <label>Tax SGST (%) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="tax_pc_sgst"
                       value="<?= htmlspecialchars($item['tax_pc_sgst']) ?>"
                       placeholder="0">
            </div>

            <div class="col-md-6 mb-3">
                <label>HSN <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="hsn"
                       value="<?= htmlspecialchars($item['hsn']) ?>"
                       placeholder="HSN code">
            </div>

            <div class="col-md-6 mb-3">
                <label>Purchase Tax CGST (%) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="purchase_tax_cgst"
                       value="<?= htmlspecialchars($item['purchase_tax_cgst']) ?>"
                       placeholder="0">
            </div>

            <div class="col-md-6 mb-3">
                <label>Purchase Tax IGST (%) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="purchase_tax_igst"
                       value="<?= htmlspecialchars($item['purchase_tax_igst']) ?>"
                       placeholder="0">
            </div>

        </div>
    </div>
</div>

<!-- ===== SUB ITEMS SECTION ===== -->
<div class="card shadow mb-4">
    <div class="card-body">

        <div class="mb-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="has_subitems"
                       <?= $hasSubItems ? 'checked' : '' ?>>
                <label class="custom-control-label" for="has_subitems" style="font-size:15px;">
                    Want to add Sub Items?
                </label>
            </div>
        </div>

        <div id="subItemsBlock" style="<?= $hasSubItems ? '' : 'display:none;' ?>">

            <div id="subItemsContainer">
                <?php foreach ($subItems as $idx => $si): ?>
                <div class="sub-item-card" data-index="<?= $idx ?>">
                    <input type="hidden" class="si-item-id" value="<?= intval($si['item_id']) ?>">
                    <div class="card-label">Sub Item <?= $idx + 1 ?></div>
                    <button type="button" class="btn btn-danger btn-sm btn-remove-sub"
                            title="Remove sub item">✕</button>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Sub Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control si-name"
                                   value="<?= htmlspecialchars($si['item_name']) ?>"
                                   placeholder="Sub item name">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Sub Item Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control si-description"
                                   value="<?= htmlspecialchars($si['item_description']) ?>"
                                   placeholder="Sub item description">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>HSN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control si-hsn"
                                   value="<?= htmlspecialchars($si['hsn']) ?>"
                                   placeholder="HSN code">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Cost <span class="text-danger">*</span></label>
                            <input type="text" class="form-control si-cost"
                                   value="<?= htmlspecialchars($si['cost']) ?>"
                                   placeholder="0.00">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Percentage (%) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control si-perc"
                                   value="<?= htmlspecialchars($si['perc']) ?>"
                                   placeholder="0">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addSubItem">
                + Add Sub Item
            </button>

            <!-- Percentage Summary -->
            <div id="percentageSummary" class="p-3 border rounded bg-light mb-2">
                <div class="d-flex flex-wrap align-items-center" style="gap:16px;">
                    <span>Sub Item Total: <span class="badge badge-info" id="subTotal">0</span>%</span>
                    <span>Parent Remaining: <span class="badge badge-success" id="parentRemaining">100</span>%</span>
                </div>
                <div class="alert alert-warning mt-2 mb-0 warning-box" id="warnFull">
                    ⚠ Sub Items consume 100%. Main Item percentage becomes <strong>0%</strong>.
                </div>
                <div class="alert alert-danger mt-2 mb-0 warning-box" id="warnOver">
                    ✕ Percentage cannot exceed 100%.
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ===== ACTIONS ===== -->
<div class="mb-5 d-flex justify-content-end">
    <button type="button" id="saveItem" class="btn btn-success px-4">
        <i class="fas fa-save mr-1"></i> Save
    </button>
</div>

</div><!-- /container-fluid -->

<?php include "footer.php"; ?>
</div>
</div>
<?php include "footer_include.php"; ?>

<script src="js/jquery-3.5.1.min.js"></script>
<script>
var subItemIndex = <?= count($subItems) ?>;

// ── Helper: is value a valid non-negative number? ────────────────────────────
function isValidNum(val) {
    var v = $.trim(val);
    return v !== '' && !isNaN(v) && parseFloat(v) >= 0;
}

// ── Helper: is value a valid percentage (0–100)? ─────────────────────────────
function isValidPerc(val) {
    return isValidNum(val) && parseFloat(val) <= 100;
}

// ── Toggle sub items block ───────────────────────────────────────────────────
$('#has_subitems').change(function () {
    $('#subItemsBlock').toggle(this.checked);
    calcPerc();
});

// ── Add sub item card ────────────────────────────────────────────────────────
$('#addSubItem').click(function () {
    var idx = subItemIndex++;
    var card = `
    <div class="sub-item-card" data-index="${idx}">
        <input type="hidden" class="si-item-id" value="">
        <div class="card-label">Sub Item ${idx + 1}</div>
        <button type="button" class="btn btn-danger btn-sm btn-remove-sub" title="Remove">✕</button>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label>Sub Item Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control si-name" placeholder="Sub item name">
            </div>
            <div class="col-md-3 mb-3">
                <label>Sub Item Description <span class="text-danger">*</span></label>
                <input type="text" class="form-control si-description" placeholder="Sub item description">
            </div>
            <div class="col-md-2 mb-3">
                <label>HSN <span class="text-danger">*</span></label>
                <input type="text" class="form-control si-hsn" placeholder="HSN code">
            </div>
            <div class="col-md-2 mb-3">
                <label>Cost <span class="text-danger">*</span></label>
                <input type="text" class="form-control si-cost" placeholder="0.00">
            </div>
            <div class="col-md-2 mb-3">
                <label>Percentage (%) <span class="text-danger">*</span></label>
                <input type="text" class="form-control si-perc" placeholder="0">
            </div>
        </div>
    </div>`;
    $('#subItemsContainer').append(card);
    renumberCards();
    calcPerc();
});

// ── Remove sub item card ─────────────────────────────────────────────────────
$(document).on('click', '.btn-remove-sub', function () {
    $(this).closest('.sub-item-card').remove();
    renumberCards();
    calcPerc();
});

function renumberCards() {
    $('#subItemsContainer .sub-item-card').each(function (i) {
        $(this).find('.card-label').text('Sub Item ' + (i + 1));
    });
}

// ── Percentage calculation ───────────────────────────────────────────────────
$(document).on('input change', '.si-perc', calcPerc);

function calcPerc() {
    var total = 0;
    $('.si-perc').each(function () {
        total += parseFloat($(this).val()) || 0;
    });
    total = Math.round(total * 100) / 100;
    var remaining = Math.round((100 - total) * 100) / 100;

    $('#subTotal').text(total);
    $('#parentRemaining').text(remaining < 0 ? 0 : remaining);

    $('#warnFull').hide();
    $('#warnOver').hide();
    $('#subTotal').removeClass('badge-danger badge-warning badge-info');
    $('#parentRemaining').removeClass('badge-danger badge-warning badge-success');

    if (!$('#has_subitems').is(':checked') || $('.sub-item-card').length === 0) {
        $('#subTotal').addClass('badge-info');
        $('#parentRemaining').addClass('badge-success');
        return true;
    }

    if (total > 100) {
        $('#warnOver').show();
        $('#subTotal').addClass('badge-danger');
        $('#parentRemaining').addClass('badge-danger');
        return false;
    } else if (total === 100) {
        $('#warnFull').show();
        $('#subTotal').addClass('badge-warning');
        $('#parentRemaining').addClass('badge-warning');
        return true;
    } else {
        $('#subTotal').addClass('badge-info');
        $('#parentRemaining').addClass('badge-success');
        return true;
    }
}

// ── Duplicate name check (warning only) ─────────────────────────────────────
var nameCheckTimer = null;
$('#item_name').on('input', function () {
    clearTimeout(nameCheckTimer);
    var name = $(this).val().trim();
    var id   = $('#item_id').val();
    $('#nameWarn').hide();
    if (name.length < 2) return;
    nameCheckTimer = setTimeout(function () {
        $.get('item_check_name.php', { item_name: name, item_id: id }, function (res) {
            if (res.trim() === '1') $('#nameWarn').show();
        });
    }, 500);
});

// ── Client-side validation ───────────────────────────────────────────────────
function validateParent() {

    var ok = true;

    // Text required fields
    if ($('#item_name').val().trim() === '') {
        alert('Item Name is required.'); return false;
    }
    if ($('#item_description').val().trim() === '') {
        alert('Item Description is required.'); return false;
    }
    if ($('#hsn').val().trim() === '') {
        alert('HSN is required.'); return false;
    }

    // Numeric required fields
    var numFields = [
        { id: 'cost',               label: 'Cost',               perc: false },
        { id: 'tax_pc',             label: 'Tax CGST (%)',        perc: true  },
        { id: 'tax_pc_sgst',        label: 'Tax SGST (%)',        perc: true  },
        { id: 'purchase_tax_cgst',  label: 'Purchase Tax CGST %', perc: true  },
        { id: 'purchase_tax_igst',  label: 'Purchase Tax IGST %', perc: true  }
    ];

    for (var i = 0; i < numFields.length; i++) {
        var f = numFields[i];
        var val = $('#' + f.id).val().trim();
        if (val === '') {
            alert(f.label + ' is required.');
            $('#' + f.id).focus();
            return false;
        }
        if (isNaN(val) || parseFloat(val) < 0) {
            alert(f.label + ' must be a valid non-negative number.');
            $('#' + f.id).focus();
            return false;
        }
        if (f.perc && parseFloat(val) > 100) {
            alert(f.label + ' cannot exceed 100.');
            $('#' + f.id).focus();
            return false;
        }
    }

    return true;
}

// ── Save ─────────────────────────────────────────────────────────────────────
$('#saveItem').click(function () {

    if (!validateParent()) return;

    var subItems = [];

    if ($('#has_subitems').is(':checked')) {

        if (!calcPerc()) {
            alert('Percentage cannot exceed 100%. Please correct sub item percentages.');
            return;
        }

        var valid = true;
        $('#subItemsContainer .sub-item-card').each(function (i) {
            var name = $(this).find('.si-name').val().trim();
            var desc = $(this).find('.si-description').val().trim();
            var hsn  = $(this).find('.si-hsn').val().trim();
            var cost = $(this).find('.si-cost').val().trim();
            var perc = $(this).find('.si-perc').val().trim();
            var sid  = $(this).find('.si-item-id').val().trim();
            var n    = i + 1;

            if (!name) {
                alert('Sub Item ' + n + ': Name is required.'); valid = false; return false;
            }
            if (!desc) {
                alert('Sub Item ' + n + ': Description is required.'); valid = false; return false;
            }
            if (!hsn) {
                alert('Sub Item ' + n + ': HSN is required.'); valid = false; return false;
            }
            if (!isValidNum(cost)) {
                alert('Sub Item ' + n + ': Cost must be a valid non-negative number.'); valid = false; return false;
            }
            if (!isValidPerc(perc)) {
                alert('Sub Item ' + n + ': Percentage must be a number between 0 and 100.'); valid = false; return false;
            }

            subItems.push({
                item_id:          sid,
                item_name:        name,
                item_description: desc,
                hsn:              hsn,
                cost:             cost,
                perc:             perc
            });
        });

        if (!valid) return;
    }

    $('#saveItem').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

    $.post('item_save.php', {
        item_id:            $('#item_id').val(),
        item_name:          $('#item_name').val(),
        item_description:   $('#item_description').val(),
        cost:               $('#cost').val(),
        tax_pc:             $('#tax_pc').val(),
        tax_pc_sgst:        $('#tax_pc_sgst').val(),
        hsn:                $('#hsn').val(),
        purchase_tax_cgst:  $('#purchase_tax_cgst').val(),
        purchase_tax_igst:  $('#purchase_tax_igst').val(),
        has_subitems:       $('#has_subitems').is(':checked') ? 1 : 0,
        sub_items:          JSON.stringify(subItems)
    }, function (r) {
        alert(r);
        if (r.indexOf('Successfully') > -1) {
            window.location.href = 'module.php?param=item';
        }
    }).always(function () {
        $('#saveItem').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save');
    });
});

// Init
calcPerc();
</script>
</body>
</html>