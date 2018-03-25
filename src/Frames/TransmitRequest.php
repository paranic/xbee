<?php

namespace Paranic\Xbee\Frames;

use Paranic\Xbee\FrameAbstract;

/**
 * Transmit Request frame - 0x10
 *
 * This frame causes the device to send payload data as an RF packet to a specific destination.
 * - For broadcast transmissions, set the 64-bit destination address to 0x000000000000FFFF .
 * - For unicast transmissions, set the 64 bit address field to the address of the desired destination node.
 * - Set the reserved field to 0xFFFE.
 * - Query the NP command to read the maximum number of payload bytes.
 */
class TransmitRequest extends FrameAbstract
{
	// Frame type: 0x10
	// Offset: 3
	public $frame_type = '10';
	
	// Frame ID:
	// Offset: 4
	// Identifies the data frame for the host to correlate with a subsequent ACK.
	// If set to 0, the device does not send a response.
	public $frame_id = '01';
	
	// 64-bit destination address:
	// Offset: 5-12
	// MSB first, LSB last. Set to the 64-bit address of the destination device.
	// Broadcast = 0x000000000000FFFF
	public $destination_64bit = '0000000000000000';
	
	// Reserved: Set to 0xFFFE.
	// Offset: 13-14
	public $destination_16bit = 'fffe';

	// Broadcast radius:
	// Offset: 15
	// Sets the maximum number of hops a broadcast transmission can occur.
	// If set to 0, the broadcast radius is set to the maximum hops value.
	public $broadcast_radius = '00';
	
	// Transmit options:
	// Offset: 16
	// 0x01 = Disable ACK
	// 0x02 = Disable network address discovery
	// 0x04 = Generate trace route frames for each hop of all transmitted packets
	// 0x08 = Generate NACK frames (which look identical to trace route frames) on
	// transmitted packets for any hop that fails along the path.
	// No trace route frames are generated for successful hops.
	public $options = '00';

	// RF data:
	// Offset: 17-n
	// Up to NP bytes per packet. Sent to the destination device.
	public $rf_data;

	public function __construct()
	{

	}

	/**
	 * Returns the actual payload of the packet
	 *
	 * Payload packet minus the first hex digit (start_delimiter), second and third hex digit (length), and the last hex digit (checksum)
	 *
	 * @return string
	 */
	protected function get_payload()
	{
		return $this->frame_type . $this->frame_id . $this->destination_64bit . $this->destination_16bit . $this->broadcast_radius . $this->options . $this->rf_data;
	}

}
