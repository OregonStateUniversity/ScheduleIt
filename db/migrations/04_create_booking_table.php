<?php

use Phinx\Migration\AbstractMigration;

class CreateBookingTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('meb_booking', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('fk_timeslot_id', 'integer')
              ->addForeignKey('fk_timeslot_id', 'meb_timeslot', 'id')
              ->addColumn('fk_user_id', 'integer')
              ->addForeignKey('fk_user_id', 'meb_user', 'id')
              ->addColumn('mod_date', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->create();
    }
}
