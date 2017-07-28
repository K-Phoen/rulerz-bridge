<?php

namespace Symfony\Bridge\RulerZ\Form;

use Symfony\Component\Form\DataTransformerInterface;

class SpecificationToStringTransformer implements DataTransformerInterface
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
     * Transforms a specification into a string.
     *
     * @param \RulerZ\Spec\Specification|null $specification
     *
     * @return string
     */
    public function transform($specification)
    {
        if ($specification === null) {
            return '';
        }

        return (string) $specification;
    }

    /**
     * Transforms a string into a specification.
     *
     * @param string $string
     *
     * @return \RulerZ\Spec\Specification|null
     */
    public function reverseTransform($string)
    {
        if ($string === null) {
            return null;
        }

        $rClass = new \ReflectionClass($this->specificationClass);
        $args = $this->constructorArgs;
        array_unshift($args, $string);

        return $rClass->newInstanceArgs($args);
    }
}
