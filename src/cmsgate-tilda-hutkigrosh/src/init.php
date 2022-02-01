<?php
use esas\cmsgate\hutkigrosh\RegistryHutkigroshTilda;
use esas\cmsgate\CmsPlugin;

if (!class_exists("esas\cmsgate\CmsPlugin")) {
    require_once(dirname(dirname(__FILE__)) . '/vendor/esas/cmsgate-core/src/esas/cmsgate/CmsPlugin.php');

    (new CmsPlugin(dirname(dirname(__FILE__)) . '/vendor', dirname(__FILE__)))
        ->setRegistry(new RegistryHutkigroshTilda())
        ->init();

}

