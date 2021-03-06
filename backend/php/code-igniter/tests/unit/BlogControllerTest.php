<?php

use Codeception\Stub\Expected;
use CodeIgniter\HTTP\ResponseInterface;
use exception\IllegalArgumentException;
use model\Post;

class BlogControllerTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        parent::_before(); // TODO: Change the autogenerated stub
    }

    function testFetchAll()
    {
        $post1 = new Post();
        $post1->setTitle('First post');
        $post1->setDescription('My very first post');

        $post2 = new Post();
        $post2->setTitle('Second post');
        $post2->setDescription('My very second post');

        $postList = [$post1, $post2];

        $blogController = $this->make(new \App\Controllers\BlogController(), [
            'dao' => $this->makeEmpty('dao\PostDBDAO', [
                'findAll' => Expected::once(function () use ($postList) {
                    return $postList;
                }),
            ]),
            'respond' => Expected::once(function ($data) use ($postList) {
                $this->assertEquals($postList, $data);
            })
        ]);

        $blogController->fetchAll();
    }

    function testCreatePost()
    {
        $post = new Post();
        $post->setTitle('First post');
        $post->setDescription('My very first post');

        $blogController = $this->make(new \App\Controllers\BlogController(), [
            'dao' => $this->makeEmpty('dao\PostDBDAO', [
                'create' => Expected::once(),
            ]),
            'request' => $this->make('CodeIgniter\HTTP\IncomingRequest', [
                'getBody' => Expected::once(function () use ($post) {
                    return json_encode($post);
                }),
            ]),
            'respond' => Expected::once(function ($data, $status) {
                $this->assertEquals(null, $data);
                $this->assertEquals(ResponseInterface::HTTP_NO_CONTENT, $status);
            })
        ]);

        $blogController->createPost();
    }

    function testCreatePostWithId()
    {
        $post = new Post();
        $post->setTitle('First post');
        $post->setDescription('My very first post');

        $blogController = $this->make(new \App\Controllers\BlogController(), [
            'dao' => $this->makeEmpty('dao\PostDBDAO', [
                'create' => Expected::once(function() {
                    $this->throwIllegalArgumentException();
                }),
            ]),
            'request' => $this->make('CodeIgniter\HTTP\IncomingRequest', [
                'getBody' => Expected::once(function () use ($post) {
                    return json_encode($post);
                }),
            ]),
            'respond' => Expected::once(function ($data, $status) {
                $this->assertEquals(null, $data);
                $this->assertEquals(ResponseInterface::HTTP_BAD_REQUEST, $status);
            })
        ]);

        $blogController->createPost();
    }

    function testUpdatePost()
    {
        $postId = 3;

        $post = new Post();
        $post->setTitle('First post');
        $post->setDescription('My very first post');
        $reflObj = new ReflectionObject($post);
        $reflProperty = $reflObj->getProperty('id');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($post, $postId);

        $blogController = $this->make(new \App\Controllers\BlogController(), [
            'dao' => $this->makeEmpty('dao\PostDBDAO', [
                'update' => Expected::once(),
            ]),
            'request' => $this->make('CodeIgniter\HTTP\IncomingRequest', [
                'getBody' => Expected::once(function () use ($post) {
                    return json_encode($post);
                }),
            ]),
            'respond' => Expected::once(function ($data, $status) {
                $this->assertEquals(null, $data);
                $this->assertEquals(ResponseInterface::HTTP_NO_CONTENT, $status);
            })
        ]);

        $blogController->updatePost($postId);
    }

    function testUpdatePostWithMismatchesId()
    {
        $postId = 3;

        $post = new Post();
        $post->setTitle('First post');
        $post->setDescription('My very first post');
        $reflObj = new ReflectionObject($post);
        $reflProperty = $reflObj->getProperty('id');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($post, $postId);

        $blogController = $this->make(new \App\Controllers\BlogController(), [
            'dao' => $this->makeEmpty('dao\PostDBDAO', [
                'update' => Expected::never(),
            ]),
            'request' => $this->make('CodeIgniter\HTTP\IncomingRequest', [
                'getBody' => Expected::once(function () use ($post) {
                    return json_encode($post);
                }),
            ]),
            'respond' => Expected::once(function ($data, $status) {
                $this->assertEquals(null, $data);
                $this->assertEquals(ResponseInterface::HTTP_BAD_REQUEST, $status);
            })
        ]);

        $blogController->updatePost($postId + 1);
    }

    function testUpdatePostWithUnknownId()
    {
        $postId = 3;

        $post = new Post();
        $post->setTitle('First post');
        $post->setDescription('My very first post');
        $reflObj = new ReflectionObject($post);
        $reflProperty = $reflObj->getProperty('id');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($post, $postId);

        $blogController = $this->make(new \App\Controllers\BlogController(), [
            'dao' => $this->makeEmpty('dao\PostDBDAO', [
                'update' => Expected::once(function() {
                    $this->throwIllegalArgumentException();
                }),
            ]),
            'request' => $this->make('CodeIgniter\HTTP\IncomingRequest', [
                'getBody' => Expected::once(function () use ($post) {
                    return json_encode($post);
                }),
            ]),
            'respond' => Expected::once(function ($data, $status) {
                $this->assertEquals(null, $data);
                $this->assertEquals(ResponseInterface::HTTP_NOT_FOUND, $status);
            })
        ]);

        $blogController->updatePost($postId);
    }

    function testDeletePost()
    {
        $postId = 3;

        $blogController = $this->make(new \App\Controllers\BlogController(), [
            'dao' => $this->makeEmpty('dao\PostDBDAO', [
                'delete' => Expected::once(),
            ]),
            'respond' => Expected::once(function ($data, $status) {
                $this->assertEquals(null, $data);
                $this->assertEquals(ResponseInterface::HTTP_NO_CONTENT, $status);
            })
        ]);

        $blogController->deletePost($postId);
    }

    function testDeletePostWithUnknownId()
    {
        $postId = 3;

        $blogController = $this->make(new \App\Controllers\BlogController(), [
            'dao' => $this->makeEmpty('dao\PostDBDAO', [
                'delete' => Expected::once(function() {
                    $this->throwIllegalArgumentException();
                }),
            ]),
            'respond' => Expected::once(function ($data, $status) {
                $this->assertEquals(null, $data);
                $this->assertEquals(ResponseInterface::HTTP_NOT_FOUND, $status);
            })
        ]);

        $blogController->deletePost($postId);
    }

    protected static function throwIllegalArgumentException()
    {
        throw new IllegalArgumentException();
    }
}