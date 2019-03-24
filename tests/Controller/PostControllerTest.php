<?php

use App\Entity\Post;
use App\Tests\BaseWebTestCase;

class PostControllerTest extends BaseWebTestCase
{
    public function testValidPostSubmit()
    {
        $data = json_encode(['title' => 'post test', 'content' => 'content test']);

        $client = static::createClient();

        $client->request(
            'POST',
            '/posts',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $data

        );

        $payload = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertArrayHasKey('id', $payload);
        $this->assertArrayHasKey('title', $payload);
        $this->assertArrayHasKey('content', $payload);
        $this->assertArrayHasKey('createdAt', $payload);
        $this->assertArrayHasKey('updatedAt', $payload);
    }

    public function testInvalidPostSubmit()
    {
        $data = json_encode(['title' => 'post test']);

        $client = static::createClient();

        $client->request(
            'POST',
            '/posts',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $data

        );

        $payload = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
        $this->assertArrayHasKey('errors', $payload);
    }

    public function testGetSinglePost()
    {
        $post =  new Post();
        $data = ['title' => 'post test', 'content' => 'content test'];

        $post->setTitle($data['title']);
        $post->setContent($data['content']);

        $em = $this->getEntityManager();
        $em->persist($post);
        $em->flush();

        $client = static::createClient();

        $client->request(
            'GET',
            '/posts/'.$post->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $payload = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertArrayHasKey('id', $payload);
        $this->assertArrayHasKey('title', $payload);
        $this->assertArrayHasKey('content', $payload);
        $this->assertArrayHasKey('createdAt', $payload);
        $this->assertArrayHasKey('updatedAt', $payload);

    }

    public function testNotFoundSinglePost()
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/posts/50000',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $payload = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testPostIndex()
    {
        $arr = [1,2,3,4,5];
        $data = ['title' => 'post test', 'content' => 'content test'];

        $postIds = [];
        
        foreach ($arr as $value) {
            $post =  new Post();
        
            $post->setTitle($data['title']. " $value");
            $post->setContent($data['content']);
    
            $em = $this->getEntityManager();
            $em->persist($post);
            $em->flush();

            array_push($postIds, $post->getId());
        }


        $client = static::createClient();

        $client->request(
            'GET',
            '/posts',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $payload = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $receivedIds = array_map(function($post){
            return $post['id'];
        }, $payload);

        sort($receivedIds);

        $this->assertCount(5, $payload);
        $this->assertEquals($postIds,$receivedIds);
    }

    
    public function testUpdatePost()
    {
        $post =  new Post();
        $data = ['title' => 'post test', 'content' => 'content test'];

        $post->setTitle($data['title']);
        $post->setContent($data['content']);

        $em = $this->getEntityManager();
        $em->persist($post);
        $em->flush();

        $modifiedData = ['title' => 'modified', 'content' => 'modified'];

        $client = static::createClient();

        $client->request(
            'PUT',
            '/posts/'.$post->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($modifiedData)
        );

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        // $this->assertTrue(
        //     $client->getResponse()->headers->contains(
        //         'Content-Type',
        //         'application/json'
        //     )
        // );

    }

    //Todo Delete
    public function testDeletePost()
    {
        $post =  new Post();
        $data = ['title' => 'post test', 'content' => 'content test'];

        $post->setTitle($data['title']);
        $post->setContent($data['content']);

        $em = $this->getEntityManager();
        $em->persist($post);
        $em->flush();

        $client = static::createClient();

        $client->request(
            'DELETE',
            '/posts/'.$post->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->assertEquals(204, $client->getResponse()->getStatusCode());

    }
}