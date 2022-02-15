<?php


namespace GreenWix\prismaFrame\controller;


use GreenWix\prismaFrame\error\runtime\RuntimeError;
use GreenWix\prismaFrame\error\runtime\RuntimeErrorException;
use GreenWix\prismaFrame\PrismaFrame;
use Throwable;

class MethodParameter
{

	/** @var bool */
	public $required = false;

	/** @var string */
	public $name;

	/** @var string[] */
	public $types;

	/** @var string */
	public $flatTypes;

	/** @var string[] */
	public $extraData;

	/** @var PrismaFrame */
	private $prismaFrame;

	/**
	 * ControllerParameter constructor.
	 * @param string $name
	 * @param string[] $types
	 * @param array $extraData
	 * @param bool $required
	 */
	public function __construct(PrismaFrame $prismaFrame, string $name, array $types, array $extraData, bool $required)
	{
		$this->name = $name;
		$this->types = $types;
		$this->extraData = $extraData;
		$this->required = $required;
		$this->flatTypes = implode("|", $types);

		$this->prismaFrame = $prismaFrame;
	}

	/**
	 * @param string $input
	 * @return mixed
	 * @throws RuntimeErrorException
	 */
	public function validateAndGetValue(string $input){
		$reasons = [];
		$typeManager = $this->prismaFrame->getTypeManager();

		foreach ($this->types as $type){
			try {
				return $typeManager->validateSupportedType($type, $input, $this->extraData);
			}catch(Throwable $e){
				$reasons[] = $type . ": " . $e->getMessage();
			}
		}

		throw RuntimeError::BAD_VALIDATION_RESULT("");
	}

}