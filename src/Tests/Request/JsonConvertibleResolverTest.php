<?php

declare(strict_types = 1);

/**
 * Copyright 2014 SURFnet bv
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Surfnet\StepupBundle\Tests\Request;

use ArrayIterator;
use Hamcrest\Matchers;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Surfnet\StepupBundle\Exception\BadJsonRequestException;
use Surfnet\StepupBundle\Request\JsonConvertibleResolver;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonConvertibleResolverTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testItThrowsABadJsonRequestExceptionWhenTheParameterIsMissing(): void
    {
        $this->expectException(BadJsonRequestException::class);
        $this->expectExceptionMessage('JSON could not be reconstituted into valid object.');

        $request = $this->createJsonRequest((object) []);
        $validator = m::mock(ValidatorInterface::class);

        $paramResolver = new JsonConvertibleResolver($validator);
        $paramResolver->resolve($request, new ArgumentMetadata('parameter', 'Irrelevant', false, false, null));
    }

    public function testItThrowsABadJsonRequestExceptionWhenUnknownPropertiesAreSent(): void
    {
        $this->expectException(BadJsonRequestException::class);
        $this->expectExceptionMessage('JSON could not be reconstituted into valid object.');

        $validator = m::mock(ValidatorInterface::class)
            ->shouldReceive('validate')->andReturn(new ConstraintViolationList([]))
            ->getMock();

        $request = $this->createJsonRequest((object) ['foo' => ['unknown' => 'prop']]);

        $paramResolver = new JsonConvertibleResolver($validator);
        $paramResolver->resolve($request, new ArgumentMetadata('parameter', Foo::class, false, false, null));
    }

    public function testItThrowsABadJsonRequestExceptionWithErrorsWhenTheConvertedObjectDoesntValidate(): void
    {
        $this->expectException(BadJsonRequestException::class);
        $this->expectExceptionMessage('JSON could not be reconstituted into valid object.');

        $validator = m::mock(ValidatorInterface::class)
            ->shouldReceive('validate')->once()->andReturn(
                m::mock(ConstraintViolationListInterface::class)
                    ->shouldReceive('count')->once()->andReturn(1)
                    ->shouldReceive('getIterator')->andReturn(new ArrayIterator)
                    ->getMock()
            )
            ->getMock();


        $request = $this->createJsonRequest((object) ['foo' => ['bar' => '']]);
        $configuration = new ArgumentMetadata('foo', Foo::class, false, false, null);

        $paramResolver = new JsonConvertibleResolver($validator);
        $paramResolver->resolve($request, $configuration);
    }

    public function testItConvertsAParameter(): void
    {
        $validator = m::mock(ValidatorInterface::class)
            ->shouldReceive('validate')->andReturn(new ConstraintViolationList([]))
            ->getMock();

        $paramResolver = new JsonConvertibleResolver($validator);

        $foo = new Foo();
        $foo->bar = 'baz';
        $foo->camelCased = 'yeah';

        $request = $this->createJsonRequest((object) ['foo' => ['bar' => 'baz', 'camel_cased' => 'yeah']]);
        $request->attributes = m::mock(ParameterBag::class)
            ->shouldReceive('set')->once()->with('foo', Matchers::equalTo($foo))
            ->getMock();

        $configuration = new ArgumentMetadata('foo', Foo::class, false, false, null);
        $result = $paramResolver->resolve($request, $configuration);
        self::assertEquals(
            [
                'bar' => 'baz',
                'camelCased' => 'yeah'
            ],
            $result
        );
    }

    public function testItConvertsASnakeCasedParameter(): void
    {
        $validator = m::mock(ValidatorInterface::class)
            ->shouldReceive('validate')->andReturn(new ConstraintViolationList([]))
            ->getMock();

        $paramResolver = new JsonConvertibleResolver($validator);

        $foo = new Foo();
        $foo->bar = 'baz';
        $foo->camelCased = 'yeah';

        $request = $this->createJsonRequest((object) ['foo_bar' => ['bar' => 'baz', 'camel_cased' => 'yeah']]);
        $request->attributes = m::mock(ParameterBag::class)
            ->shouldReceive('set')->once()->with('fooBar', Matchers::equalTo($foo))
            ->getMock();

        $configuration = new ArgumentMetadata('fooBar', Foo::class, false, false, null);

        $result = $paramResolver->resolve($request, $configuration);
        self::assertEquals(
            [
                'bar' => 'baz',
                'camelCased' => 'yeah'
            ],
            $result
        );
    }

    private function createJsonRequest(mixed $object): Request
    {
        return m::mock(Request::class)
            ->shouldReceive('getContent')->andReturn(json_encode($object, JSON_THROW_ON_ERROR))
            ->getMock();
    }
}
