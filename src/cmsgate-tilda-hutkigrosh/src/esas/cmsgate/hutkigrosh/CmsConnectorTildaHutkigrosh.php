<?php


namespace esas\cmsgate\hutkigrosh;


use esas\cmsgate\CmsConnectorTilda;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\wrappers\OrderWrapperTilda;

class CmsConnectorTildaHutkigrosh extends CmsConnectorTilda
{
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
//        $line = $orderWrapper->getOrderId() . '|' . $orderWrapper->getAmount() . '|' . $orderWrapper->getCurrency() . '|' . $this->getNotificationSecret();
        $line = $orderWrapper->getOrderId() . '|' . $orderWrapper->getAmount() . '|' . $this->getNotificationSecret() . '|' . $orderWrapper->getCurrency();
        $this->logger->info('Sign values: ' . $line);
        return hash('sha256', $line);
    }
}