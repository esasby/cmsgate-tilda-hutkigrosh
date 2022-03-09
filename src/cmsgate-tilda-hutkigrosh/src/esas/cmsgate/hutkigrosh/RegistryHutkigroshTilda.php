<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\hutkigrosh;

use esas\cmsgate\CmsConnectorTilda;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\hutkigrosh\view\client\CompletionPageHutkigrosh;
use esas\cmsgate\hutkigrosh\view\client\CompletionPanelHutkigroshTilda;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\utils\CloudSessionUtils;
use esas\cmsgate\utils\CMSGateException;
use esas\cmsgate\utils\SessionUtils;
use esas\cmsgate\utils\URLUtils;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigFormCloud;

class RegistryHutkigroshTilda extends RegistryHutkigrosh
{
    public function __construct()
    {
        $this->cmsConnector = new CmsConnectorTilda();
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
        $managedFields = $this->getManagedFieldsFactory()->getManagedFieldsOnly(AdminViewFields::CONFIG_FORM_COMMON, [
            ConfigFieldsHutkigrosh::eripId(),
            ConfigFieldsHutkigrosh::eripPath(),
            ConfigFieldsHutkigrosh::eripTreeId(),
            ConfigFieldsHutkigrosh::completionText(),
            ConfigFieldsHutkigrosh::dueInterval(),
            ConfigFieldsHutkigrosh::instructionsSection(),
            ConfigFieldsHutkigrosh::qrcodeSection(),
            ConfigFieldsHutkigrosh::webpaySection(),
            ConfigFieldsHutkigrosh::notificationEmail(),
            ConfigFieldsHutkigrosh::notificationSms(),
            ]);
        $configForm = new ConfigFormCloud(
            $managedFields,
            AdminViewFields::CONFIG_FORM_COMMON,
            null,
            ''
        );
        return $configForm;
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
            return $currentURL
                . '?' . RequestParamsTilda::ORDER_ID . '=' . CloudSessionUtils::getOrderCacheUUID();
        else
            throw new CMSGateException('Incorrect URL genearation');
    }

    public function createModuleDescriptor()
    {
        return new ModuleDescriptor(
            "tilda-hutkigrosh",
            new VersionDescriptor("1.17.0", "2022-03-09"),
            "Tilda Hutkigrosh",
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

    public function createHooks()
    {
        return new HooksHutkigroshTilda();
    }
}