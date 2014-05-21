<?php
class TrueMoneyServiceProviderTest extends TestCase{
    public function testInitialization(){
        $tmn = App::make('truemoney');
        $config = $tmn->config->get('truemoney::config');
        $this->assertSame('TrueMoney\TrueMoney', get_class($tmn), 'assert get class');
        $this->assertSame('itmapp02', $config['app_id'], 'assert app id');
        $this->assertSame('iTrueMartAppService2', $config['app_name'], 'assert app name');
        $this->assertSame('Boh.45f.GQavX+4rzpEUzLHCTGxfce30L2LDuo0SoMNqnE63NE3qhJ7UlS5LEZGHBI5wF4yWVBf1fS3kdG5K9h33kmfBXQ==', $config['token'], 'assert app token');
        $this->assertSame('af42349-bb26-417c-810a-3b65ec3092cf', $config['secret_key'], 'assert app secret key');
        $this->assertSame('testing-return-url', $config['returnURL'], 'assert app return url');
        $this->assertSame('testing-cancel-url', $config['cancelURL'], 'assert app cancel url');
    }
}