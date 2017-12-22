<?php

namespace WebComplete\core\utils\event;

class EventService
{

    protected $listeners = [];

    /**
     * @param string $eventName
     * @param array $eventData
     */
    public function trigger(string $eventName, array $eventData)
    {
        $listeners = $this->listeners[$eventName] ?? [];
        foreach ($listeners as $listener) {
            \call_user_func($listener[1], $eventData);
        }
    }

    /**
     * @param string $eventName
     * @param callable $callable
     * @param int $priority
     */
    public function on(string $eventName, callable $callable, int $priority = 100)
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        $this->listeners[$eventName][] = [$priority, $callable];
        \uasort($this->listeners[$eventName], function ($event1, $event2) {
            return $event1[0] <=> $event2[0];
        });
    }

    /**
     * @return array
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    /**
     * @param array $listeners
     */
    public function setListeners(array $listeners)
    {
        $this->listeners = $listeners;
    }
}
