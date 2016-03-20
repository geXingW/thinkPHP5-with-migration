<?php

use think\phinx\migration\AbstractMigration;

class UserTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        $table = $this->table('user');
        $table->addColumn('username', 'string', ['limit' => '60']);
        $table->addColumn('password', 'string', ['limit' => '60']);
        $table->addColumn('status', 'integer');
        $table->save();
    }

    public function down()
    {
        $table = $this->table('user');
        $table->drop();
    }
}
