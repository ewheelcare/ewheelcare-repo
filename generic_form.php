<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>

<?php
$module=$_POST["module"];
$form_name=$module."_module";
$module = $module."_module.json";
$json = file_get_contents($module);
$data = json_decode($json, true);

$form = '';
$form .= '<form action="update_generic_form.php" method="post" id="generic_form">';
$form .= "<div class='alert alert-danger' id='error'></div>";
$form .= '<table class="table table-striped">';

if (is_array($data)) {
    foreach ($data as $entry) {

        $field = $entry["field"];
        $mode = $entry["mode"];
        $name_id = explode(".", $entry["Save_to"])[1];

        $form .= "<tr>";
        $form .= "<td>$field</td><td>";

        // INPUT
        if ($mode == "input") {
            $regex = $entry["regex"];
            $form .= "<input type='text' data-regex='$regex' class='form-control input' name='$name_id' id='$name_id'>";
        }

        // DATE
        if ($mode == "date") {
            $form .= "<input type='text' class='form-control date' name='$name_id' id='$name_id'>";
        }

        // SELECT
        if ($mode == "select") {
            $refers_to = $entry["input_src"]["refers_to"];
            $condition = $entry["input_src"]["condition"];

            $form .= "<select class='form-control select' 
                        data-src='$refers_to' 
                        data-condition='$condition' 
                        name='$name_id' 
                        id='$name_id'>
                      </select>";
        }

        // CALCULATED
        if ($mode == "calculated") {
            $formula = $entry["formula"];
            $dependents = $entry["dependents"];
            $form .= "<input type='text' class='form-control calculated' 
                        id='$name_id' 
                        name='$name_id' 
                        data-formula='$formula' 
                        data-dependents='$dependents'>";
        }

        $form .= "</td></tr>";
    }

    $form .= "<tr><td colspan='2'>";
    $form .= "<input value='".$form_name."' type='hidden' name='form_name'>";
    $form .= "</td></tr>";

} else {
    $form .= "<tr><td colspan='2'>Error decoding JSON.</td></tr>";
}

$form .= "</table>";
$form .= "</form>";

echo $form;
?>

<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/jquery-ui.min.js"></script>

<script>

// DATE PICKER
$(function () {
    $(".date").datepicker({
        dateFormat: "dd-mm-yy"
    });
});

// VALIDATION
$('.input').on('blur', function() {
    const value = $(this).val().trim();
    const pattern = "^"+$(this).data('regex')+"$";
    const regex = new RegExp(pattern);

    if (regex.test(value)) {
        $(this).css('border', '2px solid green');
        $("#error").html("");
        $("#add_submit").prop("disabled", false);
    } else {
        $(this).css('border', '2px solid red');
        $("#error").html("Please enter valid value");
        $("#add_submit").prop("disabled", true);
    }
});

// REMOVE COMMAS
$(document).on("input", ".input", function () {
    this.value = this.value.replace(/,/g, "،");
});


// ✅ UPDATED SELECT LOAD (CLEAN DROPDOWN)
$('.select').on('focus', function() {

    let $select = $(this);
    let data_src = $select.data('src');
    let data_condition = $select.data('condition');

    $.post("fetch_input.php", {
        data_src: data_src,
        data_condition: data_condition
    })
    .done(function(data) {

        let data_array = JSON.parse($.trim(data));
        $select.empty();

        data_array.forEach(object => {

            // ✅ GET ONLY NAME FIELD (IMPORTANT FIX)
            let values = Object.values(object);

            let id = values[0];        // ID
            let name = values[1];      // NAME (SHOW THIS ONLY)

             // Get first word from shop name
    
   // let firstPart = name.trim().split(/\s+/)[0].toUpperCase();
    const option = $('<option></option>')
     //   .val(firstPart)   // submitted after form save
          .val(id)
        .text(name);      // full name shown in dropdown

    $select.append(option);
        });
    });
});

</script>