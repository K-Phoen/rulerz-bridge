<?php

namespace Symfony\Bridge\RulerZ\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class SpecTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $this->setDefaultOptions($resolver);
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'spec_transformer' => 'string', // string, array, boolean
            'spec_class' => null,
            'spec_options' => [],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['spec_class'] === null) {
            return;
        }

        $specOptions = array_merge(['field' => $builder->getName()], $options['spec_options']);
        $transformer = $this->getTransformer($options['spec_transformer'], $options['spec_class'],  $specOptions);

        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }

    private function getTransformer($transformer, $specClass, array $specOptions)
    {
        if ($transformer === 'string') {
            return new SpecificationToStringTransformer($specClass, $specOptions);
        }

        if ($transformer === 'boolean') {
            return new SpecificationToBooleanTransformer($specClass, $specOptions);
        }

        if ($transformer === 'array') {
            // TODO
            //return new SpecificationToArrayTransformer($specClass, ',', $specOptions);
        }

        throw new \RuntimeException('Not implemented: '.$transformer);
    }
}
