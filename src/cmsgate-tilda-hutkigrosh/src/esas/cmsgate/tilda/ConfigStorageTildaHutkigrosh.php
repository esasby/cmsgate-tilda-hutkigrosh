<?php


namespace esas\cmsgate\tilda;


use esas\cmsgate\hutkigrosh\ConfigFieldsHutkigrosh;

class ConfigStorageTildaHutkigrosh extends ConfigStorageTilda
{
    public function getConfigFieldLogin()
    {
        return ConfigFieldsHutkigrosh::login();
    }

    public function getConfigFieldPassword()
    {
        return ConfigFieldsHutkigrosh::password();
    }
}