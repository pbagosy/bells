<?php
/**
 * @author Paul Bagosy <pbagosy@gmail.com>
 */

use PHPUnit\Framework\TestCase;
include_once('clockTower.php');

class clockTowerTest extends TestCase
{
    /**
     * @var clockTower $clockTower
     */
    private $clockTower;

    /**
     * Set up new clockTower instance.
     */
    public function setUp() {
        $this->clockTower = new clockTower();
    }

    /**
     * Test getRings() method with six different assertions.
     */
    public function testCountBells()
    {
        // Two times with single-digit hours, on the hour.
        $this->assertEquals($this->clockTower->countBells('2:00', '3:00'), 5);

        // Two times with two-digit hours, on the hour.
        $this->assertEquals($this->clockTower->countBells('14:00', '15:00'), 5);

        // Two times with non-zero minutes.
        $this->assertEquals($this->clockTower->countBells('14:23', '15:42'), 3);

        // Two times across two days.
        $this->assertEquals($this->clockTower->countBells('23:00', '1:00'), 24);

        // Two times across the merdian divide.
        $this->assertEquals($this->clockTower->countBells('9:12', '13:59'), 34);

        // Two times within the same hour.
        $this->assertEquals($this->clockTower->countBells('15:12', '15:59'), 0);
    }

    /**
     * Test getStartTime() method with eight different assertions.
     */
    public function testGetStartTime()
    {
        // Valid times.
        $bells = $this->clockTower->countBells('2:00', '3:00');
        $this->assertEquals($this->clockTower->getStartTime(), '2:00');
        $this->assertNotFalse($bells);

        $bells = $this->clockTower->countBells('000', '13:00');
        $this->assertEquals($this->clockTower->getStartTime(), '0:00');
        $this->assertNotFalse($bells);

        // Invalid times.
        $bells = $this->clockTower->countBells('10pm', '3:00');
        $this->assertEquals($this->clockTower->getStartTime(), '0:00');
        $this->assertFalse($bells);

        $bells = $this->clockTower->countBells('24:00', '3:00');
        $this->assertEquals($this->clockTower->getStartTime(), '0:00');
        $this->assertFalse($bells);
    }

    /**
     * Test getEndTime() method with eight different assertions.
     */
    public function testGetEndTime()
    {
        // Valid times.
        $bells = $this->clockTower->countBells('2:00', '3:00');
        $this->assertEquals($this->clockTower->getEndTime(), '3:00');
        $this->assertNotFalse($bells);

        $bells = $this->clockTower->countBells('2:00', '000');
        $this->assertEquals($this->clockTower->getEndTime(), '0:00');
        $this->assertNotFalse($bells);

        // Invalid times.
        $bells = $this->clockTower->countBells('2:00', '5a');
        $this->assertEquals($this->clockTower->getEndTime(), '0:00');
        $this->assertFalse($bells);

        $bells = $this->clockTower->countBells('2:00', '24:00');
        $this->assertEquals($this->clockTower->getEndTime(), '0:00');
        $this->assertFalse($bells);
    }

    /**
     * Test getErrors() method with two different assertions.
     */
    public function testGetErrors()
    {
        // No errors by default.
        $this->assertEmpty($this->clockTower->getErrors());

        // Errors due to invalid times.
        $this->clockTower->countBells('2:00', '24:00');
        $this->assertNotEmpty($this->clockTower->getErrors());
    }
}
