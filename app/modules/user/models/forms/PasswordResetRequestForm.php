<?php
namespace app\modules\user\models\forms;

use app\modules\user\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
//            ['email', 'exist',
//                'targetClass' => '\app\modules\user\models\User',
//                'filter' => ['status' => User::STATUS_ACTIVE],
//                'message' => Yii::t('app', 'There is no user with such email.')
//            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /** @var User $user */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            $user->generatePasswordResetToken();
            if ($user->save(false)) {
                return \Yii::$app->getModule('mail')
                    ->createMessage()
                    ->setTemplate('forgot_password', [
                        '{email}' => $this->email,
                        '{username}' => $user->username,
                        '{link}' => Url::toRoute(['/user/users/resetpassword', 'token' => $user->password_reset_token], true)
                    ])
                    ->setTo($this->email)
                    ->send();
            }
        }

        return true;
    }
}