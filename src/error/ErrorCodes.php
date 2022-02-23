<?php

declare(strict_types=1);


namespace GreenWix\prismaFrame\error;


// todo переделать когда появятся енамы
interface ErrorCodes {

	const BAD_RESULT                            = 0x202; //
	const UNKNOWN_CONTROLLER                    = 0x203; // Передано невалидное название контроллера или данного контроллера не существует
	const UNKNOWN_METHOD                        = 0x204; // Передано невалидное название метода или данного метода не существует
	const BAD_METHOD_RUN                        = 0x205; // Не удалось запустить метод
	const BAD_VALIDATION_RESULT                 = 0x207; // В какой-то из аргументов передан невалидный аргумент
	const BAD_INPUT                             = 0x208; // Вводные данные являются невалидными
	const UNKNOWN_PARAMETER_TYPE                = 0x209; //
	const WRONG_HTTP_METHOD                     = 0x20A; // Данный метод не поддерживает HTTP метод, используемый в запросе
	const WRONG_VERSION                         = 0x20A; // Данная версия API не поддерживается

}