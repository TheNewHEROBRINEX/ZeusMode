<?php

namespace TheNewHEROBRINE\ZeusMode;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

    /*
        ZeusMode by TheNewHEROBRINE (kik: TheNewHEROBRINEX)

		88888888888 888               888b    888                        
    		888     888               8888b   888                        
    		888     888               88888b  888                        
    		888     88888b.   .d88b.  888Y88b 888  .d88b.  888  888  888 
    		888     888 "88b d8P  Y8b 888 Y88b888 d8P  Y8b 888  888  888 
    		888     888  888 88888888 888  Y88888 88888888 888  888  888 
    		888     888  888 Y8b.     888   Y8888 Y8b.     Y88b 888 d88P 
    		888     888  888  "Y8888  888    Y888  "Y8888   "Y8888888P"  
    	-------------------------------------------------------------------------------------------------
		888    888 8888888888 8888888b.   .d88888b.  888888b.   8888888b.  8888888 888b    888 8888888888 
		888    888 888        888   Y88b d88P" "Y88b 888  "88b  888   Y88b   888   8888b   888 888        
		888    888 888        888    888 888     888 888  .88P  888    888   888   88888b  888 888        
		8888888888 8888888    888   d88P 888     888 8888888K.  888   d88P   888   888Y88b 888 8888888    
		888    888 888        8888888P"  888     888 888  "Y88b 8888888P"    888   888 Y88b888 888        
		888    888 888        888 T88b   888     888 888    888 888 T88b     888   888  Y88888 888        
		888    888 888        888  T88b  Y88b. .d88P 888   d88P 888  T88b    888   888   Y8888 888        
		888    888 8888888888 888   T88b  "Y88888P"  8888888P"  888   T88b 8888888 888    Y888 8888888888 
                                                                                                                                                                                            
    */

	public $tasks = array();

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		switch($cmd->getName()){

			case 'zeusmode':
				if (!$sender instanceof Player) {
					$sender->sendMessage("§cThis command only works in-game!");
					return true;
				}

				if (!isset($args[0])) {
					$sender->sendMessage("§cUSAGE: /ZeusMode <on>|<off> [ticks]");
					return true;
				}

				if ($args[0] == "on") {
					if (!isset($this->tasks[$sender->getName()])) {
						$this->tasks[$sender->getName()] = $this->getServer()->getScheduler()->scheduleRepeatingTask(new ZeusModeTask($this, $sender), (isset($args[1]) ? $args[1] : 20))->getTaskId();
					} else
						$sender->sendMessage("§cZuesMode is already on!");
				}

				elseif ($args[0] == "off") {
					$idkey = isset($this->tasks[$sender->getName()]) ? $this->tasks[$sender->getName()] : null;
					if($idkey){
						$this->getServer()->getScheduler()->cancelTask($this->tasks[$sender->getName()]);
						unset($this->tasks[$sender->getName()]);
					} else
						$sender->sendMessage("§cZeusMode is not on!");
				}
				break;

			case 'lightning':
				switch (count($args)) {
					case '0':
						if (!$sender instanceof Player) {
							$sender->sendMessage("§cUSAGE: /lightning <player>|<x> <y> <z>");
							return true;
						}
						$this->strikeLightning($sender->getPosition());
						break;

					case '1':
						$target = $this->getServer()->getPlayer($args[0]);

						if(!$target){
							$sender->sendMessage("§cPlayer " . $args[0] . "not found");
							return true;
						}
						$this->strikeLightning($target->getPosition());
						break;
				}
		}
		return true;
	}

	public function onProjectileShoot(ProjectileHitEvent $e){
		$projectile = $e->getEntity();
		$shooter = $projectile->shootingEntity;
		if($shooter instanceof Player)
			if($shooter->hasPermission("zeusmode.projectile")) {
				$this->strikeLightning($projectile->getPosition());
			}
	}

	public function strikeLightning(Position $pos){
		$pk = New AddEntityPacket();
		$pk->type = 93;
		$pk->eid = Entity::$entityCount++;
		$pk->metadata = array();
		$pk->speedX = 0;
		$pk->speedY = 0;
        $pk->speedZ = 0;
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
        foreach($pos->getLevel()->getPlayers() as $p){
        	$p->dataPacket($pk);
        }
	}
}