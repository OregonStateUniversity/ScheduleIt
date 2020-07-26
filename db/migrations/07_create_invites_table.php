<?php

use Phinx\Migration\AbstractMigration;

class CreateInvitesTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('meb_invites', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('fk_event_id', 'integer')
              ->addForeignKey('fk_event_id', 'meb_event', 'id')
              ->addColumn('user_onid', 'string')
              ->addIndex(['user_onid', 'fk_event_id'], ['unique' => true])
              ->create();
    }
}
