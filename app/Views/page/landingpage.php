<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GreenHome.id</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      background: #f5f7fb;
      color: #172033;
      font-family: 'Inter', Arial, sans-serif;
    }

    header {
      min-height: 68px;
      padding: 0 32px;
      color: #ffffff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      inset: 0 0 auto 0;
      z-index: 10;
      background: rgba(23, 50, 77, 0.72);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.14);
    }

    header h2 {
      margin: 0;
      font-size: 20px;
      font-weight: 800;
    }

    nav {
      display: flex;
      align-items: center;
      gap: 18px;
    }

    nav a {
      color: rgba(255, 255, 255, 0.88);
      text-decoration: none;
      font-size: 14px;
      font-weight: 700;
    }

    nav a:hover {
      color: #ffffff;
    }

    .hero {
      min-height: 620px;
      padding: 108px 32px 54px;
      display: flex;
      align-items: flex-end;
      color: #ffffff;
      background:
        linear-gradient(90deg, rgba(15, 23, 42, 0.74), rgba(15, 118, 110, 0.34)),
        url('https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1800&q=85') center/cover no-repeat;
    }

    .hero-inner {
      width: min(1080px, 100%);
      margin: 0 auto;
    }

    .hero h1 {
      margin: 0 0 14px;
      max-width: 680px;
      font-size: clamp(40px, 5vw, 60px);
      line-height: 1;
      font-weight: 800;
      letter-spacing: 0;
    }

    .hero p {
      max-width: 620px;
      margin: 0 0 24px;
      color: rgba(255, 255, 255, 0.88);
      font-size: 18px;
      line-height: 1.6;
    }

    .search-box {
      width: min(560px, 100%);
      display: flex;
      gap: 10px;
      padding: 8px;
      background: rgba(255, 255, 255, 0.92);
      border-radius: 8px;
      box-shadow: 0 20px 40px rgba(15, 23, 42, 0.22);
    }

    .search-box input {
      flex: 1;
      min-width: 0;
      min-height: 44px;
      padding: 10px 13px;
      border: 1px solid #d9e0ea;
      border-radius: 8px;
      outline: none;
      font: inherit;
    }

    .search-box button {
      min-height: 44px;
      padding: 0 18px;
      border: 1px solid #2563eb;
      border-radius: 8px;
      background: #2563eb;
      color: white;
      font-weight: 800;
      cursor: pointer;
    }

    main {
      padding: 42px 32px 60px;
      background: #f5f7fb;
    }

    .section-title {
      width: min(1080px, 100%);
      margin: 0 auto 20px;
      font-size: 28px;
      color: #172033;
      font-weight: 800;
    }

    .card-container {
      width: min(1080px, 100%);
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 18px;
    }

    .card {
      background: #ffffff;
      border: 1px solid #e4e8ef;
      border-radius: 8px;
      box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
      overflow: hidden;
      transition: transform 0.16s ease, box-shadow 0.16s ease;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 16px 32px rgba(15, 23, 42, 0.1);
    }

    .card img {
      width: 100%;
      height: 190px;
      object-fit: cover;
    }

    .card-content {
      padding: 16px;
    }

    .card-content h3 {
      margin: 0 0 8px;
      color: #172033;
      font-size: 18px;
      font-weight: 800;
    }

    .card-content p {
      margin: 0;
      font-size: 14px;
      color: #647084;
      line-height: 1.55;
    }

    footer {
      background: #17324d;
      color: rgba(255, 255, 255, 0.86);
      text-align: center;
      padding: 24px 16px;
    }

    footer p {
      margin: 0;
    }

    footer a {
      color: #bfdbfe;
      margin: 0 8px;
      text-decoration: none;
      font-weight: 700;
    }

    footer a:hover {
      color: #ffffff;
    }

    @media (max-width: 760px) {
      header {
        padding: 0 18px;
      }

      nav {
        display: none;
      }

      .hero {
        min-height: 560px;
        padding: 96px 18px 42px;
      }

      .search-box {
        display: block;
      }

      .search-box button {
        width: 100%;
        margin-top: 8px;
      }

      main {
        padding: 32px 18px 48px;
      }
    }
  </style>
</head>
<body>
  <header>
    <h2>GreenHome.id</h2>
    <nav>
      <a href="#">Beranda</a>
      <a href="#">Tentang</a>
      <a href="#">Daftar Rumah</a>
      <a href="/login">Login</a>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-inner">
      <h1>GreenHome.id</h1>
      <p>Hunian modern dengan lingkungan nyaman, lokasi strategis, dan pilihan rumah yang mudah dibandingkan.</p>
      <div class="search-box">
        <input type="text" placeholder="Cari rumah berdasarkan lokasi, tipe, atau harga" />
        <button type="button">Cari</button>
      </div>
    </div>
  </section>

  <main>
    <div class="section-title">Daftar Rumah</div>
    <div class="card-container">
      <div class="card">
        <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=900&q=85" alt="Rumah Modern" />
        <div class="card-content">
          <h3>Rumah Modern</h3>
          <p>Lokasi strategis, 3 kamar tidur, harga mulai 500jt.</p>
        </div>
      </div>
      <div class="card">
        <img src="https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=900&q=85" alt="Rumah Klasik" />
        <div class="card-content">
          <h3>Rumah Klasik</h3>
          <p>Dekat alam, lingkungan tenang, harga mulai 400jt.</p>
        </div>
      </div>
      <div class="card">
        <img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=900&q=85" alt="Rumah Mewah" />
        <div class="card-content">
          <h3>Rumah Mewah</h3>
          <p>Desain elegan, 5 kamar tidur, kolam renang pribadi.</p>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; <?= date('Y') ?> GreenHome.id. Semua hak dilindungi.</p>
    <div style="margin-top: 12px;">
      <a href="https://instagram.com/greenhomeid" target="_blank">Instagram</a>
      <a href="https://facebook.com/greenhomeid" target="_blank">Facebook</a>
      <a href="mailto:info@greenhome.id">Email</a>
      <span>0812-3456-7890</span>
    </div>
  </footer>
</body>
</html>
