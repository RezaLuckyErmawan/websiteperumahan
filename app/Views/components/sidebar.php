<?php

use App\Config\Menu;

/**
 * Sidebar Component
 * @var string $currentUrl - Current URL for active menu detection
 * @var string $userRole - Current user role
 */

$menuConfig = new Menu();
$menuItems = $menuConfig->getMenuForRole($userRole);
?>

<div class="sidebar1" id="sidebar">
    <div>
        <h4>
            Sistem Manajemen Perumahan
        </h4>
        <div class="nav1">
            <?php foreach ($menuItems as $menuKey => $menuItem): ?>
                <?php if ($menuItem['type'] === 'link'): ?>
                    <!-- Single Link -->
                    <?php $isActiveLink = $menuConfig->isMenuItemActive($menuItem['link'], $currentUrl); ?>
                    <a class="menu-link <?= $isActiveLink ? 'active current-page' : '' ?>"
                       <?= $isActiveLink ? 'aria-current="page"' : '' ?>
                       href="<?= esc($menuItem['link'], 'attr') ?>">
                        <span class="material-icons rotate-icon"><?= esc($menuItem['icon']) ?></span>
                        <?= esc($menuItem['label']) ?>
                    </a>

                <?php elseif ($menuItem['type'] === 'dropdown'): ?>
                    <?php
                        $hasActiveChild = false;
                        foreach ($menuItem['items'] as $item) {
                            if ($menuConfig->isMenuItemActive($item['link'], $currentUrl)) {
                                $hasActiveChild = true;
                                break;
                            }
                        }
                    ?>
                    <!-- Dropdown Menu -->
                    <div class="menu-dropdown <?= $hasActiveChild ? 'aktif' : '' ?>" data-dropdown-key="<?= esc($menuKey, 'attr') ?>">
                        <button class="dropdown-btn <?= $hasActiveChild ? 'active-parent current-parent' : '' ?>" aria-expanded="<?= $hasActiveChild ? 'true' : 'false' ?>" data-dropdown-key="<?= esc($menuKey, 'attr') ?>">
                            <span class="material-icons rotate-icon"><?= esc($menuItem['icon']) ?></span>
                            <?= esc($menuItem['label']) ?>
                            <span class="material-icons arrow">expand_more</span>
                        </button>
                        <div class="dropdown-container">
                            <?php foreach ($menuItem['items'] as $itemKey => $item): ?>
                                <?php $isActiveChild = $menuConfig->isMenuItemActive($item['link'], $currentUrl); ?>
                                <a class="menu-link <?= $menuConfig->isMenuItemActive($item['link'], $currentUrl) ? 'active' : '' ?>"
                                   <?= $isActiveChild ? 'aria-current="page"' : '' ?>
                                   href="<?= esc($item['link'], 'attr') ?>"
                                   style="margin-top: 10px;">
                                    <span class="material-icons rotate-icon"><?= esc($item['icon']) ?></span>
                                    <?= esc($item['label']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="logout">
        <a href="/logout"><span class="material-icons">logout</span> Logout</a>
    </div>
</div>
