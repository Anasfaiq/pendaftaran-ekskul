<?php
session_start();
include '../config/conn.php'; // koneksi ke database

$error = ''; // nampung pesan error

if (isset($_POST['login'])) {
    // ambil data dari form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // escape string untuk keamanan
    $username = mysqli_real_escape_string($conn, $username);

    // cari user berdasarkan username
    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // verifikasi password hash
        if (password_verify($password, $user['password'])) {
            // set session user login
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // redirect sesuai role
            if ($user['role'] === 'siswa') {
                header('Location: dashboard-siswa.php');
                exit;
            } elseif ($user['role'] === 'guru') {
                header('Location: dashboard-guru.php');
                exit;
            } else {
                $error = 'Role pengguna tidak dikenali.';
            }
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan!';
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Web Ekskul</title>
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* small local override to constrain the card width */
    .login-card { max-width: 420px; width: 100%; border-radius: .5rem; }
  </style>
</head>
<body class="bg-gradient-to-br from-slate-800 via-blue-800 to-gray-400 ">
  <div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-sm login-card p-2">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3 gap-2">
          <!-- external logo image (downloaded into assets) -->
          <div class="logo" aria-hidden="true">
            <img src="../assets/logo2.jpg" alt="SMK Kesuma Bangsa 2" width="40" height="40" style="display:block;">
          </div>
          <div>
            <h5 class="mb-0 font-semibold">Pendaftaran Ekskul</h5>
            <small class="text-muted">Masuk untuk melanjutkan</small>
          </div>
        </div>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form action="" method="POST" class="needs-validation p-4" novalidate>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" name="username" id="username" required autofocus>
          <div class="invalid-feedback">Silakan masukkan username.</div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <input type="password" class="form-control" name="password" id="password" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">Tampilkan</button>
            <div class="invalid-feedback">Silakan masukkan password.</div>
          </div>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" name="login" class="btn btn-primary">Login</button>
        </div>
      </form>
    </div>
  </div>

  <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Run after DOM is ready to avoid timing issues
    document.addEventListener('DOMContentLoaded', function () {
      'use strict'

      // Client-side validation (Bootstrap)
      try {
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
          .forEach(function (form) {
            form.addEventListener('submit', function (event) {
              if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
              }

              form.classList.add('was-validated')
            }, false)
          })
      } catch (e) {
      }

      // Toggle password visibility
      try {
        var toggle = document.getElementById('togglePassword')
        var pwd = document.getElementById('password')
        if (toggle && pwd) {
          toggle.addEventListener('click', function (ev) {
            ev.preventDefault()
            var type = pwd.getAttribute('type') === 'password' ? 'text' : 'password'
            pwd.setAttribute('type', type)
            toggle.textContent = type === 'password' ? 'Tampilkan' : 'Sembunyikan'
          })
        }
      } catch (e) {
      }
    })
  </script>
  <style>
    /* keep small logo spacing */
    .logo svg { display: block; }
  </style>

</body>
</html>
