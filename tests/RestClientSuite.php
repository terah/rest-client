<?php declare(strict_types=1);

namespace Terah\RestClient\Test;

use Terah\Asrt\Asrt;
use Terah\Tester\Tester;
use Terah\Tester\Suite;
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

        Asrt::that($body)->isObject()->propertiesExist(['userId', 'id', 'title', 'body']);
        Asrt::that($body->userId)->id();
        Asrt::that($body->id)->id();
        Asrt::that($body->title)->isString()->notEmpty();
        Asrt::that($body->body)->isString()->notEmpty();

        Asrt::that($status)->eq(200);
        Asrt::that($headers)->isArray()->notEmpty();
        Asrt::that($method)->isString()->eq('GET');


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

        Asrt::that($body)->isObject()->propertiesExist(['userId', 'id', 'title', 'body']);
        Asrt::that($body->userId)->id()->eq($post['userId']);
        Asrt::that($body->id)->id()->eq($post['id']);
        Asrt::that($body->title)->isString()->notEmpty()->eq($post['title']);
        Asrt::that($body->body)->isString()->notEmpty()->eq($post['body']);

        Asrt::that($status)->eq(200);
        Asrt::that($headers)->isArray()->notEmpty();
        Asrt::that($method)->isString()->eq('PUT');


    })
    ;
