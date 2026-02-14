<?php

declare(strict_types=1);

if (! function_exists('instantiateClass')) {
    /**
     * @throws \ReflectionException
     */
    function instantiateClass(string $class, int $depth = 0): object
    {
        if ($depth > 6) {
            throw new \RuntimeException("Exceeded max reflection depth while building {$class}");
        }

        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null || $constructor->getNumberOfRequiredParameters() === 0) {
            return $reflection->newInstance();
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $arguments[] = valueForParameter($parameter, $depth + 1);
        }

        return $reflection->newInstanceArgs($arguments);
    }
}

if (! function_exists('valueForParameter')) {
    /**
     * @throws \ReflectionException
     */
    function valueForParameter(\ReflectionParameter $parameter, int $depth): mixed
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        $type = $parameter->getType();

        if ($type === null) {
            return null;
        }

        return valueForType($type, $parameter, $depth);
    }
}

if (! function_exists('valueForType')) {
    /**
     * @throws \ReflectionException
     */
    function valueForType(\ReflectionType $type, \ReflectionParameter $parameter, int $depth): mixed
    {
        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof \ReflectionNamedType && $unionType->getName() === 'null') {
                    continue;
                }

                try {
                    return valueForType($unionType, $parameter, $depth);
                } catch (\Throwable) {
                    continue;
                }
            }

            return null;
        }

        if ($type instanceof \ReflectionIntersectionType) {
            return null;
        }

        if (! $type instanceof \ReflectionNamedType) {
            return null;
        }

        if ($type->isBuiltin()) {
            return match ($type->getName()) {
                'string' => $parameter->getName().'_value',
                'int' => 1,
                'float' => 1.0,
                'bool' => true,
                'array' => match ($parameter->getName()) {
                    'targets' => ['example.com'],
                    'targetTypes', 'target_types' => ['root_domain'],
                    default => [],
                },
                default => null,
            };
        }

        $className = $type->getName();

        if (enum_exists($className)) {
            return $className::cases()[0] ?? null;
        }

        if (! class_exists($className)) {
            return null;
        }

        if (is_subclass_of($className, \DateTimeInterface::class)) {
            if (is_a($className, \Carbon\Carbon::class, true)) {
                return \Carbon\Carbon::now();
            }

            if (is_a($className, \Carbon\CarbonImmutable::class, true)) {
                return \Carbon\CarbonImmutable::now();
            }

            return new \DateTimeImmutable;
        }

        if (is_subclass_of($className, \Spatie\LaravelData\Data::class)) {
            return instantiateClass($className, $depth + 1);
        }

        $classReflection = new \ReflectionClass($className);

        if ($classReflection->isInstantiable()) {
            return instantiateClass($className, $depth + 1);
        }

        return null;
    }
}
