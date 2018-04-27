<?php


namespace Module\Ekom;


use Module\Ekom\Back\Helper\BackHooksHelper;

class EkomHooks
{


    //--------------------------------------------
    // HOOKS OF EKOM MODULE
    //--------------------------------------------            
    protected static function Ekom_adaptContextualConfig(array &$conf) {
            
    }

            
    protected static function Ekom_CacheLayer_deleteModules($shopId, $langId) {
            
    }

            
    protected static function Ekom_CacheLayer_rebuildModules($shopId, $langId) {
            
    }

            
    protected static function Ekom_Carrier_getShopPhysicalAddressForShipping(array &$shopAddress, array $data) {
            
    }

            
    protected static function Ekom_cart_addItemBefore(array &$hookParams) {
            
    }

            
    protected static function Ekom_Cart_defineShippingTaxGroup(&$shippingTaxGroup = null, array $cartModel) {
            
    }

            
    protected static function Ekom_Cart_getSellerTaxHint(&$taxHint, $seller, array $boxes) {
            
    }

            
    protected static function Ekom_Cart_handleAddCoupon(array &$ball, array $couponInfo) {
            
    }

            
    protected static function Ekom_CartUtil_getSellerByItem(&$seller, array $item) {
            
    }

            
    protected static function Ekom_CategoryController_decorateClawsPostRequest(\Kamille\Utils\Claws\Claws $claws, array $context) {
            
    }

            
    protected static function Ekom_CategoryController_decorateClaws(\Kamille\Utils\Claws\Claws $claws, array $model) {
            
    }

            
    protected static function Ekom_CategoryController_decorateItemsGeneratorAndClaws(\QueryFilterBox\ItemsGenerator\ItemsGenerator $generator, \Kamille\Utils\Claws\Claws $claws, array $context) {
            
    }

            
    protected static function Ekom_categoryLayer_overrideLinkOptions(array &$linkOptions, $marker) {
            
    }

            
    protected static function Ekom_CategoryModel_prepareModelWithHybridList(array &$dotKey2Control, \HybridList\HybridListInterface $hybridList, array $context, array &$controls = []) {
            
    }

            
    protected static function Ekom_CheckoutHelper_onUpdateCurrentCheckoutDataAfter(array $changes) {
            
    }

            
    protected static function Ekom_CheckoutOrderUtil_checkDataConsistency(array $checkoutData, array $cartModel) {
            
    }

            
    protected static function Ekom_CheckoutOrderUtil_handleShippingErrorCode(array &$appErrorInfo, $errorCode) {
            
    }

            
    protected static function Ekom_CheckoutOrderUtil_decorateOrderDetails(array &$orderDetails) {
            
    }

            
    protected static function Ekom_CheckoutOrderUtil_processInvoiceAfter($invoiceId, array $invoice) {
            
    }

            
    protected static function Ekom_CheckoutPageUtil_onCheckoutNewSession() {
            
    }

            
    protected static function Ekom_CheckoutOrderUtil_onPlaceOrderSuccessAfter($orderId, array $orderModel) {
            
    }

            
    protected static function Ekom_CheckoutPageUtil_onStepCompleted($stepName, array $data) {
        // mit-start:Ekom
        \Module\Ekom\Helper\HooksHelper::Ekom_CheckoutPageUtil_onStepCompleted($stepName, $data);
        // mit-end:Ekom    
    }

            
    protected static function Ekom_CheckoutPageUtil_registerSteps(\Module\Ekom\Utils\Checkout\CheckoutPageUtil $checkoutPageUtil) {
            
    }

            
    protected static function Ekom_CheckoutProcess_decorateCheckoutProcess(\Module\Ekom\Utils\CheckoutProcess\CheckoutProcessInterface $process, array $cartModel) {
            
    }

            
    protected static function Ekom_CheckoutUtil_overrideCurrentCartLayer(&$cartLayer) {
            
    }

            
    protected static function Ekom_CheckoutUtil_overrideCurrentCheckoutProcess(&$checkoutProcess) {
            
    }

            
    protected static function Ekom_CheckoutUtil_overrideCurrentCheckoutProcessModel(&$checkoutProcessModel) {
            
    }

            
    protected static function Ekom_CheckoutUtil_overrideCurrentCheckoutOrderUtil(&$checkoutOrderUtil) {
            
    }

            
    protected static function Ekom_CheckoutUtil_overrideCurrentCheckoutThankYouRoute(&$thankYouRoute) {
            
    }

            
    protected static function Ekom_configureCheckoutLayerProvider(\Module\Ekom\CheckoutLayerProvider\CheckoutLayerProvider $provider) {
            
    }

            
    protected static function Ekom_configureListBundle(\Module\Ekom\ListParams\ListBundleFactory\EkomListBundleFactory $factory) {
            
    }

            
    protected static function Ekom_getReferenceProvider() {
            
    }

            
    protected static function Ekom_cart_decorateDefaultCart(array &$defaultCart) {
            
    }

            
    protected static function Ekom_CartLayer_decorateCartModel(array &$model) {
            
    }

            
    protected static function Ekom_Connexion_decorateUserConnexionData(array &$userConnexionData) {
            
    }

            
    protected static function Ekom_createAccountAfter(array &$hookData) {
            
    }

            
    protected static function Ekom_DataChangeDispatcher_decorateDispatcher(\Dispatcher\Basic\BasicDispatcherInterface $dispatcher) {
            
    }

            
    protected static function Ekom_decorateBoxModel(array &$primitiveBoxModel, array $productBoxContext) {
            
    }

            
    protected static function Ekom_decorateProductBoxClaws(\Kamille\Utils\Claws\Claws $claws, array $model) {
            
    }

            
    protected static function Ekom_Ecp_decorateOutput(array &$out, $action, $intent = "") {
            
    }

            
    protected static function Ekom_ProductHelper_removeProductById_after(array $idInfo) {
            
    }

            
    protected static function Ekom_decorateProductIdToUniqueProductIdAdaptor(\Module\Ekom\Utils\ProductIdToUniqueProductIdAdaptor\ProductIdToUniqueProductIdAdaptor $adaptor) {
            
    }

            
    protected static function Ekom_Ecp_logInvalidArgumentException(\Ecp\Exception\EcpInvalidArgumentException $e) {
            
    }

            
    protected static function Ekom_feedAttributesModelGeneratorFactory(\Module\Ekom\ProductBox\AttributesModel\GeneratorFactory\EkomAttributesModelGeneratorFactory $factory) {
            
    }

            
    protected static function Ekom_feedCarrierCollection(\Module\Ekom\Carrier\Collection\CarrierCollectionInterface $collection) {
            
    }

            
    protected static function Ekom_feedCustomerMenu(\Models\AdminSidebarMenu\Lee\LeeAdminSidebarMenuModel $menu) {
            
    }

            
    protected static function Ekom_feedFrontControllerClaws(\Kamille\Utils\Claws\Claws $claws) {
            
    }

            
    protected static function Ekom_feedOrderBuilderCollection(\Module\Ekom\Utils\OrderBuilder\Collection\OrderBuilderCollection $collection) {
            
    }

            
    protected static function Ekom_feedPaymentMethodHandlerCollection(\Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface $collection) {
            
    }

            
    protected static function Ekom_feedPaymentMethodsContainer(\Kamille\Utils\Claws\Claws $claws) {
            
    }

            
    protected static function Ekom_feedCartAllowedExtraArgs(array $allowedExtraArgs) {
            
    }

            
    protected static function Ekom_feedEkomProductPriceChain(\Module\Ekom\Price\PriceChain\EkomProductPriceChain $chain) {
            
    }

            
    protected static function Ekom_feedEkomCartPriceChain(\Module\Ekom\Price\PriceChain\EkomCartPriceChain $chain) {
            
    }

            
    protected static function Ekom_feedEkomTotalPriceChain(\Module\Ekom\Price\PriceChain\EkomTotalPriceChain $chain) {
            
    }

            
    protected static function Ekom_feedJsApiLoader(\Module\Ekom\JsApiLoader\EkomJsApiLoader $loader) {
            
    }

            
    protected static function Ekom_feedEkomNotifier(\Module\Ekom\Notifier\EkomNotifier $notifier) {
            
    }

            
    protected static function Ekom_feedStatusProviderCollection(\Module\Ekom\Status\ProviderCollection\StatusProviderCollection $collection) {
            
    }

            
    protected static function Ekom_feedDynamicWidgetBinder(\Kamille\Utils\Laws\DynamicWidgetBinder\DynamicWidgetBinder $binder) {
            
    }

            
    protected static function Ekom_FrontController_decorateCommonWidgets($ball) {
            
    }

            
    protected static function Ekom_lazyConfig_getValue(&$value, $key) {
            
    }

            
    protected static function Ekom_Mailer_decorateVariables(array &$variables, $mailType, $recipient) {
            
    }

            
    protected static function Ekom_onCartUpdate($shopId, $userId, array $cart, $operationName = null) {
            
    }

            
    protected static function Ekom_onProductVisited($productId, array $productDetails = []) {
            
    }

            
    protected static function Ekom_PdfHtmlInfo_decorate($pdfId, \Module\Ekom\Utils\Pdf\PdfHtmlInfoInterface $pdfHtmlInfo) {
            
    }

            
    protected static function Ekom_onUserConnectedAfter() {
            
    }

            
    protected static function Ekom_prepareJsonService($service) {
            
    }

            
    protected static function Ekom_ProductBox_collectAvailableProductDetails(array &$data) {
            
    }

            
    protected static function Ekom_ProductBox_collectGeneralContext(array &$data) {
            
    }

            
    protected static function Ekom_ProductBox_getTabathaDeleteIdentifiers(array &$data) {
            
    }

            
    protected static function Ekom_ProductCard_prepareClaws(\Kamille\Utils\Claws\ClawsInterface $claws, array $headModel, array $tailModel) {
            
    }

            
    protected static function Ekom_ProductPage_decoratePageTailModel(array &$tailModel, array $headModel) {
            
    }

            
    protected static function Ekom_SearchResults_Provider(array &$model, array $context) {
            
    }

            
    protected static function Ekom_service_cartAddItem_decorateOutput(array &$out) {
            
    }

            
    protected static function Ekom_Theme_decorate_SokoLoginFormModel(array &$model) {
            
    }

            
    protected static function Ekom_updateItemQuantity_decorateCartModel(array &$cartModel, array $data) {
            
    }


