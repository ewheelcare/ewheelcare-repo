 <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/popper.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
 <!--   <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
  <!--  <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>-->
	<script>
		window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});

	 $(document).ready(function() {
    $('#login_opener').on('click', function() {
      $('#loginModal').modal('show');
    });
  });

 $('#successModal').on('hidden.bs.modal', function () {
  location.reload();
});
 $('#login').on('click', function() {
      let uid=$("#uid").val();
	  let pwd=$("#pwd").val();
	  let shop=$("#shop").val().split("~")[0];
	  let shop_name=$("#shop").val().split("~")[1];
	  let msg="";
//alert("===");	 
 
if(uid=="" || pwd=="" || shop=="")	{
msg = "Fill up login credentials properly"	;
$("#error_block").html(msg);
$('#errorModal').modal('show');	
return false;
} 

$.post( "loginvalidate.php", { uid: $("#uid").val(), pwd: $("#pwd").val(), shop:shop,shop_name:shop_name })
  .done(function( data ) {
  //alert(data);
  let status=(JSON.parse($.trim(data))).status;
    msg=(JSON.parse($.trim(data))).message;
  if($.trim(status)=="S")
    {//msg = ( "Successfully logged in" );
	$("#success_block").html(msg);
$('#successModal').modal('show');
 setTimeout(() => {
      $('#successModal').modal('hide');
    }, 1600);	
	}
	else
	{//msg = ( "Login Failed. Please Retry" );
$("#error_block").html(msg);
$('#errorModal').modal('show');	
}
  }); 
    });
	
function load_shop_pic(){
	let shop_pic="img/shop/"+document.getElementById("shop").value.split("~")[0]+".jpeg";
	document.getElementById("shop_pic").src=shop_pic;
	
}
		
function goBack() {
    let url = window.location.href;

    if (url.includes("service_trans_new")) {
        $.post("delete_draft_service.php", {
            value: $("#trans_id").val()
        }, function () {
            window.location.href = "service.php";
        });
    } else if (url.includes("sales_trans_new")) {
        $.post("delete_draft_sales.php", {
            value: $("#trans_id").val(),
            ver: $("#ver").val()
        }, function () {
            window.location.href = "sales.php";
        });
    } else if (url.includes("receipt_trans_new")) {
        // Fallback for receipt module
        window.location.href = "receipt.php";
    } else if (url.includes("payments")) {
        window.location.href = "payments.php";
    } else if (url.includes("service_receipt")) {
        window.location.href = "service.php";
    } else {
        redirectBack();
    }
}

/* Reusable redirect function */
function redirectBack() {
    let url = window.location.href;
    let path = window.location.pathname;

    if (document.referrer !== "" && !document.referrer.includes(path)) {
        window.history.back();
    } else {
        // Safe fallbacks based on context, avoiding infinite loops
        if (url.includes("service") && !url.includes("service.php")) {
            window.location.href = "service.php";
        } else if (url.includes("sales") && !url.includes("sales.php")) {
            window.location.href = "sales.php";
        } else if (url.includes("receipt") && !url.includes("receipt.php")) {
            window.location.href = "receipt.php";
        } else if (url.includes("payment") && !url.includes("payments.php")) {
            window.location.href = "payments.php";
        } else {
            window.location.href = "index.php";
        }
    }
}

	</script>