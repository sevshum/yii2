<?php

use app\modules\admin\models\User;
use yii\db\Migration;
use yii\db\Schema;

class m140630_181441_admin_init extends Migration
{
    public function up()
    {    
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        
        $this->createTable('admins', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING . ' NOT NULL',
            'role' => Schema::TYPE_STRING . '(16) NULL DEFAULT "admin"',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ], $tableOptions);
        
        User::create([
            'username' => 'Main Admin',
            'password' => '123123',
            'role'     => User::ROLE_ADMIN,
            'email'    => 'admin@admin.com',
            'status'   => User::STATUS_ACTIVE,
        ]);
    }

    public function down()
    {
        $this->dropTable('admins');
    }
}