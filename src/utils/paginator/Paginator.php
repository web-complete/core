<?php

namespace WebComplete\core\utils\paginator;

class Paginator
{
    const DEFAULT_ITEMS_PER_PAGE = 25;

    protected $currentPage = 1;
    protected $itemsPerPage = 0;
    protected $total = 0;

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = (int)$total;
        $this->validate();
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = (int)$currentPage;
    }

    /**
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = (int)$itemsPerPage;
    }


    /**
     * @return int
     */
    public function getPageCount(): int
    {
        if (!$this->getItemsPerPage()) {
            return 0;
        }
        return (int)\ceil($this->getTotal() / $this->getItemsPerPage());
    }

    /**
     * @return array
     */
    public function getCurrentPages(): array
    {
        $result = [];
        $pageCurrent = $this->getCurrentPage();
        if ($pageCount = $this->getPageCount()) {
            if ($pageCount <= 5) {
                for ($i = 1; $i <= $pageCount; $i++) {
                    $result[] = $i;
                }
            } else {
                if ($pageCurrent <= 3) {
                    for ($i = 1; $i <= 5; $i++) {
                        $result[] = $i;
                    }
                } elseif ($pageCount - $pageCurrent <= 2) {
                    for ($i = $pageCount - 4; $i <= $pageCount; $i++) {
                        $result[] = $i;
                    }
                } else {
                    for ($i = $pageCurrent - 2; $i <= $pageCurrent + 2; $i++) {
                        $result[] = $i;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->itemsPerPage * ($this->currentPage - 1);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->itemsPerPage;
    }

    /**
     *
     */
    protected function validate()
    {
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        }
        if ($this->itemsPerPage < 0) {
            $this->itemsPerPage = 0;
        }
        if ($this->total < 0) {
            $this->total = 0;
        }
        if ($this->itemsPerPage * ($this->currentPage - 1) > $this->total) {
            $this->currentPage = 1;
        }
    }
}
