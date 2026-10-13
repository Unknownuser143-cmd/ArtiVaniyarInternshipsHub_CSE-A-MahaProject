<?php
session_start();
require_once __DIR__ . '/config/database.php';

$data = db_load();
$error = '';
$success = '';
$name = $email = '';
$role = 'student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'student';

    if ($name === '' || $email === '' || $password === '') $error = 'Please fill in all required fields.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Please enter a valid email address.';
    elseif (strlen($password) < 6) $error = 'Password must contain at least 6 characters.';
    elseif (!in_array($role, ['student','company'], true)) $error = 'Please select a valid account type.';
    elseif (db_find_user_by_email($data, $email)) $error = 'An account with this email already exists.';
    else {
        $userId = db_next_id($data, 'users');
        $data['users'][] = ['id'=>$userId,'name'=>$name,'email'=>$email,'password'=>password_hash($password,PASSWORD_DEFAULT),'role'=>$role,'created_at'=>db_now()];
        if ($role === 'student') {
            $data['students'][] = ['id'=>db_next_id($data,'students'),'user_id'=>$userId,'phone'=>'','college'=>'','course'=>'','skills'=>'','bio'=>'','resume'=>''];
        } else {
            $data['companies'][] = ['id'=>db_next_id($data,'companies'),'user_id'=>$userId,'company_name'=>$name,'website'=>'','description'=>''];
        }
        if (db_save($data)) {
            $success = 'Account created successfully. You can now sign in.';
            $name = $email = '';
            $role = 'student';
        } else {
            $error = 'Unable to save the account. Please check folder permissions.';
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create Account · Arti Vaniyar Internships Hub</title><link rel="stylesheet" href="css/style.css?v=blue-toggle-2"></head>
<body class="auth-page"><div class="auth-container"><div class="auth-brand"><a href="index.php" class="logo">Arti Vaniyar Internships Hub</a><p class="eyebrow" style="margin-top:55px">INTERNSHIP PORTAL</p><h1>Turn your Dreams into Reality.</h1><p>Your Skills, Your Opportunities, Your Career—all on One Platform.</p></div>
<div class="auth-card"><h2>Create your account</h2><p class="muted">Choose Your Role, Unlock New Opportunities.</p><?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?><?php if($success): ?><div class="alert success"><?=e($success)?></div><?php endif; ?>
<form method="POST" onsubmit="return validateRegister()"><label>Full name</label><input name="name" id="name" value="<?=e($name)?>" placeholder="Your full name" required><label>Email address</label><input type="email" name="email" id="email" value="<?=e($email)?>" placeholder="you@example.com" required><label for="password">Password</label><div class="password-field"><input type="password" name="password" id="password" placeholder="At least 6 characters" autocomplete="new-password" required><button class="password-toggle" type="button" onclick="togglePasswordVisibility('password', this)" aria-controls="password" aria-label="Show password" aria-pressed="false">Show</button></div><label>Account type</label><select name="role" id="role"><option value="student" <?=$role==='student'?'selected':''?>>Student</option><option value="company" <?=$role==='company'?'selected':''?>>Company</option></select><button class="btn primary full" type="submit">Create account</button></form><p class="auth-bottom">Already registered? <a href="login.php">Sign in</a></p></div></div><script src="js/validation.js?v=blue-toggle-2"></script></body></html>
