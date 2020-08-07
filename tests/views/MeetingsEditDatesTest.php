<?php

/**
 * MeetingsEditDatesTest
 * php version 7.2.28
 */

use PHPUnit\Framework\TestCase;

class MeetingsEditDatesTest extends TestCase
{
    /**
     * Test file permission.
     *
     * @return void
     */
    public function testFilePermissionIs644()
    {
        $fileName = ABSPATH . 'views/meetings/edit_dates.php';
        $permission = substr(sprintf('%o', fileperms($fileName)), -4);
        $this->assertEquals('0644', $permission);
    }
}
