<?php

use Phinx\Migration\AbstractMigration;

class AddModDateToInvites extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('meb_invites');
        $table->addColumn('mod_date', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->update();
    }
}

