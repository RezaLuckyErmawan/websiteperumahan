<?php
if (!function_exists('page_nav_is_active')) {
    function page_nav_is_active(string $path): bool
    {
        $current = trim((string) service('request')->getUri()->getPath(), '/');
        $target = trim($path, '/');

        return $current === $target;
    }
}
?>
<style>
  .site-header {
    position: sticky;
    top: 0;
    z-index: 100;
    background: rgba(255, 255, 255, 0.88);
    backdrop-filter: blur(18px);
    border-bottom: 1px solid rgba(217, 229, 240, 0.95);
    box-shadow: 0 2px 14px rgba(15, 23, 42, 0.06);
  }

  .site-header-inner {
    width: min(1440px, calc(100% - clamp(20px, 4vw, 56px)));
    margin: 0 auto;
    min-height: 76px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
  }

  .brand {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    font-weight: 900;
    letter-spacing: -0.02em;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .brand-mark {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1d4ed8, #0ea5e9);
    color: #fff;
    box-shadow: 0 6px 22px rgba(29, 78, 216, 0.18);
    flex-shrink: 0;
  }

  .brand-mark .material-icons {
    font-size: 20px;
  }

  .brand-name {
    font-size: 18px;
    color: #0f1f35;
  }

  .nav-toggle {
    display: none;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border: 1px solid rgba(29, 78, 216, 0.14);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.94);
    color: #1d4ed8;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(29, 78, 216, 0.08);
    flex-shrink: 0;
  }

  .nav-toggle .material-icons {
    font-size: 22px;
  }

  .site-header-menu {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .header-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .header-nav a,
  .btn-login {
    min-height: 42px;
    padding: 0 14px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 800;
    text-decoration: none;
    transition: transform 0.16s ease, background-color 0.16s ease, color 0.16s ease, border-color 0.16s ease;
  }

  .header-nav a {
    display: inline-flex;
    align-items: center;
    color: #5f7289;
  }

  .header-nav a:hover,
  .header-nav a.active {
    color: #1d4ed8;
    background: rgba(219, 234, 254, 0.8);
  }

  .btn-login {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 1px solid rgba(29, 78, 216, 0.16);
    background: rgba(255, 255, 255, 0.92);
    color: #1d4ed8;
    box-shadow: 0 8px 20px rgba(29, 78, 216, 0.08);
    white-space: nowrap;
  }

  .btn-login:hover {
    background: #1d4ed8;
    color: #fff;
    transform: translateY(-1px);
  }

  @media (max-width: 1024px) {
    .site-header-inner {
      min-height: 72px;
      flex-wrap: wrap;
      padding-block: 10px;
    }

    .nav-toggle {
      display: inline-flex;
      margin-left: auto;
    }

    .site-header-menu {
      width: 100%;
      margin-left: 0;
      display: grid;
      grid-template-rows: 0fr;
      opacity: 0;
      pointer-events: none;
      transition: grid-template-rows 0.22s ease, opacity 0.18s ease;
    }

    .site-header[data-open="true"] .site-header-menu {
      grid-template-rows: 1fr;
      opacity: 1;
      pointer-events: auto;
    }

    .site-header-menu > .menu-shell {
      overflow: hidden;
    }

    .header-nav {
      padding-top: 12px;
      flex-direction: column;
      align-items: stretch;
      gap: 10px;
    }

    .header-nav a,
    .btn-login {
      width: 100%;
      justify-content: flex-start;
      padding-inline: 16px;
    }
  }

  @media (max-width: 767px) {
    .site-header-inner {
      width: min(100%, calc(100% - 20px));
    }

    .brand-name {
      font-size: 17px;
    }

    .brand-mark {
      width: 38px;
      height: 38px;
    }

    .header-nav a,
    .btn-login {
      min-height: 44px;
    }
  }

  @media (min-width: 1025px) {
    .site-header-menu {
      display: flex !important;
      grid-template-rows: none !important;
      opacity: 1 !important;
      pointer-events: auto !important;
    }

    .nav-toggle {
      display: none !important;
    }
  }
</style>
<header class="site-header" data-site-nav data-open="false">
    <div class="site-header-inner">
        <a class="brand" href="/">
            <span class="brand-mark"><span class="material-icons">home</span></span>
            <span class="brand-name">TAMAN MAHKOTA ROGOJAMPI</span>
        </a>
        <button class="nav-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" data-nav-toggle>
            <span class="material-icons">menu</span>
        </button>
        <div class="site-header-menu" data-nav-menu>
            <div class="menu-shell">
                <nav class="header-nav">
                    <a href="/" class="<?= page_nav_is_active('/') ? 'active' : '' ?>">Beranda</a>
                    <a href="/katalog-rumah" class="<?= page_nav_is_active('/katalog-rumah') ? 'active' : '' ?>">Katalog Rumah</a>
                    <a href="/login" class="btn-login">Login</a>
                </nav>
            </div>
        </div>
    </div>
</header>
<script>
(function () {
  var header = document.querySelector('[data-site-nav]');
  if (!header || header.dataset.navBound === '1') {
    return;
  }

  var toggle = header.querySelector('[data-nav-toggle]');
  var menu = header.querySelector('[data-nav-menu]');
  if (!toggle || !menu) {
    return;
  }

  header.dataset.navBound = '1';

  var mobileQuery = window.matchMedia('(max-width: 1024px)');

  function setOpen(open) {
    header.dataset.open = open ? 'true' : 'false';
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  toggle.addEventListener('click', function () {
    setOpen(header.dataset.open !== 'true');
  });

  menu.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () {
      if (mobileQuery.matches) {
        setOpen(false);
      }
    });
  });

  mobileQuery.addEventListener('change', function (event) {
    if (!event.matches) {
      setOpen(false);
    }
  });

  setOpen(false);
})();
</script>
