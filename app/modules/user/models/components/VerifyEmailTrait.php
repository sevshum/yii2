<?php

namespace app\modules\user\models\components;

use Yii;

/**
 * Description of VerifyEmail
 *
 * @property string $verify_email_token
 * @property string $is_email_verified
 */
trait VerifyEmailTrait
{
    public function populateVerifyEmailToken()
    {
        return $this->updateAttributes([
            'verify_email_token' => Yii::$app->get('security')->generateRandomString() . '_' . time(), 
            'is_email_verified' => 0
        ]);
    }
    
    /**
     * @param string $token
     * @return static
     */
    public static function findByVerifyEmailToken($token)
    {
        return static::findOne(['verify_email_token' => $token]);
    }
    
    public function verifyEmail()
    {
        return $this->updateAttributes(['verify_email_token' => null, 'is_email_verified' => 1]);
    }
}
