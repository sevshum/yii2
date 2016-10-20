<?php
namespace app\modules\mail\models;

use app\modules\core\components\AppActiveRecord;
use Yii;
/**
 * This is the model class for table "mail_templates".
 *
 * The followings are the available columns in table 'mail_templates':
 * @property integer $id
 * @property string $token
 * @property string $from
 * @property string $from_name
 * @property string $bcc
 * @property string $created_at
 * @property string $updated_at
 */
class Template extends AppActiveRecord
{
    use \app\modules\core\traits\I18nActiveRecordTrait;
    
    public $i18nModel = '\app\modules\mail\models\TemplateI18n';
    
    protected static $_templates = [];

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'mail_templates';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [['from', 'from_name', 'token'], 'required'],
            [['token'], 'unique'],
            [['from', 'bcc'], 'email'],
            [['from', 'bcc'], 'string', 'max' => 255],
            ['from_name', 'string', 'max' => 128],
            ['translateattrs', 'safe'],
        ];
    }
    
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function() {
                    return date('Y-m-d H:i:s');
                }
            ]
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'token'     => Yii::t('app', 'Token'),
            'from'      => Yii::t('app', 'From'),
            'from_name' => Yii::t('app', 'From Name'),
            'bcc'       => Yii::t('app', 'Bcc'),
        ];
    }
    
    /**
     * Find template by token
     * @param string $token
     * @param array $data
     * @param string $lang
     * @return Template 
     */
    public static function findByToken($token, &$data = [], $lang = null)
    {
        self::_normalizeData($data);
        $app = Yii::$app;
        $lang = $lang === null ? $app->language : $lang;
        $cacheToken = $token . '.' . $lang; 
        if (!isset(self::$_templates[$cacheToken])) {
            $template = static::find()->withLang($lang)->where(['token' => $token])->one();
            $module = $app->getModule('mail');
            if ($template === null) {
                if (!$module->createIfNotExists) {
                    return null;
                }
                $params = $module->defaultEmailParams;
                $template = new Template([
                    'token'     => $token,
                    'from'      => $params['from'],
                    'from_name' => $params['fromName'],
                ]);              
                $template->save(false);
                
                // insert translation
                $defaultLanguage = $app->getModule('language')->getDefault();
                $i18n = new TemplateI18n([
                    'parent_id' => $template->id,
                    'lang_id'   => $defaultLanguage,
                    'content'   => implode(' ', array_keys($data)),
                    'subject'   => 'not set'
                ]);
                $i18n->save(false);
                $template->populateRelation('i18ns', [$defaultLanguage => $i18n]);
            }
            self::$_templates[$cacheToken] = $template;
        }
        return self::$_templates[$cacheToken];
    }
    
    private static function _normalizeData(&$data)
    {
        foreach ($data as $key => $value) {
            if (isset($key[0]) && $key[0] !== '{') {
                unset($data[$key]);
                $data['{' . $key . '}'] = $value; 
            }
        }
    }
}