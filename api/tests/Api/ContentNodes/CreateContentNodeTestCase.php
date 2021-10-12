<?php

namespace App\Tests\Api\ContentNodes;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\ContentNode;
use App\Entity\ContentType;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;

/**
 * Base CREATE (post) test case to be used for various ContentNode types.
 *
 * This test class covers all tests that are the same across all content node implementations
 *
 * @internal
 */
abstract class CreateContentNodeTestCase extends ECampApiTestCase {
    protected string $contentNodeClass;

    protected string $endpoint;

    protected ContentType $defaultContentType;

    protected ContentNode $defaultParent;

    public function setUp(): void {
        parent::setUp();

        $this->defaultParent = static::$fixtures['columnLayout1'];
    }

    public function testCreateIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', "/content_node/{$this->endpoint}", ['json' => $this->getExampleWritePayload()]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateIsDeniedForInvitedCollaborator() {
        $this->create(user: static::$fixtures['user6invited']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateIsDeniedForInactiveCollaborator() {
        $this->create(user: static::$fixtures['user5inactive']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateIsDeniedForUnrelatedUser() {
        $this->create(user: static::$fixtures['user4unrelated']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateIsDeniedForGuest() {
        $this->create(user: static::$fixtures['user3guest']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateIsAllowedForMember() {
        $this->create(user: static::$fixtures['user2member']);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateIsAllowedForManager() {
        // when
        $response = $this->create(user: static::$fixtures['user1manager']);
        $id = json_decode($response->getContent())->id;
        $newContentNode = $this->getEntityManager()->getRepository($this->contentNodeClass)->find($id);

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload($newContentNode), true);
    }

    protected function create(array $payload = null, ?User $user = null) {
        $credentials = null;
        if (null !== $user) {
            $credentials = ['username' => $user->getUsername()];
        }

        if (null === $payload) {
            $payload = $this->getExampleWritePayload();
        }

        return static::createClientWithCredentials($credentials)->request('POST', "/content_node/{$this->endpoint}", ['json' => $payload]);
    }

    protected function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            $this->contentNodeClass,
            OperationType::COLLECTION,
            'post',
            array_merge([
                'parent' => $this->getIriFor($this->defaultParent),
                'contentType' => $this->getIriFor($this->defaultContentType),
                'position' => 10,
                'prototype' => null,
            ], $attributes),
            [],
            $except
        );
    }

    protected function getExampleReadPayload(ContentNode $self, $attributes = [], $except = []) {
        /** @var ContentNode $parent */
        $parent = $this->defaultParent;

        /** @var ContentType $contentType */
        $contentType = $this->defaultContentType;

        return [
            'slot' => 'footer',
            'position' => 10,
            'instanceName' => 'Schlechtwetterprogramm',
            'contentTypeName' => $contentType->name,
            '_links' => [
                'self' => [
                    'href' => $this->getIriFor($self),
                ],
                'root' => [
                    'href' => $this->getIriFor($parent->root),
                ],
                'parent' => [
                    'href' => $this->getIriFor($parent),
                ],
                'children' => [
                    'href' => '/content_nodes?parent='.$this->getIriFor($self),
                ],
                'contentType' => [
                    'href' => $this->getIriFor($contentType),
                ],
                'owner' => [
                    'href' => $this->getIriFor($parent->getRootOwner()),
                ],
                'ownerCategory' => [
                    'href' => $this->getIriFor($parent->getOwnerCategory()),
                ],
            ],
        ];

        /*
        return $this->getExamplePayload(
            $this->contentNodeClass,
            OperationType::ITEM,
            'get',
            array_merge([
            ], $attributes),
            [],
            $except
        );*/
    }
}
