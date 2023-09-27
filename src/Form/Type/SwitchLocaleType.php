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

namespace Surfnet\StepupBundle\Form\Type;

use Surfnet\StepupBundle\Form\ChoiceList\LocaleChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SwitchLocaleType extends AbstractType
{
    public function __construct(private readonly LocaleChoiceList $localeChoiceList, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAction($this->urlGenerator->generate($options['route'], $options['route_parameters']));
        $builder->setMethod(\Symfony\Component\HttpFoundation\Request::METHOD_POST);
        $builder->add('locale', ChoiceType::class, [
            'label' => /** @Ignore */ false,
            'required' => true,
            'choices' => $this->localeChoiceList->create(),
            'attr' => [ 'class' => 'fa-language' ],
        ]);
        $builder->add('switch', SubmitType::class, [
            'label' => 'stepup_middleware_client.form.switch_locale.switch',
            'attr' => [ 'class' => 'btn btn-default' ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'route'            => null,
            'route_parameters' => [],
            'data_class'       => \Surfnet\StepupBundle\Command\SwitchLocaleCommand::class,
        ]);

        $resolver->setRequired(['route']);

        $resolver->setAllowedTypes('route', 'string');
        $resolver->setAllowedTypes('route_parameters', 'array');
    }

    public function getBlockPrefix()
    {
        return 'stepup_switch_locale';
    }
}
