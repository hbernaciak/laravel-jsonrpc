<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2017/4/14
 * Time: 14:03
 */

namespace Tests;

use PHPUnit\Framework\TestCase;
use Wwtg99\JsonRpc\Http\JsonRpcRequest;
use Wwtg99\JsonRpc\Server\ProcessHandler;

/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2017/4/14
 * Time: 14:03
 */
class ServerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once 'BindingTest.php';
    }

    public function testBinding()
    {
        $ph = new ProcessHandler();
        $ph->bind('m1', function ($request) {
            return [1, 2, 3];
        });
        $ph->bind('m2', 'Tests\BindingTest@test1');
        $req1 = new JsonRpcRequest('m1', 1, []);
        $res1 = $ph->execute($req1)
            ->getResponse();
        self::assertEquals(1, $res1->getId());
        self::assertEquals([1, 2, 3], $res1->getResult());
        self::assertEquals(['jsonrpc' => '2.0', 'id' => 1, 'result' => [1, 2, 3]], $res1->toArray());
        self::assertEquals(['jsonrpc' => '2.0', 'id' => 1, 'result' => [1, 2, 3]], $ph->getResponseArray());
        $req2 = new JsonRpcRequest('m2', 2, []);
        $res2 = $ph->execute($req2)
            ->getResponse();
        self::assertEquals(2, $res2->getId());
        self::assertEquals('test1', $res2->getResult());
        $req3 = new JsonRpcRequest('m3', 3, []);
        $res3 = $ph->execute($req3)
            ->getResponse();
        self::assertEquals(3, $res3->getId());
        self::assertEquals(-32601, $res3->getError()->getCode());
        $res4 = $ph->execute([$req1, $req2, $req3])
            ->getResponse();
        self::assertCount(3, $res4);
        self::assertEquals([
            ['jsonrpc' => '2.0', 'id' => 1, 'result' => [1, 2, 3]],
            ['jsonrpc' => '2.0', 'id' => 2, 'result' => 'test1'],
            ['jsonrpc' => '2.0', 'id' => 3, 'error' => ['code' => -32601, 'message' => 'Method not found']],
        ], $ph->getResponseArray());
    }
}
