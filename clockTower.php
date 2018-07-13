<?php
/**
 * @author Paul Bagosy <pbagosy@gmail.com>
 */

/**
 * Class clockTower
 */
class clockTower
{
    protected $startTime;
    protected $endTime;
    protected $startHours;
    protected $startMinutes;
    protected $endHours;
    protected $endMinutes;
    protected $ringHours = [];
    protected $rings = 0;
    protected $errorList = [];

    /**
     * clockTower constructor.
     */
    public function __construct() {}

    /**
     * Return the current ring count.
     *
     * @param string $startTime The time to start calculation from.
     * @param string $endTime The time to calculate until.
     * @return int|bool
     */
    public function countBells($startTime, $endTime)
    {
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);

        if (empty($this->errorList)) {
            $this->generateHours();
            $this->calculateRings();
            return $this->rings;
        }

        return false;
    }

    /**
     * Return the formatted start time.
     *
     * @return string
     */
    public function getStartTime()
    {
        return $this->startHours . ':' . $this->startMinutes;
    }

    /**
     * Return the formatted end time.
     *
     * @return string
     */
    public function getEndTime()
    {
        return $this->endHours . ':' . $this->endMinutes;
    }

    /**
     * Return the error list.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errorList;
    }

    /**
     * Calculate the range of hours that the bell will be ringing on.
     */
    private function generateHours()
    {
        $startTime = ceil($this->startTime / 100);
        $endTime = floor($this->endTime / 100);

        $this->ringHours = [];
        if ($this->endTime <= $this->startTime) {
            // If the start time is equal to or past the end time,
            // we're split across two days.

            // Get the hours for the first day.
            for ($x = $startTime; $x <= 23; $x++) {
                $this->ringHours[] = (int)$x;
            }
            // Get the hours for the second day.
            for ($x = 0; $x <= $endTime; $x++) {
                $this->ringHours[] = (int)$x;
            }
        } else {
            // Range falls in a single day - get all hours.

            if (100 > ($this->endTime - $this->startTime)) {
                if ($this->startHours != $this->endHours) {
                    // If the start and end times are less than an hour apart,
                    // and on different hours, then we only have one ring hour.
                    // If they are on the same hour, there are no rings.
                    $this->ringHours[] = (int)$endTime;
                }
            } else {
                // Otherwise, calculate as normal.
                for ($x = $startTime; $x <= $endTime; $x++) {
                    $this->ringHours[] = (int)$x;
                }
            }
        }
    }

    /**
     * Calculate the number of rings considering the hours
     * that the bell will be ringing on.
     */
    private function calculateRings()
    {
        $this->rings = 0;
        foreach ($this->ringHours as $ringHour) {
            if (12 < $ringHour) {
                // Account for 24-hour shift.
                $ringCount = $ringHour - 12;
            } else if (0 == $ringHour) {
                // Account for 0 being 12 midnight.
                $ringCount = 12;
            } else {
                $ringCount = $ringHour;
            }

            $this->rings = $this->rings + $ringCount;
        }
    }

    /**
     * Normalize the provided time into a usable integer.
     *
     * @param string $time The time string to normalize.
     * @return array A string of the hour, a string of the minutes,
     *               and an integer representing the time.
     * @throws Exception
     */
    private function normalizeTime($time)
    {
        // We're only concerned with digits, but we still
        // need to evaluate as a string.
        $time = (string)preg_replace('/\D/', '', $time);

        $hours = substr($time, 0, -2);
        $minutes = substr($time, -2, 2);

        // Check to make sure we have a valid two-digit minutes number.
        if (!$minutes || 2 != strlen($minutes) || 59 < $minutes) {
            throw new Exception('Invalid time.');
        }

        // Check to make sure we have a valid hours number that can be zero.
        if (('0' != $hours && !$hours) || 23 < $hours) {
            throw new Exception('Invalid time.');
        }

        return [$hours, $minutes, (int)$time];
    }

    /**
     * Set the start time variable.
     *
     * @param string $startTime The start time value to set
     */
    private function setStartTime($startTime)
    {
        try {
            list(
                $this->startHours, $this->startMinutes, $this->startTime
            ) = $this->normalizeTime($startTime);
            unset($this->errorList['Start time']);
        } catch (Exception $e) {
            $this->errorList['Start time'] = $e->getMessage();
        }
    }

    /**
     * Set the end time variable.
     *
     * @param string $endTime The end time value to set
     */
    private function setEndTime($endTime)
    {
        try {
            list(
                $this->endHours, $this->endMinutes, $this->endTime
            ) = $this->normalizeTime($endTime);
            unset($this->errorList['End time']);
        } catch (Exception $e) {
            $this->errorList['End time'] = $e->getMessage();
        }
    }
}
