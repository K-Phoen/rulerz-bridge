<?php

namespace Symfony\Bridge\RulerZ\Form;

use Symfony\Component\Form\DataTransformerInterface;

class SpecificationToBooleanTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $specificationClass;

    /**
     * @var array
     */
    private $constructorArgs;

    public function __construct($specificationClass, array $constructorArgs = [])
    {
        $this->specificationClass = $specificationClass;
        $this->constructorArgs = $constructorArgs;
    }

    /**
     * Transforms a specification into a boolean.
     *
     * @param \RulerZ\Spec\Specification|null $specification
     *
     * @return bool
     */
    public function transform($specification)
    {
        if ($specification === null) {
            return false;
        }

        return (bool) ((string) $specification);
    }

    /**
     * Transforms a value into a specification.
     *
     * @param bool $boolean
     *
     * @return \RulerZ\Spec\Specification|null
     */
    public function reverseTransform($boolean)
    {
        if (!$boolean) {
            return null;
        }

        $rClass = new \ReflectionClass($this->specificationClass);

        if ($rClass->getConstructor() && $rClass->getConstructor()->getNumberOfParameters() > 0) {
            return $rClass->newInstanceArgs($this->constructorArgs);
        }

        return $rClass->newInstance();
    }
}
