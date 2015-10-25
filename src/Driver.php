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

/**
 * Defines a driver capable of content federation. This usually means it
 * takes a content object, serializes it somehow, and sends it to an external
 * data store from which it can be retrieved via search or otherwise.
 *
 * Search is not part of this interface, giving implementers the freedom to
 * only do content federation, search, or both.
 *
 * @since   1.0
 */
interface Driver
{
    /**
     * Called to update a post object in the backend. This will be call every
     * time a post is updated, created, etc.
     *
     * @param   int $blogId The current blog ID. This will always be an integer
     *          multi-site or not.
     * @param   array $post The post to persist. This is a "normalized" version
     *          of the `WP_Post` object. The core plugin will take care of the
     *          normalization.
     * @throws  Exception Any exception may be thrown.
     * @return  void
     */
    public function persist($blogId, array $post);

    /**
     * Update one or more posts from a single blog. Some drivers may simply
     * look through and call `persist`, but some storage backends may support
     * bulk updates that are more efficient.
     *
     * @param   int $blogId The current blog ID. This will always be an integer
     *          multi-site or not.
     * @param   array[] $posts The posts to persist
     * @throws  Exception Any exception may be thrown.
     * @return  void
     */
    public function bulkPersist($blogId, array $post);

    /**
     * Remove a post from the storage backend. Called every time a post goes
     * from a puglished to unavailable state. Whether that's during a real
     * delete, trash, or simply a status change.
     *
     * @param   int $blogId The current blog ID. This will always be an integer
     *          multi-site or not.
     * @param   int $postId The post to delete. It's up to the driver to
     *          persists posts in such a way that they can be deleted with
     *          only the post IDs. This will be called after the post is deleted
     * @throws  Exception Any exception may be thrown.
     * @return  void
     */
    public function delete($blogId, $postId);

    /**
     * Remove one or more posts from the storage backend. Some drivers might just
     * loop through each post and call delete, but others may have more efficient
     * bulk operations.
     *
     * @param   int $blogId The current blog ID. This will always be an integer
     *          multi-site or not.
     * @param   int[] $postIds The posts to delete
     * @throws  Exception Any exception may be thrown.
     * @return  void
     */
    public function bulkDelete($blogId, array $postIds);
}
