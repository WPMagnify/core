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

class DefaultNormalizerTest extends \Magnify\Core\MagnifyTestCase
{
    private $normalizer;

    public function testNormalizeProducesTheExpectedArray()
    {
        $postId = wp_insert_post([
            'post_content'      => 'test content',
            'post_title'        => 'test title',
            'post_author'       => $this->makeUser(),
            'post_category'     => array_filter([$this->makeCategory()]),
        ]);
        $post = get_post($postId);
        $this->assertNotNull($post);

        $result = $this->normalizer->normalize(1, $post);

        $this->assertEquals('test content', $result['post_content']);
        $this->assertEquals('test title', $result['post_title']);

        $this->assertCount(1, $result['terms']);
        $cat = $result['terms'][0];
        $this->assertEquals('DefaultNormalizerTest', $cat['name']);
        $this->assertEquals('default-normalizer-test', $cat['slug']);
        $this->assertEquals('test term description', $cat['description']);
        $this->assertEquals('category', $cat['taxonomy']);

        $author = $result['post_author'];
        $this->assertInternalType('array', $author);
        $this->assertEquals('defaultnormalizertest', $author['login']);
        $this->assertEquals('Kim', $author['first_name']);
        $this->assertEquals('Doe', $author['last_name']);
        $this->assertEquals('Display', $author['display_name']);
    }

    public function setUp()
    {
        parent::setUp();
        $this->normalizer = new DefaultNormalizer();
    }

    private function makeUser()
    {
        $userId = wp_insert_user([
            'user_login'    => 'defaultnormalizertest',
            'user_pass'     => 'pass',
            'first_name'    => 'Kim',
            'last_name'     => 'Doe',
            'display_name'  => 'Display',
        ]);

        return $userId && !is_wp_error($userId) ? $userId : null;
    }

    private function makeCategory()
    {
        $term = wp_insert_term('DefaultNormalizerTest', 'category', [
            'slug'          => 'default-normalizer-test',
            'description'   => 'test term description',
        ]);

        return $term && !is_wp_error($term) ? $term['term_id'] : null;
    }
}
