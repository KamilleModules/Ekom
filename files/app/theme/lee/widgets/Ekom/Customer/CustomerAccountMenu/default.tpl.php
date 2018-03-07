<?php

use Kamille\Ling\Z;
use Module\Ekom\Utils\E;

$currentRouteId = Z::requestParam("route");

switch ($currentRouteId) {
    case 'Ekom_customerAddressEdit':
    case 'Ekom_customerAddressNew':
        $currentRouteId = 'Ekom_customerAddressBook';
        break;
    default:
        break;
}


$items = [
    "Ekom_customerDashboard" => "Account Dashboard",
    "Ekom_customerInformation" => "Account Information",
    "Ekom_customerAddressBook" => "Address Book",
    "Ekom_customerOrders" => "My Orders",
    "Ekom_customerBillingAgreements" => "Billing Agreements",
    "Ekom_customerRecurringProfiles" => "Recurring Profiles",
    "Ekom_customerProductReviews" => "My Product Reviews",
    "Ekom_customerTags" => "My Tags",
    "Ekom_customerWishList" => "Wishlist",
    "Ekom_customerApplications" => "My Applications",
    "Ekom_customerNewsletterSubscription" => "Newsletter Subscriptions",
    "Ekom_customerDownloadableProducts" => "My Downloadable Products",
];

?>
<div class="block block-account">
    <div class="block-title">
        <strong><span>Account</span></strong>
    </div>
    <div class="block-content">
        <ul>

            <?php
            $nbItems = count($items);
            $cpt = 1;
            foreach ($items as $routeId => $label):
                $sLast = ($cpt === $nbItems) ? ' last' : '';
                ?>
                <?php if ($currentRouteId === $routeId): ?>
                <li class="current<?php echo $sLast; ?>"><strong><?php echo $label; ?></strong></li>
            <?php else: ?>
                <li class="<?php echo $sLast; ?>"><a href="<?php echo E::link($routeId); ?>"><?php echo $label; ?></a>
                </li>
            <?php endif; ?>
                <?php
                $cpt++;
            endforeach ?>
        </ul>
    </div>
</div>