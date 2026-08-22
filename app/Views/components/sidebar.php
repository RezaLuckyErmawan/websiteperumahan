<?php  
  
use App\Config\Menu;  
  
/**  
 * Sidebar Component  
 * @var string $currentUrl - Current URL for active menu detection  
 * @var string $userRole - Current user role  
 */  
  
$menuConfig = new Menu();  
$menuItems = $menuConfig->getMenuForRole($userRole);  
  
/*  
 * Daftar menu yang ingin disembunyikan  
 */  
$hiddenMenus = [  
    'Bahan Bangunan',  
    'RAB Rumah',  
    'RAB Bahan',  
    'RAB Pekerja',  
    'Realisasi Rumah',  
    'Realisasi Bahan',  
    'Realisasi Pekerja',  
    'Data Bahan Pembangunan',  
    'Data Pekerjaan Insidentil',  
    'Manajemen Logistik',  
    'Data Mandor'  
];  
?>  
  
<div class="sidebar1" id="sidebar">  
    <div>  
        <h4>  
            Sistem Manajemen Informasi Perumahan  
        </h4>  
  
        <div class="nav1">  
  
            <?php foreach ($menuItems as $menuKey => $menuItem): ?>  
  
                <?php  
                /*  
                 * Sembunyikan menu utama jika label-nya ada  
                 * di dalam $hiddenMenus  
                 */  
                if (  
                    isset($menuItem['label']) &&  
                    in_array($menuItem['label'], $hiddenMenus)  
                ) {  
                    continue;  
                }  
                ?>  
  
                <?php if ($menuItem['type'] === 'link'): ?>  
  
                    <!-- Single Link -->  
                    <?php  
                    $isActiveLink = $menuConfig->isMenuItemActive(  
                        $menuItem['link'],  
                        $currentUrl  
                    );  
  
                    /*  
                     * Rename menu utama  
                     */  
                    $menuLabel = $menuItem['label'];  
  
                    if ($menuLabel === 'Pembelian Rumah') {  
                        $menuLabel = 'Penjualan Rumah';  
                    }  
  
                    if ($menuLabel === 'Data Pembelian Rumah') {  
                        $menuLabel = 'Data Penjualan Rumah';  
                    }  
                    ?>  
  
                    <a class="menu-link <?= $isActiveLink ? 'active current-page' : '' ?>"  
                       <?= $isActiveLink ? 'aria-current="page"' : '' ?>  
                       href="<?= esc($menuItem['link'], 'attr') ?>">  
  
                        <span class="material-icons rotate-icon">  
                            <?= esc($menuItem['icon']) ?>  
                        </span>  
  
                        <?= esc($menuLabel) ?>  
  
                    </a>  
  
                <?php elseif ($menuItem['type'] === 'dropdown'): ?>  
  
                    <?php  
                    /*  
                     * Cek apakah ada child yang aktif.  
                     * Child yang masuk $hiddenMenus tidak ikut dicek.  
                     */  
                    $hasActiveChild = false;  
  
                    foreach ($menuItem['items'] as $item) {  
  
                        if (  
                            isset($item['label']) &&  
                            in_array($item['label'], $hiddenMenus)  
                        ) {  
                            continue;  
                        }  
  
                        if (  
                            $menuConfig->isMenuItemActive(  
                                $item['link'],  
                                $currentUrl  
                            )  
                        ) {  
                            $hasActiveChild = true;  
                            break;  
                        }  
                    }  
                    ?>  
  
                    <!-- Dropdown Menu -->  
                    <div class="menu-dropdown <?= $hasActiveChild ? 'aktif' : '' ?>"  
                         data-dropdown-key="<?= esc($menuKey, 'attr') ?>">  
  
                        <button  
                            class="dropdown-btn <?= $hasActiveChild ? 'active-parent current-parent' : '' ?>"  
                            aria-expanded="<?= $hasActiveChild ? 'true' : 'false' ?>"  
                            data-dropdown-key="<?= esc($menuKey, 'attr') ?>">  
  
                            <span class="material-icons rotate-icon">  
                                <?= esc($menuItem['icon']) ?>  
                            </span>  
  
                            <?= esc($menuItem['label']) ?>  
  
                            <span class="material-icons arrow">  
                                expand_more  
                            </span>  
  
                        </button>  
  
                        <div class="dropdown-container">  
  
                            <?php foreach ($menuItem['items'] as $itemKey => $item): ?>  
  
                                <?php  
                                /*  
                                 * Sembunyikan submenu tertentu  
                                 */  
                                if (  
                                    isset($item['label']) &&  
                                    in_array($item['label'], $hiddenMenus)  
                                ) {  
                                    continue;  
                                }  
  
                                $isActiveChild = $menuConfig->isMenuItemActive(  
                                    $item['link'],  
                                    $currentUrl  
                                );  
  
                                /*  
                                 * Rename submenu  
                                 */  
                                $itemLabel = $item['label'];  
  
                                if ($itemLabel === 'Pembelian Rumah') {  
                                    $itemLabel = 'Penjualan Rumah';  
                                }  
  
                                if ($itemLabel === 'Data Pembelian Rumah') {  
                                    $itemLabel = 'Data Penjualan Rumah';  
                                }  
                                ?>  
  
                                <a class="menu-link <?= $isActiveChild ? 'active' : '' ?>"  
                                   <?= $isActiveChild ? 'aria-current="page"' : '' ?>  
                                   href="<?= esc($item['link'], 'attr') ?>"  
                                   style="margin-top: 10px;">  
  
                                    <span class="material-icons rotate-icon">  
                                        <?= esc($item['icon']) ?>  
                                    </span>  
  
                                    <?= esc($itemLabel) ?>  
  
                                </a>  
  
                            <?php endforeach; ?>  
  
                        </div>  
                    </div>  
  
                <?php endif; ?>  
  
            <?php endforeach; ?>  
  
        </div>  
    </div>  
  
    <div class="logout">  
        <a href="/logout">  
            <span class="material-icons">logout</span>  
            Logout  
        </a>  
    </div>  
</div>