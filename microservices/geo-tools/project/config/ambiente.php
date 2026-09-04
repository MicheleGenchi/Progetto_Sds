<?php 
    $path=function($env):string {
        return match ($env) {
                "testing" => "./resources/json/postalCode.json",
                "develop" => "./../resources/json/postalCode.json",
                default => "./../resources/json/postalCode.json"
        };
    };

    return array(
        'path_resources' => $path(getEnv("APP_ENV"))
    );