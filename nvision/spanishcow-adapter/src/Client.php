<?php
/**
 * This file is part of the spanish-cow project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Created by PhpStorm.
 * User: loicb
 * Date: 08/06/18
 * Time: 12:28
 */

namespace Nvision\SpanishCowAdapter;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Nvision\SpanishCowAdapter\Model\Asset;
use Nvision\SpanishCowAdapter\Model\Translation;
use Psr\Log\LoggerInterface;

class Client
{
    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $jwt;

    /**
     * @var string
     */
    private $project;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(string $baseUrl, string $username, string $password, string $project)
    {
        $this->client = new HttpClient([
            'base_uri' => $baseUrl,
            'verify' => false,
        ]);
        $this->username = $username;
        $this->password = $password;
        $this->project = $project;
    }

    public function login()
    {
        if (null !== $this->jwt) {
            return;
        }

        $options = [
            'form_params' => [
                '_username' => $this->username,
                '_password' => $this->password,
            ],
        ];

        $response = $this->sendRequest('api/login_check', 'POST', $options);

        switch ($response->getStatusCode()) {
            case 200:
                $token = json_decode((string) $response->getBody(), true);
                $this->jwt = $token['token'];
                break;

            case 400:
            case 401:
            default:
                throw new \ErrorException("Unable to connect to Spanish-Cow API, responded with: [{$response->getStatusCode()}] {$response->getBody()}");
        }
    }

    public function getTranslation($locale, $domain, $resname)
    {
        $this->login();

        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->jwt}",
            ],
            'query' => [
                'asset.domain.project' => $this->project,
                'asset.domain.name' => $domain,
                'asset.resname' => $resname,
                'locale.code' => $locale,
            ],
        ];

        $response = $this->sendRequest('api/translations', 'GET', $options);
        $results = json_decode((string) $response->getBody(), true);
        if (count($results)) {
            return current($results);
        }

        return null;
    }

    public function postTranslation(Translation $translation)
    {
        $this->login();

        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->jwt}",
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($translation),
        ];

        $response = $this->sendRequest("api/{$this->project}/{$translation->getDomain()}/{$translation->getResname()}/{$translation->getLocale()}/translations", 'POST', $options);
        $data = json_decode((string) $response->getBody(), true);

        return new Translation($data);
    }

    public function deleteTranslation(Translation $translation)
    {
        $this->login();

        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->jwt}",
                'Content-Type' => 'application/json',
            ],
        ];

        $response = $this->sendRequest("api/{$this->project}/{$translation->getDomain()}/{$translation->getResname()}/{$translation->getLocale()}/translations", 'DELETE', $options);

        return true;
    }

    public function postAsset(Asset $asset)
    {
        $this->login();

        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->jwt}",
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($asset),
        ];

        $response = $this->sendRequest("api/{$this->project}/{$asset->getDomain()}/assets", 'POST', $options);
        $data = json_decode((string) $response->getBody(), true);

        return new Asset($data);
    }

    public function export($domain, $locale)
    {
        $this->login();

        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->jwt}",
                'Content-Type' => 'application/json',
            ],
        ];

        $response = $this->sendRequest("api/{$this->project}/{$domain}/{$locale}/export", 'GET', $options);
        $data = json_decode((string) $response->getBody(), true);

        return $data['xliff'];
    }

    public function import($xliff, $domain, $locale)
    {
        $this->login();

        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->jwt}",
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode(['xliff' => $xliff]),
        ];

        $response = $this->sendRequest("api/{$this->project}/{$domain}/{$locale}/import", 'POST', $options);

        return true;
    }

    protected function sendRequest($endpoint, $method = 'GET', $options = [])
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
        } catch (ServerException $e) {
            $this->logger->error($e->getMessage(), compact($method, $endpoint, $options));

            $response = $e->getResponse();
        } catch (ClientException $e) {
            $this->logger->error($e->getMessage(), compact($method, $endpoint, $options));

            $response = $e->getResponse();
        } finally {
            $this->logger->info(sprintf('%s %s', $method, $endpoint), [
                'options' => $options,
                'response' => $response,
                'content' => (string) $response->getBody(),
            ]);

            return $response;
        }
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return Client
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
