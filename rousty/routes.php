<?php 


//--------------------------------------------
// STATIC
//--------------------------------------------
$routes["Ekom_home"] = ["/", null, null, 'Controller\Ekom\Front\HomeController:renderClaws'];


$routes["Ekom_categoryWomen"] = ["/women", null, null, 'Controller\Ekom\Front\CategoryController:render'];
$routes["Ekom_categoryWomen"] = ["/women", null, null, 'Controller\Ekom\Front\CategoryController:render'];
$routes["Ekom_categoryEquipement"] = ["/formation", null, null, 'Controller\Ekom\Front\FormationCategoryController:render'];
$routes["Ekom_categoryEquipement"] = ["/formation", null, null, 'Controller\Ekom\Front\FormationCategoryController:render'];
//$routes["Ekom_productCard"] = ["/women/shirts/{productName}", null, null, 'Controller\Ekom\Front\ProductController:render'];
$routes["Ekom_forgotPassword"] = ["/forgot-password", null, null, 'Controller\Ekom\Front\ForgotPasswordController:render'];
$routes["Ekom_forgotPassword"] = ["/forgot-password", null, null, 'Controller\Ekom\Front\ForgotPasswordController:render'];
$routes["Ekom_forgotPasswordSuccess"] = ["/forgot-password-success", null, null, 'Controller\Ekom\Front\ForgotPasswordController:renderSuccess'];
$routes["Ekom_forgotPasswordSuccess"] = ["/forgot-password-success", null, null, 'Controller\Ekom\Front\ForgotPasswordController:renderSuccess'];
$routes["Ekom_resetPassword"] = ["/reset-password", null, null, 'Controller\Ekom\Front\ResetPasswordController:renderClaws'];
$routes["Ekom_resetPassword"] = ["/reset-password", null, null, 'Controller\Ekom\Front\ResetPasswordController:renderClaws'];


$routes["Ekom_login"] = ["/login", null, null, 'Controller\Ekom\Front\LoginController:renderClaws'];
$routes["Ekom_login"] = ["/login", null, null, 'Controller\Ekom\Front\LoginController:renderClaws'];
$routes["Ekom_createAccount"] = ["/create-account", null, null, 'Controller\Ekom\Front\CreateAccountController:renderClaws'];
$routes["Ekom_createAccount"] = ["/create-account", null, null, 'Controller\Ekom\Front\CreateAccountController:renderClaws'];
$routes["Ekom_createAccountSuccess"] = ["/create-account-success", null, null, 'Controller\Ekom\Front\CreateAccountSuccessController:renderClaws'];
$routes["Ekom_createAccountSuccess"] = ["/create-account-success", null, null, 'Controller\Ekom\Front\CreateAccountSuccessController:renderClaws'];
$routes["Ekom_createAccountValidate"] = ["/create-account-validate", null, null, 'Controller\Ekom\Front\CreateAccountValidateController:renderClaws'];
$routes["Ekom_createAccountValidate"] = ["/create-account-validate", null, null, 'Controller\Ekom\Front\CreateAccountValidateController:renderClaws'];
//$routes["Ekom_createAccount"] = ["/create-account", null, null, 'Controller\Ekom\Front\CreateAccountControllerOnTheFlyForm:render'];


