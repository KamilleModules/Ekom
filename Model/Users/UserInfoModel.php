<?php


namespace Module\Ekom\Model\Users;


use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\OrderStats\OrderStatsUtil;
use Module\NullosAdmin\Morphic\Helper\NullosMorphicHelper;
use QuickPdo\QuickPdo;
use SqlQuery\SqlQuery;

class UserInfoModel
{

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
o.amount


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