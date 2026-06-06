<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | Sistem Manajemen Perumahan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background:
        linear-gradient(120deg, rgba(8, 47, 73, 0.58), rgba(15, 118, 110, 0.42)),
        url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      color: #172033;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: radial-gradient(circle at top right, rgba(20, 184, 166, 0.22), transparent 30rem);
      z-index: 1;
    }

    .login-box {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.94);
      padding: 34px;
      border-radius: 8px;
      border: 1px solid rgba(255, 255, 255, 0.74);
      box-shadow: 0 22px 60px rgba(15, 23, 42, 0.24);
      width: 100%;
      max-width: 420px;
      animation: slideIn 0.35s ease;
      backdrop-filter: blur(12px);
    }

    @keyframes slideIn {
      from {
        transform: translateY(18px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .login-box h2 {
      margin: 0 0 26px;
      color: #172033;
      text-align: center;
      font-size: 28px;
      font-weight: 800;
      letter-spacing: 0;
    }

    .login-box label {
      font-weight: 800;
      color: #334155;
      display: block;
      margin-bottom: 6px;
      font-size: 13px;
    }

    .login-box input[type="text"],
    .login-box input[type="password"] {
      width: 100%;
      min-height: 44px;
      padding: 11px 12px;
      margin-bottom: 18px;
      border: 1px solid #d9e0ea;
      border-radius: 8px;
      background: #ffffff;
      color: #172033;
      outline: none;
      transition: border-color 0.16s ease, box-shadow 0.16s ease;
    }

    .login-box input:focus {
      border-color: rgba(37, 99, 235, 0.62);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .login-box button {
      width: 100%;
      min-height: 44px;
      padding: 11px 14px;
      background: #2563eb;
      color: white;
      border: 1px solid #2563eb;
      border-radius: 8px;
      font-weight: 800;
      cursor: pointer;
      transition: background-color 0.16s ease, transform 0.16s ease;
    }

    .login-box button:hover {
      background: #1d4ed8;
      transform: translateY(-1px);
    }

    .footer-text {
      text-align: center;
      margin-top: 15px;
      color: #647084;
      font-size: 14px;
    }

    .footer-text a {
      color: #2563eb;
      text-decoration: none;
      font-weight: 700;
    }

    .footer-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="overlay"></div>
  <div class="login-box">
    <h2>Login</h2>
    <form action="/login/auth" method="post">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" placeholder="Masukkan username" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" placeholder="Masukkan password" required>

      <button type="submit">Masuk</button>
    </form>
    <div class="footer-text">
      <!-- Belum punya akun? <a href="/register">Daftar</a> <br>
      <a href="/lupapassword">Lupa Password?</a> -->
    </div>
  </div>
</body>
</html>
