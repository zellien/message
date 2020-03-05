<?php
/**
 * Created by Bogdan Tereshchenko <development.sites@gmail.com>
 * Copyright: 2006-2019 Bogdan Tereshchenko
 * Link: https://zelliengroup.com/
 * Date: 04.03.2020 20:56
 */

declare(strict_types=1);

namespace Zellien\Message;

use ReflectionException;
use ReflectionObject;
use Zellien\Message\Exception\PropertyNotFoundException;

/**
 * Class AbstractMessage
 * @package Zellien\Message
 */
abstract class AbstractMessage implements MessageInterface {

    /**
     * @param $name
     * @return mixed
     * @throws ReflectionException
     */
    final public function __get($name) {
        $reflection = new ReflectionObject($this);
        if (!$reflection->hasProperty($name)) {
            throw new PropertyNotFoundException(sprintf('Property with name %s not found', $name));
        }
        $property = $reflection->getProperty($name);
        if ($property->isPrivate() || $property->isProtected()) {
            $property->setAccessible(true);
        }
        return $property->getValue($this);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties();
        $data = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            $data[$name] = $this->{$name};
        }
        return $data;
    }

}
