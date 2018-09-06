<?php declare(strict_types = 1);

namespace PHPStan\Rules\Methods;

class MissingMethodParameterTypehintRuleTest extends \PHPStan\Testing\RuleTestCase
{

	protected function getRule(): \PHPStan\Rules\Rule
	{
		return new MissingMethodParameterTypehintRule();
	}

	public function testRuleTypehint(): void
	{
		$this->analyse([__DIR__ . '/data/missing-method-parameter-typehint.php'], [
			[
				'Method MissingMethodParameterTypehint\FooInterface::getFoo() has parameter $p1 with no typehint specified.',
				8,
			],
			[
				'Method MissingMethodParameterTypehint\FooParent::getBar() has parameter $p2 with no typehint specified.',
				15,
			],
			[
				'Method MissingMethodParameterTypehint\Foo::getFoo() has parameter $p1 with no typehint specified.',
				25,
			],
			[
				'Method MissingMethodParameterTypehint\Foo::getBar() has parameter $p2 with no typehint specified.',
				33,
			],
			[
				'Method MissingMethodParameterTypehint\Foo::getBaz() has parameter $p3 with no typehint specified.',
				42,
			],
		]);
	}

	public function testRuleArrayTypehint(): void
	{
		$this->analyse([__DIR__ . '/data/missing-array-types-in-method-parameter-typehint.php'], [
			/* not detected by now
			[
				'Method MissingArrayValueTypeInMethodParameter\FooInterface::getFoo() has parameter $p1 of type array with no value typehint specified.',
				11,
			],
			*/
			[
				'Method MissingArrayValueTypeInMethodParameter\FooParent::getBar() has parameter $p2 of type array with no value typehint specified.',
				18,
			],
			[
				'Method MissingArrayValueTypeInMethodParameter\Foo::getBar() has parameter $p2 of type array with no value typehint specified.',
				39,
			],
			[
				'Method MissingArrayValueTypeInMethodParameter\Foo::getBaz() has parameter $p4 with no typehint specified.',
				47,
			],
		]);
	}

}
