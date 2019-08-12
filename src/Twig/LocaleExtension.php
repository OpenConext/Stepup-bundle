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

namespace Surfnet\StepupBundle\Twig;

use Surfnet\StepupBundle\Command\SwitchLocaleCommand;
use Surfnet\StepupBundle\Form\Type\SwitchLocaleType;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LocaleExtension extends AbstractExtension
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('stepup_locale_switcher', [$this, 'getLocalePreferenceForm']),
        ];
    }

    public function getLocalePreferenceForm($currentLocale, $route, array $routeParameters = [])
    {
        $command = new SwitchLocaleCommand();
        $command->locale = $currentLocale;

        $form = $this->formFactory->create(
            SwitchLocaleType::class,
            $command,
            ['route' => $route, 'route_parameters' => $routeParameters]
        );

        return $form->createView();
    }

    public function getName()
    {
        return 'stepup_locale';
    }
}
