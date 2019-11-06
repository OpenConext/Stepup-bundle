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

    /**
     * @SuppressWarnings(PHPMD.MissingImport) - Unable to import the dynamic class creation at line 63
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $snakeCasedName = $this->camelCaseToSnakeCase($name);
        $class = $configuration->getClass();

        $json = $request->getContent();
        $object = json_decode($json, true);

        if (!isset($object[$snakeCasedName]) || !is_array($object[$snakeCasedName])) {
            throw new BadJsonRequestException([sprintf("Missing parameter '%s'", $name)]);
        }

        $object = $object[$snakeCasedName];
        $convertedObject = new $class;

        $errors = [];

        foreach ($object as $key => $value) {
            $properlyCasedKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));

            if (!property_exists($convertedObject, $properlyCasedKey)) {
                $errors[] = sprintf("Unknown property '%s.%s'", $snakeCasedName, $key);

                continue;
            }

            $convertedObject->$properlyCasedKey = $value;
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
     * @param string $camelCase
     * @return string
     * @see \Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter
     */
    private function camelCaseToSnakeCase($camelCase)
    {
        $snakeCase = '';

        $len = strlen($camelCase);
        for ($i = 0; $i < $len; $i++) {
            if (ctype_upper($camelCase[$i])) {
                $snakeCase .= '_'.strtolower($camelCase[$i]);
            } else {
                $snakeCase .= strtolower($camelCase[$i]);
            }
        }

        return $snakeCase;
    }
}
