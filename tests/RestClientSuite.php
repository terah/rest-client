<?php declare(strict_types=1);

namespace Terah\RestClient\Test;

use Terah\Assert\Assert;
use Terah\Assert\Tester;
use Terah\Assert\Suite;
use Terah\RestClient\RestClient;

Tester::suite('RestClientSuite')

    ->fixture('restClient', new RestClient('http://jsonplaceholder.typicode.com/'))

    ->test('testRequestPost', function(Suite $suite) {

        /** @var RestClient $restClient */
        $restClient = $suite->getFixture('restClient');

        $response   = $restClient
            ->data(['param1' => 'value1'])
            ->accept('json')
            ->contentType('json')
            ->method('get')
            ->getResponse('posts/1');

        $body       = $response->getBody();
        $status     = $response->getHttpStatusCode();
        $headers    = $response->get('headers');
        $method     = $response->get('method');

        (new Assert($body))->isObject()->propertiesExist(['userId', 'id', 'title', 'body']);
        (new Assert($body->userId))->id();
        (new Assert($body->id))->id();
        (new Assert($body->title))->string()->notEmpty();
        (new Assert($body->body))->string()->notEmpty();

        (new Assert($status))->eq(200);
        (new Assert($headers))->isArray()->notEmpty();
        (new Assert($method))->string()->eq('GET');


    })

    ->test('testSavePost', function(Suite $suite) {

        /** @var RestClient $restClient */
        $restClient = $suite->getFixture('restClient');
        $post       = [
            'userId'    => 1,
            'id'        => 1,
            'title'     => 'This is a new title',
            'body'      => 'This is a new body',
        ];
        $response   = $restClient
            ->data($post)
            ->accept('json')
            ->contentType('json')
            ->method('put')
            ->getResponse('posts/1');

        $body       = $response->getBody();
        $status     = $response->getHttpStatusCode();
        $headers    = $response->get('headers');
        $method     = $response->get('method');

        (new Assert($body))->isObject()->propertiesExist(['userId', 'id', 'title', 'body']);
        (new Assert($body->userId))->id()->eq($post['userId']);
        (new Assert($body->id))->id()->eq($post['id']);
        (new Assert($body->title))->string()->notEmpty()->eq($post['title']);
        (new Assert($body->body))->string()->notEmpty()->eq($post['body']);

        (new Assert($status))->eq(200);
        (new Assert($headers))->isArray()->notEmpty();
        (new Assert($method))->string()->eq('PUT');


    })
    ;
