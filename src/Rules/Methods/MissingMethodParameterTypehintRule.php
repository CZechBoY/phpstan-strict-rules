<?php declare(strict_types = 1);

namespace PHPStan\Rules\Methods;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;

final class MissingMethodParameterTypehintRule implements \PHPStan\Rules\Rule
{

	/**
	 * @return string Class implementing \PhpParser\Node
	 */
	public function getNodeType(): string
	{
		return \PhpParser\Node\Stmt\ClassMethod::class;
	}

	/**
	 * @param \PhpParser\Node\Stmt\ClassMethod $node
	 * @param \PHPStan\Analyser\Scope $scope
	 *
	 * @return string[] errors
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!$scope->isInClass()) {
			throw new \PHPStan\ShouldNotHappenException();
		}

		$methodReflection = $scope->getClassReflection()->getNativeMethod($node->name->name);

		$messages = [];

		foreach (ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getParameters() as $parameterReflection) {
			$messageT = $this->checkMethodParameterWithoutTypehint($methodReflection, $parameterReflection);
			if ($messageT !== null) {
				$messages[] = $messageT;
				continue;
			}

			$messageA = $this->checkMethodParameterWithoutArrayTypehint($methodReflection, $parameterReflection);
			if ($messageA === null) {
				continue;
			}
			$messages[] = $messageA;
		}

		return $messages;
	}

	private function checkMethodParameterWithoutTypehint(
		MethodReflection $methodReflection,
		ParameterReflection $parameterReflection
	): ?string
	{
		$parameterType = $parameterReflection->getType();

		if ($parameterType instanceof MixedType && !$parameterType->isExplicitMixed()) {
			return sprintf(
				'Method %s::%s() has parameter $%s with no typehint specified.',
				$methodReflection->getDeclaringClass()->getDisplayName(),
				$methodReflection->getName(),
				$parameterReflection->getName()
			);
		}

		return null;
	}

	private function checkMethodParameterWithoutArrayTypehint(
		MethodReflection $methodReflection,
		ParameterReflection $parameterReflection
	): ?string
	{
		$parameterType = $parameterReflection->getType();

		if (!$parameterType instanceof ArrayType) {
			return null;
		}

		$valueType = $parameterType->getIterableValueType();

		if ($valueType instanceof MixedType && !$valueType->isExplicitMixed()) {
			return sprintf(
				'Method %s::%s() has parameter $%s of type array with no value typehint specified.',
				$methodReflection->getDeclaringClass()->getDisplayName(),
				$methodReflection->getName(),
				$parameterReflection->getName()
			);
		}

		return null;
	}

}
