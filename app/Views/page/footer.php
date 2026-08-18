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
<footer class="site-footer">
    <div class="footer-inner">
        <strong>GreenHome.id</strong> &copy; <?= date('Y') ?> Sistem Manajemen Perumahan
    </div>
</footer>
