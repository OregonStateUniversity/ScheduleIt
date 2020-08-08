<?php

use Phinx\Migration\AbstractMigration;

class CreateTimeslotTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('meb_timeslot', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('hash', 'string')
              ->addColumn('start_time', 'datetime')
              ->addColumn('end_time', 'datetime')
              ->addColumn('duration', 'integer')
              ->addColumn('slot_capacity', 'integer')
              ->addColumn('spaces_available', 'integer')
              ->addColumn('is_full', 'boolean', ['default' => 0])
              ->addColumn('fk_event_id', 'integer')
              ->addForeignKey('fk_event_id', 'meb_event', 'id', ['delete' => 'CASCADE'])
              ->addColumn('mod_date', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->create();
    }
}
