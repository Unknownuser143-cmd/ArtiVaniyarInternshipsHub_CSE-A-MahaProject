function validateLogin(){const email=document.getElementById('email')?.value.trim()||'',password=document.getElementById('password')?.value||'';if(!email||!password){alert('Please enter your email and password.');return false;}return true;}
function validateRegister(){const name=document.getElementById('name')?.value.trim()||'',email=document.getElementById('email')?.value.trim()||'',password=document.getElementById('password')?.value||'',role=document.getElementById('role')?.value||'';if(!name||!email||!password||!role){alert('Please complete all fields.');return false;}if(password.length<6){alert('Password must contain at least 6 characters.');return false;}return true;}

// Show/Hide password control used by both login.php and register.php.
function togglePasswordVisibility(inputId, button) {
  const input = document.getElementById(inputId);
  if (!input || !button) return;
  const shouldReveal = input.type === 'password';
  input.type = shouldReveal ? 'text' : 'password';
  button.textContent = shouldReveal ? 'Hide' : 'Show';
  button.setAttribute('aria-label', shouldReveal ? 'Hide password' : 'Show password');
  button.setAttribute('aria-pressed', shouldReveal ? 'true' : 'false');
}
