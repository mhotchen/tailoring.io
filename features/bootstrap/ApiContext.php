<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\{
    Client as HttpClient,
    Exception\GuzzleException,
    Psr7\Request, Psr7\Response
};
use PHPUnit\Framework\Assert;
use Psr\Http\Message\MessageInterface;
use Ramsey\Uuid\Uuid;

class ApiContext implements Context
{
    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    /**
     * @Transform :httpMethod
     * @param string $httpMethod
     * @return string
     */
    public function castHttpMethod(string $httpMethod): string
    {
        return strtoupper($httpMethod);
    }

    /**
     * @Transform :resource
     * @param string $resource
     * @return array
     */
    public function castResource(string $resource): array
    {
        return [
            'company' => [
                'data' => [
                    'name'  => 'Tailoring CompanyController ltd',
                    'users' => [
                        [
                            'data' => [
                                'email'    => sprintf('test-%s@tailoring.io', Uuid::uuid4()),
                                'password' => 'IAmNotSecure',
                            ],
                        ],
                    ],
                ],
            ],
        ][$resource];
    }

    /**
     * @When I :httpMethod a :resource to :uri
     * @When I :httpMethod a :resource to :uri with the following changes:
     * @param string         $httpMethod
     * @param array          $resource
     * @param string         $uri
     * @param TableNode|null $updates
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function submitRequest(string $httpMethod, array $resource, string $uri, TableNode $updates = null): void
    {
        if ($updates) foreach ($updates as $update) {
            data_set($resource, $update['key'], $update['value']);
        }

        $client = new HttpClient(['base_uri' => env('APP_URL')]);
        $this->request = new Request(
            $httpMethod,
            $uri,
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            json_encode($resource)
        );
        $this->response = $client->send(
            $this->request,
            [
                'http_errors'  => false,
                'read_timeout' => 0.5,
                'timeout'      => 0.5,
            ]
        );
    }

    /**
     * @Then I should receive a :responseCode response
     * @param int $responseCode
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function validateResponseCode(int $responseCode): void
    {
        Assert::assertEquals($responseCode, $this->response->getStatusCode());
    }

    /**
     * @Then (I) debug
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function debug()
    {
        echo PHP_EOL;
        if (!$this->request) {
            echo 'No request object created' . PHP_EOL;
        } else {
            echo 'REQUEST:' . PHP_EOL;
            echo $this->request->getMethod() . ' ' . $this->request->getUri() . PHP_EOL;

            $this->printMessage($this->request);
        }

        echo PHP_EOL;

        if (!$this->response) {
            echo 'No response object created' . PHP_EOL;
        } else {
            echo 'RESPONSE:' . PHP_EOL;
            echo $this->response->getStatusCode() . ' ' . $this->response->getReasonPhrase() . PHP_EOL;
            $this->printMessage($this->response);
        }

        exit;
    }

    /**
     * @param MessageInterface $message
     * @throws RuntimeException
     */
    private function printMessage(MessageInterface $message)
    {
        foreach ($message->getHeaders() as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $innerValue) {
                    echo "$field: $innerValue" . PHP_EOL;
                }
            } else {
                echo "$field: $value" . PHP_EOL;
            }
        }

        echo PHP_EOL;

        $message->getBody()->rewind();
        $content = $message->getBody()->getContents();

        if (!$content === '') {
            return;
        }

        $decoded = json_decode($content);
        echo json_last_error() ? $content : json_encode($decoded, JSON_PRETTY_PRINT);
        echo PHP_EOL;

        if (json_last_error()) {
            printf('%sWARNING: body is invalid JSON (error: %s).%s', PHP_EOL, json_last_error_msg(), PHP_EOL);
        }
    }
}