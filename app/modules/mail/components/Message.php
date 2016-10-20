<?php

namespace app\modules\mail\components;

use app\modules\mail\models\Template;


class Message extends \yii\swiftmailer\Message
{
    protected $htmlBody;
    
    /**
     * Set additional params for mail
     * @param string $token
     * @param array $data
     * @param string $lang
     * @return mixed 
     */
    public function setTemplate($token, $data = []) 
    {
        $template = Template::findByToken($token, $data);
        $message = $this->getSwiftMessage();
        if ($template->bcc) {
            $message->setBcc($template->bcc);            
        }
        $message->setSubject($subject = strtr($template->getI18n('subject'), $data));
        
        $data = array_merge($data, [
            '{subject}' => $subject,
            '{current_year}' => date('Y')
        ]);
        
        $message->setFrom([$template->from => $template->from_name]);
        $this->setTextBody(strtr($template->getI18n('content_plain'), $data));
        $this->setHtmlBody($this->htmlBody = strtr(
            s('app.email_template', '{content}'), array_merge($data, [
                '{content}' => strtr($template->getI18n('content'), $data),
            ])
        ));
        return $this;
    }
    
    public function getHtmlBody()
    {
        return $this->htmlBody;
    }
    
}