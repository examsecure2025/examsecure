<?php require_once 'auth/check_session.php'; ?>
<?php require_once 'database/db_config.php'; ?>
<?php
// Fetch current user details
$user_id = $_SESSION['unique_id'];
$sql = "SELECT first_name, middle_name, last_name, email, date_created FROM accounts WHERE unique_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result && $result->num_rows ? $result->fetch_assoc() : [
    'first_name' => '', 'middle_name' => '', 'last_name' => '', 'email' => '', 'date_created' => ''
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | Exam Secure</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/sidebar-style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Montserrat', sans-serif; margin:0; padding:0; background:#0a2240; }
        .settings-container { display:flex; }
        .main-content { flex:1; padding:2rem; }
        .section { background:#0a1a36; border-radius:1rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 0.25rem 1.2rem rgba(0,0,0,0.18); }
        .section-title { color:#fff; font-size:1.25rem; font-weight:700; margin-bottom:1rem; }
        .grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:1rem; }
        .form-group { display:flex; flex-direction:column; }
        .form-group label { color:#b3c6e0; margin-bottom:0.4rem; font-weight:600; }
        .form-group input { background:#17305c; color:#fff; border:none; border-radius:0.5rem; padding:0.75rem 0.875rem; outline:none; width:100%; box-sizing:border-box; }
        .pwd-wrap { position:relative; }
        .pwd-toggle { position:absolute; right:0.5rem; top:50%; transform:translateY(-50%); background:transparent; color:#b3c6e0; border:none; cursor:pointer; padding:0; height:1.5rem; width:1.75rem; display:flex; align-items:center; justify-content:center; }
        .pwd-toggle:focus { outline:none; }
        .hint { color:#b3c6e0; font-size:0.85rem; }
        .actions { display:flex; gap:0.75rem; margin-top:1rem; }
        .btn-primary { background:#ffe600; color:#002147; border:none; border-radius:0.5rem; padding:0.7rem 1.25rem; font-weight:700; cursor:pointer; }
        .btn-secondary { background:#25477a; color:#fff; border:none; border-radius:0.5rem; padding:0.7rem 1.25rem; font-weight:700; cursor:pointer; }
        .divider { height:1px; background:#25477a; margin:1rem 0; }
        .alert { margin-top:0.75rem; color:#fff; padding:0.6rem 0.8rem; border-radius:0.5rem; display:none; }
        .alert.success { background:#1e7b60; }
        .alert.error { background:#e74c3c; }
    </style>
    <script>
    function showAlert(id, ok, msg){
        const el = document.getElementById(id);
        el.className = 'alert ' + (ok ? 'success' : 'error');
        el.textContent = msg;
        el.style.display = 'block';
        setTimeout(()=>{ el.style.display='none'; }, 3500);
    }
    function togglePwd(inputId, btnId){
        const inp = document.getElementById(inputId);
        const btn = document.getElementById(btnId);
        if (!inp || !btn) return;
        const toType = inp.type === 'password' ? 'text' : 'password';
        inp.type = toType;
        const icon = btn.querySelector('i');
        if (icon){
            icon.className = toType === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        }
        btn.setAttribute('aria-label', toType === 'password' ? 'Show password' : 'Hide password');
    }
    async function updateDetails(){
        const payload = {
            action: 'update_details',
            first_name: document.getElementById('first_name').value.trim(),
            middle_name: document.getElementById('middle_name').value.trim(),
            last_name: document.getElementById('last_name').value.trim(),
            email: document.getElementById('email').value.trim()
        };
        const res = await fetch('phpfiles/update_account.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        const data = await res.json().catch(()=>({success:false,message:'Invalid response'}));
        showAlert('detailsAlert', !!data.success, data.message || (data.success?'Updated':'Update failed'));
    }
    async function updatePassword(){
        const current_password = document.getElementById('current_password').value;
        const new_password = document.getElementById('new_password').value;
        const confirm_password = document.getElementById('confirm_password').value;
        const payload = { action:'update_password', current_password, new_password, confirm_password };
        const res = await fetch('phpfiles/update_account.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        const data = await res.json().catch(()=>({success:false,message:'Invalid response'}));
        showAlert('passwordAlert', !!data.success, data.message || (data.success?'Password updated':'Update failed'));
        if (data.success) {
            document.getElementById('current_password').value='';
            document.getElementById('new_password').value='';
            document.getElementById('confirm_password').value='';
        }
    }
    </script>
    
</head>
<body>
<div class="settings-container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <div class="section">
            <div class="section-title"><i class="bi bi-person-gear" style="margin-right:0.5rem;"></i>Account Details</div>
            <div class="grid">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input id="first_name" type="text" placeholder="Enter first name" value="<?= htmlspecialchars($user['first_name']) ?>" />
                </div>
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input id="middle_name" type="text" placeholder="Enter middle name" value="<?= htmlspecialchars($user['middle_name']) ?>" />
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input id="last_name" type="text" placeholder="Enter last name" value="<?= htmlspecialchars($user['last_name']) ?>" />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" placeholder="name@example.com" value="<?= htmlspecialchars($user['email']) ?>" />
                    <span class="hint">We'll never share your email.</span>
                </div>
            </div>
            <div class="actions">
                <button type="button" class="btn-primary" onclick="updateDetails()">Save Details</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='assessments.php'">Cancel</button>
            </div>
            <div id="detailsAlert" class="alert"></div>
        </div>

        <div class="section">
            <div class="section-title"><i class="bi bi-shield-lock" style="margin-right:0.5rem;"></i>Change Password</div>
            <div class="grid">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="pwd-wrap">
                        <input id="current_password" type="password" placeholder="Enter current password" style="padding-right:2.25rem;" />
                        <button type="button" class="pwd-toggle" id="toggle_current_password" onclick="togglePwd('current_password','toggle_current_password')" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div></div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="pwd-wrap">
                        <input id="new_password" type="password" placeholder="Enter new password" style="padding-right:2.25rem;" />
                        <button type="button" class="pwd-toggle" id="toggle_new_password" onclick="togglePwd('new_password','toggle_new_password')" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="pwd-wrap">
                        <input id="confirm_password" type="password" placeholder="Re-enter new password" style="padding-right:2.25rem;" />
                        <button type="button" class="pwd-toggle" id="toggle_confirm_password" onclick="togglePwd('confirm_password','toggle_confirm_password')" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="actions">
                <button type="button" class="btn-primary" onclick="updatePassword()">Update Password</button>
            </div>
            <div id="passwordAlert" class="alert"></div>
        </div>
    </main>
</div>
</body>
</html>


