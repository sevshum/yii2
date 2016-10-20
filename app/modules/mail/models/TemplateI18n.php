<?php
namespace app\modules\mail\models;

use Yii;
use app\modules\core\components\AppActiveRecord;


/**
 * This is the model class for table "mail_template_i18ns".
 *
 * The followings are the available columns in table 'mail_template_i18ns':
 * @property integer $id
 * @property string $lang_id
 * @property integer $parent_id
 * @property string $subject
 * @property string $content
 * @property string $content_plain
 */
class TemplateI18n extends AppActiveRecord
{
    public $dirtyParams = ['subject', 'content', 'content_plain'];
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'mail_template_i18ns';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [['content', 'subject', 'lang_id'], 'required'],
            ['subject', 'string', 'max' => 128],
            [['content', 'content_plain'], 'string', 'max' => 65534],
            ['content_plain', 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'subject'       => Yii::t('app', 'Subject'),
            'content'       => Yii::t('app', 'Content'),
            'content_plain' => Yii::t('app', 'Plain Content'),
        ];
    }
}