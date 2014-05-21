<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{

    private $_app_pkey = '45311375168544';
    private $_url = NULL;

    /**
     * Creates the application.
     *
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__ . '/../../bootstrap/start.php';
    }

    public function assertStatusCode($expected_code = 200, $expected_status = 'success', $response = NULL, $debug = FALSE, $uri = NULL, $param = array())
    {

        if (empty($response)) {
            $this->fail('Curl Return Empty');
        } else {

            $response = json_decode($response->getContent());
            if ($debug === TRUE) {

                echo PHP_EOL . PHP_EOL . '-------------------------------' . PHP_EOL;
                echo '####### Debug ######' . PHP_EOL;
                echo '-------------------------------' . PHP_EOL;
                echo 'url => ' . $this->makeRequestUrl($uri) . PHP_EOL;
                echo 'params => ' . PHP_EOL;
                print_r($param);
                echo 'response => ' . PHP_EOL;
                print_r($response);
                echo '-------------------------------' . PHP_EOL . PHP_EOL;

            }

            $test_result = isset($response->status) ? $response->status : NULL;

            $this->assertTrue($expected_code == $response->code,
                'Expected Result is \'' . $expected_status . '\' but API return \'' . $test_result . '\'' . PHP_EOL . 'code = ' . $response->code . ',' . PHP_EOL . 'message = ' . $response->message . PHP_EOL . PHP_EOL);
        }

    }

    public function makeRequestUrl($uri)
    {
        return  '/api/' . $this->_app_pkey . '/' . $uri;
    }
}
