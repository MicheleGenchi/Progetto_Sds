<?php

namespace App\Traits;

use Symfony\Component\Validator\Validation;

trait GetWithBodyTrait
{
    /**
     * Call the given URI with a JSON request.
     *
     * @param  string  $method
     * @param  string  $uri
     * @param  array  $data
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Testing\TestResponse
     */
    public abstract function json($method, $uri, array $data = [], array $headers = [], $options = 0);
    /**
     * chiamata get con parametri nel body
     *
     * @param  string  $uri
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Testing\TestResponse
     */
    public function getWithBody($uri, array $data = [], array $headers = [], $options = 0)
    {
        return $this->json('GET', $uri, $data, $headers, $options);
    }
}
