<?php

use esas\cmsgate\CmsPluginCloud;
use esas\cmsgate\hutkigrosh\CloudRegistryHutkigroshTilda;
use esas\cmsgate\hutkigrosh\RegistryHutkigroshTilda;

if (!class_exists("esas\cmsgate\CmsPluginCloud")) {
    require_once(dirname(dirname(__FILE__)) . '/vendor/esas/cmsgate-cloud-lib/src/esas/cmsgate/CmsPluginCloud.php');

    (new CmsPluginCloud(dirname(dirname(__FILE__)) . '/vendor', dirname(__FILE__)))
        ->setRegistry(new RegistryHutkigroshTilda())
        ->setCloudRegistry(new CloudRegistryHutkigroshTilda())
        ->init();

}

