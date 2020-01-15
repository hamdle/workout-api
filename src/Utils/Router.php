<?php
namespace Utils;

use Controllers\Controller;

class Router
{
    private $endpoints;

    public function __construct($endpoints)
    {
        $this->endpoints = $endpoints;
    }

    public function getController($request)
    {
        // TODO (2) make it better.

        // Filter out requests that have no potential endpoint.
        $endpoints = $this->quickFilterEndpoints($request);
        if (empty($endpoints))
        {
            return new Controller('ErrorController', 'get');
        }

        // Now, try to match the request parts with the endpoint parts.
        $requestParts = $request->getPathParts();
        $match = true;
        $uriArgs = [];
        // Run through each endpoint.
        foreach ($endpoints as $key => $value)
        {
            $match = true;
            $uriArgs = [];
            $keyParts = explode('/', $key);
            // Test to see if every uri part matches every endpoint part.
            for ($n = 0; $n < count($requestParts); $n++)
            {
                if ($requestParts[$n] == $keyParts[$n])
                {
                    continue;
                }
                if (intval($requestParts[$n]) === 0)
                {
                    $match = false;
                }
                else
                {
                    $argKey = str_replace(['{', '}'], '', $keyParts[$n]);
                    $uriArgs[$argKey] = intval($requestParts[$n]);
                }
            }
            if ($match)
            {
                $parts = explode('.', $value);
                $class= $parts[0];
                $method = $parts[1];
                $args = [];
                $args['uri'] = $uriArgs;
                $args['data'] = $request->getData();
                $args['post'] = $request->getPost();

                return new Controller($class, $method, $args);
            }
        }

        return new Controller('ErrorController', 'get');
    }

    // Return endpoints with the same number of parts and method
    // as the request
    // @return [$key => $value]
    private function quickFilterEndpoints($request)
    {
        $filteredEndpoints = [];
        $requestParts = $request->getPathParts();

        foreach ($this->endpoints as $uri => $controller) {
            $uriParts = explode('/', $uri);

            $controllerParts = explode('.', $controller);
            $class = $controllerParts[0];
            $method = $controllerParts[1];

            // URI parts length match
            if (count($requestParts) === count($uriParts))
            {
                // Request method match
                if ($method == strtolower($request->getMethod()))
                {
                    $filteredEndpoints[$uri] = $controller;
                }
            }
        }

        return $filteredEndpoints;
    }
}
