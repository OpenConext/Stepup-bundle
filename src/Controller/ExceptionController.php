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

namespace Surfnet\StepupBundle\Controller;

use DateTime;
use DateTimeInterface;
use SAML2\Response\Exception\InvalidResponseException;
use SAML2\Response\Exception\PreconditionNotMetException;
use Surfnet\SamlBundle\Http\Exception\AuthnFailedSamlResponseException;
use Surfnet\SamlBundle\Http\Exception\SignatureValidationFailedException;
use Surfnet\SamlBundle\Http\Exception\UnknownServiceProviderException;
use Surfnet\SamlBundle\Http\Exception\UnsignedRequestException;
use Surfnet\SamlBundle\Http\Exception\UnsupportedSignatureException;
use Surfnet\StepupBundle\Exception\Art;
use Surfnet\StepupBundle\Exception\MissingRequiredAttributeException;
use Surfnet\StepupBundle\Request\RequestId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * @package Surfnet\StepupBundle\Controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Contains extensive mapping for exceptions
 */
class ExceptionController extends FrameworkController
{
    public function __construct(
        protected readonly TranslatorInterface $translator,
        // Service: surfnet_stepup.request.request_id
        protected RequestId $requestId
    ) {
    }

    public function show(Request $request, Throwable $exception): Response
    {
        $statusCode = $this->getStatusCode($exception);

        if ($statusCode == 404) {
            $template = 'SurfnetStepupBundle:Exception:error404.html.twig';
        } else {
            $template = 'SurfnetStepupBundle:Exception:error.html.twig';
        }

        $response = new Response('', $statusCode);

        $timestamp = (new DateTime)->format(DateTimeInterface::ATOM);
        $hostname  = $request->getHost();
        $requestId = $this->requestId;
        $errorCode = Art::forException($exception);
        $userAgent = $request->headers->get('User-Agent');
        $ipAddress = $request->getClientIp();

        return $this->render(
            $template,
            [
                'timestamp'   => $timestamp,
                'hostname'    => $hostname,
                'request_id'  => $requestId->get(),
                'error_code'  => $errorCode,
                'user_agent'  => $userAgent,
                'ip_address'  => $ipAddress,
            ] + $this->getPageTitleAndDescription($exception),
            $response
        );
    }

    /**
     * @return int HTTP status code
     */
    protected function getStatusCode(Throwable $exception): int
    {
        if ($exception instanceof PreconditionNotMetException || $exception instanceof AccessDeniedException) {
            return Response::HTTP_FORBIDDEN;
        }

        if ($exception instanceof AuthenticationException || $exception instanceof InvalidResponseException) {
            return Response::HTTP_UNAUTHORIZED;
        }

        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        // Unknown exceptions are server errors!
        return 500;
    }

    /**
     * @return array View parameters 'title' and 'description'
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getPageTitleAndDescription(Throwable $exception): array
    {
        $translator = $this->getTranslator();

        if ($exception instanceof SignatureValidationFailedException) {
            $title = $translator->trans('stepup.error.signature_validation_failed.title');
            $description = $translator->trans('stepup.error.signature_validation_failed.description');
        } elseif ($exception instanceof UnsignedRequestException) {
            $title = $translator->trans('stepup.error.unsigned_request.title');
            $description = $translator->trans('stepup.error.unsigned_request.description');
        } elseif ($exception instanceof UnsupportedSignatureException) {
            $title = $translator->trans('stepup.error.unsupported_signature.title');
            $description = $translator->trans('stepup.error.unsupported_signature.description');
        } elseif ($exception instanceof UnknownServiceProviderException) {
            $title = $translator->trans('stepup.error.unknown_service_provider.title');
            $description = $exception->getMessage();
        } elseif ($exception instanceof AuthnFailedSamlResponseException) {
            $title = $translator->trans('stepup.error.authn_failed.title');
            $description = $translator->trans('stepup.error.authn_failed.description');
        } elseif ($exception instanceof MissingRequiredAttributeException) {
            $title = $translator->trans('stepup.error.missing_required_attributes.title');
            $description = $exception->getMessage();
        } elseif ($exception instanceof PreconditionNotMetException) {
            $title = $translator->trans('stepup.error.precondition_not_met.title');
            $description = $translator->trans('stepup.error.precondition_not_met.description');
        } elseif ($exception instanceof InvalidResponseException) {
            $title = $translator->trans('stepup.error.authentication_error.title');
            $description = $translator->trans('stepup.error.authentication_error.description');
        } elseif ($exception instanceof AuthenticationException) {
            $title = $translator->trans('stepup.error.authentication_error.title');
            $description = $translator->trans('stepup.error.authentication_error.description');
        } else {
            $title = $translator->trans('stepup.error.generic_error.title');
            $description = $translator->trans('stepup.error.generic_error.description');
        }

        return [
            'title' => $title,
            'description' => $description,
        ];
    }

    protected function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }
}
