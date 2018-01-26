<?php

namespace WebComplete\core\utils\cache;

class HtmlCache
{

    /**
     * @var CacheService
     */
    protected $cacheService;
    /**
     * @var string
     */
    protected $key;
    /**
     * @var int
     */
    protected $ttl;
    /**
     * @var array
     */
    protected $tags;

    /**
     * @param CacheService $cacheService
     * @param string $key
     * @param int $ttl
     * @param array $tags
     */
    public function __construct(CacheService $cacheService, string $key, int $ttl = null, array $tags = [])
    {
        $this->cacheService = $cacheService;
        $this->key = $key;
        $this->ttl = $ttl;
        $this->tags = $tags;
    }

    public function end()
    {
        $content = ob_get_contents();
        ob_end_clean();
        Cache::set($this->key, $content, $this->ttl, $this->tags);
        echo $content;
    }
}
