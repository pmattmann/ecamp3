<?php

namespace App\Tests\Api\ActivityProgressLabel;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\ActivityProgressLabel;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateActivityProgressLabelTest extends ECampApiTestCase {
    public function testCreateActivityProgressLabelIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/activity_progress_labels', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateActivityProgressLabelIsNotPossibleForUnrelatedUserBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/activity_progress_labels', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateActivityProgressLabelIsNotPossibleForInactiveCollaboratorBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/activity_progress_labels', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateActivityProgressLabelIsDeniedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/activity_progress_labels', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateActivityProgressLabelIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/activity_progress_labels', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateActivityProgressLabelIsAllowedForManager() {
        static::createClientWithCredentials()
            ->request('POST', '/activity_progress_labels', ['json' => $this->getExampleWritePayload()])
    ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateActivityProgressLabelValidatesMissingCamp() {
        static::createClientWithCredentials()
            ->request('POST', '/activity_progress_labels', ['json' => $this->getExampleWritePayload([], ['camp'])])
    ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateActivityProgressLabelValidatesMissingTitle() {
        static::createClientWithCredentials()
            ->request('POST', '/activity_progress_labels', ['json' => $this->getExampleWritePayload([], ['title'])])
    ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ActivityProgressLabel::class,
            Post::class,
            array_merge([
                'camp' => $this->getIriFor('camp1'),
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ActivityProgressLabel::class,
            Get::class,
            $attributes,
            ['camp'],
            $except
        );
    }
}
