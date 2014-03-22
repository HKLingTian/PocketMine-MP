<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace PocketMine\Block;

use PocketMine\Item\Item;
use PocketMine\Level\Level;
use PocketMine;

class SnowLayer extends Flowable{
	public function __construct($meta = 0){
		parent::__construct(self::SNOW_LAYER, $meta, "Snow Layer");
		$this->isReplaceable = true;
		$this->isSolid = false;
		$this->isFullBlock = false;
		$this->hardness = 0.5;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, PocketMine\Player $player = null){
		$down = $this->getSide(0);
		if($down instanceof Solid){
			$this->level->setBlock($block, $this, true, false, true);

			return true;
		}

		return false;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(0)->getID() === self::AIR){ //Replace with common break method
				$this->level->setBlock($this, new Air(), true, false, true);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}

	public function getDrops(Item $item){
		if($item->isShovel() !== false){
			return array(
				array(Item::SNOWBALL, 0, 1),
			);
		}

		return array();
	}
}