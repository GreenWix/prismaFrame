<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\error;


// todo переделать когда появятся енамы
interface ErrorCodes {

	const UNKNOWN_CONTROLLER = 0x201; // Передано невалидное название контроллера или данного контроллера не существует
	const UNKNOWN_METHOD = 0x202; // Передано невалидное название метода или данного метода не существует
	const BAD_VALIDATION_RESULT = 0x203; // В какой-то из аргументов передан невалидный аргумент
	const BAD_INPUT = 0x204; // Вводные данные являются невалидными
	const WRONG_HTTP_METHOD = 0x205; // Данный метод не поддерживает HTTP метод, используемый в запросе
	const WRONG_VERSION = 0x206; // Данная версия API не поддерживается

	const INTERNAL_ERROR = 0x500; // внутренняя ошибка

}