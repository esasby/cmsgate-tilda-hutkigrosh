<?php


namespace esas\cmsgate\hutkigrosh;


use esas\cmsgate\controller\ControllerTildaNotify;
use esas\cmsgate\hutkigrosh\protocol\HutkigroshBillInfoRs;
use esas\cmsgate\Registry;
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