<?php

use esas\cmsgate\CloudRegistry;
use esas\cmsgate\controllers\ControllerCloudConfig;
use esas\cmsgate\controllers\ControllerCloudLogin;
use esas\cmsgate\controllers\ControllerCloudLogout;
use esas\cmsgate\controllers\ControllerCloudSecretGenerate;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAddBill;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAlfaclick;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshCompletionPage;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshNotify;
use esas\cmsgate\hutkigrosh\RegistryHutkigroshTilda;
use esas\cmsgate\hutkigrosh\utils\RequestParamsHutkigrosh;
use esas\cmsgate\Registry;
use esas\cmsgate\tilda\RequestParamsTilda;
use esas\cmsgate\utils\CloudSessionUtils;
use esas\cmsgate\utils\JSONUtils;
use esas\cmsgate\utils\RedirectUtilsCloud;
use esas\cmsgate\utils\StringUtils;
use esas\cmsgate\utils\Logger as LoggerCms;

require_once((dirname(__FILE__)) . '/src/init.php');

$request = $_SERVER['REDIRECT_URL'];
const PATH_CONFIG = '/config';
const PATH_CONFIG_SECRET_NEW = '/config/secret/new';
const PATH_CONFIG_LOGIN = '/config/login';
const PATH_CONFIG_LOGOUT = '/config/logout';
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
            CloudRegistry::getRegistry()->getConfigCacheService()->checkAuthAndLoadConfig($_REQUEST);
            CloudRegistry::getRegistry()->getOrderCacheService()->addSessionOrderCache($_REQUEST);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            if ($orderWrapper->getExtId() == null || $orderWrapper->getExtId() == '') {
                $controller = new ControllerHutkigroshAddBill();
                $controller->process($orderWrapper);
            }
            $controller = new ControllerHutkigroshCompletionPage();
            $completeionPage = $controller->process($orderWrapper);
            $completeionPage->render();
        } elseif (strpos($request, PATH_BILL_VIEW) !== false) {
            $uuid = $_REQUEST[RequestParamsTilda::ORDER_ID];
            CloudSessionUtils::setOrderCacheUUID($uuid);
            $orderWrapper = Registry::getRegistry()->getOrderWrapperForCurrentUser();
            $controller = new ControllerHutkigroshCompletionPage();
            $completeionPage = $controller->process($orderWrapper);
            $completeionPage->render();
        } elseif (StringUtils::endsWith($request, PATH_BILL_ALFACLICK)) {
            $controller = new ControllerHutkigroshAlfaclick();
            $controller->process();
        } elseif (strpos($request, PATH_BILL_NOTIFY) !== false) {
            $extId = $_REQUEST[RequestParamsHutkigrosh::PURCHASE_ID];
            CloudRegistry::getRegistry()->getOrderCacheService()->loadSessionOrderCacheByExtId($extId);
            $controller = new ControllerHutkigroshNotify();
            $controller->process($extId);
        } else {
            http_response_code(404);
            return;
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
} else {
    if (StringUtils::endsWith($request, PATH_CONFIG_LOGIN)) {
        $controller = new ControllerCloudLogin();
        $controller->process();
    } elseif (StringUtils::endsWith($request, PATH_CONFIG_LOGOUT)) {
        $controller = new ControllerCloudLogout();
        $controller->process();
    } elseif (StringUtils::endsWith($request, PATH_CONFIG_SECRET_NEW)) {
        $controller = new ControllerCloudSecretGenerate();
        $controller->process();
    } elseif (StringUtils::endsWith($request, PATH_CONFIG)) {
        $controller = new ControllerCloudConfig();
        $controller->process();
    } else {
        http_response_code(404);
    }
}

