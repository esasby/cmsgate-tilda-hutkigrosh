<?php

use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAddBill;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAlfaclick;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshCompletionPage;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshNotify;
use esas\cmsgate\hutkigrosh\RegistryHutkigroshTilda;
use esas\cmsgate\hutkigrosh\utils\RequestParamsHutkigrosh;
use esas\cmsgate\Registry;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\utils\JSONUtils;
use esas\cmsgate\utils\SessionUtils;
use esas\cmsgate\utils\StringUtils;
use esas\cmsgate\utils\Logger as LoggerCms;

require_once((dirname(__FILE__)) . '/src/init.php');

$request = $_SERVER['REDIRECT_URL'];
const PATH_BILL_ADD = '/bill/add';
const PATH_BILL_VIEW = '/bill/view';
const PATH_BILL_NOTIFY = '/bill/notify';
const PATH_BILL_ALFACLICK = '/bill/alfaclick';

$logger = LoggerCms::getLogger('index');
try {
    $logger->info('Got request from Tilda: ' . JSONUtils::encodeArrayAndMask($_REQUEST, ["ps_hg_password"]));
    switch ($request) {
        case StringUtils::endsWith($request, PATH_BILL_ADD):
            // приходится сохрянть заказ где-то в кэше, для возможнсоти повторного отображения страницы в случае возврата с webpay
            Registry::getRegistry()->getCmsConnector()->checkSignature($_REQUEST);
            Registry::getRegistry()->getCacheRepository()->addSessionCache($_REQUEST);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            if ($orderWrapper->getExtId() == null || $orderWrapper->getExtId() == '') {
                $controller = new ControllerHutkigroshAddBill();
                $controller->process($orderWrapper);
            }
            $controller = new ControllerHutkigroshCompletionPage();
            $completeionPage = $controller->process($orderWrapper);
            $completeionPage->render();
            break;
        case strpos($request, PATH_BILL_VIEW) !== false:
            $uuid = $_REQUEST[RequestParamsTilda::ORDER_ID];
            SessionUtils::setCacheUUID($uuid);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            $controller = new ControllerHutkigroshCompletionPage();
            $completeionPage = $controller->process($orderWrapper);
            $completeionPage->render();
            break;
        case StringUtils::endsWith($request, PATH_BILL_ALFACLICK):
            $controller = new ControllerHutkigroshAlfaclick();
            $controller->process();
            break;
        case strpos($request, PATH_BILL_NOTIFY) !== false:
            $extId = $_REQUEST[RequestParamsHutkigrosh::PURCHASE_ID];
            Registry::getRegistry()->getCacheRepository()->loadSessionCacheByExtId($extId);
            $controller = new ControllerHutkigroshNotify();
            $controller->process($extId);
            break;
        default:
            http_response_code(404);
            break;
    }
} catch (Exception $e) {
    $logger->error("Exception", $e);
    $errorPage = RegistryHutkigroshTilda::getRegistry()->getCompletionPage(
        Registry::getRegistry()->getOrderWrapperForCurrentUser(),
        null
    );
    $errorPage->render();
} catch (Throwable $e) {
    $logger->error("Exception", $e);
    $errorPage = RegistryHutkigroshTilda::getRegistry()->getCompletionPage(
        Registry::getRegistry()->getOrderWrapperForCurrentUser(),
        null
    );
    $errorPage->render();
}