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

class FilteringNormalizerTest extends \Magnify\Core\MagnifyTestCase
{
    private $wrapped, $normalizer;

    public function testNormalizeCallsWrappedNormalizerAndFiltersOutput()
    {
        $called = false;
        add_filter(magnify_hook('normalized_post'), function ($result) use (&$called) {
            $called = true;
            return $result;
        });
        $postId = $this->factory->post->create($in = ['post_title' => 'FilteringNormalizerTest']);
        $post = get_post($postId);
        $this->wrapped->expects($this->once())
            ->method('normalize')
            ->with(1, $this->identicalTo($post))
            ->willReturn($in);

        $result = $this->normalizer->normalize(1, $post);

        $this->assertEquals($in, $result);
        $this->assertTrue($called, 'should have called the filter');
    }

    public function setUp()
    {
        parent::setUp();
        $this->wrapped = $this->getMock(Normalizer::class);
        $this->normalizer = new FilteringNormalizer($this->wrapped);
    }
}
