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

namespace Surfnet\StepupBundle\DependencyInjection;

use Surfnet\StepupBundle\Form\ChoiceList\LocaleChoiceList;
use Surfnet\StepupBundle\Http\CookieHelper;
use Surfnet\StepupBundle\Service\LoaResolutionService;
use Surfnet\StepupBundle\Service\SecondFactorTypeService;
use Surfnet\StepupBundle\Service\SmsRecoveryTokenService;
use Surfnet\StepupBundle\Service\SmsSecondFactorService;
use Surfnet\StepupBundle\Value\Loa;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use function array_key_exists;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SurfnetStepupExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $config);

        $container->setParameter('logging.application_name', $config['logging']['application_name']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        if (isset($config['loa_definition']) && $config['loa_definition']['enabled']) {
            $this->defineLoas($config['loa_definition'], $container);
        } else {
            $container->removeDefinition('surfnet_stepup.service.loa_resolution');
            $container->removeAlias(LoaResolutionService::class);
        }

        if ($config['sms']['enabled'] === false) {
            $container->removeDefinition('surfnet_stepup.service.sms_second_factor');
            $container->removeAlias(SmsSecondFactorService::class);
            $container->removeDefinition('surfnet_stepup.service.challenge_handler');
        } else {
            $this->configureSmsSecondFactorServices($config, $container);
            if (!$config['gateway_api']['enabled'] && $config['sms']['service'] === Configuration::DEFAULT_SMS_SERVICE) {
                throw new RuntimeException(
                    'The gateway API is not enabled and no replacement SMS service is configured'
                );
            }
        }

        if ($config['gateway_api']['enabled']) {
            $this->configureGatewayApiClient($config, $container);
        } else {
            // Remove the Gateway API SMS service and its Guzzle client.
            $container->removeDefinition('surfnet_stepup.service.gateway_api_sms');
            $container->removeDefinition('surfnet_stepup.guzzle.gateway_api');
        }

        if ($config['locale_cookie']['enabled']) {
            $this->configureLocaleCookieSettings($config, $container);
        } else {
            $container->removeDefinition('surfnet_stepup.locale_cookie_helper');
            $container->removeAlias(CookieHelper::class);
            $container->removeDefinition('surfnet_stepup.locale_cookie_settings');
        }

        $this->configureLocaleSelectionWidget($config, $container);
        $this->configureSecondFactorTypeService($config, $container);
    }

    private function defineLoas(array $loaDefinitions, ContainerBuilder $container): void
    {
        $loaService = $container->getDefinition('surfnet_stepup.service.loa_resolution');

        $loa1 = new Definition(Loa::class, [Loa::LOA_1, $loaDefinitions['loa1']]);
        $loa2 = new Definition(Loa::class, [Loa::LOA_2, $loaDefinitions['loa2']]);
        $loa3 = new Definition(Loa::class, [Loa::LOA_3, $loaDefinitions['loa3']]);

        $arguments = [$loa1, $loa2, $loa3];

        if (array_key_exists('loa_self_asserted', $loaDefinitions)) {
            $arguments[] = new Definition(Loa::class, [Loa::LOA_SELF_VETTED, $loaDefinitions['loa_self_asserted']]);
        }
        $loaService->addArgument($arguments);
    }

    private function configureLocaleCookieSettings(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('surfnet_stepup.locale_cookie_settings')
            ->setArguments(
                [
                    $config['locale_cookie']['name'],
                    null,
                    $config['locale_cookie']['expire'],
                    $config['locale_cookie']['path'],
                    $config['locale_cookie']['domain'],
                    $config['locale_cookie']['secure'],
                    $config['locale_cookie']['http_only'],
                ]
            );
    }

    private function configureGatewayApiClient(array $config, ContainerBuilder $container): void
    {
        $handlerStack = $container->getDefinition('surfnet_stepup.guzzle.handler_stack');

        // Configure the Gateway API SMS service's Guzzle client.
        $gatewayGuzzleOptions = [
            'base_uri' => $config['gateway_api']['url'],
            'auth'    => [
                $config['gateway_api']['credentials']['username'],
                $config['gateway_api']['credentials']['password'],
                'basic'
            ],
            'headers' => [
                'Accept' => 'application/json'
            ],
            'handler' => $handlerStack,
            'http_errors' => false
        ];

        $gatewayGuzzle = $container->getDefinition('surfnet_stepup.guzzle.gateway_api');
        $gatewayGuzzle->replaceArgument(0, $gatewayGuzzleOptions);
    }

    private function configureSmsSecondFactorServices(array $config, ContainerBuilder $container): void
    {
        $smsSecondFactorService = $container->getDefinition('surfnet_stepup.service.sms_second_factor');
        $smsSecondFactorService->replaceArgument(0, new Reference($config['sms']['service']));
        $smsSecondFactorService->replaceArgument(2, $config['sms']['originator']);

        $recoveryTokenService = $container->getDefinition(SmsRecoveryTokenService::class);
        $recoveryTokenService->replaceArgument(0, new Reference($config['sms']['service']));
        $recoveryTokenService->replaceArgument(2, $config['sms']['originator']);

        $container
            ->getDefinition('surfnet_stepup.service.challenge_handler')
            ->replaceArgument(2, $config['sms']['otp_expiry_interval'])
            ->replaceArgument(3, $config['sms']['maximum_otp_requests']);
    }

    private function configureLocaleSelectionWidget(array $loaDefinitions, ContainerBuilder $container): void
    {
        if ($container->hasParameter('locales')) {
            $container->getDefinition('surfnet_stepup.form.choice_list.locales')
                ->replaceArgument(0, $container->getParameter('locales'));
        } else {
            $container->removeDefinition('surfnet_stepup.form.choice_list.locales');
            $container->removeAlias(LocaleChoiceList::class);
            $container->removeDefinition('surfnet_stepup.form.switch_locale');
        }
    }

    private function configureSecondFactorTypeService(array $loaDefinitions, ContainerBuilder $container): void
    {
        if (!$container->hasParameter('enabled_generic_second_factors')) {
            $container->removeDefinition('surfnet_stepup.service.second_factor_type');
            $container->removeAlias(SecondFactorTypeService::class);
        }
    }
}
