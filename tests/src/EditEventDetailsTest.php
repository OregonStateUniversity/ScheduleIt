<?php

/**
 * EditEventDetailsTest
 * php version 7.2.28
 */

use PHPUnit\Framework\TestCase;

class EditEventDetailsTest extends TestCase
{
    /**
     * Test file permission.
     *
     * @return void
     */
    public function testFilePermissionIs644()
    {
        $fileName = dirname(dirname(__DIR__)) . '/src/edit_event_details.php';
        $permission = substr(sprintf('%o', fileperms($fileName)), -4);
        $this->assertEquals('0644', $permission);
    }
}
