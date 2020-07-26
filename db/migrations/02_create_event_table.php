<?php

use Phinx\Migration\AbstractMigration;

class CreateEventTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('meb_event', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('hash', 'string')
              ->addColumn('name', 'string')
              ->addColumn('description', 'string')
              ->addColumn('fk_event_creator', 'integer')
              ->addForeignKey('fk_event_creator', 'meb_user', 'id')
              ->addColumn('location', 'string')
              ->addColumn('capacity', 'integer')
              ->addColumn('open_slots', 'integer')
              ->addColumn('mod_date', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->addColumn('is_anon', 'boolean', ['default' => 0])
              ->addColumn('enable_upload', 'boolean', ['default' => 0])
              ->addColumn('event_file', 'string', ['null' => true])
              ->create();
    }
}
