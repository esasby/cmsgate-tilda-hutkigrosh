<?php

use esas\cmsgate\bridge\controllers\ControllerBridge;
use esas\cmsgate\bridge\dao\Order;
use esas\cmsgate\bridge\service\OrderService;
use esas\cmsgate\bridge\service\SessionServiceBridge;
use esas\cmsgate\bridge\service\ShopConfigService;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAddBill;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAlfaclick;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshCompletionPage;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshNotify;
use esas\cmsgate\hutkigrosh\utils\RequestParamsHutkigrosh;
use esas\cmsgate\hro\pages\ClientOrderCompletionPageHROFactory;
use esas\cmsgate\Registry;
use esas\cmsgate\tilda\properties\PropertiesTilda;
use esas\cmsgate\tilda\protocol\RequestParamsTilda;
use esas\cmsgate\utils\JSONUtils;
use esas\cmsgate\utils\StringUtils;
use esas\cmsgate\utils\Logger as LoggerCms;

require_once((dirname(__FILE__)) . '/src/init.php');

$request = &$_SERVER['REDIRECT_URL'];

const PATH_BILL_ADD = '/api/bill/add';
const PATH_BILL_VIEW = '/api/bill/view';
const PATH_BILL_NOTIFY = '/api/bill/notify';
const PATH_BILL_ALFACLICK = '/api/bill/alfaclick';

$logger = LoggerCms::getLogger('index');

if (strpos($request, 'api') !== false) {
    try {
        $logger->info('Got request from Tilda: ' . JSONUtils::encodeArrayAndMask($_REQUEST, ["ps_hg_password"]));
        if (StringUtils::endsWith($request, PATH_BILL_ADD)) {
            // приходится сохрянть заказ где-то в кэше, для возможнсоти повторного отображения страницы в случае возврата с webpay
            ShopConfigService::fromRegistry()->checkAuthAndLoadConfig($_REQUEST);
            $order = new Order();
            $order->setOrderData($_REQUEST);
            OrderService::fromRegistry()->addSessionOrder($order);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            if ($orderWrapper->getExtId() == null || $orderWrapper->getExtId() == '') {
                $controller = new ControllerHutkigroshAddBill();
                $controller->process($orderWrapper);
            }
            renderCompletionPage($orderWrapper);
        } elseif (strpos($request, PATH_BILL_VIEW) !== false) {
            $uuid = $_REQUEST[RequestParamsTilda::ORDER_ID];
            SessionServiceBridge::fromRegistry()->setOrderUUID($uuid);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            renderCompletionPage($orderWrapper);
        } elseif (StringUtils::endsWith($request, PATH_BILL_ALFACLICK)) {
            $controller = new ControllerHutkigroshAlfaclick();
            $controller->process();
        } elseif (strpos($request, PATH_BILL_NOTIFY) !== false) {
            $extId = $_REQUEST[RequestParamsHutkigrosh::PURCHASE_ID];
            OrderService::fromRegistry()->loadSessionOrderByExtId($extId);
            $controller = new ControllerHutkigroshNotify();
            $controller->process($extId);
        } else {
            http_response_code(404);
            return;
        }
    } catch (Exception $e) {
        $logger->error("Exception", $e);
        ClientOrderCompletionPageHROFactory::findBuilder()
            ->setOrderWrapper(Registry::getRegistry()->getOrderWrapperForCurrentUser())
            ->setElementCompletionPanel(null)
            ->addCssLink(PropertiesTilda::fromRegistry()->getDefaultClientUICssLink())
            ->render();
    } catch (Throwable $e) {
        $logger->error("Exception", $e);
        ClientOrderCompletionPageHROFactory::findBuilder()
            ->setOrderWrapper(Registry::getRegistry()->getOrderWrapperForCurrentUser())
            ->setElementCompletionPanel(null)
            ->addCssLink(PropertiesTilda::fromRegistry()->getDefaultClientUICssLink())
            ->render();
    }
} else {
    $controller = new ControllerBridge();
    $controller->process();
}

/**
 * @param $orderWrapper \esas\cmsgate\wrappers\OrderWrapper
 * @throws Throwable
 */
function renderCompletionPage($orderWrapper)
{
    $controller = new ControllerHutkigroshCompletionPage();
    $completeionPage = $controller->process($orderWrapper);
    $completeionPage->addCssLink(PropertiesTilda::fromRegistry()->getDefaultClientUICssLink());
    $completeionPage->render();
}
