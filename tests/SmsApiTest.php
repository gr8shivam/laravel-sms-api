<?php

namespace Gr8Shivam\SmsApi\Tests;

use SmsApi;
use Gr8Shivam\SmsApi\Exception\InvalidMethodException;

class SmsApiTest extends AbstractTestCase
{
    /** @test */
    public function it_can_send_sms_successfully(): void
    {
        $this->mockSmsGateway(200, 'Message sent');
        
        $response = SmsApi::sendMessage("9999999999", "Test message");
        
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertNotEmpty($response->response());
    }
    
    /** @test */
    public function it_can_send_to_multiple_numbers(): void
    {
        $this->mockSmsGateway(200, 'Messages sent');
        
        $response = SmsApi::sendMessage(
            ["9999999999", "8888888888"], 
            "Test message"
        );
        
        $this->assertEquals(200, $response->getResponseCode());
    }
    
    /** @test */
    public function it_adds_country_code(): void
    {
        $this->mockSmsGateway();
        
        $response = SmsApi::countryCode('1')
            ->sendMessage("5555555555", "Test");
        
        $this->assertEquals(200, $response->getResponseCode());
    }
    
    /** @test */
    public function it_can_use_custom_gateway(): void
    {
        config(['sms-api.custom_gw' => [
            'method' => 'GET',
            'url' => 'http://custom.com',
            'params' => [
                'send_to_param_name' => 'to',
                'msg_param_name' => 'msg',
                'others' => [],
            ],
            'add_code' => false,
        ]]);
        
        $this->mockSmsGateway();
        
        $response = SmsApi::gateway('custom_gw')
            ->sendMessage("9999999999", "Test");
        
        $this->assertEquals(200, $response->getResponseCode());
    }
    
    /** @test */
    public function it_throws_exception_for_invalid_method(): void
    {
        $this->expectException(InvalidMethodException::class);
        
        config(['sms-api.test_gateway.method' => 'PUT']);
        
        SmsApi::sendMessage("9999999999", "Test");
    }
    
    /** @test */
    public function it_can_add_extra_params(): void
    {
        $this->mockSmsGateway();
        
        $response = SmsApi::sendMessage(
            "9999999999", 
            "Test",
            ['custom_param' => 'value']
        );
        
        $this->assertEquals(200, $response->getResponseCode());
    }
    
    /** @test */
    public function it_can_add_extra_headers(): void
    {
        $this->mockSmsGateway();
        
        $response = SmsApi::sendMessage(
            "9999999999", 
            "Test",
            null,
            ['X-Custom-Header' => 'value']
        );
        
        $this->assertEquals(200, $response->getResponseCode());
    }
    
    /** @test */
    public function it_can_use_wrapper_params(): void
    {
        $this->mockSmsGateway();
        
        $response = SmsApi::addWrapperParams(['campaign' => 'test'])
            ->sendMessage("9999999999", "Test");
        
        $this->assertEquals(200, $response->getResponseCode());
    }
}
