<?php

namespace Paranic\Xbee\Frames;

use Paranic\Xbee\FrameAbstract;

/**
 * Receive Packet frame - 0x90
 *
 * When a device configured with a standard API Rx Indicator (AO = 0) receives an RF data packet,
 * it sends it out the serial interface using this message type.
 */
class ReceivePacket extends FrameAbstract
{
	// Frame type: 0x10
	// Offset: 3
	public $frame_type = '90';

	// 64-bit source address:
	// Offset: 4-11
	// The sender's 64-bit address. MSB first, LSB last.
	public $source_64bit = 'ffffffffffffffff';

	// Reserved: Reserved.
	// Offset: 12-13
	public $source_16bit = 'fffe';

	// Receive options: 
	// Offset: 14
	// 0x01 = Packet acknowledged
	// 0x02 = Packet was a broadcast packet
	public $recieve_options = '00';

	// Received data:
	// Offset: 15-n
	// The RF data the device receives.
	public $rf_data;

	public function __construct()
	{

	}

	/**
	 * Parses binary frame data and sets instance properties
	 *
	 * @return void
	 */
	public function parse($frame)
	{
		$chars = str_split($frame);

		$start_delimiter = array_slice($chars, 0, 1);
		$length = array_slice($chars, 1, 2);
		$source_64bit = array_slice($chars, 4, 8);
		$source_16bit = array_slice($chars, 12, 2);
		$recieve_options = array_slice($chars, 14, 1);
		$rf_data = array_slice($chars, 15, (count($chars)-16));
		$checksum = array_slice($chars, -1, 1);

		$this->start_delimiter = unpack('H*', implode('', $start_delimiter))[1];
		$this->length = unpack('H*', implode('', $length))[1];
		$this->source_64bit = unpack('H*', implode('', $source_64bit))[1];
		$this->source_16bit = unpack('H*', implode('', $source_16bit))[1];
		$this->recieve_options = unpack('H*', implode('', $recieve_options))[1];
		$this->rf_data = unpack('H*', implode('', $rf_data))[1];
		$this->checksum = unpack('H*', implode('', $checksum))[1];
	}
}
