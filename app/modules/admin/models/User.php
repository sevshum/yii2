<?php
namespace app\modules\admin\models;

use app\modules\core\components\AppActiveRecord;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends AppActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 5;
    const STATUS_ACTIVE  = 10;

    const ROLE_USER  = 'user';
    const ROLE_ADMIN = 'admin';
    
    protected $_tempPassword;
    
    public static function tableName()
    {
        return 'admins';
    }
    
    public function attributeLabels()
    {
        return [
            'tempPassword' => 'Password'
        ];
    }
    
    /**
     * Creates a new user
     *
     * @param  array       $attributes the attributes given by field => value
     * @return static|null the newly created model, or null on failure
     */
    public static function create($attributes)
    {
        /** @var User $user */
        $user = new static();
        $user->setAttributes($attributes);
        $user->setPassword($attributes['password']);
        $user->generateAuthKey();
        if ($user->save()) {
            return $user;
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function() {
                    return date('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username, $role = self::ROLE_ADMIN)
    {
        return static::findOne(['email' => $username, 'role' => $role]);
    }

    /**
     * Finds user by password reset token
     *
     * @param  string      $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->get('security')->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->get('security')->generatePasswordHash($password);
    }
    
    public function getTempPassword()
    {
        return $this->_tempPassword;
    }
    
    public function setTempPassword($password)
    {
        $this->_tempPassword = $password;
        $this->setPassword($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->get('security')->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->get('security')->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_BLOCKED, self::STATUS_DELETED]],

            ['role', 'default', 'value' => self::ROLE_ADMIN],

            [['username', 'email'], 'filter', 'filter' => 'trim'],
            [['username', 'email'], 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            
            ['email', 'email'],
            ['email', 'unique'],
            
            ['tempPassword', 'required', 'on' => ['insert', 'public']]
        ];
    }
    
    public function scenarios()
    {
        
        return array_merge(parent::scenarios(), [
            'insert' => ['email', 'status', 'role', 'username', 'tempPassword'],
            'update' => ['email', 'status', 'role', 'username', 'tempPassword'],
            'public' => ['tempPassword', 'email', 'username']
        ]);
    }


    public static function statuses($status = null)
    {
        static $statuses = [
            self::STATUS_ACTIVE  => 'Active',
            self::STATUS_BLOCKED => 'Blocked',
            self::STATUS_DELETED => 'Deleted',
        ];
        return $status === null ? $statuses : $statuses[$status];
    }
    
    public static function roles($role = null)
    {
        static $roles = [
            self::ROLE_ADMIN  => 'Admin'
        ];
        return $role === null ? $roles : $roles[$role];
    }
    
}