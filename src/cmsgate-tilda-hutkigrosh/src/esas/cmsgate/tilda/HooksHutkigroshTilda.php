<?php


namespace esas\cmsgate\tilda;

use esas\cmsgate\hutkigrosh\HooksHutkigrosh;
use esas\cmsgate\hutkigrosh\protocol\HutkigroshBillInfoRs;
use esas\cmsgate\Registry;
use esas\cmsgate\tilda\controllers\ControllerTildaNotify;
use esas\cmsgate\wrappers\OrderWrapper;

class HooksHutkigroshTilda extends HooksHutkigrosh
{

    public function onNotifyStatusPayed(OrderWrapper $orderWrapper, HutkigroshBillInfoRs $resp)
    {
        parent::onNotifyStatusPayed($orderWrapper, $resp);
        $controller = new ControllerTildaNotify();
        $controller->process(Registry::getRegistry()->getOrderWrapperForCurrentUser());
    }
}