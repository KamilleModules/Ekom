<?php


namespace Module\Ekom\Model\Users;


use QuickPdo\QuickPdo;
use SqlQuery\SqlQuery;

class UserInfoModel
{


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


            $beforeTable = [];
            $afterTable = [];
            $table = [
                [
                    "Titre de civilité",
                    $row['gender'],
                ],
                [
                    "Âge",
                    $age,
                ],
                [
                    "Date d'inscription",
                    $row['date_creation'],
                ],
                [
                    "Dernière visite",
                    $row['date_last_connection'] ?? 'aucune connexion',
                ],
                [
                    "Inscrit à la newsletter",
                    (bool)($row['newsletter']),
                ],
            ];


            $ret['table'] = $table;
            $ret['beforeTable'] = $beforeTable;
            $ret['afterTable'] = $afterTable;


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
}