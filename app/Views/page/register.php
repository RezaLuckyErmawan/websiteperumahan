<?php
$tanggalDaftar = $tanggalDaftar ?? date('Y-m-d');
$error = session()->getFlashdata('error');
$success = session()->getFlashdata('success');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register | Sistem Manajemen Perumahan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }

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

    .register-box {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.94);
      padding: 34px;
      border-radius: 10px;
      border: 1px solid rgba(255, 255, 255, 0.74);
      box-shadow: 0 22px 60px rgba(15, 23, 42, 0.24);
      width: 100%;
      max-width: 560px;
      animation: slideIn 0.35s ease;
      backdrop-filter: blur(12px);
    }

    @keyframes slideIn {
      from { transform: translateY(18px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .register-box h2 {
      margin: 0 0 10px;
      color: #172033;
      text-align: center;
      font-size: 28px;
      font-weight: 800;
    }

    .subtitle {
      margin: 0 0 24px;
      text-align: center;
      color: #647084;
      font-size: 14px;
      line-height: 1.6;
    }

    .notice {
      margin-bottom: 14px;
      padding: 12px 14px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
    }

    .notice.error { background: #fee2e2; color: #991b1b; }
    .notice.success { background: #dcfce7; color: #166534; }

    .grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .field {
      margin-bottom: 16px;
    }

    label {
      font-weight: 800;
      color: #334155;
      display: block;
      margin-bottom: 6px;
      font-size: 13px;
    }

    input, textarea {
      width: 100%;
      min-height: 44px;
      padding: 11px 12px;
      border: 1px solid #d9e0ea;
      border-radius: 8px;
      background: #ffffff;
      color: #172033;
      outline: none;
      transition: border-color 0.16s ease, box-shadow 0.16s ease;
      font: inherit;
    }

    input::placeholder,
    textarea::placeholder {
      color: #94a3b8;
      opacity: 1;
    }

    textarea {
      min-height: 92px;
      resize: vertical;
    }

    input:focus, textarea:focus {
      border-color: rgba(37, 99, 235, 0.62);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .readonly {
      background: #f8fafc;
      color: #475569;
    }

    .hint {
      margin-top: 6px;
      color: #647084;
      font-size: 12px;
    }

    button {
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

    button:hover {
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

    .btn-back {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      min-height: 44px;
      padding: 11px 14px;
      margin-top: 10px;
      background: transparent;
      color: #475569;
      border: 1px solid #d9e0ea;
      border-radius: 8px;
      font-weight: 700;
      font-size: 14px;
      text-decoration: none;
      cursor: pointer;
      transition: background-color 0.16s ease, border-color 0.16s ease, color 0.16s ease, transform 0.16s ease;
    }

    .btn-back:hover {
      background: #f1f5f9;
      border-color: #94a3b8;
      color: #172033;
      transform: translateY(-1px);
    }

    @media (max-width: 640px) {
      .register-box {
        padding: 24px;
      }

      .grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="overlay"></div>
  <div class="register-box">
    <h2>Register Customer</h2>
    <p class="subtitle">Daftar akun customer baru.</p>

    <?php if ($error): ?>
      <div class="notice error"><?= esc($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="notice success"><?= esc($success) ?></div>
    <?php endif; ?>

    <form action="/register" method="post">
      <?= csrf_field() ?>

      <div class="grid">
        <div class="field">
          <label for="username">Username</label>
          <input type="text" name="username" id="username" placeholder="Contoh: budi123" value="<?= esc(old('username') ?? '') ?>" required>
        </div>

        <div class="field">
          <label for="nama">Nama</label>
          <input type="text" name="nama" id="nama" placeholder="Masukkan nama" value="<?= esc(old('nama') ?? '') ?>" required>
        </div>
      </div>

      <div class="grid">
        <div class="field">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" placeholder="Masukkan email" value="<?= esc(old('email') ?? '') ?>" required>
        </div>

        <div class="field">
          <label for="telepon">Telepon</label>
          <input type="text" name="telepon" id="telepon" placeholder="Masukkan nomor telepon" value="<?= esc(old('telepon') ?? '') ?>" required>
        </div>
      </div>

      <div class="field">
        <label for="alamat">Alamat</label>
        <textarea name="alamat" id="alamat" placeholder="Masukkan alamat lengkap" required><?= esc(old('alamat') ?? '') ?></textarea>
      </div>

      <div class="grid">
        <div class="field">
          <label for="tanggal_pembelian">Tanggal Daftar</label>
          <input type="date" name="tanggal_pembelian" id="tanggal_pembelian" class="readonly" value="<?= esc($tanggalDaftar) ?>" readonly>
          <div class="hint">Diisi otomatis sesuai tanggal pendaftaran.</div>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" placeholder="Silahkan isi password" required>
        </div>
      </div>

      <div class="field">
        <label for="password_confirmation">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password yang sama" required>
      </div>

      <button type="submit">Daftar Sekarang</button>
    </form>

    <a href="/login" class="btn-back">Kembali ke Login</a>

    <div class="footer-text">
      Sudah punya akun? <a href="/login">Masuk</a>
    </div>
  </div>
</body>
</html>
