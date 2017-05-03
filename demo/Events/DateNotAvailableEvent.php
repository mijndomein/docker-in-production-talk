<?php
declare(strict_types=1);

namespace Mijndomein\Demo\Events;

use DateTime;
use JsonSerializable;

final class DateNotAvailableEvent implements JsonSerializable
{
    /**
     * @var string
     */
    private $message;

    /**
     * @param string $message
     */
    private function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param string $message
     * @return self
     */
    public static function createWithMessage(string $message)
    {
        return new self($message);
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
            'message' => $this->message,
        ];
    }
}
