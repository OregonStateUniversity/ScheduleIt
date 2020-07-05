<?php

/**
 * ReserveSlotTest
 * php version 7.2.28
 */

use PHPUnit\Framework\TestCase;

class ReserveSlotTest extends TestCase
{
    /**
     * Test file permission.
     *
     * @return void
     */
    public function testFilePermissionIs644()
    {
        $fileName = dirname(dirname(__DIR__)) . '/src/reserve_slot.php';
        $permission = substr(sprintf('%o', fileperms($fileName)), -4);
        $this->assertEquals('0644', $permission);
    }
}
