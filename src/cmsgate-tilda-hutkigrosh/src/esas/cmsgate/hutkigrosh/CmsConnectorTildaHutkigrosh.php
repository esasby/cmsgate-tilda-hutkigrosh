<?php


namespace esas\cmsgate\hutkigrosh;


use esas\cmsgate\CmsConnectorTilda;
use esas\cmsgate\Registry;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\wrappers\OrderWrapperTilda;

class CmsConnectorTildaHutkigrosh extends CmsConnectorTilda
{
    public function getNotificationURL()
    {
        $cache = Registry::getRegistry()->getCacheRepository()->getSessionCacheSafe();
        return $cache->getOrderData()[RequestParamsTilda::NOTIFICATION_URL]; // can be hardcoded hear after tilda moderation. it's common for all projects on tilda
    }

    public function getNotificationSecret() {
        return $_REQUEST[RequestParamsTilda::SECRET];
    }

    public function checkSignature($request)
    {
        return true;
    }

    /**
     * @param OrderWrapperTilda $orderWrapper
     * @return mixed
     */
    public function createNotificationSignature($orderWrapper)
    {
        $line = $orderWrapper->getOrderId() . '|' . $orderWrapper->getAmount() . '|' . $orderWrapper->getCurrency() . '|' . $this->getNotificationSecret();
        return hash('sha255', $line);
    }
}