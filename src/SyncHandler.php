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

use Psr\Log\LoggerInterface;

/**
 * Connects a driver to WordPress's hooks.
 *
 * @since   1.0
 */
class SyncHandler implements Hookable
{
    /**
     * @var Driver
     */
    private $driver;

    /**
     * @var Normalizer
     */
    private $normalizer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Driver $driver, Normalizer $normalizer, LoggerInterface $logger)
    {
        $this->driver = $driver;
        $this->normalizer = $normalizer;
        $this->logger = $logger;
    }

    public function connect()
    {
        add_action('wp_insert_post', array($this, 'handlePost'), 999, 2);
        add_action('edit_attachment', array($this, 'handleAttachment'), 999);
        add_action('add_attachment', array($this, 'handleAttachment'), 999);
        add_action('deleted_post', array($this, 'handleDelete'), 999);
    }

    public function disconnect()
    {
        remove_action('wp_insert_post', array($this, 'handlePost'), 999, 2);
        remove_action('edit_attachment', array($this, 'handleAttachment'), 999);
        remove_action('add_attachment', array($this, 'handleAttachment'), 999);
        remove_action('deleted_post', array($this, 'handleDelete'), 999);
    }


    public function handlePost($postId, $post)
    {
        if (
            magnify_filter('disable_persist', false, $this->driver, $post)
            || !$this->isPersistablePostType($post)
            || $this->isAutosave()
            || $this->isAutoDraft($post)
        ) {
            return;
        }

        if ($this->isPersistableStatus($post)) {
            $blogId = $this->getCurrentBlog();
            try {
                $this->driver->persist($blogId, $this->normalizer->normalize($blogId, $post));
                magnify_act('persisted_post', $blogId, $post);
            } catch (\Exception $e) {
                $this->logException($e, sprintf('persisting post #%d', $post->ID));
            }
        } else {
            // catches all non-published posts. Like trashed
            // or posts taht moved from published back to draft
            $this->handleDelete($post->ID);
        }

    }

    public function handleAttachment($postId)
    {
        return $this->handlePost($postId, get_post($postId));
    }

    public function handleDelete($postId)
    {
        if (magnify_filter('disable_delete', false, $this->driver, $postId)) {
            return;
        }

        try {
            $this->driver->delete($blogId = $this->getCurrentBlog(), $postId);
            magnify_act('deleted_post', $blogId, $postId);
        } catch (\Exception $e) {
            $this->logException($e, sprintf('deleting post #%d', $postId));
        }
    }

    private function getCurrentBlog()
    {
        return magnify_filter('current_blog', get_current_blog_id());
    }

    private function logException(\Exception $e, $ctx)
    {
        $this->logger->error("Caught {cls}({code}) while {ctx}: {msg}\n{tb}", array(
            'cls'   => get_class($e),
            'code'  => $e->getCode(),
            'ctx'   => $ctx,
            'msg'   => $e->getMessage(),
            'tb'    => $e->getTraceAsString()
        ));
    }

    private function isPersistablePostType($post)
    {
        $type = get_post_type_object($post->post_type);
        return magnify_filter('persistable_type', $type && (bool)$type->public, $type);
    }

    private function isPersistableStatus($post)
    {
        return magnify_filter(
            'persistable_status',
            'publish' === $post->post_status,
            $post->post_status,
            $post
        );
    }

    private function isAutosave()
    {
        return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE;
    }

    private function isAutoDraft($post)
    {
        return 'auto-draft' === $post->post_status;
    }
}
