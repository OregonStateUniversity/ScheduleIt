<?php

/**
 * UploadTest
 * php version 7.2.28
 */

use PHPUnit\Framework\TestCase;

class UploadTest extends TestCase
{
    /**
     * Test file permission.
     *
     * @return void
     */
    public function testFilePermissionIs644()
    {
        $fileName = dirname(dirname(__DIR__)) . '/src/upload.php';
        $permission = substr(sprintf('%o', fileperms($fileName)), -4);
        $this->assertEquals('0644', $permission);
    }
}
