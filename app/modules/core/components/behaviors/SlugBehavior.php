<?php
namespace app\modules\core\components\behaviors;

use yii\base\Behavior;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\validators\Validator;


/**
 * Class SlugBehavior
 *
 * @method ActiveRecord owner
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext/slug-behavior
 */
class SlugBehavior extends Behavior
{
	/** @var string|callable */
	public $sourceAttribute = 'title';
	/** @var string */
	public $slugAttribute = 'slug';
	/** @var bool */
	public $lowercase = true;
	/** @var string */
	public $delimiter = '-';
	/** @var int */
	public $length;
	/** @var array */
	public $replacements = [
        "А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
        "Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
        "Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
        "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
        "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
        "Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
        "Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
        "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
        "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
        "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
        "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
        "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
        "ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
    ];
	/** @var callable */
	public $translator;
    
    public $enabled = true;

	public function events()
	{
		return [
			Model::EVENT_BEFORE_VALIDATE => 'addSlugValidators',
		];
	}

	public function addSlugValidators()
	{
        /** @var ActiveRecord $owner */
		$owner = $this->owner;

        if ($this->enabled instanceof Closure) {
            $enabled = call_user_func($this->enabled, $owner);
        } else {
            $enabled = $this->enabled;
        }
        
		if ($enabled) {            
			$this->attachSlug();
		}
	}

	public function attachSlug()
	{
        $owner = $this->owner;
        $title = is_callable($this->sourceAttribute) ? 
            call_user_func($this->sourceAttribute, $owner) :
            $owner->getAttribute($this->sourceAttribute);
		
		if (!empty($title)) {
			$owner->setAttribute($this->slugAttribute, $this->generateSlug($title));
            $owner->validators[] = Validator::createValidator('unique', $owner, $this->slugAttribute);
		}
	}

	public function validateUniqueSlug()
	{
		$owner = $this->owner;
		Validator::createValidator('unique', $owner, $this->slugAttribute)->validateAttribute($owner, $this->slugAttribute);
		if (is_string($this->sourceAttribute)) {
            $owner->addErrors([
                $this->sourceAttribute => $owner->getErrors($this->slugAttribute)
            ]);
        }
	}

	protected function generateSlug($string)
	{
		// Make sure string is in UTF-8 and strip invalid UTF-8 characters
		$string = mb_convert_encoding((string) $string, 'UTF-8', mb_list_encodings());

		// Make custom replacements
		$string = strtr($string, $this->replacements);

		if (is_callable($this->translator)) {
			$string = call_user_func($this->translator, $string);
		}

		// Replace non-alphanumeric characters with our delimiter
		$string = preg_replace('/[^\p{L}\p{Nd}]+/u', $this->delimiter, $string);

		// Remove duplicate delimiters
		$string = preg_replace('/(' . preg_quote($this->delimiter, '/') . '){2,}/', '$1', $string);

		if ((int) $this->length > 0) {
			$string = mb_substr($string, 0, $this->length, 'UTF-8');
		}

		// Remove delimiter from ends
		$string = trim($string, $this->delimiter);

		return $this->lowercase ? mb_strtolower($string, 'UTF-8') : $string;
	}
}
