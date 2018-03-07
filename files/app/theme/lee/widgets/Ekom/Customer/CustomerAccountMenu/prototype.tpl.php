<?php

use Kamille\Ling\Z;
use Module\Ekom\Utils\E;

$route = Z::requestParam("route");

?>
<div class="block block-account">
    <div class="block-title">
        <strong><span>Account</span></strong>
    </div>
    <div class="block-content">
        <ul>
            <li class="current"><strong>Account Dashboard</strong></li>
<!--            <li><a href="http://ultimo.infortis-themes.com/demo/default/customer/account/edit/">Account Dashboard</a></li>-->
            <li><a href="<?php echo E::link("Ekom_customerInformation"); ?>">Account Information</a></li>
            <li><a href="http://ultimo.infortis-themes.com/demo/default/customer/address/">Address Book</a></li>
            <li><a href="http://ultimo.infortis-themes.com/demo/default/sales/order/history/">My Orders</a></li>
            <li><a href="http://ultimo.infortis-themes.com/demo/default/sales/billing_agreement/">Billing Agreements</a></li>
            <li><a href="http://ultimo.infortis-themes.com/demo/default/sales/recurring_profile/">Recurring Profiles</a></li>
            <li><a href="http://ultimo.infortis-themes.com/demo/default/review/customer/">My Product Reviews</a></li>
            <li><a href="http://ultimo.infortis-themes.com/demo/default/tag/customer/">My Tags</a></li>
            <li><a href="http://ultimo.infortis-themes.com/demo/default/wishlist/">Wishlist</a></li>
            <li><a href="http://ultimo.infortis-themes.com/demo/default/oauth/customer_token/">My Applications</a></li>
            <li><a href="http://ultimo.infortis-themes.com/demo/default/newsletter/manage/">Newsletter Subscriptions</a></li>
            <li class="last"><a href="http://ultimo.infortis-themes.com/demo/default/downloadable/customer/products/">My Downloadable Products</a></li>
        </ul>
    </div>
</div>