<?php

namespace Paranic\Xbee;

abstract class FrameAbstract
{
	// Start delimiter: 0x7e
	// Byte: 1
	public $start_delimiter = '7e';

	// Length: Most Significant Byte, Least Significant Byte
	// Byte: 2-3
	public $length;

	// Checksum: 1 byte
	// Byte n+1
	public $checksum;

	/**
	 * Calculate length and checksum and returns the final packet
	 *
	 * @return string
	 */
	public function generate_packet()
	{
		$this->length = $this->get_length();

		$this->checksum = $this->get_checksum();

		return $this->start_delimiter . $this->length . $this->get_payload() . $this->checksum;
	}

	/**
	 * Calculate payload length and returns the value in 2 hex digits
	 *
	 * @return string
	 */
	private function get_length()
	{
		$payload_in_hex = pack('H*', $this->get_payload());

		return sprintf('%04X', strlen($payload_in_hex));
	}

	/**
	 * Calculate frame checksum
	 *
	 * @return string
	 */
	private function get_checksum()
	{
		/* convert payload to hex in order to walk every digit */
		$payload_in_hex = pack('H*', $this->get_payload());

		$sum = 0;
		for ($i=0; $i<strlen($payload_in_hex); $i++)
		{
			/* convert hex digit to dec to make integer calculations */
			$hex_digit_in_dec = unpack('C*', $payload_in_hex[$i])[1];
			$sum += $hex_digit_in_dec;
		}

		/* keep the last 2 digits of sum and add 0xFF */
		$checksum = 0xFF - ($sum & 0xFF);

		$checksum_hex = base_convert($checksum, 10, 16);

		return str_pad($checksum_hex, 2, '0', STR_PAD_LEFT);
	}
}
