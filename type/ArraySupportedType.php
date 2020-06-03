<?php


namespace GreenWix\prismaFrame\type;


use GreenWix\prismaFrame\controller\Checker;
use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;

class ArraySupportedType extends SupportedType{

	private $elementsName;

	public function __construct(string $var = "", array $extraData = [], string $elementsName = ""){
		$this->elementsName = $elementsName;
		parent::__construct($var, $extraData);
	}

	public function getName(): string{
		return $this->elementsName . '[]';
	}

	public function isArrayType(): bool{
		return false;
	}

	/**
	 * @param string $var
	 * @param array $extraData
	 * @return array
	 * @throws RuntimeErrorException
	 */
	public function validate(string $var, array $extraData): array{
		$readyData = [];
		$part = null;
		foreach(explode(",", $var) as $el){
			if(Checker::validateSupportedType($this->elementsName, $el, $part, $extraData, $reason)){
				$readyData[] = $part;
			}else{
				throw RuntimeError::BAD_VALIDATION_RESULT($reason);
			}
		}
		return $readyData;
	}

}