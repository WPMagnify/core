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
 * Turns a post object into an array. The point of normalizers is to take posts
 * to be federated to other databases and consistently normalize them across
 * drivers. While drivers can do whatever they like, normalizers in core means
 * drivers don't really have to touch WP functions if they don't want to.
 *
 * @since   1.0
 */
interface Normalizer
{
    /**
     * Turn a post object into an array.
     *
     * @param   int $blogId The blog to which the post belongs
     * @param   object $post the WP_Post object to normalize
     * @return  array
     */
    public function normalize($blogId, $post);
}
