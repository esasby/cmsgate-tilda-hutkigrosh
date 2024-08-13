<?php


namespace esas\cmsgate\tilda\hro;


use esas\cmsgate\bridge\view\client\RequestParamsBridge;
use esas\cmsgate\Registry;
use esas\cmsgate\hro\HRO;
use esas\cmsgate\hro\HROTuner;
use esas\cmsgate\hro\pages\AdminLoginPageHRO;
use esas\cmsgate\tilda\PropertiesTildaHutkigrosh;

class AdminLoginPageHROTunerTildaHutkigrosh implements HROTuner
{
    /**
     * @param AdminLoginPageHRO $hroBuilder
     * @return HRO|void
     */
    public function tune($hroBuilder)
    {
        return $hroBuilder
            ->setLoginField(RequestParamsBridge::LOGIN_FORM_LOGIN, "Login")
            ->setPasswordField(RequestParamsBridge::LOGIN_FORM_PASSWORD, 'Password')
            ->setSandbox(PropertiesTildaHutkigrosh::fromRegistry()->isSandbox())
            ->setMessage("Login to cmsgate " . Registry::getRegistry()->getPaysystemConnector()->getPaySystemConnectorDescriptor()->getPaySystemMachinaName());
    }
}