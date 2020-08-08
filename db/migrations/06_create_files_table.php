<?php

use Phinx\Migration\AbstractMigration;

class CreateFilesTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('meb_files', ['collation' => 'utf8mb4_general_ci']);
        $table->addColumn('path', 'string')
              ->addColumn('fk_booking_id', 'integer')
              ->addForeignKey('fk_booking_id', 'meb_booking', 'id', ['delete' => 'CASCADE'])
              ->create();
    }
}
