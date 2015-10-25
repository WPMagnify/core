<?php
/*
 * This file is part of the wp-magnify/core package.
 *
 * (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magnify\Core;

class SyncHandlerTest extends MagnifyTestCase
{
    private $logger, $driver, $normalier, $handler;

    public function testCreatingPostsWithoutPublishedStatusCausesADriverDelete()
    {
        $this->driver->expects($this->once())
            ->method('delete')
            ->with(get_current_blog_id(), $this->isType('numeric'));

        $this->factory->post->create([
            'post_title'    => 'test',
            'post_status'   => 'draft',
        ]);

        $this->assertNoErrors();
    }

    public function testPublishedPostsAreNormalizedAndPersistedWithTheDriver()
    {
        $blogId = get_current_blog_id();
        $this->normalizer->expects($this->once())
            ->method('normalize')
            ->with($blogId, $this->isInstanceOf('WP_Post'))
            ->willReturn($norm = ['one' => 'two']);
        $this->driver->expects($this->once())
            ->method('persist')
            ->with($blogId, $norm);

        $this->factory->post->create([
            'post_title'    => 'test',
            'post_status'   => 'publish',
        ]);

        $this->assertNoErrors();
    }

    public function testPostsWithNonPublicPostTypesAreIgnored()
    {
        $this->driver->expects($this->never())
            ->method('persist');
        register_post_type('synchandertest', ['public' => false]);

        $this->factory->post->create([
            'post_title'    => 'test',
            'post_status'   => 'publish',
            'post_type'     => 'synchandlertest',
        ]);

        $this->assertNoErrors();
    }

    public function testPersistCanBeDisabledWithFilter()
    {
        add_filter(magnify_hook('disable_persist'), '__return_true');

        $this->driver->expects($this->never())
            ->method('persist');

        $this->factory->post->create([
            'post_title'    => 'test',
            'post_status'   => 'publish',
        ]);

        $this->assertNoErrors();

        remove_filter(magnify_hook('disable_persist'), '__return_true');
    }

    public function testErrorsFromPersistingPostsAreLogged()
    {
        $this->normalizer->expects($this->once())
            ->method('normalize')
            ->willReturn([]);
        $this->driver->expects($this->once())
            ->method('persist')
            ->willThrowException(new \LogicException('oops'));

        $this->factory->post->create([
            'post_title'    => 'test',
            'post_status'   => 'publish',
        ]);

        $this->assertCount(1, $this->logger);
        foreach ($this->logger as $msg) {
            $this->assertContains('LogicException', $msg);
            $this->assertContains('oops', $msg);
        }
    }

    public function testDeletingPostCallsDeleteOnTheDriver()
    {
        $this->handler->disconnect();
        $postId = $this->factory->post->create([
            'post_title'    => 'test',
            'post_status'   => 'publish',
        ]);
        $this->handler->connect();
        $this->driver->expects($this->once())
            ->method('delete')
            ->with(get_current_blog_id(), $postId);

        wp_delete_post($postId, true);

        $this->assertNoErrors();
    }

    public function testErrorsDuringDeletionAreLogged()
    {
        $this->handler->disconnect();
        $postId = $this->factory->post->create([
            'post_title'    => 'test',
            'post_status'   => 'publish',
        ]);
        $this->handler->connect();
        $this->driver->expects($this->once())
            ->method('delete')
            ->with(get_current_blog_id(), $postId)
            ->willThrowException(new \LogicException('oops'));

        wp_delete_post($postId, true);

        $this->assertCount(1, $this->logger);
        foreach ($this->logger as $msg) {
            $this->assertContains('LogicException', $msg);
            $this->assertContains('oops', $msg);
        }
    }

    public function testDeletionCanBeDisabledWithAFilter()
    {
        add_filter(magnify_hook('disable_delete'), '__return_true');
        $this->handler->disconnect();
        $postId = $this->factory->post->create([
            'post_title'    => 'test',
            'post_status'   => 'publish',
        ]);
        $this->handler->connect();
        $this->driver->expects($this->never())
            ->method('delete');

        wp_delete_post($postId, true);

        $this->assertNoErrors();
        remove_filter(magnify_hook('disable_delete'), '__return_true');
    }

    public function setUp()
    {
        parent::setUp();
        $this->logger = new Logger\SpyLogger();
        $this->driver = $this->getMock(Driver::class);
        $this->normalizer = $this->getMock(Normalizer::class);
        $this->handler = new SyncHandler($this->driver, $this->normalizer, $this->logger);
        $this->handler->connect();
    }

    private function assertNoErrors()
    {
        $this->assertCount(
            0,
            $this->logger,
            'Generated Unexpected Log Messages'.PHP_EOL.implode(PHP_EOL, iterator_to_array($this->logger))
        );
    }
}
