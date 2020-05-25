<?php


namespace SociallHouse\prismaFrame\error\runtime;


interface RuntimeErrorCodes
{

	const BAD_RESULT                            = 0x202; //
	const UNKNOWN_CONTROLLER                    = 0x203; // Передано невалидное название контроллера или данного контроллера не существует
	const UNKNOWN_METHOD                        = 0x204; // Передано невалидное название метода или данного метода не существует
	const BAD_METHOD_RUN                        = 0x205; // Не удалось запустить метод
	const BAD_VALIDATION_RESULT                 = 0x207; // В какой-то из аргументов передан невалидный аргумент
	const BAD_INPUT                             = 0x208; // Вводные данные являются невалидными
	const SECURITY                              = 0x20F; // Ошибка, возникшая во время выполнении метода, из-за которой невозможна работа приложения. Вызывается только из неймспейса SociallHouse\prismaFrame\security

}