<?php

namespace tests\core\utils\event;

use WebComplete\core\utils\event\EventService;

class EventServiceTest extends \CoreTestCase
{

    public function testEvents()
    {
        $callData1 = null;
        $callData2_1 = null;
        $callData2_2 = null;
        $eventService = new EventService();
        $eventService->on('event1', function ($eventData) use (&$callData1) {
            $callData1 = $eventData;
        });
        $eventService->on('event2', function ($eventData) use (&$callData2_1) {
            $callData2_1 = $eventData;
        });
        $eventService->on('event2', function ($eventData) use (&$callData2_2) {
            $callData2_2 = $eventData;
        });
        $eventService->trigger('event1', ['some' => 1]);
        $eventService->trigger('event2', ['some' => 2]);
        $eventService->trigger('event3', ['some' => 3]);
        $this->assertEquals(['some' => 1], $callData1);
        $this->assertEquals(['some' => 2], $callData2_1);
        $this->assertEquals(['some' => 2], $callData2_2);
        $this->assertCount(2, $eventService->getListeners());
        $eventService->setListeners([]);
        $this->assertCount(0, $eventService->getListeners());
    }
}
