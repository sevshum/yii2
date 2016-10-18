<?php
namespace app\modules\user\models;

use app\modules\core\components\AppActiveRecord;
use app\modules\user\models\components\VerifyEmailInterface;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
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
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * 
 * @property Profile $profile
 * @property Provider[] $providers
 */
class User extends AppActiveRecord implements IdentityInterface, VerifyEmailInterface
{
    use components\VerifyEmailTrait;
    
    const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 5;
    const STATUS_ACTIVE  = 10;
    
    public $photo;
    
    protected $_tempPassword;
    
    public static function tableName()
    {
        return 'users';
    }
    
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'status' => Yii::t('app', 'Status'),
            'tempPassword' => Yii::t('app', 'Password'),
            'email' => Yii::t('app', 'Email'),
        ];
    }
    
    /**
     * Creates a new user
     *
     * @param  array       $attributes the attributes given by field => value
     * @return static|null the newly created model, or null on failure
     */
    public static function create($attributes, $scenario = null, $validate = true)
    {
        /** @var User $user */
        $user = new static();
        $user->setAttributes($attributes);
        if (isset($attributes['password'])) {
            $user->setPassword($attributes['password']);
        }
        if (isset($attributes['role'])) {
            $user->setRole($attributes['role']);
        }
        if ($scenario !== null) {
            $user->scenario = $scenario;
        }
        if ($user->save($validate)) {
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
    public function rules()
    {
        $rules = [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_BLOCKED, self::STATUS_DELETED]],

            [['username', 'email'], 'filter', 'filter' => 'trim'],
            [['username', 'email'], 'required', 'except' => ['social']],
            ['username', 'string', 'min' => 2, 'max' => 255],
            
            ['email', 'email', 'when' => function($model) { return $model->email !== ''; }],
            ['email', 'unique', 'when' => function($model) { return $model->email !== ''; }],
            ['profileattrs', 'safe'],
            ['tempPassword', 'required', 'on' => ['insert', 'public']]
        ];
        if ($this instanceof VerifyEmailInterface) {
            $rules[] = ['is_email_verified', 'safe'];
        }
        return $rules;
    }
    
    public function scenarios()
    {
        
        return array_merge(parent::scenarios(), [
            'insert' => ['email', 'status', 'is_email_verified', 'username', 'tempPassword', 'profileattrs'],
            'update' => ['email', 'status', 'is_email_verified', 'username', 'tempPassword', 'profileattrs'],
            'public' => ['tempPassword', 'email', 'username', 'profileattrs'],
            'public_update' => ['tempPassword', 'email', 'username', 'profileattrs'],
            'social' => ['email', 'username'],
        ]);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }
    
    public function setProfileattrs($profile)
    {
        $this->setNestedRelation('profile', $profile);
    }
    
    public function getProfileattrs()
    {
        return [];
    }

    /**
     * @return ActiveQuery
     */
    public function getProviders()
    {
        return $this->hasMany(Provider::className(), ['user_id' => 'id']);
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
     * @param  string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username]);
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
        if (trim($this->password_hash) === '') {
            return false;
        }
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
        if ($password !== '') {
            $this->_tempPassword = $password;
            $this->setPassword($password);
        }
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

    public static function statuses($status = null)
    {
        $statuses = [
            self::STATUS_ACTIVE => Yii::t('app', 'Active'),
            self::STATUS_BLOCKED => Yii::t('app', 'Blocked'),
//            self::STATUS_DELETED => Yii::t('app', 'Deleted'),
        ];
        return $status === null ? $statuses : (isset($statuses[$status]) ? $statuses[$status] : $status);
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $profile = new Profile;
            $this->link('profile', $profile);
        }
    }
    
    public function afterDelete()
    {
        Profile::deleteAll(['user_id' => $this->id]);
        Provider::deleteAll(['user_id' => $this->id]);
        parent::afterDelete();
    }
    
    public static function listing()
    {
        $q = static::find()->asArray();
        return $q->all();
    }
    
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['password_hash'], $fields['password_reset_token'], $fields['auth_key']);
        return $fields;
    }
    
}