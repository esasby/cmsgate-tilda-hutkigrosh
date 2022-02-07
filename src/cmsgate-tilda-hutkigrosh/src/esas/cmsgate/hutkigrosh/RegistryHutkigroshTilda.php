<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\hutkigrosh;

use esas\cmsgate\cache\CacheRepository;
use esas\cmsgate\cache\CacheRepositoryPDOCrypto;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\hutkigrosh\view\client\CompletionPageHutkigrosh;
use esas\cmsgate\hutkigrosh\view\client\CompletionPanelHutkigroshTilda;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\utils\CMSGateException;
use esas\cmsgate\utils\SessionUtils;
use esas\cmsgate\utils\URLUtils;
use Exception;

class RegistryHutkigroshTilda extends RegistryHutkigrosh
{
    public function __construct()
    {
        $this->cmsConnector = new CmsConnectorTildaHutkigrosh();
        $this->paysystemConnector = new PaysystemConnectorHutkigrosh();
    }


    /**
     * Переопределение для упрощения типизации
     * @return RegistryHutkigroshTilda
     */
    public static function getRegistry()
    {
        return parent::getRegistry();
    }

    /**
     * @throws \Exception
     */
    public function createConfigForm()
    {
        throw new Exception('Not implemented');
    }


    function getUrlAlfaclick($orderWrapper)
    {
        return "";
    }


    function getUrlWebpay($orderWrapper)
    {
        $currentURL = URLUtils::getCurrentURLNoParams();
        $currentURL = str_replace(PATH_BILL_ADD, PATH_BILL_VIEW, $currentURL);
        if (strpos($currentURL, PATH_BILL_VIEW) !== false)
            return $currentURL . '?' . RequestParamsTilda::ORDER_ID . '=' . SessionUtils::getCacheUUID();
        else
            throw new CMSGateException('Incorrect URL genearation');
    }

    public function createModuleDescriptor()
    {
        return new ModuleDescriptor(
            "commerce-tilda-hutkigrosh", // код должен совпадать с кодом решения в маркете (@id в Plugin\Commerce\PaymentGateway\xxx.php)
            new VersionDescriptor("1.16.0", "2022-01-12"),
            "Прием платежей через ЕРИП (сервис Hutkigrosh)",
            "https://bitbucket.org/esasby/cmsgate-tilda-hutkigrosh/src/master/",
            VendorDescriptor::esas(),
            "Выставление пользовательских счетов в ЕРИП"
        );
    }

    public function getCompletionPanel($orderWrapper)
    {
        return new CompletionPanelHutkigroshTilda($orderWrapper);
    }

    /**
     * @param $orderWrapper
     * @param $completionPanel
     * @return CompletionPageHutkigrosh
     */
    public function getCompletionPage($orderWrapper, $completionPanel)
    {
        return new CompletionPageHutkigrosh($orderWrapper, $completionPanel);
    }

    /**
     * @return CacheRepository
     */
    public function createCacheRepository()
    {
        return new CacheRepositoryPDOCrypto(
            "mysql:host=127.0.0.1;dbname=cmsgate;charset=utf8",
            'user',
            'password',
            'tilda_hutkigrosh_cache');
    }

    public function createHooks()
    {
        return new HooksHutkigroshTilda();
    }
}