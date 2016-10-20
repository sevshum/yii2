<?php
namespace app\modules\mail;

use app\modules\core\components\AppModule;
use app\modules\mail\components\Message;
use Yii;


/**
 *  Example usage
 *  Yii::$app->getModule('mail')->createMessage()
 *      ->setTemplate('test_message', ['{fullname}' => 'Test Name'])
 *      ->setTo('test@gmail.com')
 *      ->send();
 */
class Module extends AppModule 
{
    public $controllerNamespace = 'app\modules\mail\controllers'; 
    
    public $mailComponent = 'mailer';
    
    public $createIfNotExists = true;
    
    public $defaultEmailParams = [
        'from'     => 'test@test.com',
        'fromName' => 'Site support',
    ];
    
    /**
     * @return Message
     */
    public function createMessage()
    {
        $message = new Message;
        $message->mailer = Yii::$app->get($this->mailComponent);
        return $message;
    }
    
    /**
     * @param string $token
     * @param array $data
     * @param string $attribute
     * @return string
     */
    public function getTemplateBody($token, $data, $attribute = 'content')
    {
        $template = Template::findByToken($token, $data);
        if ($template === null) {
            return null;
        }
        $body = $attribute === 'content' ? trim(strip_tags($template->$attribute)) : $template->$attribute;
        return strtr($body, $data);
    }
    
}