    //--------------------------------------------
    // SUBSCRIBED HOOKS
    //--------------------------------------------            
    protected static function NullosAdmin_layout_addTopBarRightWidgets(array &$topbarRightWidgets) {
        // mit-start:Ekom
        $prefixUri = "/theme/" . \Kamille\Architecture\ApplicationParameters\ApplicationParameters::get("theme");
        $imgPrefix = $prefixUri . "/production";

        unset($topbarRightWidgets['topbar_right.userMessages']);

        $topbarRightWidgets["topbar_right.shopListDropDown"] = [
            "tpl" => "Ekom/ShopListDropDown/prototype",
            "conf" => [
                'nbMessages' => 10,
                'badgeColor' => 'red',
                'showAllMessagesLink' => true,
                'allMessagesText' => "See All Alerts",
                'allMessagesLink' => "/user-alerts",
                "messages" => [
                    [
                        "link" => "/ji",
                        "title" => "John Smith",
                        "image" => $imgPrefix . '/images/ling.jpg',
                        "aux" => "3 mins ago",
                        "message" => "Film festivals used to be do-or-die moments for movie makers. They were where...",
                    ],
                    [
                        "link" => "/ji",
                        "title" => "John Smith",
                        "image" => $imgPrefix . '/images/img.jpg',
                        "aux" => "12 mins ago",
                        "message" => "Film festivals used to be do-or-die moments for movie makers. They were where...",
                    ],
                ],
            ],
        ];
        // mit-end:Ekom    
    }

            
    protected static function NullosAdmin_layout_sideBarMenuModelObject(\Models\AdminSidebarMenu\Lee\LeeAdminSidebarMenuModel $sideBarMenuModel) {
        // mit-start:Ekom
        BackHooksHelper::NullosAdmin_layout_sideBarMenuModelObject($sideBarMenuModel);
        // mit-end:Ekom    
    }

            
    protected static function NullosAdmin_layout_sideBarMenuModel(array &$sideBarMenuModel) {
        // mit-start:Ekom
        $sideBarMenuModel['sections'][] = [
            "label" => "Ekom",
            "items" => [
                [
                    "icon" => "fa fa-home",
                    "label" => "test",
                    'badge' => [
                        'type' => "success",
                        'text' => "success",
                    ],
                    "items" => [
                        [
                            "icon" => "fa fa-but",
                            "label" => "bug",
                            "link" => "/pou",
                            "items" => null,
                        ],
                    ],
                ],
            ],
        ];
        // mit-end:Ekom    
    }

            
    protected static function NullosAdmin_prepareClaws(\Kamille\Utils\Claws\ClawsInterface $claws, $type = null) {
        // mit-start:Ekom
        \Module\Ekom\Back\Helper\BackHooksHelper::NullosAdmin_prepareClaws($claws, $type);
        // mit-end:Ekom    
    }

            
    protected static function NullosAdmin_SokoForm_NullosBootstrapRenderer_AutocompleteInitialValue(&$label, $action, $value) {
        // mit-start:Ekom
        \Module\Ekom\Back\Helper\BackHooksHelper::NullosAdmin_SokoForm_NullosBootstrapRenderer_AutocompleteInitialValue($label, $action, $value);
        // mit-end:Ekom    
    }

            
    protected static function NullosAdmin_User_hasRight(&$hasRight, $privilege) {
        // mit-start:Ekom
        \Module\Ekom\Back\Helper\BackHooksHelper::NullosAdmin_User_hasRight($hasRight, $privilege);
        // mit-end:Ekom    
    }

            
    protected static function NullosAdmin_User_populateConnectedUser(array &$user) {
        // mit-start:Ekom
        \Module\Ekom\Back\Helper\BackHooksHelper::NullosAdmin_User_populateConnectedUser($user);
        // mit-end:Ekom    
    }
    protected static function Nullos_Back_getElementAvatar(&$avatar, $table, array $context = []) {
        // mit-start:Ekom
        \Module\Ekom\Back\Helper\BackHooksHelper::Nullos_Back_getElementAvatar($avatar, $table, $context);
        // mit-end:Ekom
    }

        
        
}