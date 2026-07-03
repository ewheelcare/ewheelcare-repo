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

	var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
	const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
	const successModal = new bootstrap.Modal(document.getElementById('successModal'));
 
	 $(document).ready(function() {
    $('#login_opener').on('click', function() {
      //alert('Button was clicked!');
      // You can run any function here
	  loginModal.show();
    });
  });
 /* $(document).ready(function() {
    $('.btn-close').on('click', function() {
       const $modal = $(this).closest('.modal')[0];
      const modalInstance = bootstrap.Modal.getOrCreateInstance($modal);
      modalInstance.hide();
    });
  });*/
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
msg = "Fill up login credentials,properly"	;
$("#error_block").html(msg);
errorModal.show();	
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
successModal.show();
 setTimeout(() => {
      successModal.hide();
    }, 1600);	
	}
	else
	{//msg = ( "Login Failed. Please Retry" );
$("#error_block").html(msg);
errorModal.show();	
}
  }); 
    });
	
function load_shop_pic(){
	let shop_pic="img/shop/"+document.getElementById("shop").value.split("~")[0]+".jpeg";
	document.getElementById("shop_pic").src=shop_pic;
	
}
		
function goBack() {

    if (window.location.href.includes("service_trans_new")) {

        $.post("delete_draft_service.php", {
            value: $("#trans_id").val()
        }, function () {
            redirectBack();
        });

    } else if (window.location.href.includes("sales_trans_new")) {

        $.post("delete_draft_sales.php", {
            value: $("#trans_id").val(),
			ver:$("#ver").val()
        }, function () {
            //redirectBack();
			window.location.href = "sales.php";
        });

    } else if (window.location.href.includes("receipt_trans_new")) {

        let active_status = "<?php echo $active_status;?>";

        if (active_status != "A") {

            let r = confirm("Discard the unsaved transaction??");

            //if (r) {
                setTimeout(() => {
                    save_dummy_back();
                    window.location.href = "receipt.php";
                }, 1000);
            //}

        } else {
            redirectBack();
        }

    } else {
        redirectBack();
    }
}


/* Reusable redirect function */
function redirectBack() {
    if (document.referrer !== "") {
        window.history.back();
    } else {
        window.location.href = "index.php";
    }
}

	</script>