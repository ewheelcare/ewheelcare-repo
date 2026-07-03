<?php

header('Content-Type: application/json');

if (!isset($_COOKIE["user_id"])) {
    echo json_encode([
        "status" => "ERROR",
        "message" => "Session Expired."
    ]);
    exit();
}

include "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try
{

    $customer_id = intval($_POST["customer_id"]);
    $from_date   = $_POST["from_date"];
    $to_date     = $_POST["to_date"];

    if ($customer_id <= 0) {
        throw new Exception("Invalid Customer.");
    }

    if ($from_date == "" || $to_date == "") {
        throw new Exception("Invalid Date.");
    }

    /*
    -------------------------------------------------------
        Calculate Financial Year
    -------------------------------------------------------
    */

    $year  = date("Y", strtotime($from_date));
    $month = date("n", strtotime($from_date));
    
    if ($month >= 4) {
    $financial_start_date = $year . "-04-01";
     } else {
    $financial_start_date = ($year - 1) . "-04-01";
     }
    
    if ($month >= 4) {

        $financial_year =
            $year . "-" . substr($year + 1, 2, 2);

    }
    else {

        $financial_year =
            ($year - 1) . "-" . substr($year, 2, 2);

    }

    /*
    -------------------------------------------------------
        Customer Details
    -------------------------------------------------------
    */

    $sql = "
    SELECT
    A.owner_name,
    A.owner_mobile,
    B.address  FROM customer A 
    LEFT JOIN customer_address B ON A.customer_id = B.customer_id WHERE A.customer_id = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i",$customer_id);

    $stmt->execute();

    $customer = $stmt->get_result()->fetch_assoc();

    if(!$customer)
    {
        throw new Exception("Customer Not Found.");
    }

    /*
    -------------------------------------------------------
        Annual Opening Balance
    -------------------------------------------------------
    */

    $opening_balance = 0;

    $sql = "

    SELECT

        opening_balance

    FROM customer_opening_balance

    WHERE customer_id=?

    AND financial_year=?

    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "is",
        $customer_id,
        $financial_year
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if($row=$result->fetch_assoc())
    {
        $opening_balance =
            floatval($row["opening_balance"]);
    }

    /*
    -------------------------------------------------------
        Add Ledger Transactions
        Before From Date
    -------------------------------------------------------
    */

    $sql="

    SELECT

        COALESCE(SUM(debit),0) total_debit,

        COALESCE(SUM(credit),0) total_credit

    FROM customer_ledger

    WHERE customer_id=?

    AND trans_date >= ? AND trans_date < ?

    ";

    $stmt=$conn->prepare($sql);

    $stmt->bind_param(

        "iss",

        $customer_id,
        
        $financial_start_date,

        $from_date
       
    );

    $stmt->execute();

    $sum=$stmt->get_result()->fetch_assoc();

    $opening_balance +=
        $sum["total_debit"];

    $opening_balance -=
        $sum["total_credit"];

    /*
    -------------------------------------------------------

        Remaining code in Part-2

    -------------------------------------------------------
    */
    /*
    -------------------------------------------------------
        Fetch Sales Ledger
    -------------------------------------------------------
    */

    $sql = "

    SELECT

        trans_date,
        source_type,
        trans_id,
        payment_id,
        remarks,
        debit,
        credit

    FROM customer_ledger

    WHERE customer_id = ?
      AND trans_date BETWEEN ? AND ?

    ORDER BY trans_date,
             trans_id,
             payment_id

    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(

        "iss",

        $customer_id,
        $from_date,
        $to_date

    );

    $stmt->execute();

    $result = $stmt->get_result();

    /*
    -------------------------------------------------------
        Running Balance
    -------------------------------------------------------
    */

    $running_balance = $opening_balance;

    $total_debit = 0;
    $total_credit = 0;

    $rows = "";

    /*
    -------------------------------------------------------
        Opening Balance Row
    -------------------------------------------------------
    */

    $rows .= "

    <tr class='opening-row'>

        <td>" . date("d-m-Y", strtotime($from_date)) . "</td>

        <td>OPENING</td>

        <td>-</td>

        <td>OPENING BALANCE</td>

        <td class='text-right'>" .
            number_format($opening_balance,2) .
        "</td>

        <td class='text-right'>0.00</td>

        <td class='text-right'>" .
            number_format($opening_balance,2) .
        "</td>

    </tr>

    ";

    /*
    -------------------------------------------------------
        Ledger Rows
    -------------------------------------------------------
    */

    while($ledger = $result->fetch_assoc())
    {

        $running_balance +=
            $ledger["debit"];

        $running_balance -=
            $ledger["credit"];

        $total_debit +=
            $ledger["debit"];

        $total_credit +=
            $ledger["credit"];

        $row_class = "";

        if($ledger["remarks"]=="Sales Invoice")
        {
            $row_class="sales-row";
        }
        else
        {
            $row_class="receipt-row";
        }

        $voucher_no = "";

        if($ledger["trans_id"]!="")
        {
            $voucher_no =
                $ledger["trans_id"];
        }

        if($ledger["payment_id"]!="")
        {
            $voucher_no =
                $ledger["payment_id"];
        }

        $rows .= "

        <tr class='".$row_class."'>

            <td>

            ".date(
                "d-m-Y",
                strtotime($ledger["trans_date"])
            )."

            </td>

            <td>

            ".$ledger["source_type"]."

            </td>

            <td>

            ".$voucher_no."

            </td>

            <td>

            ".$ledger["remarks"]."

            </td>

            <td class='text-right'>

            ".number_format(
                $ledger["debit"],
                2
            )."

            </td>

            <td class='text-right'>

            ".number_format(
                $ledger["credit"],
                2
            )."

            </td>

            <td class='text-right'>

            ".number_format(
                $running_balance,
                2
            )."

            </td>

        </tr>

        ";

    }

    /*
    -------------------------------------------------------
        Remaining code in Part-3
    -------------------------------------------------------
    */
        /*
    -------------------------------------------------------
        Format Opening Balance
    -------------------------------------------------------
    */

    $opening_display = number_format(abs($opening_balance),2);

    if($opening_balance >= 0)
        $opening_display .= " Dr";
    else
        $opening_display .= " Cr";

    /*
    -------------------------------------------------------
        Closing Balance
    -------------------------------------------------------
    */

    $closing_display = number_format(abs($running_balance),2);

    if($running_balance >= 0)
        $closing_display .= " Dr";
    else
        $closing_display .= " Cr";

    /*
    -------------------------------------------------------
        Report Period
    -------------------------------------------------------
    */

    $period =
        date("d-m-Y",strtotime($from_date))
        ." To ".
        date("d-m-Y",strtotime($to_date));

    /*
    -------------------------------------------------------
        Return JSON
    -------------------------------------------------------
    */

    echo json_encode(

        array(

            "status"=>"SUCCESS",

            "customer_name"=>$customer["owner_name"],

            "customer_mobile"=>$customer["owner_mobile"],

            "customer_address"=>$customer["address"],

            "period"=>$period,

            "opening_balance"=>$opening_display,

            "total_debit"=>number_format($total_debit,2),

            "total_credit"=>number_format($total_credit,2),

            "closing_balance"=>$closing_display,

            "rows"=>$rows

        )

    );

}
catch(Exception $e)
{

    echo json_encode(

        array(

            "status"=>"ERROR",

            "message"=>$e->getMessage()

        )

    );

}

$conn->close();

?>