<?php
include "db_config.php";

// ✅ Validate inputs
$module = $_POST["module"] ?? '';
$id = $_POST["id"] ?? '';

if (!$module || !$id) {
    echo "❌ Invalid request";
    exit;
}

$db = $module;
$pk = $module . "_id";

// ✅ Keep module name safe
$form_name = $module . "_module";

// ✅ JSON file
$json_file = $module . "_module.json";

if (!file_exists($json_file)) {
    echo "❌ JSON file not found";
    exit;
}

// Load JSON config
$json = file_get_contents($json_file);
$data = json_decode($json, true);

if (!$data) {
    echo "❌ Invalid JSON structure";
    exit;
}

// Fetch row
$sql = "SELECT * FROM $db WHERE $pk='" . mysqli_real_escape_string($conn, $id) . "'";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    echo "❌ Record not found";
    exit;
}

$row = $result->fetch_assoc();

// Start form
$form = '';
$form .= '<form action="update_generic_form.php" method="post" id="generic_form_update">';
$form .= '<table class="table table-striped">';

foreach ($data as $entry) {

    $field = $entry["field"];
    $mode = $entry["mode"];
    $name_id = explode(".", $entry["Save_to"])[1];

    $value = htmlspecialchars($row[$name_id] ?? '');

    $form .= "<tr><td>$field</td><td>";

    // ✅ INPUT
    if ($mode == "input") {
        $form .= "<input type='text' class='form-control input' 
                    name='$name_id' 
                    value='$value'>";
    }

    // ✅ DATE
    if ($mode == "date") {
        $form .= "<input type='text' class='form-control date' 
                    name='$name_id' 
                    value='$value'>";
    }

    // ✅ SELECT
    if ($mode == "select") {

        $refers_to = $entry["input_src"]["refers_to"] ?? '';
        $condition = $entry["input_src"]["condition"] ?? '';

        $sql1 = "SELECT * FROM $refers_to";
        if (!empty($condition)) {
            $sql1 .= " WHERE $condition";
        }

        $result1 = $conn->query($sql1);

        $form .= "<select class='form-control' name='$name_id'>";

        while ($row1 = $result1->fetch_assoc()) {

            $id_field = array_key_first($row1);
            $option_value = $row1[$id_field];

            $label_parts = [];
            foreach ($row1 as $key => $val) {
                $parts = explode("_", $key);
                $labelKey = (count($parts) > 1) ? $parts[1] : $key;
                $label_parts[] = "$labelKey: $val";
            }

            $label = implode(", ", $label_parts);
            $selected = ($option_value == $value) ? "selected" : "";

            $form .= "<option value='$option_value' $selected>$label</option>";
        }

        $form .= "</select>";
    }

    $form .= "</td></tr>";
}

// ✅ Hidden fields (VERY IMPORTANT)
$form .= "<tr><td colspan='2'>";
$form .= "<input type='hidden' name='form_name' value='$form_name'>";
$form .= "<input type='hidden' name='$pk' value='$id'>";
$form .= "</td></tr>";

$form .= "</table></form>";

echo $form;
?>

<!-- Scripts -->
<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>

<script>
// ✅ Datepicker
$(function () {
    $(".date").datepicker({
        dateFormat: "dd-mm-yy"
    });
});

// ✅ Validation (safe)
$(document).on('blur', '.input', function() {
    const value = $(this).val().trim();
    const regexPattern = $(this).data('regex');

    if (!regexPattern) return; // skip if no regex

    const pattern = "^" + regexPattern + "$";
    const regex = new RegExp(pattern);

    if (regex.test(value)) {
        $(this).css('border', '2px solid green');
        $("#edit_submit").prop("disabled", false);
    } else {
        $(this).css('border', '2px solid red');
        $("#edit_submit").prop("disabled", true);
    }
});
</script>