<?php


namespace GreenWix\prismaFrame\type;


use GreenWix\prismaFrame\controller\ControllerChecker;
use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;

class TypedArrayTypeValidator extends TypeValidator{

	/** @var string  */
	private $typeName;

	/** @var TypeManager */
	private $typeManager;

	public function __construct(string $typeName, TypeManager $manager){
		$this->typeName = $typeName;
		$this->typeManager = $manager;
	}

	public function getFullTypeName(): string{
		return $this->typeName . '[]';
	}

	public function createAlsoArrayType(): bool{
		return false;
	}

	/**
	 * @param string $var
	 * @param array $extraData
	 * @return array
	 * @throws RuntimeErrorException
	 */
	public function validateAndGetValue(string $var, array $extraData): array{
		$result = [];
		$elements = explode(",", $var);

		foreach($elements as $element){
			$value = $this->typeManager->validateTypedInput($this->typeName, $element, $extraData);

			$result[] = $value;
		}
		return $result;
	}

}