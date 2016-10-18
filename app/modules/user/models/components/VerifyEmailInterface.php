<?php

namespace app\modules\user\models\components;

/**
 * Description of VerifyEmailInterface
 *
 * @property string $verify_email_token
 * @property string $is_email_verified
 */
interface VerifyEmailInterface
{
    public function populateVerifyEmailToken();
    
    public static function findByVerifyEmailToken($token);
    
    public function verifyEmail();
}
