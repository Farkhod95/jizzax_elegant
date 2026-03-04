<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m210108_093219_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'permission' => $this->integer()->comment("Роль"),
            'address' => $this->string(255)->comment("address"),
            'username' => $this->string(255)->comment("Логин"),
            'status' => $this->integer()->comment("Статус пользователя"),
            'email' => $this->string(255)->comment("Email"),
            'password' => $this->string(255)->comment("Пароль"),
            'avatar' => $this->string(255)->comment("Аватар"),
            'surname' => $this->string(255)->comment("Фамилия"),
            'name' => $this->string(255)->comment("Имя"),
            'middle_name' => $this->string(255)->comment("Отчество"),
            'phone' => $this->string(255)->comment("Телефон номер"),
            'last_seen' => $this->datetime()->comment("Последное активнос"),
            'access_token' => $this->string(255)->comment("Токен"),
            'registry_date' => $this->datetime()->comment("Дата регистраци"),
            'email_verified' => $this->boolean()->comment(" E-mail Верифицирован"),
            'sms_code' => $this->string(255)->comment("Смс код"),
            'phone_verified' => $this->boolean()->comment("Телефон номер Верифицирован"),
            'referal_id' => $this->integer()->comment("Рефералный пользователь"),

        ]);

        $this->insert('users',array(
            'surname' => 'Boboqulov',
            'name' => 'Bahodir',
            'middle_name' => 'Bobur o\'gli',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '+998998110023',
            'password' => Yii::$app->security->generatePasswordHash('admin'),
            'permission' => 1,
            'status' => 1,
            'access_token' => Yii::$app->getSecurity()->generateRandomString(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
