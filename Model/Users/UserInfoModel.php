<?php


namespace Module\Ekom\Model\Users;


use Core\Services\A;
use Core\Services\Hooks;
use Models\InfoTable\InfoTableHelper;
use Module\Ekom\Api\Layer\ProductCommentLayer;
use Module\Ekom\Api\Layer\WishListLayer;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\OrderStats\OrderStatsUtil;
use Module\EkomUserTracker\Api\Layer\UserTrackerLayer;
use Module\NullosAdmin\Morphic\Helper\NullosMorphicHelper;
use QuickPdo\QuickPdo;
use SqlQuery\SqlQuery;

class UserInfoModel
{


    public static function getLastCommentsByUserId(int $userId)
    {
        $comments = ProductCommentLayer::getCommentsByUserId($userId, true, 5);
        $rows = [];

        foreach ($comments as $row) {
            $rows[] = [
                'id' => $row['comment_id'],
                'product_type_id' => $row['product_type_id'],
                'product_id' => $row['product_id'],
                'card_id' => $row['card_id'],
                'date' => $row['comment_date'],
                'ref' => $row['ref'],
                'label' => $row['label'],
                'photo' => $row['imageThumb'],
                'comment' => $row['comment_comment'],
                'rating' => $row['comment_rating'], // rating up to 100
                'active' => $row['comment_active'],
                'action' => '',
            ];
        }

        //--------------------------------------------
        // LAST BOOKMARKS
        //--------------------------------------------
        $productLinkFmt = A::link("Ekom_Catalog_Product_Form") . "?form=1&t=products&t2=product&product_type=%s&id=%s&product_id=%s";
        $commentLinkFmt = A::link("Ekom_Catalog_ProductCommentList") . "?form&id=%s";
        $infoTable = [
            'headers' => [
                "Date",
                "Libellé",
                "Photo",
                "Commentaire",
                "Note",
                "Présent sur le site",
                "", // actions
            ],
            'rows' => $rows,
            'hidden' => [
                'id',
                'ref',
                'product_id',
                'product_type_id',
                'card_id',
            ],
            'colTransformers' => [
                'active' => NullosMorphicHelper::getStandardColTransformer("active"),
                'rating' => NullosMorphicHelper::getStandardColTransformer("rating"),
                'photo' => NullosMorphicHelper::getStandardColTransformer("image", ['title' => function ($row) {
                    return $row['ref'];
                }]),
                'action' => NullosMorphicHelper::getStandardColTransformer("dropdown", [
                    'callback' => function ($value, array $row) use ($productLinkFmt, $commentLinkFmt) {

                        $isActive = (bool)$row['active'];
                        $word = (true === $isActive) ? "invisible" : "visible";

                        return [
                            "label" => "Actions",
                            "openingSide" => "left", // left|right
                            "items" => [
                                [
                                    "label" => "Rendre $word sur le site",
                                    "link" => "#",
                                    "class" => "bionic-btn",
                                    "attributes" => [
                                        "data-action" => "ecp:Ekom:back.updateProductCommentActive",
                                        "data-param-id" => $row['id'],
                                        "data-param-is_active" => (int)!$isActive,
                                        "data-directive-reload" => 1,
                                    ],
                                ],
                                [
                                    "label" => "Modifier le produit",
                                    "link" => sprintf($productLinkFmt, $row['product_type_id'], $row['product_id'], $row['card_id']),
                                ],
                                [
                                    "label" => "Modifier le commentaire",
                                    "link" => sprintf($commentLinkFmt, $row['id']),
                                ],
                            ],
                        ];
                    }
                ]),
            ],
        ];
        return $infoTable;
    }


