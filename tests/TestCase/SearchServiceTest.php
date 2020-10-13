<?php

namespace MeiliSearch\Bundle\Test\TestCase;

use Doctrine\ORM\EntityManager;
use Exception;
use MeiliSearch\Bundle\Engine;
use MeiliSearch\Bundle\SearchService;
use MeiliSearch\Bundle\Test\BaseTest;
use MeiliSearch\Bundle\Test\Entity\Comment;
use MeiliSearch\Bundle\Test\Entity\ContentAggregator;
use MeiliSearch\Bundle\Test\Entity\Image;
use MeiliSearch\Bundle\Test\Entity\Link;
use MeiliSearch\Bundle\Test\Entity\Post;
use MeiliSearch\Bundle\Test\Entity\Tag;
use MeiliSearch\Exceptions\HTTPRequestException;

/**
 * Class SearchServiceTest
 *
 * @package MeiliSearch\Bundle\Test\TestCase
 */
class SearchServiceTest extends BaseTest
{

    /** @var SearchService $searchService */
    protected $searchService;

    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var Engine $engine */
    protected $engine;

    public function setUp(): void
    {
        parent::setUp();
        $this->searchService = $this->get('search.service');
        $this->entityManager = $this->get('doctrine')->getManager();
        $this->engine        = new Engine($this->get('search.client'));
    }

    public function cleanUp()
    {
        try {
            $this->searchService->delete(Post::class);
            $this->searchService->delete(Comment::class);
            $this->searchService->delete(ContentAggregator::class);
        } catch (HTTPRequestException $e) {
        }
    }

    public function testIsSearchableMethod()
    {
        $this->assertTrue($this->searchService->isSearchable(Post::class));
        $this->assertTrue($this->searchService->isSearchable(Comment::class));
        $this->assertFalse($this->searchService->isSearchable(BaseTest::class));
        $this->assertFalse($this->searchService->isSearchable(Image::class));
        $this->assertTrue($this->searchService->isSearchable(ContentAggregator::class));
        $this->assertTrue($this->searchService->isSearchable(Tag::class));
        $this->assertTrue($this->searchService->isSearchable(Link::class));
        $this->cleanUp();
    }

    public function testSearchMethod()
    {
        $searchablePost = $this->createSearchablePost();
        $this->engine->index($searchablePost);

        try {
            $test = $this->searchService->search($this->entityManager, Post::class);
            dd($test);
        } catch (Exception $e) {
            $this->assertInstanceOf(HTTPRequestException::class, $e);
        }
    }
}