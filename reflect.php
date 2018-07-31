<?php

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Roave\BetterReflection\BetterReflection;

require_once __DIR__.'/vendor/autoload.php';

$factory = new LazyLoadingValueHolderFactory();
$reflector = (new BetterReflection())->classReflector();

$classes = [
    \Redis::class => [
        function (& $wrappedObject, $proxy, $method, $parameters, & $initializer) {
            $wrappedObject = new \Redis();
            $wrappedObject->connect('redis');
            $initializer   = null;
        },
        ['__construct', '__destruct', 'connect', 'close'],
        [
            'blPop' => [
                [null, 1],
            ],
            'brPop' => [
                [null, 1],
            ],
        ]
    ],
];

foreach ($classes as $class => [0 => $init, 1 => $skip, 2 => $callParams]) {
    $proxy = $factory->createProxy($class, $init);
    $classReflection = $reflector->reflect($class);

    $methods = $classReflection->getMethods(\ReflectionMethod::IS_PUBLIC);
    foreach ($methods as $method) {
        $name = $method->getName();
        if (in_array($name, $skip)) {
            continue;
        }
        echo $method;

        $paramSets = $callParams[$name] ?? [];

        if ($paramSets) {
            foreach ($paramSets as $paramSet) {
                call_user_func_array([$proxy, $name], $paramSet);
            }
        } else {
            $all = [];
            $req = [];

            foreach ($method->getParameters() as $parameter) {
                if ($parameter->isArray()) {
                    $v = [];
                } else {
                    $v = null;
                }

                $all[] = $v;
                if (!$parameter->isOptional()) {
                    $req[] = $v;
                }
            }
            
            call_user_func_array([$proxy, $name], $all);
            call_user_func_array([$proxy, $name], $req);
        }
    }
}
