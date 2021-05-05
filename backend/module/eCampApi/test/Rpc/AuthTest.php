<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class AuthTest extends AbstractApiControllerTestCase {
    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/auth';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $loader = new Loader();
        $loader->addFixture($userLoader);

        $this->loadFixtures($loader);
        $this->user = $userLoader->getReference(UserTestData::$USER1);
    }

    public function testFetchAsGuest(): void {
        $this->dispatch("{$this->apiEndpoint}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<'JSON'
            {
                "role": "guest",
                "user": null
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "api": {
                    "href": "http://{$this->host}/api"
                },
                "self": {
                    "href": "http://{$this->host}/api/auth"
                },
                "register": {
                    "href": "http://{$this->host}/api/register"
                },
                "login": {
                    "href": "http://{$this->host}/api/auth/login"
                },
                "google": {
                    "href": "http://{$this->host}/api/auth/google{?callback}",
                    "templated": true
                },
                "pbsmidata": {
                    "href": "http://{$this->host}/api/auth/pbsmidata{?callback}",
                    "templated": true
                },
                "logout": {
                    "href": "http://{$this->host}/api/auth/logout"
                }
            }
JSON;
        $expectedEmbeddedObjects = [];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAsUser(): void {
        $this->authenticateUser($this->user);
        $this->dispatch("{$this->apiEndpoint}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertIsObject($this->getResponseContent()->_embedded);
        $this->assertIsObject($this->getResponseContent()->_embedded->user);
        $this->assertEquals('test-user', $this->getResponseContent()->_embedded->user->username);
    }

    public function testLogin(): void {
        $this->assertNull($this->getAuthenticatedUserId());

        $this->setRequestContent([
            'username' => 'test-user',
            'password' => 'test',
        ]);
        $this->dispatch("{$this->apiEndpoint}/login", 'GET');

        $this->assertResponseStatusCode(302);
        $this->assertEquals('/api/auth', $this->getResponseHeader('location')->getFieldValue());
        $this->assertEquals($this->user->getId(), $this->getAuthenticatedUserId());
    }

    public function testLogout(): void {
        $this->authenticateUser($this->user);
        $this->assertEquals($this->user->getId(), $this->getAuthenticatedUserId());

        $this->dispatch("{$this->apiEndpoint}/logout", 'GET');

        $this->assertResponseStatusCode(302);
        $this->assertEquals('/api/auth', $this->getResponseHeader('location')->getFieldValue());
        $this->assertNull($this->getAuthenticatedUserId());
    }
}
