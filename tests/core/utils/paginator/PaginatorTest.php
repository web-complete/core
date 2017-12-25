<?php

namespace tests\core\utils\paginator;

use WebComplete\core\utils\paginator\Paginator;

class PaginatorTest extends \CoreTestCase
{

    public function testPaginator()
    {
        $paginator = new Paginator();
        $this->assertEquals(0, $paginator->getTotal());
        $this->assertEquals(25, $paginator->getItemsPerPage());
        $this->assertEquals(1, $paginator->getCurrentPage());
        $this->assertEquals(0, $paginator->getOffset());
        $this->assertEquals(25, $paginator->getLimit());
        $this->assertEquals(0, $paginator->getPageCount());
        $this->assertEquals([], $paginator->getCurrentPages());

        $paginator->setTotal(100);
        $paginator->setItemsPerPage(20);
        $paginator->setCurrentPage(2);

        $this->assertEquals(100, $paginator->getTotal());
        $this->assertEquals(20, $paginator->getItemsPerPage());
        $this->assertEquals(2, $paginator->getCurrentPage());
        $this->assertEquals(20, $paginator->getOffset());
        $this->assertEquals(20, $paginator->getLimit());
        $this->assertEquals(5, $paginator->getPageCount());
        $this->assertEquals([1,2,3,4,5], $paginator->getCurrentPages());
    }
}
