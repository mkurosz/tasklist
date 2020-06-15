<?php

namespace Tasklist\Authorization\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Tasklist\Authorization\Service\AuthenticationServiceClient;

class JWTAuthenticator
{
    // if changed update also security.firewalls.main.logout.delete_cookies
    const JWT_COOKIE_NAME = 'jwtTokenCookie';
    const COOKIE_PATH = '/';

    /**
     * @var string
     */
    private $token;

    /**
     * @var AuthenticationServiceClient
     */
    private $authenticationServiceClient;

    /**
     * @param AuthenticationServiceClient $authenticationServiceClient
     */
    public function __construct(AuthenticationServiceClient $authenticationServiceClient)
    {
        $this->authenticationServiceClient = $authenticationServiceClient;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();

        if (!$token instanceof PostAuthenticationGuardToken) {
            return;
        }

        $jwtToken = $this->authenticationServiceClient->getToken(
            $token->getUsername(),
            $token->getUser()->getPlainPassword()
        );

        $this->token = $jwtToken;
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMasterRequest() || empty($this->token)) {
            return;
        }

        $event->getResponse()->headers->setCookie(
            new Cookie(
                self::JWT_COOKIE_NAME,
                $this->token,
                0,
                self::COOKIE_PATH,
                null,
                false,
                false
            )
        );
    }
}
