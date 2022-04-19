<?php


namespace esas\cmsgate\hutkigrosh;


use esas\cmsgate\CloudRegistryPDO;
use esas\cmsgate\security\ApiAuthServiceTilda;
use esas\cmsgate\security\CryptServiceImpl;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\view\admin\AdminConfigPage;
use esas\cmsgate\view\admin\AdminLoginPage;
use esas\cmsgate\security\AuthConfigMapper;
use PDO;

class CloudRegistryHutkigroshTilda extends CloudRegistryPDO
{
    private $config;

    public function __construct()
    {
        define('read_config', true);
        $this->config = require (dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.php');
    }

    public function getPDO()
    {
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO(
            $this->config[CONFIG_PDO_DSN],
            $this->config[CONFIG_PDO_USERNAME],
            $this->config[CONFIG_PDO_PASSWORD],
            $opt);
    }

    protected function createApiAuthService()
    {
        return new ApiAuthServiceTilda(
            RequestParamsTilda::SIGNATURE);
    }

    public function createAdminConfigPage()
    {
        return new AdminConfigPage();
    }

    public function createAdminLoginPage()
    {
        return new AdminLoginPage();
    }

    public function isSandbox()
    {
        return $this->config[CONFIG_SANDBOX];
    }

    protected function createCryptService()
    {
        return new CryptServiceImpl('/opt/cmsgate/storage');
    }


    public function createAuthConfigMapper()
    {
        return new AuthConfigMapper(
            ConfigFieldsHutkigrosh::login(),
            ConfigFieldsHutkigrosh::password());
    }
}