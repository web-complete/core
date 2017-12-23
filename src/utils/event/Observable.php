<?php

namespace WebComplete\core\utils\event;

interface Observable
{

    /**
     * @param string $eventName
     * @param array $eventData
     */
    public function trigger(string $eventName, array $eventData);

    /**
     * @param string $eventName
     * @param callable $callable
     * @param int $priority
     */
    public function on(string $eventName, callable $callable, int $priority = 100);
}
