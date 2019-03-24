<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

    class HealthcheckControllerTest extends WebTestCase 
    {
        public function testHealthcheck()
        {
            $client = static::createClient();

            $client->request(
                'GET',
                '/healthcheck',
                [],
                [],
                [
                    'CONTENT_TYPE' => 'application/json',
                ]
            );

            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertTrue(
                $client->getResponse()->headers->contains(
                    'Content-Type',
                    'application/json'
                )
            );

            $payload = $client->getResponse()->getContent();

            $this->assertContains('api',$payload);
        }
    }
?>