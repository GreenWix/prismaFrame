<?php


namespace GreenWix\prismaFrame\type;


abstract class TypeValidator
{

	/**
	 * Возвращает полный неймспейс класса, который формирует данный валидатор
	 * @return string
	 */
	abstract public function getFullTypeName(): string;

	/**
	 * Возвращает имя класса без неймспейса.
	 * Нужно оно для тех моментов, когда мы детектим тип аргумента по PhpDoc (а там, как известно, редко кто пишет фулл неймспейс)
	 * Основное предназначение - чтобы писать в аргументе функции array, а в PhpDoc юзать Type[]
	 * @return string
	 */
	public function getDocTypeName(): string{
		$name = $this->getFullTypeName(); // \namespace\a\b\c\SomeClass

		$parts = explode("\\", $name);  // [namespace, a, b, c, SomeClass]

		return array_pop($parts); // SomeClass
	}

	abstract public function createAlsoArrayType(): bool;

	abstract public function validateAndGetValue(string $var, array $extraData);

}