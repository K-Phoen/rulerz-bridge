<?php

namespace Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use RulerZ\Parser\Parser;
use Symfony\Bridge\RulerZ\Validator\Constraints\RuleValidator;
use Symfony\Bridge\RulerZ\Validator\Constraints\ValidRule;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class RuleValidatorTest extends TestCase
{
    /**
     * @dataProvider validRulesProvider
     */
    public function testValidateWithValidRules($rule, Constraint $constraint)
    {
        $context = $this->createMock(ExecutionContext::class);
        $context->expects($this->never())->method('buildViolation');

        $validator = new RuleValidator(new Parser());
        $validator->initialize($context);

        $validator->validate($rule, $constraint);
    }

    /**
     * @dataProvider invalidRulesProvider
     */
    public function testValidateWithInvalidRules($rule, Constraint $constraint)
    {
        $context = $this->createMock(ExecutionContext::class);
        $context
            ->expects($this->once())
            ->method('buildViolation')
            ->willReturn($this->getConstraintViolationBuilderMock());

        $validator = new RuleValidator(new Parser());
        $validator->initialize($context);

        $validator->validate($rule, $constraint);
    }

    public function validRulesProvider(): array
    {
        $simpleRuleConstraint = new ValidRule([
            'allowed_operators' => null, // all
            'allowed_variables' => null, // all
        ]);

        $checkOperatorsConstraint = new ValidRule([
            'allowed_operators' => ['=', 'AND'],
            'allowed_variables' => null, // all
        ]);

        $checkVariablesConstraint = new ValidRule([
            'allowed_operators' => null, // all
            'allowed_variables' => ['foo', 'bar', 'readingTime']
        ]);

        return [
            ['foo = 42', $simpleRuleConstraint],
            ['foo = 42 AND bar = joe(foo)', $simpleRuleConstraint],

            ['foo = 42', $checkOperatorsConstraint],
            ['foo = 42 AND bar = foo', $checkOperatorsConstraint],

            ['foo = 42', $checkVariablesConstraint],
            ['readingTime <= 42', $checkVariablesConstraint],
            ['foo = 42 AND bar = foo', $checkOperatorsConstraint],
        ];
    }

    public function inValidRulesProvider(): array
    {
        $simpleRuleConstraint = new ValidRule([
            'allowed_operators' => null, // all
            'allowed_variables' => null, // all
        ]);

        $checkOperatorsConstraint = new ValidRule([
            'allowed_operators' => ['=', 'AND'],
            'allowed_variables' => null, // all
        ]);

        $checkVariablesConstraint = new ValidRule([
            'allowed_operators' => null, // all
            'allowed_variables' => ['foo', 'bar']
        ]);

        return [
            // syntax errors
            ['foo = 42 AND', $simpleRuleConstraint],
            ['foo = 42 AND bar = joe(foo', $simpleRuleConstraint],

            // invalid operator
            ['foo != 42', $checkOperatorsConstraint],
            ['foo = 42 OR bar = foo', $checkOperatorsConstraint],

            // unknown variable
            ['foo = baz', $checkVariablesConstraint],
            ['foo.bar = 42', $checkVariablesConstraint],
        ];
    }

    private function getConstraintViolationBuilderMock(): ConstraintViolationBuilderInterface
    {
        $mock = $this->createMock(ConstraintViolationBuilderInterface::class);
        $mock->method('setParameter')->willReturnSelf();

        return $mock;
    }
}
