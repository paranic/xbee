<?php

namespace Paranic\Xbee;

class FrameFactory
{
	public function create($frame_type)
	{
		$frame_class = __NAMESPACE__ . '\Frames\\' . $frame_type;

		if (class_exists($frame_class))
		{
			return new $frame_class();
		}
		else
		{
			return 'Frame ' . $frame_type . ' not supported';
		}
	}

	public function parse($frame)
	{
		if (!self::checkFrameStartDelimiter($frame))
		{
			throw new \Exception('Frame has invalid start delimiter.');
		}

		if (!self::checkFrameLength($frame))
		{
			throw new \Exception('Frame has invalid length.');
		}

		if (!self::checkFrameLength($frame))
		{
			throw new \Exception('Frame has not supported type.');
		}

		$supported_frames = self::getSupportedFrames();
		$frame_object = new $supported_frames[self::getFrameType($frame)]();
		$frame_object->parse($frame);

		return $frame_object;
	}

	static function getSupportedFrames()
	{
		return [
			'10' => 'Paranic\Xbee\Frames\TransmitRequest',
			'90' => 'Paranic\Xbee\Frames\ReceivePacket'
		];
	}

	static function getPayloadLength($frame)
	{
		$chars = str_split($frame);

		if (count($chars) > 3)
		{
			$payload_length = $chars[1] . $chars[2];
			$payload_length = unpack('H*payload_length', $payload_length);

			return hexdec($payload_length['payload_length']);
		}

		return false;
	}

	static function getFrameType($frame)
	{
		$chars = str_split($frame);

		$frame_type = unpack('H*frame_type', $chars[3]);

		return $frame_type['frame_type'];
	}

	static function checkFrameStartDelimiter($frame)
	{
		$chars = str_split($frame);

		$start_delimiter = unpack('H*start_delimiter', $chars[0]);

		if ($start_delimiter['start_delimiter'] == '7e')
		{
			return true;
		}

		return false;
	}

	static function checkFrameLength($frame)
	{
		$chars = str_split($frame);

		if (count($chars) == self::getPayloadLength($frame) + 4)
		{
			return true;
		}

		return false;
	}

	static function checkFrameType($frame)
	{
		if (array_key_exists(self::getFrameType($frame), self::getSupportedFrames()))
		{
			return true;
		}

		return false;
	}
}