$routes["Ekom_customerDashboard"] = ["/customer/dashboard", null, null, 'Controller\Ekom\Front\Customer\DashboardController:renderClaws'];
$routes["Ekom_customerDashboard"] = ["/customer/dashboard", null, null, 'Controller\Ekom\Front\Customer\DashboardController:renderClaws'];
$routes["Ekom_customerInformation"] = ["/customer/information", null, null, 'Controller\Ekom\Front\Customer\InformationController:render'];
$routes["Ekom_customerInformation"] = ["/customer/information", null, null, 'Controller\Ekom\Front\Customer\InformationController:render'];
$routes["Ekom_customerAddressNew"] = ["/customer/address/new", null, null, 'Controller\Ekom\Front\Customer\AddressNewController:render'];
$routes["Ekom_customerAddressNew"] = ["/customer/address/new", null, null, 'Controller\Ekom\Front\Customer\AddressNewController:render'];
$routes["Ekom_customerAddressBook"] = ["/customer/address/book", null, null, 'Controller\Ekom\Front\Customer\AddressBookController:renderClaws'];
$routes["Ekom_customerAddressBook"] = ["/customer/address/book", null, null, 'Controller\Ekom\Front\Customer\AddressBookController:renderClaws'];
$routes["Ekom_customerCredentials"] = ["/customer/credentials", null, null, 'Controller\Ekom\Front\Customer\CredentialsController:renderClaws'];
$routes["Ekom_customerCredentials"] = ["/customer/credentials", null, null, 'Controller\Ekom\Front\Customer\CredentialsController:renderClaws'];
$routes["Ekom_customerPaymentMethods"] = ["/customer/payment-methods", null, null, 'Controller\Ekom\Front\Customer\PaymentMethodsController:renderClaws'];
$routes["Ekom_customerPaymentMethods"] = ["/customer/payment-methods", null, null, 'Controller\Ekom\Front\Customer\PaymentMethodsController:renderClaws'];
$routes["Ekom_customerAddressEdit"] = ["/customer/address/edit", null, null, 'Controller\Ekom\Front\Customer\AddressEditController:render'];
$routes["Ekom_customerAddressEdit"] = ["/customer/address/edit", null, null, 'Controller\Ekom\Front\Customer\AddressEditController:render'];
$routes["Ekom_customerOrders"] = ["/customer/orders", null, null, 'Controller\Ekom\Front\Customer\OrdersController:renderClaws'];
$routes["Ekom_customerOrders"] = ["/customer/orders", null, null, 'Controller\Ekom\Front\Customer\OrdersController:renderClaws'];
$routes["Ekom_customerBillingAgreements"] = ["/customer/billing-agreements", null, null, 'Controller\Ekom\Front\Customer\BillingAgreementsController:render'];
$routes["Ekom_customerBillingAgreements"] = ["/customer/billing-agreements", null, null, 'Controller\Ekom\Front\Customer\BillingAgreementsController:render'];
$routes["Ekom_customerInvoices"] = ["/customer/invoices", null, null, 'Controller\Ekom\Front\Customer\InvoicesController:renderClaws'];
$routes["Ekom_customerInvoices"] = ["/customer/invoices", null, null, 'Controller\Ekom\Front\Customer\InvoicesController:renderClaws'];
$routes["Ekom_customerRecurringProfiles"] = ["/customer/recurring-profiles", null, null, 'Controller\Ekom\Front\Customer\RecurringProfilesController:render'];
$routes["Ekom_customerRecurringProfiles"] = ["/customer/recurring-profiles", null, null, 'Controller\Ekom\Front\Customer\RecurringProfilesController:render'];
$routes["Ekom_customerProductReviews"] = ["/customer/product-reviews", null, null, 'Controller\Ekom\Front\Customer\ProductReviewsController:render'];
$routes["Ekom_customerProductReviews"] = ["/customer/product-reviews", null, null, 'Controller\Ekom\Front\Customer\ProductReviewsController:render'];
$routes["Ekom_customerTags"] = ["/customer/tags", null, null, 'Controller\Ekom\Front\Customer\TagsController:render'];
$routes["Ekom_customerTags"] = ["/customer/tags", null, null, 'Controller\Ekom\Front\Customer\TagsController:render'];
$routes["Ekom_customerWishList"] = ["/customer/wishlist", null, null, 'Controller\Ekom\Front\Customer\WishListController:renderClaws'];
$routes["Ekom_customerWishList"] = ["/customer/wishlist", null, null, 'Controller\Ekom\Front\Customer\WishListController:renderClaws'];
$routes["Ekom_customerApplications"] = ["/customer/applications", null, null, 'Controller\Ekom\Front\Customer\ApplicationsController:render'];
$routes["Ekom_customerApplications"] = ["/customer/applications", null, null, 'Controller\Ekom\Front\Customer\ApplicationsController:render'];
$routes["Ekom_customerLoyaltyPoints"] = ["/customer/loyalty-points", null, null, 'Controller\ThisApp\Front\Customer\LoyaltyPointsController:renderClaws'];
$routes["Ekom_customerLoyaltyPoints"] = ["/customer/loyalty-points", null, null, 'Controller\ThisApp\Front\Customer\LoyaltyPointsController:renderClaws'];


