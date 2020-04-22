<?php
/**
 * Created by Bogdan Tereshchenko <development.sites@gmail.com>
 * Copyright: 2006-2019 Bogdan Tereshchenko
 * Link: https://zelliengroup.com/
 * Date: 04.03.2020 20:56
 */

declare(strict_types=1);

namespace Zellien\Message;

use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use Zellien\Message\Exception\InvalidArgumentException;
use Zellien\Message\Exception\RuntimeException;

/**
 * Class AbstractMessage
 * @package Zellien\Message
 */
abstract class AbstractMessage implements MessageInterface {

    /**
     * @param array $data
     * @param bool  $ignoreProperties
     * @return static
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function fromArray(array $data = [], $ignoreProperties = false): self {
        try {
            $reflection = new ReflectionClass(static::class);
            /** @var self $message */
            $message = $reflection->newInstanceWithoutConstructor();
            foreach ($data as $name => $value) {
                $name = self::convertPropertyToCamelCase($name);
                if (!$reflection->hasProperty($name)) {
                    if (false === $ignoreProperties) {
                        $message = sprintf('Property "%s" is not a valid property on message "%s"', $name, $reflection->getShortName());
                        throw new InvalidArgumentException($message);
                    }
                    continue;
                }
                $property = $reflection->getProperty($name);
                if ($property->isPrivate() || $property->isProtected()) {
                    $property->setAccessible(true);
                }
                $property->setValue($message, $value);
            }
            return $message;
        } catch (ReflectionException $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param $name
     * @return mixed
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __get($name) {
        try {
            $reflection = new ReflectionObject($this);
            if (!$reflection->hasProperty($name)) {
                $message = sprintf('Property "%s" is not a valid property on message "%s"', $name, $reflection->getShortName());
                throw new InvalidArgumentException($message);
            }
            $property = $reflection->getProperty($name);
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            return $property->getValue($this);
        } catch (ReflectionException $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @param bool $convertCase
     * @return array
     */
    public function toArray($convertCase = true) {
        $data = [];
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(null);
        foreach ($properties as $property) {
            $name = $property->getName();
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            if ($convertCase) {
                $name = self::convertPropertyToUnderscore($name);
            }
            $data[$name] = $property->getValue($this);
        }
        return $data;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return $this->toArray(true);
    }

    /**
     * @param string $name
     * @return string
     */
    private static function convertPropertyToCamelCase(string $name): string {
        $name = ucwords($name, '_');
        $name = str_replace('_', '', $name);
        $name = lcfirst($name);
        return $name;
    }

    /**
     * @param string $name
     * @return string
     */
    private static function convertPropertyToUnderscore(string $name): string {
        $name = preg_replace('/(?<!^)[A-Z]/', '_$0', $name);
        $name = strtolower($name);
        return $name;
    }

}