    public static function getLastBookmarksByUserId(int $userId)
    {
        $fullRows = WishListLayer::getWishListItemsByUserId($userId, 5);


        $rows = [];
        foreach ($fullRows as $row) {
            $rows[] = [
                'ref' => $row['ref'],
                'label' => $row['label'],
                'photo' => $row['imageThumb'],
                'sale_price' => $row['priceSale'],
                'date' => $row['wishlist_date'],
                'action' => '',
                // hidden
                'product_type_id' => $row['product_type_id'],
                'card_id' => $row['card_id'],
                'product_id' => $row['product_id'],
            ];
        }

        //--------------------------------------------
        // LAST BOOKMARKS
        //--------------------------------------------
        $linkFmt = E::link("Ekom_Catalog_Product_Form") . "?form=1&t=products&t2=product&product_type=%s&id=%s&product_id=%s";
        $infoTable = [
            'headers' => [
                "Réf",
                "Libellé",
                "Photo",
                "Prix de vente",
                "Date ajout favori",
                "", // actions
            ],
            'rows' => $rows,
            'hidden' => [
                "product_type_id",
                "card_id",
                "product_id",
            ],
            'colTransformers' => [
                'photo' => NullosMorphicHelper::getStandardColTransformer("image"),
                'action' => function ($value, $row) use ($linkFmt) {
                    $link = sprintf($linkFmt, $row['product_type_id'], $row['card_id'], $row['product_id']);
                    return <<<EEE
<a href="$link" class="btn btn-default btn-xs">Voir le produit</a>
EEE;

                },
            ],
        ];
        return $infoTable;
    }


    public static function getWishStatsByUserId(int $userId)
    {

        $nbFavoritesCurrent = WishListLayer::getNbUserWishItems($userId);
        $nbFavorisDeleted = WishListLayer::getNbUserWishItems($userId, 'deleted');
        $nbFavoritesTotal = $nbFavorisDeleted + $nbFavoritesCurrent;
        $oldestFavoriteDate = "N/A";
        $oldestFavorite = WishListLayer::getFirstFavoriteAddedDateByUserId($userId);
        if (false !== $oldestFavorite) {
            $oldestFavoriteDate = $oldestFavorite;
        }

        $ret = [
            'nb_total_bookmarks' => $nbFavoritesTotal,
            'nb_current_bookmarks' => $nbFavoritesCurrent,
            'nb_deleted_bookmarks' => $nbFavorisDeleted,
            'first_bookmark_date' => $oldestFavoriteDate,
        ];

        $translations = [
            "Nombre de favoris total",
            "Nombre de favoris en cours",
            "Nombre de favoris dans la poubelle",
            "Date du premier favori ajouté",
        ];

        return array_combine($translations, $ret);
    }


    public static function getOrderStatsByUserId(int $id)
    {
        $orderStatsReport = OrderStatsUtil::getUserReport($id, null, null, function ($date) {
            return E::localys()->getLongDate(strtotime($date)) . " " . substr($date, 10);
        });
//        az($orderStatsReport);
        return $orderStatsReport;
    }


    public static function getLastOrdersByUserId(int $id, int $nbMaxOrders = null)
    {

        $ret = [];
        if (null === $nbMaxOrders) {
            $nbMaxOrders = 5;
        }

        $q = "
select 
o.id,
o.`date`,
if(
    '' != o.payment_method_extra,
    concat(
      @pay := (select label from ek_payment_method where name=o.payment_method),
      ' ',
      o.payment_method_extra
    ),
    @pay
),

(
  select label 
  from ek_order_status s 
  inner join ek_order_has_order_status h on h.order_status_id=s.id
  where h.order_id=o.id
  order by h.date desc 
  limit 0,1 
   
) as last_status,
o.cart_quantity as nb_products,
o.amount,
'' as action 


from ek_order o
where o.user_id=$id
order by o.date desc 
limit 0,$nbMaxOrders   
        ";

//        az($q);
        $rows = QuickPdo::fetchAll($q);


        //--------------------------------------------
        // LAST ORDERS
        //--------------------------------------------
        $linkFmt = E::link("Ekom_Orders_Order_Info") . "?form&id=%s";
        $infoTable = [
            'headers' => [
                "Id",
                "Date",
                "Paiement",
                "État",
                "Produits",
                "Total payé",
                "", // actions
            ],
            'rows' => $rows,
            'colTransformers' => [
                'amount' => NullosMorphicHelper::getStandardColTransformer("Ekom.price"),
                'action' => function ($value, $row) use ($linkFmt) {
                    $link = sprintf($linkFmt, $row['id']);
                    return <<<EEE
<a href="$link" class="btn btn-default btn-xs">Voir la commande</a>
EEE;

                },
            ],
        ];
        return $infoTable;
    }


