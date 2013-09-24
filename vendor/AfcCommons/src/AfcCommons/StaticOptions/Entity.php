<?php

namespace AfcCommons\StaticOptions;

class Entity {
    private static $NONE = 0;
	private static $DELETE = 1;
	private static $WRITE = 2;
	private static $WRITE_AND_DELETE = 3;
	private static $READ = 4;
	private static $READ_AND_DELETE = 5;
	private static $READ_AND_WRITE = 6;
	private static $READ_AND_WRITE_AND_DELETE = 7;
	
	public static function checkReadAccess($access_mode = 0) {
		$access_mode = ( int ) $access_mode;
		if ($access_mode == static::$READ || $access_mode == static::$READ_AND_WRITE || $access_mode == static::$READ_AND_DELETE || $access_mode == static::$READ_AND_WRITE_AND_DELETE) {
			return true;
		}
		return false;
	}
	public static function checkWriteAccess($access_mode = 0) {
		$access_mode = ( int ) $access_mode;
		if ($access_mode == static::$WRITE || $access_mode == static::$READ_AND_WRITE || $access_mode == static::$WRITE_AND_DELETE || $access_mode == static::$READ_AND_WRITE_AND_DELETE) {
			return true;
		}
		return false;
	}
	public static function checkDeleteAccess($access_mode = 0) {
		$access_mode = ( int ) $access_mode;
		if ($access_mode == static::$DELETE || $access_mode == static::$READ_AND_DELETE || $access_mode == static::$WRITE_AND_DELETE || $access_mode == static::$READ_AND_WRITE_AND_DELETE) {
			return true;
		}
		return false;
	}
}