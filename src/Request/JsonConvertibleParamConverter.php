<?php

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

namespace Surfnet\StepupBundle\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Surfnet\StepupBundle\Exception\BadJsonRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * ParamConverter that converts JSON objects with underscore notation mapped to snake-cased, public properties of
 * classes that implement JsonConvertible.
 *
 * @see JsonConvertible
 */
class JsonConvertibleParamConverter implements ParamConverterInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();

        $json = $request->getContent();
        $object = $this->camelCaseObjectPropertyNames(json_decode($json, false));

        if (!isset($object->$name) || !is_object($object->$name)) {
            throw new BadJsonRequestException([sprintf("Missing parameter '%s'", $name)]);
        }

        $object = $object->$name;
        $convertedObject = new $class;

        $errors = [];

        foreach ($object as $key => $value) {
            if (!property_exists($convertedObject, $key)) {
                $errors[] = sprintf("Unknown property '%s.%s'", $name, $key);

                continue;
            }

            $convertedObject->$key = $value;
        }

        $violations = $this->validator->validate($convertedObject);

        if (count($errors) + count($violations) > 0) {
            throw BadJsonRequestException::createForViolationsAndErrors($violations, $name, $errors);
        }

        $request->attributes->set($name, $convertedObject);
    }

    public function supports(ParamConverter $configuration)
    {
        $class = $configuration->getClass();

        if (!is_string($class)) {
            return null;
        }

        return is_subclass_of($class, 'Surfnet\StepupBundle\Request\JsonConvertible');
    }

    /**
     * Deeply converts an object's property names to camel case. Doesn't support circular references!
     *
     * @param object $object
     * @return object
     */
    private function camelCaseObjectPropertyNames($object)
    {
        $camelCasedObject = (object) [];

        foreach ($object as $property => $value) {
            $camelCasedKey = is_string($property)
                ? lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $property))))
                : $property;

            if (is_object($value)) {
                $value = $this->camelCaseObjectPropertyNames($value);
            }

            $camelCasedObject->$camelCasedKey = $value;
        }

        return $camelCasedObject;
    }
}
