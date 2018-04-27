<?php


namespace Module\Ekom\Helper;

use Ecp\EcpServiceUtil;
use Ecp\Output\EcpOutputInterface;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\CouponLayer;
use Module\Ekom\Api\Layer\UserHasCouponLayer;
use Module\Ekom\Utils\E;

class EcpServiceHelper
{


    public static function addCouponHandler(CartLayer $cart, EcpOutputInterface $output, array &$out, string $cartKeyName){
        $code = EcpServiceUtil::get("code");
        $errorMessage = null;
        $warningMessage = null;


        if (!empty($code)) {

            $cartModel = $cart->getCartModel();

            $error = null;
            $couponInfo = null;
            if (true === CouponLayer::couponIsValidByCode($code, $cartModel, $error, $couponInfo)) {

                /**
                 * About coupons.
                 * class-modules/Ekom/doc/cart/coupons.md
                 */
                $couponId = (int)$couponInfo['id'];


                /**
                 * If it's already in the cart, we reject it (for now)
                 */
                $couponIds = $cart->getCouponBag();
                if (in_array($couponId, $couponIds)) {
                    $errorMessage = "Votre panier contient déjà ce coupon";
                } else {


                    $quantityAvailable = $couponInfo['quantity'];
                    if (null === $quantityAvailable || (int)$quantityAvailable > 0) {


                        $quantityPerUser = $couponInfo['quantity_per_user'];
                        if (null !== $quantityPerUser) {
                            /**
                             * Checking quantity per user if necessary
                             */
                            if (true === E::userIsConnected()) {
                                $userId = E::getUserId();
                                $nbUserCoupons = UserHasCouponLayer::getNbCouponsByCouponIdUserId($couponId, $userId);

                                if ($quantityPerUser <= $nbUserCoupons) {
                                    $errorMessage = "Vous ne pouvez plus bénéficier de ce coupon (quantité épuisée)";
                                }
                            } else {
                                $cart->addCouponToCheckUponConnection($couponId);

                                $warningMessage = "Attention, le coupon a bien été ajouté à votre panier, mais sous réserve que vous puissiez bien en bénéficier.<br>
                                Lorsque vous vous connecterez, un message d'alerte apparaîtra si il se trouve que vous ne pouvez pas bénéficier de ce coupon. 
                                "; // this is more a warning message...
                            }
                        }


                        if (null === $errorMessage) {
                            $cart->addCoupon($couponId);
                            $cartModel = $cart->getCartModel(); // acknowledge new coupons
                            $out[$cartKeyName] = $cartModel;

                            if (null !== $warningMessage) {
                                $errorMessage = $warningMessage; // for now, the gui doesn't really differentiate between errors and warning... @todo-ling: differentiate them
                            }
                        }

                    } else {
                        $errorMessage = "Ce coupon n'est plus disponible (quantité épuisée)";
                    }
                }
            } else {
                switch ($error) {
                    case "invalid":
                        $errorMessage = "Ce coupon n'est pas valide";
                        break;
                    case "notFound":
                        $errorMessage = "Ce coupon n'existe pas dans notre base de données";
                        break;
                    case "inactive":
                        $errorMessage = "Ce coupon n'est plus actif";
                        break;
                    case "mismatch:condition_rules":
                        $errorMessage = "Votre panier ne remplit pas les conditions définies par ce coupon";
                        break;
                    default:
                        $errorMessage = null;
                        if (0 === strpos($error, 'mismatch:')) {
                            $p = explode(':', $error, 3);
                            $errorType = $p[1];
                            $errorParams = $p[2] ?? null;
                            switch ($errorType) {
                                case "seller":
                                    $errorMessage = "Votre panier ne contient aucun produit du vendeur $errorParams";
                                    break;
                                case "user":
                                    $errorMessage = "Vous n'êtes pas désigné(e) comme le bénéficiaire de ce coupon";
                                    break;
                                case "date_start":
                                    $errorMessage = "Ce coupon ne sera valide qu'à partir du " . _l()->getLongerDate(strtotime($errorParams)) . " à " . substr($errorParams, 11, 5);
                                    break;
                                case "date_end":
                                    $errorMessage = "Ce coupon est périmé depuis le " . _l()->getLongerDate(strtotime($errorParams)) . " à " . substr($errorParams, 11, 5);
                                    break;
                                case "minimum_amount":
                                    $errorMessage = "Ce coupon ne peut s'appliquer que si le montant de votre panier est supérieur à $errorParams €";
                                    break;
                                case "country_id":
                                    $errorMessage = "Votre adresse de livraison ne correspond pas aux critères du coupon";
                                    break;
                                case "user_group_id":
                                    $errorMessage = "Ce coupon s'applique à un autre groupe de client";
                                    break;
                                case "cumulable":
                                    $errorMessage = "Ce coupon ne peut pas s'appliquer à cause de certains coupons que vous avez déjà appliqués à votre panier. <br>
                                        Essayez de retirer des coupons de votre panier pour voir...";
                                    break;
                                default:
                                    break;
                            }
                        }


                        if (null === $errorMessage) {
                            $errorMessage = "Ce coupon n'est pas valide";
                            $errorMessage = "Vous ne pouvez pas bénéficier de ce coupon: <br>$errorMessage";
                        }
                        break;
                }


            }
        } else {
            $errorMessage = "Le champ coupon ne peut pas être vide";
        }
        if (null !== $errorMessage) {
            $output->error($errorMessage);
        }
    }

}