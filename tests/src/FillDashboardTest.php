<?php

/**
 * FillDashboardTest
 * php version 7.2.28
 */

use PHPUnit\Framework\TestCase;

class FillDashboardTest extends TestCase
{
    /**
     * Test file permission.
     *
     * @return void
     */
    public function testFilePermissionIs644()
    {
        $fileName = dirname(dirname(__DIR__)) . '/src/fill_dashboard.php';
        $permission = substr(sprintf('%o', fileperms($fileName)), -4);
        $this->assertEquals('0644', $permission);
    }
}
