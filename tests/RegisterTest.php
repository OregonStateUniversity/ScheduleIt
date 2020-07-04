<?php

/**
 * RegisterTest
 * php version 7.2.28
 */

use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    /**
     * Test file permission.
     *
     * @return void
     */
    public function testFilePermissionIs644()
    {
        $fileName = dirname(__DIR__) . '/register.php';
        $permission = substr(sprintf('%o', fileperms($fileName)), -4);
        $this->assertEquals('0644', $permission);
    }
}
