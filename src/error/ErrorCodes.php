<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\error;


// todo переделать когда появятся енамы
interface ErrorCodes {

	const INTERNAL_ERROR = 0; // внутренняя ошибка

	const UNKNOWN_CONTROLLER = 1; // Передано невалидное название контроллера или данного контроллера не существует
	const UNKNOWN_METHOD = 2; // Передано невалидное название метода или данного метода не существует
	const BAD_VALIDATION_RESULT = 3; // В какой-то из аргументов передан невалидный аргумент
	const BAD_INPUT = 4; // Вводные данные являются невалидными
	const WRONG_HTTP_METHOD = 5; // Данный метод не поддерживает HTTP метод, используемый в запросе
	const WRONG_VERSION = 6; // Данная версия API не поддерживается

}