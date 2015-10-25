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
 * Default implementation of `Normalizer`.
 *
 * @since   1.0
 */
final class DefaultNormalizer implements Normalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($blogId, \WP_Post $post)
    {
        $out = $post->to_array();
        unset($out['post_category'], $out['tags_input']);
        $out['terms'] = $this->buildTerms($post);
        if (!empty($post->post_author)) {
            $out['post_author'] = $this->buildAuthor($post);
        }

        return $out;
    }

    private function buildAuthor(\WP_Post $post)
    {
        $author = get_user_by('id', $post->post_author);
        if (!$author) {
            return null;
        }

        return [
            'id'            => $author->ID,
            'login'         => $author->user_login,
            'first_name'    => $author->first_name,
            'last_name'     => $author->last_name,
            'display_name'  => $author->display_name,
        ];
    }

    private function buildTerms(\WP_Post $post)
    {
        $out = [];
        foreach (get_object_taxonomies($post) as $tax) {
            $out[$tax] = $this->fetchTaxonomy($tax, $post);
        }

        return array_filter($out);
    }

    private function fetchTaxonomy($taxonomy, \WP_Post $post)
    {
        $terms = get_the_terms($post->ID, $taxonomy);
        if (!$terms || is_wp_error($terms)) {
            return null;
        }

        $out = [];
        foreach ($terms as $term) {
            $out[] = [
                'id'            => $term->term_id,
                'name'          => $term->name,
                'slug'          => $term->slug,
                'description'   => $term->description,
            ];
        }

        return $out;
    }
}
