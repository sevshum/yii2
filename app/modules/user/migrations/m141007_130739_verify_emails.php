<?php

use app\modules\user\models\User;
use yii\db\Migration;
use yii\db\Schema;

class m141007_130739_verify_emails extends Migration
{
    public function up()
    {
        $implements = class_implements(User::className());
        if (in_array('app\modules\user\models\components\VerifyEmailInterface', $implements)) {
            $this->addColumn('users', 'verify_email_token', Schema::TYPE_STRING);
            $this->addColumn('users', 'is_email_verified', Schema::TYPE_BOOLEAN);
            $this->createIndex('verify_email_token_idx', 'users', 'verify_email_token');
        }
    }

    public function down()
    {
        
    }
}