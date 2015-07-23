<?php namespace BoundedContext\Event;

use BoundedContext\ValueObject\Uuid;

class AbstractEvent implements Event
{
    protected $_id;
    protected $_occured_at;
    protected $_version = 1;

    public function __construct(Uuid $id, \DateTime $occured_at)
    {
        $this->_id = $id;
        $this->_occured_at = $occured_at;
    }

    public function id()
    {
        return $this->_id;
    }
    
    public function occured_at()
    {
        return $this->_occured_at;
    }
    
    public function version()
    {
        return $this->_version;
    }

    public function toArray()
    {
        $event = [
            'id' => $this->id()->toString()
        ];

        $class_vars = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        foreach ($class_vars as $property) {
            $name = $property->getName();
            $event[$name] = $this->$name;
        }

        return $event;
    }
}
