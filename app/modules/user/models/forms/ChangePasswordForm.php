<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\user\models\forms;

use app\modules\user\models\User;
use Yii;
use yii\base\Model;


class ChangePasswordForm extends Model
{
    public $needActualPassword = false;
    
    public $password;
    public $repeatPassword;
    public $actualPassword;
    
    /**
     * @var User 
     */
    private $_u;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        $rules = array(
            [['password', 'repeatPassword'], 'required'],                        
            [['password', 'repeatPassword'], 'string', 'min' => 5],
            ['password', 'compare', 'compareAttribute' => 'repeatPassword'],
        );
        if ($this->needActualPassword) {
            $rules[] = array('actualPassword', 'required');
            $rules[] = array('actualPassword', 'verify');            
        }
        return $rules;
    }
    
    /**
     * Should be changed if needed
     * @param type $attribute
     * @param type $params 
     */
    public function verify($attribute, $params)
    {
        if (!$this->_u->validatePassword($this->actualPassword)) {
            $this->addError('actualPassword', Yii::t('app', 'Actual password is incorrect'));
        }
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'password'       => Yii::t('app', 'Password'),
            'repeatPassword' => Yii::t('app', 'Password Repeat'),
            'actualPassword' => Yii::t('app', 'Actual Password')
        );
    }
    
    /**
     * Unset model values 
     */
    public function refresh()
    {
        $this->actualPassword = $this->password = $this->repeatPassword = null;
    }
    
    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_u;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->_u = $user;
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function perform()
    {     
        if (!$this->validate()) {
            return false;
        }        
        $this->_u->setPassword($this->password);
        $this->_u->password_reset_token = null;
        
        return $this->_u->save(false);
    }

}
