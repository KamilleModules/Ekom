<?php

use Module\Ekom\Utils\E;


?>
<div class="my-account">
    <div class="page-title title-buttons">
        <h1>Address Book</h1>
        <button type="button" title="Add New Address" class="button"
                onclick="window.location='<?php echo E::link("Ekom_customerAddressNew"); ?>';"><span><span>Add New Address</span></span>
        </button>
    </div>
    <div class="col2-set addresses-list">
        <div class="col-1 addresses-primary">
            <h2>Default Addresses</h2>
            <ol>
                <li class="item">
                    <h3>Default Billing Address</h3>
                    <address>
                        Pierre Lafitte<br/>
                        Komin&gt;<br/>
                        6 rue port feu hugon<br/>


                        Tours, Indre-et-Loire, 37000<br/>
                        France<br/>
                        T: 0247609841

                    </address>
                    <p><a href="<?php echo E::link("Ekom_customerAddressEdit"); ?>">Change
                            Billing Address</a></p>
                </li>

                <li class="item">
                    <h3>Default Shipping Address</h3>
                    <address>
                        Pierre Lafitte<br/>
                        Komin&gt;<br/>
                        6 rue port feu hugon<br/>


                        Tours, Indre-et-Loire, 37000<br/>
                        France<br/>
                        T: 0247609841

                    </address>
                    <p><a href="<?php echo E::link("Ekom_customerAddressEdit"); ?>">Change
                            Shipping Address</a></p>
                </li>
            </ol>
        </div>
        <div class="col-2 addresses-additional">
            <h2>Additional Address Entries</h2>
            <ol>
                <li class="item empty">
                    <p>You have no additional address entries in your address book.</p>
                </li>
            </ol>
        </div>
    </div>
    <div class="buttons-set">
        <p class="back-link"><a href="http://ultimo.infortis-themes.com/demo/default/customer/address/new/">
                <small>&laquo;</small>
                Back</a></p>
    </div>
    <script type="text/javascript">
        //<![CDATA[
        function deleteAddress(addressId) {
            if (confirm('Are you sure you want to delete this address?')) {
                window.location = 'http://ultimo.infortis-themes.com/demo/default/customer/address/delete/form_key/6VaIgSGcM0c2OBhQ/id/' + addressId;
            }
            return false;
        }
        //]]>
    </script>
</div>