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

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\sound\DoorSound;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Bearing;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Trapdoor extends Transparent{
	public const MASK_UPPER = 0x04;
	public const MASK_OPENED = 0x08;
	public const MASK_SIDE = 0x03;
	public const MASK_SIDE_SOUTH = 2;
	public const MASK_SIDE_NORTH = 3;
	public const MASK_SIDE_EAST = 0;
	public const MASK_SIDE_WEST = 1;

	protected $id = self::TRAPDOOR;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getName() : string{
		return "Wooden Trapdoor";
	}

	public function getHardness() : float{
		return 3;
	}

	protected function recalculateBoundingBox() : ?AxisAlignedBB{

		$damage = $this->getDamage();

		$f = 0.1875;

		if(($damage & self::MASK_UPPER) > 0){
			$bb = new AxisAlignedBB(0, 1 - $f, 0, 1, 1, 1);
		}else{
			$bb = new AxisAlignedBB(0, 0, 0, 1, $f, 1);
		}

		if(($damage & self::MASK_OPENED) > 0){
			$side = $damage & 0x03;
			if($side === self::MASK_SIDE_NORTH){
				$bb->setBounds(0, 0, 1 - $f, 1, 1, 1);
			}elseif($side === self::MASK_SIDE_SOUTH){
				$bb->setBounds(0, 0, 0, 1, 1, $f);
			}elseif($side === self::MASK_SIDE_WEST){
				$bb->setBounds(1 - $f, 0, 0, 1, 1, 1);
			}elseif($side === self::MASK_SIDE_EAST){
				$bb->setBounds(0, 0, 0, $f, 1, 1);
			}
		}

		return $bb;
	}

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		static $directions = [
			Bearing::SOUTH => 2,
			Bearing::WEST => 1,
			Bearing::NORTH => 3,
			Bearing::EAST => 0
		];
		if($player !== null){
			$this->meta = $directions[$player->getDirection()];
		}
		if(($clickVector->y > 0.5 and $face !== Facing::UP) or $face === Facing::DOWN){
			$this->meta |= self::MASK_UPPER; //top half of block
		}

		return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

	public function getVariantBitmask() : int{
		return 0;
	}

	public function onActivate(Item $item, Player $player = null) : bool{
		$this->meta ^= self::MASK_OPENED;
		$this->getLevel()->setBlock($this, $this, true);
		$this->level->addSound(new DoorSound($this));
		return true;
	}

	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}

	public function getFuelTime() : int{
		return 300;
	}
}
