<?php

use yii\db\Migration;

class m170223_113221_addBlameableBehavior extends Migration
{
    public function up()
    {
        $this->addColumn('{{%files}}', 'createdBy', $this->integer(10)->unsigned()->null()->defaultValue(null));
        $this->addColumn('{{%files}}', 'modifiedBy', $this->integer(10)->unsigned()->null()->defaultValue(null));
    }

    public function down()
    {
    	$this->dropColumn('{{%files}}', 'createdBy');
    	$this->dropColumn('{{%files}}', 'modifiedBy');
    }
}
