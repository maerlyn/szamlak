<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class GoogleClientService
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $dataPath;
    protected $logger;

    public function __construct($clientId, $clientSecret, $redirectUri, $dataPath, LoggerInterface $logger)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->dataPath = $dataPath;
        $this->logger = $logger;
    }

    public function getClient()
    {
        $client = new \Google_Client();
        $client->setLogger($this->logger);
        $client->setApplicationName("szamlak");
        $client->setScopes([
            \Google_Service_Gmail::GMAIL_MODIFY,
        ]);
        $client->setAccessType("offline");
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->setRedirectUri($this->redirectUri);
        $client->setIncludeGrantedScopes(true);

        if (file_exists($this->dataPath . "/token")) {
            $accessToken = json_decode(file_get_contents($this->dataPath . "/token"), true);
        } else {
            $authUrl = $client->createAuthUrl();
            echo "auth url: " . $authUrl . "\n";
            echo "verification code: ";
            $authCode = trim(fgets(STDIN));

            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            file_put_contents($this->dataPath . "/token", json_encode($accessToken));
        }
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($this->dataPath . "/token", $client->getAccessToken());
        }

        return $client;
    }

    public function getGmailService()
    {
        return new \Google_Service_Gmail($this->getClient());
    }
}
