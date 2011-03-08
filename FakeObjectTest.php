<?php
class FakeObjectTest extends PHPUnit_Framework_TestCase
{
    public function testMergesTwoThreads()
    {
        $dao = new FakePostDao(array(
            1 => array(
                new Post('Hello'),
                new Post('Hello!'),
                new Post('')
            ),
            2 => array(
                new Post('Hi'),
                new Post('Hi!')
            ),
            3 => array(
                new Post('Good morning.')
            )
        ));
        
        $forumManager = new ForumManager($dao);
        $forumManager->mergeThreadsByIds(1, 2);
        $thread = $dao->getThread(1);
        $this->assertEquals(5, count($thread));
    }
}

class ForumManager
{
    private $dao;

    public function __construct(PostsDao $dao)
    {
        $this->dao = $dao;
    }

    public function mergeThreadsByIds($originalId, $toBeMergedId)
    {
        $original = $this->dao->getThread($originalId);
        $toBeMerged = $this->dao->getThread($toBeMergedId);
        $newOne = array_merge($original, $toBeMerged);
        $this->dao->removeThread($originalId);
        $this->dao->removeThread($toBeMergedId);
        $this->dao->addThread($originalId, $newOne);
    }
}

interface PostsDao
{
    public function getThread($id);
    public function removeThread($id);
    public function addThread($id, array $thread);
}

class FakePostDao implements PostsDao
{
    private $threads;

    public function __construct(array $initialState)
    {
        $this->threads = $initialState;
    }

    public function getThread($id)
    {
        return $this->threads[$id];
    }

    public function removeThread($id)
    {
        unset($this->threads[$id]);
    }

    /**
     * We model Thread as array of Posts for simplicity.
     */
    public function addThread($id, array $thread)
    {
        $this->threads[$id] = $thread;
    }
}

/**
 * Again a Dummy object: minimal implementation, to make this test pass.
 */
class Post
{
}
