<?php

use yii\db\Migration;

/**
 * Handles the creation for table `ImageManager`.
 */
class m160622_085710_create_ImageManager_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
		//ImageManager: create table
        $this->createTable('{{%ImageManager}}', [
            'id' => $this->primaryKey(),
			'title' => $this->string(250)->notNull(),
			'description' => $this->string(250)->notNull(),
			'fileMime' => $this->string(100)->notNull(),
			'path' => $this->string(100)->notNull(),
			'url_path' => $this->string(200)->notNull(),
			'url_path_cache' => $this->string(250)->notNull(),
			'type' => $this->string(50)->notNull(),
			'folder_name' => $this->string(100)->notNull(),
			'title_upload' => $this->string(250)->notNull(),
			'size_file' => $this->string(200)->notNull(),
			'user_id' => $this->integer(11)->notNull()->default(1),
			'enabled' => $this->tinyint(1)->notNull()->default(1),
			'created' => $this->datetime()->notNull(),
			'modified' => $this->datetime(),
        ]);
        
		//ImageManager: alter id column
		$this->alterColumn('{{%ImageManager}}', 'id', 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%ImageManager}}');
    }
}
