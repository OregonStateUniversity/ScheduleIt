<?php

/**
 * InvitesShowTest
 * php version 7.2.28
 */

use PHPUnit\Framework\TestCase;

class InvitesShowTest extends TestCase
{
    /**
     * Test file permission.
     *
     * @return void
     */
    public function testFilePermissionIs644()
    {
        $fileName = ABSPATH . 'views/invites/show.php';
        $permission = substr(sprintf('%o', fileperms($fileName)), -4);
        $this->assertEquals('0644', $permission);
    }
}
