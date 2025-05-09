<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\Yaml\Yaml;

abstract class Controller
{
    public function yamlConvertor(): JsonResponse
    {
        $yamlFilePath = resource_path('swagger/openapi.yaml');
        $yaml = Yaml::parse(file_get_contents($yamlFilePath));
        $json = json_encode($yaml, JSON_PRETTY_PRINT);
        file_put_contents(storage_path('api-docs/api-docs.json'), $json);
        return success('your api docs has been converted');
    }
}
