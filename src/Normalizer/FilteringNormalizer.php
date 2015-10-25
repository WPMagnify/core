<?php
/*
 * This file is part of the wp-magnify/core package.
 *
 * (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Magnify\Core\Normalizer;

use Magnify\Core\Normalizer;

/**
 * A decorator that filters normalizer output.
 *
 * @since   1.0
 */
final class FilteringNormalizer implements Normalizer
{
    /**
     * @var Normalizer
     */
    private $wrapped;

    public function __construct(Normalizer $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($blogId, \WP_Post $post)
    {
        return magnify_filter(
            'normalized_post',
            $this->wrapped->normalize($blogId, $post),
            $blogId,
            $post
        );
    }
}
