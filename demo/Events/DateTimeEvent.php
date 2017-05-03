<?php
declare(strict_types=1);

namespace Mijndomein\Demo\Events;

final class DateTimeEvent implements \JsonSerializable
{
    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @param \DateTime $dateTime
     */
    private function __construct(\DateTime $dateTime)
    {

        $this->dateTime = $dateTime;
    }

    /**
     * @param \DateTime $dateTime
     * @return DateTimeEvent
     */
    public static function create(\DateTime $dateTime)
    {
        return new self($dateTime);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'date' => $this->dateTime->format('Y-m-d'),
            'time' => $this->dateTime->format('h:m:s'),
            'timeZone' => $this->dateTime->getTimezone()->getName()
        ];
    }
}
