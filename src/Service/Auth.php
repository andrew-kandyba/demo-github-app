<?php
namespace App\Service;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;

/**
 * Auth service.
 */
class Auth
{
    /**
     * Github app ID.
     *
     * @var string
     */
    private const APP_ID = '25941';

    /**
     * JWT algorithm.
     *
     * @var string
     */
    private const JWT_ALG = 'RS256';

    /**
     * Github app private key
     *
     * @var string
     */
    private const APP_PRIVATE_KEY = __DIR__ . '/../../.ssh/key.pem';

    /**
     * Http client instance.
     *
     * @var Client
     */
    private $httpClient;

    /**
     * Auth service constructor.
     *
     * @param Client $httpClient Http client instance.
     */
    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Generate app jwt token.
     *
     * @return string
     */
    private function generateJwtToken(): string
    {
        $privateKey = file_get_contents(self::APP_PRIVATE_KEY);
        $payload = array(
            'iss' => self::APP_ID,
            'iat' => time(),
            'exp' => time() + (10 * 60),
        );

        return JWT::encode($payload, $privateKey, self::JWT_ALG);
    }

    /**
     * Get installation access token.
     *
     * @see Please see:
     * https://developer.github.com/apps/building-github-apps/authenticating-with-github-apps/#authenticating-as-an-installation
     *
     * @param int $installationId
     * @return string
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getInstallationAccessToken(int $installationId): string
    {
        $apiEndpoint = str_replace(
            ':installation_id',
            $installationId,
            'https://api.github.com/app/installations/:installation_id/access_tokens'
        );

        $jwt = $this->generateJwtToken();

        $request = $this->httpClient->request(
            'POST',
            $apiEndpoint,
            ['headers' => [
                'Accept' => 'application/vnd.github.machine-man-preview+json',
                'Authorization' => 'Bearer ' . $jwt
            ]]
        );

        /** @noinspection PhpComposerExtensionStubsInspection */
        return json_decode($request->getBody()->getContents())->token;
    }
}