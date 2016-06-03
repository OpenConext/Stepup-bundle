<?php

namespace Surfnet\StepupBundle\EventListener;

use Psr\Log\LoggerInterface;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class LocaleCookieListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $expire;

    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $secure;
    /**
     * @var bool
     */
    private $httpOnly;

    /**
     * @param string $name
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     */
    public function __construct(
        TokenStorageInterface $token,
        LoggerInterface $logger,
        $name,
        $domain,
        $expire,
        $path,
        $secure,
        $httpOnly
    ) {
        $this->tokenStorage = $token;
        $this->logger = $logger;

        if (!is_string($name)) {
            throw InvalidArgumentException::invalidType('string', 'name', $name);
        }
        if (empty($name)) {
            throw new InvalidArgumentException('Empty name provided to ' . __CLASS__);
        }

        $this->name = $name;

        if (!is_string($domain)) {
            throw InvalidArgumentException::invalidType('string', 'domain', $domain);
        }
        if (empty($domain)) {
            throw new InvalidArgumentException(sprintf('Empty domain provided to ' . __CLASS__));
        }

        $this->domain = $domain;

        if (!is_integer($domain)) {
            throw InvalidArgumentException::invalidType('integer', 'expire', $expire);
        }

        $this->expire = $expire;

        if (!is_string($path)) {
            throw InvalidArgumentException::invalidType('string', 'path', $path);
        }

        $this->path = $path;

        if (!is_bool($secure)) {
            throw InvalidArgumentException::invalidType('bool', 'secure', $secure);
        }

        $this->secure = $secure;

        if (!is_bool($httpOnly)) {
            throw InvalidArgumentException::invalidType('bool', 'httpOnly', $httpOnly);
        }

        $this->httpOnly = $httpOnly;
    }

    /**
     * If there is a logged in user with a preferred language, set it as a cookie.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $token = $this->tokenStorage->getToken();

        // No token, no logged in user, nothing to do.
        if (!$token) {
            return;
        }

        $user = $token->getUser();

        if (empty($user)) {
            $this->logger->warning('Unable to set locale cookie because we have a token without a user');
            return;
        }

        if (!$user instanceof LocaleIdentity) {
            $this->logger->warning(
                'Unable to set locale cookie because we have a token with a user that cannot provide a locale'
            );
            return;
        }

        $event->getResponse()->headers->setCookie(
            new Cookie(
                $this->name,
                $user->getPreferredLocale(),
                $this->expire,
                $this->path,
                $this->domain,
                $this->secure,
                $this->httpOnly
            )
        );
    }
}
