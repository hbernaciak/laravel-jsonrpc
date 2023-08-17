<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Wwtg99\JsonRpc\Client\JsonRpcClient;
use Wwtg99\JsonRpc\Http\JsonRpcRequest;

/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2017/4/14
 * Time: 14:42
 */
class ClientTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function testRequest()
    {
        $cli = new JsonRpcClient();
        $req1 = new JsonRpcRequest('m1', 1, [1, 2, 3]);
        $b1 = $cli->appendRequest($req1)
            ->getContentBody();
        $this->assertEquals(['jsonrpc' => '2.0', 'method' => 'm1', 'params' => [1, 2, 3], 'id' => 1], $b1);

        $req2 = new JsonRpcRequest('m2', 2, ['a' => 'b']);
        $b2 = $cli->appendRequest($req2)
            ->getContentBody();
        $this->assertEquals([
            ['jsonrpc' => '2.0', 'method' => 'm1', 'params' => [1, 2, 3], 'id' => 1],
            ['jsonrpc' => '2.0', 'method' => 'm2', 'params' => ['a' => 'b'], 'id' => 2],
        ], $b2);
    }
}
