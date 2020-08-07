<?php

/**
 * ManageTest
 * php version 7.2.28
 */

use PHPUnit\Framework\TestCase;

class ManageTest extends TestCase
{
    /**
     * Test file permission.
     *
     * @return void
     */
    public function testFilePermissionIs644()
    {
        $fileName = ABSPATH . 'views/manage/index.php';
        $permission = substr(sprintf('%o', fileperms($fileName)), -4);
        $this->assertEquals('0644', $permission);
    }
}
