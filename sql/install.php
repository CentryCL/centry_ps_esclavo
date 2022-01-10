<?php
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'img_variant_centry` (
    `id_img_ps` int(11) NOT NULL DEFAULT 0,
    `id_img_ct` varchar(255) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id_img_ps`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
