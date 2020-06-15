<?php

namespace Tasklist\Authorization\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthenticationServiceClient
{
    const LOGIN_URL = '/api/login-check';

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param HttpClientInterface $httpClient
     * @param UrlHelper $urlHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        HttpClientInterface $httpClient,
        UrlHelper $urlHelper,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->urlHelper = $urlHelper;
        $this->logger = $logger;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return string|null
     */
    public function getToken($username, $password)
    {
        try {
            $resp = $this->login($username, $password);

            return $resp['token'];
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            $this->logger->critical($e->getMessage());

            return null;
        }
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return string[]|null
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function login($username, $password): ?array
    {
        return $this->makeRequest(
            $this->urlHelper->getAbsoluteUrl(self::LOGIN_URL),
            ['username' => $username, 'password' => $password]
        );
    }

    /**
     * @param string $url
     * @param array $data
     *
     * @return string[]|null
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function makeRequest($url, array $data): ?array
    {
        $response = $this->httpClient->request(
            'POST',
            $url,
            ['json' => $data]
        );

        return json_decode((string)$response->getContent(), true);
    }
}
