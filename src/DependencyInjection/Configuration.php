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

namespace Surfnet\StepupBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    const DEFAULT_SMS_SERVICE = 'surfnet_stepup.service.gateway_api_sms';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('surfnet_bundle');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('logging')
                    ->isRequired()
                    ->children()
                        ->scalarNode('application_name')
                            ->info('This is the application name that is included with each log message')
                            ->isRequired()
                            ->validate()
                                ->ifTrue(function ($name) {
                                    return !is_string($name);
                                })
                                ->thenInvalid('surfnet_bundle.logging.application_name must be string')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('loa_definition')
                    ->canBeDisabled()
                    ->children()
                        ->scalarNode('loa1')
                            ->example('https://gateway.tld/authentication/loa1')
                            ->validate()
                            ->ifTrue(function ($value) {
                                return !is_string($value);
                            })
                                ->thenInvalid('Loa definition for "loa1" must be a string')
                            ->end()
                        ->end()
                        ->scalarNode('loa2')
                            ->example('https://gateway.tld/authentication/loa2')
                            ->validate()
                            ->ifTrue(function ($value) {
                                return !is_string($value);
                            })
                                ->thenInvalid('Loa definition for "loa2" must be a string')
                            ->end()
                        ->end()
                        ->scalarNode('loa3')
                            ->example('https://gateway.tld/authentication/loa3')
                            ->validate()
                            ->ifTrue(function ($value) {
                                return !is_string($value);
                            })
                                ->thenInvalid('Loa definition for "loa3" must be a string')
                            ->end()
                        ->end()
                        ->scalarNode('loa_self_asserted')
                            ->example('https://gateway.tld/authentication/loa_self_asserted')
                            ->validate()
                                ->ifTrue(function ($value) {
                                    return !is_string($value);
                                })
                                ->thenInvalid('Loa definition for "loa_self_asserted" must be a string')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('attach_request_id_injector_to')
                    ->prototype('scalar')
                    ->validate()
                        ->ifTrue(function ($serviceId) { return !is_string($serviceId); })
                        ->thenInvalid('surfnet_bundle.attach_request_id_injector_to must be array of strings')
                    ->end()
                ->end()
            ->end();

        $this->createGatewayApiConfiguration($rootNode);
        $this->createSmsConfiguration($rootNode);
        $this->createLocaleCookieConfiguration($rootNode);

        return $treeBuilder;
    }

    private function createGatewayApiConfiguration(ArrayNodeDefinition $root)
    {
        $root
            ->children()
                ->arrayNode('gateway_api')
                    ->canBeEnabled()
                    ->info('Gateway API configuration')
                    ->children()
                        ->arrayNode('credentials')
                            ->info('Basic authentication credentials')
                            ->children()
                                ->scalarNode('username')
                                    ->info('Username for the Gateway API')
                                    ->isRequired()
                                    ->validate()
                                        ->ifTrue(function ($value) {
                                            return (!is_string($value) || empty($value));
                                        })
                                        ->thenInvalid(
                                            'Invalid Gateway API username specified: "%s". Must be non-empty string'
                                        )
                                    ->end()
                                ->end()
                                ->scalarNode('password')
                                    ->info('Password for the Gateway API')
                                    ->isRequired()
                                    ->validate()
                                        ->ifTrue(function ($value) {
                                            return (!is_string($value) || empty($value));
                                        })
                                        ->thenInvalid(
                                            'Invalid Gateway API password specified: "%s". Must be non-empty string'
                                        )
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('url')
                            ->info('The URL to the Gateway application (e.g. https://gateway.tld)')
                            ->isRequired()
                            ->validate()
                                ->ifTrue(function ($value) {
                                    return (!is_string($value) || empty($value) || !preg_match('~/$~', $value));
                                })
                                ->thenInvalid(
                                    'Invalid Gateway URL specified: "%s". Must be string ending in forward slash'
                                )
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function createSmsConfiguration(ArrayNodeDefinition $root)
    {
        $root
            ->children()
                ->arrayNode('sms')
                    ->canBeDisabled()
                    ->info('SMS configuration')
                    ->isRequired()
                    ->children()
                        ->scalarNode('service')
                            ->info(
                                'The ID of the SMS service used for sending SMS messages. ' .
                                'Must implement "Surfnet\StepupBundle\Service\SmsService".'
                            )
                            ->defaultValue(self::DEFAULT_SMS_SERVICE)
                            ->validate()
                                ->ifTrue(function ($value) {
                                    return !is_string($value);
                                })
                                ->thenInvalid('The SMS service ID must be specified using a string.')
                            ->end()
                        ->end()
                        ->scalarNode('originator')
                            ->info('Originator (sender) for SMS messages')
                            ->validate()
                                ->ifTrue(function ($value) {
                                    return (!is_string($value) || !preg_match('~^[a-z0-9]{1,11}$~i', $value));
                                })
                                ->thenInvalid(
                                    'Invalid SMS originator specified: "%s". Must be a string matching '
                                    . '"~^[a-z0-9]{1,11}$~i".'
                                )
                            ->end()
                        ->end()
                        ->integerNode('otp_expiry_interval')
                            ->info('After how many seconds an SMS challenge OTP expires')
                            ->validate()
                                ->ifTrue(function ($value) {
                                    return $value <= 0;
                                })
                                ->thenInvalid(
                                    'Invalid SMS challenge OTP expiry, must be one or more seconds.'
                                )
                            ->end()
                        ->end()
                        ->integerNode('maximum_otp_requests')
                            ->info('How many challenges a user may request during a session')
                            ->validate()
                                ->ifTrue(function ($value) {
                                    return $value <= 0;
                                })
                                ->thenInvalid(
                                    'Maximum OTP requests has a minimum of 1'
                                )
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function createLocaleCookieConfiguration(ArrayNodeDefinition $root)
    {
        $root
            ->children()
                ->arrayNode('locale_cookie')
                    ->canBeDisabled()
                    ->info('Cookie settings for locale cookie')
                    ->children()
                        ->scalarNode('name')
                            ->info('Name for the cookie')
                            ->defaultValue('stepup_locale')
                        ->end()
                        ->scalarNode('domain')
                            ->info('Domain the cookie is scoped to')
                            ->defaultValue('example.org')
                        ->end()
                        ->integerNode('expire')
                            ->info('Defines a specific number of seconds for when the browser should delete the cookie.')
                            ->defaultValue(0)
                        ->end()
                        ->scalarNode('path')
                            ->info('Path the cookie is scoped to')
                            ->defaultValue('/')
                        ->end()
                        ->booleanNode('secure')
                            ->info('Only transmit cookie over secure connections?')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('http_only')
                            ->info('Directs browsers not to expose cookies through channels other than HTTP (and HTTPS) requests')
                            ->defaultValue(true)
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
