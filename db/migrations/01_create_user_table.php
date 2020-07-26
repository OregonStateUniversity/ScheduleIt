<?php

use Phinx\Migration\AbstractMigration;

class CreateUserTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('meb_user', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('onid', 'string')
              ->addColumn('email', 'string')
              ->addColumn('first_name', 'string')
              ->addColumn('last_name', 'string')
              ->addIndex(['onid'], ['unique' => true])
              ->addIndex(['email'], ['unique' => true])
              ->create();
    }
}
