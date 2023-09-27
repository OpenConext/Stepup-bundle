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

namespace Surfnet\StepupBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AttachRequestIdInjectorToGuzzleClientsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig('surfnet_stepup');
        $config = $configs[0];

        // Attach Stepup request ID to outgoing requests of specific Guzzle clients
        if (!isset($config['attach_request_id_injector_to'])) {
            return;
        }

        $guzzleClientRequestIDConfigurator = $container
            ->getDefinition('surfnet_stepup.configurator.guzzle_request_id_configurator');

        foreach ($config['attach_request_id_injector_to'] as $guzzleServiceId) {
            $container
                ->getDefinition($guzzleServiceId)
                ->setConfigurator([$guzzleClientRequestIDConfigurator, 'configure']);
        }
    }
}
