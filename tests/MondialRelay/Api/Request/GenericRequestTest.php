<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\MondialRelay\Api\Request;

use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\MondialRelay\Api\Request\GenericRequest;

class GenericRequestTest extends TestCase
{
    public function testGetPayloadBeforeSigning(): void
    {
        $payload = ['Pays' => 'FR', 'CP' => '75001'];
        $request = new GenericRequest('BDTEST13', 'SecretKey123', $payload);

        $this->assertSame($payload, $request->getPayload());
    }

    public function testSign(): void
    {
        $payload = [
            'Pays' => 'FR',
            'CP' => '75001',
        ];
        $merchantId = 'BDTEST13';
        $secret = 'SecretKey123';

        $request = new GenericRequest($merchantId, $secret, $payload);
        $request->sign();

        $resultPayload = $request->getPayload();

        $this->assertArrayHasKey('Enseigne', $resultPayload);
        $this->assertSame('BDTEST13', $resultPayload['Enseigne']);
        $this->assertArrayHasKey('Security', $resultPayload);

        $expectedHash = strtoupper(md5('BDTEST13' . 'FR' . '75001' . 'SecretKey123'));
        $this->assertSame($expectedHash, $resultPayload['Security']);
    }
}
