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

namespace Surfnet\StepupBundle\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Surfnet\StepupBundle\Command\SendSmsCommand;
use Surfnet\StepupBundle\Http\JsonHelper;

/**
 * Sends SMSes by calling the Gateway API over HTTP.
 */
class GatewayApiSmsService implements SmsService
{
    /**
     * @param Client $guzzleClient A Guzzle client configured with the SMS API base URL and authentication.
     */
    public function __construct(private readonly Client $guzzleClient, private readonly LoggerInterface $logger)
    {
    }

    public function sendSms(SendSmsCommand $command): bool
    {
        $this->logger->info('Requesting Gateway to send SMS');

        $body = [
            'requester' => ['institution' => $command->institution, 'identity' => $command->identity],
            'message' => [
                'originator' => $command->originator,
                'recipient'  => $command->recipient,
                'body'       => $command->body
            ],
        ];
        $response = $this->guzzleClient->post('api/send-sms', ['json' => $body]);
        $statusCode = $response->getStatusCode();

        if ($statusCode != 200) {
            $this->logger->error(
                sprintf('SMS sending failed, error: [%s] %s', $response->getStatusCode(), $response->getReasonPhrase()),
                ['http-body' => $response->getBody() ? $response->getBody()->getContents() : '',]
            );

            return false;
        }

        try {
            $result = JsonHelper::decode((string) $response->getBody());
        } catch (\RuntimeException) {
            $this->logger->error('SMS sending failed; server responded with malformed JSON.');

            return false;
        }

        if (!isset($result['status'])) {
            $this->logger->error('SMS sending failed; server responded without status report.');

            return false;
        }

        if ($result['status'] !== 'OK') {
            $this->logger->error('SMS sending failed; server responded with non-OK status report.');

            return false;
        }

        return true;
    }
}
