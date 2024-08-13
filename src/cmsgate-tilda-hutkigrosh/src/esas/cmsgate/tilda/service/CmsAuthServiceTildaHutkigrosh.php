<?php


namespace esas\cmsgate\tilda\service;


use esas\cmsgate\hutkigrosh\ConfigFieldsHutkigrosh;
use esas\cmsgate\tilda\security\CmsAuthServiceTilda;

class CmsAuthServiceTildaHutkigrosh extends CmsAuthServiceTilda
{
    public function getRequestFieldLogin()
    {
        return ConfigFieldsHutkigrosh::login();
    }
}