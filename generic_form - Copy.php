<?php
$json = file_get_contents('state_module.json');
$data = json_decode($json, true);

$form = '';
$form .= '<form action="save_generic_form.php" method="post">';
$form .= '<table>';

if (is_array($data)) {
    foreach ($data as $entry) {
        $field = $entry["field"];
        $mode = $entry["mode"];
        $name_id = explode(".", $entry["Save_to"])[1];

        $form .= "<tr>";
        $form .= "<td>$field</td><td>";

        if ($mode == "input") {
            $regex = $entry["regex"];
            $form .= "<input type='text' data-regex='$regex' class='input' name='$name_id' id='$name_id'>";
        }

        if ($mode == "select") {
            $refers_to = $entry["input_src"]["refers_to"];
            $condition = $entry["input_src"]["condition"];
            $form .= "<select class='select' data-src='$refers_to' data-condition='$condition' name='$name_id' id='$name_id'></select>";

            foreach ($entry["input_src"]["display"] as $disp_name) {
                $form .= "<input name='$disp_name' id='$disp_name' readonly data-takes-after='$name_id'>";
            }
        }

        if ($mode == "calculated") {
            $formula = $entry["formula"];
            $dependents = $entry["dependents"];
            $form .= "<input type='text' class='calculated' id='$name_id' name='$name_id' data-formula='$formula' data-dependents='$dependents'>";
        }

        $form .= "</td></tr>";
    }

    $form .= "<tr><td colspan='2'>";
    $form .= "<input value='state_module' type='hidden' name='form_name'>";
    $form .= "<input type='submit' value='Go'>";
    $form .= "</td></tr>";
} else {
    $form .= "<tr><td colspan='2'>Error decoding JSON.</td></tr>";
}

$form .= "</table>";
$form .= "</form>";

echo $form;
?>

   <script src="vendor/jquery/jquery.min.js"></script>
<script>
$('.input').on('blur', function() {
    const value = $(this).val().trim();
    const pattern = "^"+$(this).data('regex')+"$";
    const regex = new RegExp(pattern);

    if (regex.test(value)) {
        console.log('✅ Valid:', value);
        $(this).css('border', '2px solid green');
    } else {
        console.log('❌ Invalid:', value);
        $(this).css('border', '2px solid red');
    }
});
$('.select').on('focus', function() {
  let $select = $(this); // Store the jQuery object
  let data_src = $select.data('src');
  let data_condition = $select.data('condition');

  $.post("fetch_input.php", { data_src: data_src, data_condition: data_condition })
    .done(function(data) {
      console.log($.trim(data));
      let data_array = JSON.parse($.trim(data));
      
      $select.empty(); // Clear existing options

      data_array.forEach(object => {
        // Assuming object has fields like id and name
        const option = $('<option></option>')
          .val(object.id || JSON.stringify(object))
          .text(object.name || object.label || JSON.stringify(object));
          
        $select.append(option);
      });
    });
});
$('.select').on('focusout', function () {
    const name = $(this).attr('name');
    const value = JSON.parse($(this).val());
	alert(value);
	$(`input[data-takes-after="${name}"]`).each(function () {
		let json_key = $(this).attr('name');
		console.log(json_key);
		let value_key = value[json_key];
		console.log(value_key);
		
      $(this).val(value_key); // Optional: update the value
      console.log(`Matched input for ${name}:`, this);
	  resolve(json_key);
    });
  });
  function resolve(decider){
	  var yourSubstring = decider;

var matchingElements = $('.calculated').filter(function () {
  return $(this).data('dependents')?.toString().includes(yourSubstring);
});

// Do something with the matching elements, e.g., log them
matchingElements.each(function () {
 
  console.log($(this).data('formula'));
   console.log($(this).data('dependents'));
   let vars=$(this).data('dependents').split("#");
   let parts = $(this).data('formula').toUpperCase();
   let exp="";
   let dep_ind=0;
  for(var i=0;i<parts.length;i++){
	  if(parts[i]>='A' && parts[i]<='Z'){
		  exp+=document.getElementById(vars[dep_ind++]).value;
		 // console.log(vars[dep_ind++]);
		  
	  }else{
		  exp+=parts[i];
	  }
	  
  }
  let ans=eval(exp) ;
  $(this).val(ans);
});
	  
	  
  }
</script>