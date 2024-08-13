<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 12:05
 */

namespace esas\cmsgate\tilda;

use esas\cmsgate\bridge\security\CmsAuthService;
use esas\cmsgate\bridge\service\SessionServiceBridge;
use esas\cmsgate\bridge\view\admin\ConfigFormBridge;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\hutkigrosh\ConfigFieldsHutkigrosh;
use esas\cmsgate\hutkigrosh\hro\client\CompletionPanelHutkigroshHRO;
use esas\cmsgate\hutkigrosh\hro\client\CompletionPanelHutkigroshHRO_v2;
use esas\cmsgate\hutkigrosh\hro\sections\FooterSectionCompanyInfoHROTunerHutkigrosh;
use esas\cmsgate\hutkigrosh\hro\sections\HeaderSectionLogoContactsHROTunerHutkigrosh;
use esas\cmsgate\hutkigrosh\PaysystemConnectorHutkigrosh;
use esas\cmsgate\hutkigrosh\RegistryHutkigrosh;
use esas\cmsgate\hro\HROManager;
use esas\cmsgate\hro\pages\AdminLoginPageHRO;
use esas\cmsgate\hro\sections\FooterSectionCompanyInfoHRO;
use esas\cmsgate\hro\sections\HeaderSectionLogoContactsHRO;
use esas\cmsgate\tilda\hro\AdminLoginPageHROTunerTildaHutkigrosh;
use esas\cmsgate\tilda\protocol\RequestParamsTilda;
use esas\cmsgate\tilda\service\CmsAuthServiceTildaHutkigrosh;
use esas\cmsgate\tilda\service\ServiceProviderTilda;
use esas\cmsgate\utils\CMSGateException;
use esas\cmsgate\utils\URLUtils;
use esas\cmsgate\view\admin\AdminViewFields;

class RegistryHutkigroshTilda extends RegistryHutkigrosh
{
    public function __construct()
    {
        $this->cmsConnector = new CmsConnectorTilda();
        $this->paysystemConnector = new PaysystemConnectorHutkigrosh();
    }

    public function init()
    {
        parent::init();
        $this->registerServicesFromProvider(new ServiceProviderTilda());
        $this->registerService(CmsAuthService::class, new CmsAuthServiceTildaHutkigrosh());

        HROManager::fromRegistry()->addImplementation(CompletionPanelHutkigroshHRO::class, CompletionPanelHutkigroshHRO_v2::class);
        HROManager::fromRegistry()->addTuner(AdminLoginPageHRO::class, AdminLoginPageHROTunerTildaHutkigrosh::class);
        HROManager::fromRegistry()->addTuner(FooterSectionCompanyInfoHRO::class, FooterSectionCompanyInfoHROTunerHutkigrosh::class);
        HROManager::fromRegistry()->addTuner(HeaderSectionLogoContactsHRO::class, HeaderSectionLogoContactsHROTunerHutkigrosh::class);
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
        $configForm = new ConfigFormBridge(
            $managedFields,
            AdminViewFields::CONFIG_FORM_COMMON,
            null,
            ''
        );
        return $configForm;
    }


    function getUrlWebpay($orderWrapper)
    {
        $currentURL = URLUtils::getCurrentURLNoParams();
        $currentURL = str_replace(PATH_INVOICE_ADD, PATH_INVOICE_VIEW, $currentURL);
        if (strpos($currentURL, PATH_INVOICE_VIEW) !== false)
            return $currentURL . '?' . RequestParamsTilda::ORDER_ID . '=' . SessionServiceBridge::fromRegistry()->getOrderUUID();
        else
            throw new CMSGateException('Incorrect URL generation');
    }

    public function createModuleDescriptor()
    {
        return new ModuleDescriptor(
            "hutkigrosh",
            new VersionDescriptor("1.17.0", "2022-03-09"),
            "Tilda Hutkigrosh",
            "https://bitbucket.org/esasby/cmsgate-tilda-hutkigrosh/src/master/",
            VendorDescriptor::esas(),
            "Выставление пользовательских счетов в ЕРИП"
        );
    }

    public function createHooks()
    {
        return new HooksHutkigroshTilda();
    }

    public function getUrlAlfaclick($orderWrapper)
    {
        return '';
    }

    public function createConfigStorage()
    {
        return new ConfigStorageTildaHutkigrosh();
    }

    public function createProperties()
    {
        return new PropertiesTildaHutkigrosh();
    }
}