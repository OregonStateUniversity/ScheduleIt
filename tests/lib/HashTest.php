<?php

require_once(ABSPATH . 'lib/hash.php');

/**
 * HashTest
 * php version 7.2.28
 */

use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{
    /**
     * Test hash is processed.
     *
     * @return void
     */
    public function testProcessHash()
    {
        $this->assertEquals(
            processHash("TestHash123"),
            "321hsahtset"
        );
    }

    /**
     * Test hash is filtered.
     *
     * @return void
     */
    public function testFilterHash()
    {
        $this->assertEquals(
            filterHash("TestHash123?!"),
            "TestHash123"
        );
    }

    /**
     * Test hash is correct length.
     *
     * @return void
     */
    public function testCreateEventHash()
    {
        $this->assertEquals(
            strlen(createEventHash("Test Event", "Test Description", 1, "Test Location")),
            16
        );
    }

    /**
     * Test hash is correct length.
     *
     * @return void
     */
    public function testCreateTimeSlotHash()
    {
        $this->assertEquals(
            strlen(createTimeSlotHash("2020-08-01 08:00:00", "2020-08-01 09:00:00", "TestHash")),
            16
        );
    }

    /**
     * Test file permission.
     *
     * @return void
     */
    public function testFilePermissionIs644()
    {
        $fileName = ABSPATH . 'lib/hash.php';
        $permission = substr(sprintf('%o', fileperms($fileName)), -4);
        $this->assertEquals('0644', $permission);
    }
}
