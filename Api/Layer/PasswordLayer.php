<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Services\XConfig;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class PasswordLayer
{


    /**
     * Return whether or not the given password corresponds to the given hash
     *
     * @return bool
     */
    public function passwordVerify($password, $hash)
    {
        return (true === password_verify($password, $hash));
    }


    public function passEncrypt($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @return array|false, false if the code is not valid or expired, or already used.
     *      The following array otherwise:
     *      - id: the ek_password_recovery_request.id
     *      - user_id
     */
    public function getCodeValidInfo($code)
    {
        $nbSecondMax = XConfig::get('Ekom.passwordRecoveryNbSeconds');
        $maxTime = time() - $nbSecondMax;
        $maxDateTime = date("Y-m-d H:i:s", $maxTime);

        return QuickPdo::fetch("
select 
r.id, r.user_id, r.date_created, r.code, r.date_used, u.email
from ek_password_recovery_request r
inner join ek_user u on u.id=r.user_id

where code=:code
and date_used is null
and date_created > '$maxDateTime'
        
        ", [
            'code' => $code,
        ]);
    }


    /**
     * Creating a code only works if the user hasn't already a pending token
     * (a token which expiration date is not expired yet).
     *
     * The code is returned, either the pending code, or a new code.
     *
     *
     */
    public function createCodeByUser($userId)
    {
        $userId = (int)$userId;
        if (false !== ($code = $this->getPendingCode($userId))) {
            return $code;
        }

        $code = md5(uniqid(time() . ')k' . $userId));

        EkomApi::inst()->passwordRecoveryRequest()->create([
            "user_id" => $userId,
            "date_created" => date('Y-m-d H:i:s'),
            "code" => $code,
            "date_used" => null,
        ]);
        return $code;
    }


    public function useCode($id)
    {
        EkomApi::inst()->passwordRecoveryRequest()->update([
            "date_used" => date('Y-m-d H:i:s'),
        ], [
            "id" => $id,
        ]);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @param $userId
     * @return false|string, return the pending code if one exists, or false otherwise.
     */
    private function getPendingCode($userId)
    {
        $nbSecondMax = XConfig::get('Ekom.passwordRecoveryNbSeconds');
        $maxTime = time() - $nbSecondMax;
        $maxDateTime = date("Y-m-d H:i:s", $maxTime);

        $row = QuickPdo::fetch("
select code 
from ek_password_recovery_request
where user_id=$userId
and date_used is null
and date_created > '$maxDateTime'
        
        ");

        if (false !== $row) {
            return $row['code'];
        }
        return false;
    }
}