    public static function getModelByUserId(int $id)
    {
        $ret = [];

        $userQuery = SqlQuery::create()
            ->addField("
u.*,
gr.id as group_id,
gr.label as group_label,
g.label as gender

                
                ")
            ->setTable("ek_user u")
            ->addJoin("
inner join ek_gender g on g.id=u.gender_id                
inner join ek_user_group gr on gr.id=u.user_group_id                
                ")
            ->addWhere("and u.id=$id");

        Hooks::call("Ekom_UserInfoModel_decorateSqlQuery", $userQuery);


        $markers = $userQuery->getMarkers();

        $row = QuickPdo::fetch($userQuery, $markers);
        if ($row) {


            // title
            $userAvatar = $row['first_name'];
            if ($userAvatar) {
                $userAvatar = ucfirst(substr($userAvatar, 0, 1)) . ". ";
            }
            $userAvatar .= strtoupper($row['last_name']);
            $ret['title'] = "Informations sur le client " . $userAvatar;


            // full name
            $fullName = $row['first_name'];
            if ($fullName) {
                $fullName .= " ";
            }
            $fullName .= $row['last_name'];

            $row['fullName'] = strtoupper($fullName);
            $ret['info'] = $row;


            //--------------------------------------------
            // TABLE
            //--------------------------------------------
            /**
             * Note: format of table is so that modules can do whatever they want, including
             * unsetting keys, or splicing into the main table...
             *
             * The fourth optional argument of an item is a data type, so that templates can transform
             * the data if necessary.
             * The available types are the following:
             *
             * - phone: <phonePrefix> <:> <phone>
             *
             *
             *
             */
            $age = null;
            if ($row['birthday']) {
                $date = new \DateTime($row['birthday']);
                $now = new \DateTime();
                $interval = $now->diff($date);
                $age = $interval->y;
            } else {
                $age = "non renseigné";
            }


            $dateCreation = $row['date_creation'];
            $dateCreation = E::localys()->getLongDate(strtotime($dateCreation)) . " " . substr($dateCreation, 10);

            $dateLastConnection = $row['date_last_connection'];
            if ($dateLastConnection) {
                $dateLastConnection = E::localys()->getLongDate(strtotime($dateLastConnection)) . " " . substr($dateLastConnection, 10);
            } else {
                $dateLastConnection = 'aucune connexion enregistrée';
            }


            $beforeTable = [];
            $afterTable = [];
            $table = [
                [
                    'gender',
                    "Titre de civilité",
                    $row['gender'],
                ],
                [
                    'age',
                    "Âge",
                    $age,
                ],
                [
                    'phone',
                    "Téléphone",
                    self::phone($row),
                    'phone',
                ],
                [
                    'group_label',
                    "Groupe",
                    $row['group_label'],
                ],
                [
                    'date_creation',
                    "Date d'inscription",
                    $dateCreation,
                ],
                [
                    'date_last_connection',
                    "Dernière visite",
                    $dateLastConnection,
                ],
            ];


            $ret['table'] = $table;
            $ret['beforeTable'] = $beforeTable;
            $ret['afterTable'] = $afterTable;
            $ret['row'] = $row;


            Hooks::call("Ekom_UserInfoModel_decorate", $ret);


        } else {
            throw new \Exception("User not found with id $id");
        }


        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected static function formatAddress(array $address)
    {

        if (
            false === array_key_exists("libelle", $address) &&
            array_key_exists('fName', $address)
        ) {
            $address['libelle'] = $address['fName'];
        }


        $phoneFormatted = $address['phone'];
        if ($phoneFormatted && $address['phone_prefix']) {
            $phoneFormatted = "(+$address[phone_prefix]) " . $phoneFormatted;
        }
        $address['phone_formatted'] = $phoneFormatted;
        return $address;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function phone(array $item)
    {
        return "+" . $item['phone_prefix'] . ": " . $item['phone'];
    }
}