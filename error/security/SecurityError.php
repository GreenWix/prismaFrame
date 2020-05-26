<?php


namespace SociallHouse\prismaFrame\error\security;


use SociallHouse\prismaFrame\error\HTTPCodes;

final class SecurityError
{

	private function __construct(){}

	public static function SUSPICIOUS_INPUT(string $message): SecurityErrorException{
		return new SecurityErrorException(SecurityErrorCodes::SUSPICIOUS_INPUT, $message, HTTPCodes::INTERNAL_SERVER_ERROR);
	}

	public static function INTERNAL_EXCEPTION(string $message): SecurityErrorException{
		return new SecurityErrorException(SecurityErrorCodes::INTERNAL_EXCEPTION, $message, HTTPCodes::INTERNAL_SERVER_ERROR);
	}

	public static function SUSPICIOUS_OUTPUT(string $message): SecurityErrorException{
		return new SecurityErrorException(SecurityErrorCodes::SUSPICIOUS_OUTPUT, $message, HTTPCodes::INTERNAL_SERVER_ERROR);
	}

	public static function CUSTOM_SECURITY_ISSUE(string $message): SecurityErrorException{
		return new SecurityErrorException(SecurityErrorCodes::CUSTOM_SECURITY_ISSUE, $message, HTTPCodes::INTERNAL_SERVER_ERROR);
	}

}