$routes["Ekom_searchResults"] = ["/customer/search-results", null, null, 'Controller\Ekom\Front\SearchResultsController:renderClaws'];
$routes["Ekom_searchResults"] = ["/customer/search-results", null, null, 'Controller\Ekom\Front\SearchResultsController:renderClaws'];
$routes["Ekom_pdf"] = ["/pdf/{pdfId}", null, null, 'Controller\Ekom\Front\Util\PdfController:render'];
$routes["Ekom_customerProductHistory"] = ["/customer/product-history", null, null, 'Controller\EkomUserTracker\UserProductHistoryController:renderClaws'];


// fish mailer
$routes["FishMailer_redirectWall"] = ["/mail-redirectwall", null, null, 'Controller\FishMailer\RedirectWallController:redirect'];
$routes["Ekom_test"] = ["/test", null, null, 'Controller\ThisApp\TestController:render'];




$routes["Ekom_cart"] = ["/cart", null, null, 'Controller\Ekom\Front\CartController:renderClaws'];
$routes["Ekom_cart"] = ["/cart", null, null, 'Controller\Ekom\Front\CartController:renderClaws'];
//$routes["Ekom_checkoutOnePage"] = ["/checkout-one-page", null, null, 'Controller\Ekom\Front\Checkout\CheckoutOnePageController:render']; // old amazon
$routes["Ekom_checkoutOnePage"] = ["/checkout-one-page", null, null, 'Controller\Ekom\Front\Checkout\CheckoutController:renderClaws'];
$routes["Ekom_checkoutOnePageThankYou"] = ["/checkout-one-page/thankyou", null, null, 'Controller\Ekom\Front\Checkout\CheckoutOnePageThankYouController:renderClaws'];
$routes["Ekom_pageAboutUs"] = ["/about-us", null, null, 'Controller\Ekom\Front\Page\AboutUsController:render'];
$routes["Ekom_ajaxApi"] = ["/service/Ekom/ecp/api", null, null, '']; // Core ajax early route
$routes["Ekom_productCardRef"] = ["/card/{slug}/{ref}", null, null, 'Controller\Ekom\Front\ProductCardController:renderClaws']; // card slug, product ref

//--------------------------------------------
// DYNAMIC
//--------------------------------------------
$routes["Ekom_pdf"] = ["/pdf/{pdfId}", null, null, 'Controller\Ekom\Front\Util\PdfController:render'];

/**
 * @todo-ling: put this behind an auth or other security wall
 */
$routes["Ekom_pdf_private"] = ["/pdf-private/{pdfId}", null, null, 'Controller\Ekom\Front\Util\PdfController:privateRender'];
$routes["Ekom_pdf_private"] = ["/pdf-private/{pdfId}", null, null, 'Controller\Ekom\Front\Util\PdfController:privateRender'];
$routes["Ekom_pdf_download"] = ["/download-pdf/{pdfId}", null, null, 'Controller\Ekom\Front\Util\PdfController:download'];
$routes["Ekom_pdf_download"] = ["/download-pdf/{pdfId}", null, null, 'Controller\Ekom\Front\Util\PdfController:download'];


// ekom estimate
$routes["EkomEstimate_customerEstimateHistory"] = ["/customer/mes-devis", null, null, 'Controller\EkomEstimate\Front\Customer\EstimateController:renderClaws'];
$routes["Ekom_product"] = ["/product/{id}", null, null, 'Controller\Ekom\Front\ProductCardController:renderClawsByProductId'];
$routes["Ekom_productCardRef"] = ["/card/{slug}/{ref}", null, null, 'Controller\Ekom\Front\ProductCardController:renderClaws']; // card slug, product ref
//$routes["Ekom_productCardRef"] = ["/card/{slug}/{ref}", null, null, 'Controller\Ekom\Front\ProductCardControllerCopy:render']; // card slug, product ref
$routes["Ekom_productCard"] = ["/card/{slug+}", null, null, 'Controller\Ekom\Front\ProductCardController:renderClaws'];
$routes["Ekom_category"] = ["/category/{slug+}", null, null, 'Controller\Ekom\Front\CategoryController:renderClaws'];
$routes["Ekom_defaultFront"] = ["/{all+}", null, null, 'Controller\Ekom\Front\HomeController:renderClaws'];
$routes["Ekom_checkoutMultipleAddress"] = ["/checkout-multiple-address/{step+}", null, null, 'Controller\Ekom\Front\Checkout\CheckoutMultipleAddressController:render'];
