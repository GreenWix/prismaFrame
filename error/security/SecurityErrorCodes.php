<?php


namespace SociallHouse\prismaFrame\error\security;


interface SecurityErrorCodes
{

	const SUSPICIOUS_INPUT = 0x01; // Подозрительные вводимые данные (например, если заподозрена попытка SQLi)
	const INTERNAL_EXCEPTION = 0x02; // Если откуда-то был выброшен не PrismaException, а другой, неизвестный вид исключений
	const SUSPICIOUS_OUTPUT = 0x03; // Подозрительный вывод (например, если результат не похож на тот, каким должен быть, например если кто-то найдет уязвимость и попытается скачать что-то, к чему не должен иметь доступ)
	const CUSTOM_SECURITY_ISSUE = 0x0F; // Используется только

}