<?php


namespace GreenWix\prismaFrame\type;


class TypedArrayTypeValidator extends TypeValidator {

	/** @var string */
	private $typeName;

	/** @var TypeManager */
	private $typeManager;

	public function __construct(string $typeName, TypeManager $manager) {
		$this->typeName = $typeName;
		$this->typeManager = $manager;
	}

	public function getFullTypeName(): string {
		return $this->typeName . '[]';
	}

	public function createAlsoArrayType(): bool {
		return false;
	}

	/**
	 * @param string $input
	 * @param array $extraData
	 * @return array
	 * @throws TypeManagerException
	 */
	public function validateAndGetValue(string $input, array $extraData): array {
		$result = [];
		$elements = explode(",", $input);

		foreach ($elements as $element) {
			$value = $this->typeManager->validateTypedInput($this->typeName, $element, $extraData);

			$result[] = $value;
		}
		return $result;
	}

}