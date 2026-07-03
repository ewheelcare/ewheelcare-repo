<?php
if (!isset($_COOKIE["user_id"])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
include "db_config.php";

// ── SAVE on POST ──────────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_privileges"])) {
    $sel_user = $conn->real_escape_string($_POST["sel_user"]);
    // Remove all existing privileges for this user
    $conn->query("DELETE FROM user_privilege WHERE user_id='" . $sel_user . "'");
    // Insert selected ones
    if (!empty($_POST["privileges"]) && is_array($_POST["privileges"])) {
        foreach ($_POST["privileges"] as $pid) {
            $pid = intval($pid);
            $conn->query("INSERT IGNORE INTO user_privilege (user_id, privilege_id, granted_by, granted_on)
                          VALUES ('" . $sel_user . "', " . $pid . ", '" . $conn->real_escape_string($_COOKIE["user_id"]) . "', NOW())");
        }
    }
    $save_msg = "Privileges saved successfully!";
}

// ── FETCH users ───────────────────────────────────────────────
$users_result = $conn->query("SELECT user_id, user_name FROM expert_login WHERE active_status='A' ORDER BY user_name");

// ── FETCH all privilege masters grouped by module ─────────────
$priv_result  = $conn->query("SELECT * FROM privilege_master WHERE active_status='A' ORDER BY module, privilege_name");
$all_privs    = [];
$modules      = [];
while ($r = $priv_result->fetch_assoc()) {
    $all_privs[] = $r;
    $modules[$r["module"]][] = $r;
}

// ── FETCH current user's privileges (for pre-check) ───────────
$sel_user       = isset($_POST["sel_user"]) ? $_POST["sel_user"] : (isset($_GET["sel_user"]) ? $_GET["sel_user"] : "");
$granted_ids    = [];
if (!empty($sel_user)) {
    $gr = $conn->query("SELECT privilege_id FROM user_privilege WHERE user_id='" . $conn->real_escape_string($sel_user) . "'");
    while ($gr && $r2 = $gr->fetch_assoc()) {
        $granted_ids[] = $r2["privilege_id"];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include "header_include.php"; ?>
<style>
.priv-card {
    border: 1px solid #2E2E2E;
    border-radius: 8px;
    margin-bottom: 18px;
    overflow: hidden;
}
.priv-card-header {
    background: #2E2E2E;
    color: #fff;
    padding: 10px 16px;
    font-weight: 700;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.priv-card-body {
    padding: 12px 16px;
    background: #f8faff;
}
.priv-row {
    display: flex;
    align-items: flex-start;
    padding: 8px 10px;
    border-radius: 6px;
    margin-bottom: 4px;
    background: #fff;
    border: 1px solid #e5e7eb;
    transition: background 0.15s;
}
.priv-row:hover { background: #eff6ff; }
.priv-row input[type=checkbox] {
    width: 18px; height: 18px;
    margin-right: 12px;
    margin-top: 2px;
    accent-color: #2563eb;
    cursor: pointer;
    flex-shrink: 0;
}
.priv-label { cursor: pointer; }
.priv-label strong { font-size: 14px; color: #1e293b; display: block; }
.priv-label small  { font-size: 12px; color: #64748b; }
.user-select-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(37,99,235,0.06);
}
.select-all-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    background: #eff6ff;
    border-radius: 6px;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #2563eb;
    cursor: pointer;
}
.select-all-row input { accent-color: #2563eb; width:16px; height:16px; }
.save-bar {
    position: sticky;
    bottom: 0;
    background: #fff;
    border-top: 2px solid #e2e8f0;
    padding: 14px 0;
    text-align: center;
    z-index: 100;
}
.badge-module {
    background: rgba(255,255,255,0.25);
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 12px;
    font-weight: 500;
}
</style>
</head>
<body id="page-top">
<div id="wrapper">
    <?php include "sidemenu.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include "topmenu.php"; ?>
            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <!-- <i class="fas fa-user-shield mr-2" style="color:#2563eb;"></i> -->
                        <span style="font-size:18px;"><strong>User Privilege Management</strong>
                        </span>
                    </h1>
                </div>

                <?php if (!empty($save_msg)) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $save_msg; ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                <?php } ?>

                <form method="POST" id="priv_form">
                <!-- ── USER SELECTION ─────────────────────────── -->
                <div class="user-select-card">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="font-weight-bold" style="font-size:15px;">
                                <i class="fas fa-user mr-1" style="color:#2563eb;"></i> Select User
                            </label>
                            <select name="sel_user" id="sel_user" class="form-control form-control-lg"
                                    onchange="loadUserPrivileges(this.value)">
                                <option value="">-- Select a User --</option>
                                <?php
                                if ($users_result) {
                                    while ($u = $users_result->fetch_assoc()) {
                                        $selected = ($u["user_id"] == $sel_user) ? "selected" : "";
                                ?>
                                <option value="<?php echo htmlspecialchars($u["user_id"]); ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($u["user_name"]); ?>
                                    (<?php echo htmlspecialchars($u["user_id"]); ?>)
                                </option>
                                <?php }} ?>
                            </select>
                        </div>
                        <div class="col-md-4 mt-3 mt-md-0">
                            <button type="button" class="btn btn-outline-primary"
                                    onclick="checkAll(true)" style="margin-right:6px;">
                                <i class="fas fa-check-square mr-1"></i> Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary"
                                    onclick="checkAll(false)">
                                <i class="far fa-square mr-1"></i> Clear All
                            </button>
                        </div>
                        <div class="col-md-3 mt-3 mt-md-0 text-right">
                            <div id="priv_count_badge" class="badge badge-primary p-2" style="font-size:13px; display:none;">
                                <i class="fas fa-key mr-1"></i>
                                <span id="priv_count">0</span> privilege(s) selected
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── PRIVILEGES BY MODULE ───────────────────── -->
                <div id="priv_area" style="<?php echo empty($sel_user) ? 'display:none' : ''; ?>">
                <?php foreach ($modules as $module => $privs) { ?>
                <div class="priv-card">
                    <div class="priv-card-header">
                        <i class="fas fa-puzzle-piece"></i>
                        <?php echo htmlspecialchars($module); ?>
                        <span class="badge-module"><?php echo count($privs); ?> items</span>
                        <div class="ml-auto">
                            <label class="mb-0" style="font-weight:500; font-size:13px; cursor:pointer;">
                                <input type="checkbox" class="module-check"
                                       data-module="<?php echo htmlspecialchars($module); ?>"
                                       onchange="toggleModule('<?php echo htmlspecialchars($module); ?>', this.checked)"
                                       style="margin-right:5px; accent-color:#fff;">
                                Select All in <?php echo htmlspecialchars($module); ?>
                            </label>
                        </div>
                    </div>
                    <div class="priv-card-body">
                    <?php foreach ($privs as $priv) {
                        $checked = in_array($priv["privilege_id"], $granted_ids) ? "checked" : "";
                    ?>
                    <div class="priv-row">
                        <input type="checkbox"
                               name="privileges[]"
                               value="<?php echo $priv["privilege_id"]; ?>"
                               id="priv_<?php echo $priv["privilege_id"]; ?>"
                               class="priv-check module-<?php echo htmlspecialchars($module); ?>"
                               <?php echo $checked; ?>
                               onchange="updateCount()">
                        <label class="priv-label mb-0" for="priv_<?php echo $priv["privilege_id"]; ?>">
                            <strong><?php echo htmlspecialchars($priv["privilege_name"]); ?></strong>
                            <small><?php echo htmlspecialchars($priv["description"]); ?></small>
                        </label>
                    </div>
                    <?php } ?>
                    </div>
                </div>
                <?php } ?>
                </div>

                <?php if (!empty($sel_user)) { ?>
                <input type="hidden" name="save_privileges" value="1">
                <div class="save-bar">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-21"></i> Save </button>
                    <a href="privilege_config.php" class="btn btn-secondary">
                        <i class="fas fa-times mr-11"></i> Cancel
                    </a>
                </div>
                <?php } ?>

                </form>

            </div>
        </div>
        <?php include "footer.php"; ?>
    </div>
</div>
<?php include "footer_include.php"; ?>
<script src="js/jquery-3.5.1.min.js"></script>
<script>
var allGranted = <?php echo json_encode($granted_ids); ?>;

function loadUserPrivileges(userId) {
    if (!userId) return;
    window.location.href = "privilege_config.php?sel_user=" + encodeURIComponent(userId);
}

function checkAll(state) {
    document.querySelectorAll(".priv-check").forEach(function(cb) { cb.checked = state; });
    document.querySelectorAll(".module-check").forEach(function(cb) { cb.checked = state; });
    updateCount();
}

function toggleModule(module, state) {
    document.querySelectorAll(".module-" + module).forEach(function(cb) { cb.checked = state; });
    updateCount();
}

function updateCount() {
    var count = document.querySelectorAll(".priv-check:checked").length;
    var badge = document.getElementById("priv_count_badge");
    document.getElementById("priv_count").innerText = count;
    badge.style.display = count > 0 ? "inline-block" : "none";

    // Update module-level checkboxes state
    document.querySelectorAll(".module-check").forEach(function(modCb) {
        var mod = modCb.getAttribute("data-module");
        var all = document.querySelectorAll(".module-" + mod);
        var chk = document.querySelectorAll(".module-" + mod + ":checked");
        modCb.checked = (all.length > 0 && all.length === chk.length);
        modCb.indeterminate = (chk.length > 0 && chk.length < all.length);
    });
}

document.addEventListener("DOMContentLoaded", function() {
    if (document.getElementById("priv_area").style.display !== "none") {
        updateCount();
    }
});
</script>
</body>
</html>
