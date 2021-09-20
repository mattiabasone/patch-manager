<?php

namespace Cypress\PatchManager\Tests\Request;

use Cypress\PatchManager\Exception\InvalidJsonRequestContent;
use Cypress\PatchManager\Exception\MissingOperationNameRequest;
use Cypress\PatchManager\Exception\MissingOperationRequest;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Cypress\PatchManager\Request\Adapter;
use Cypress\PatchManager\Request\Operations;

class OperationsTest extends PatchManagerTestCase
{
    public function test_request_with_invalid_json()
    {
        $this->expectException(InvalidJsonRequestContent::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('{"test": error}');
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }

    public function test_exeception_with_null_request()
    {
        $this->expectException(InvalidJsonRequestContent::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn(null);
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }

    public function test_correct_operations_number_with_one_operation()
    {
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('{"op": "data"}');
        $operations = new Operations($adapter->reveal());
        $this->assertCount(1, $operations->all());
        $op = $operations->all()->get(0);
        $this->assertEquals('data', $op['op']);
    }

    public function test_correct_operations_number_with_multiple_operation()
    {
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('[{"op": "data"},{"op": "data2"}]');
        $operations = new Operations($adapter->reveal());
        $this->assertCount(2, $operations->all());
        $op1 = $operations->all()->get(0);
        $this->assertEquals('data', $op1['op']);
        $op2 = $operations->all()->get(1);
        $this->assertEquals('data2', $op2['op']);
    }

    public function test_exeception_with_empty_request()
    {
        $this->expectException(MissingOperationRequest::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('""');
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }

    public function test_exeception_with_operation_without_op()
    {
        $this->expectException(MissingOperationNameRequest::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('[{"op_wrong": "data"}]');
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }

    public function test_exeception_with_multiple_operation_without_op()
    {
        $this->expectException(MissingOperationNameRequest::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('[{"op": "data"},{"op_wrong": "data"}]');
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }
}
