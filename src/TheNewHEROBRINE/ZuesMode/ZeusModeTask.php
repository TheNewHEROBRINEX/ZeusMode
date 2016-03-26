<?php

namespace TheNewHEROBRINE\ZeusMode;

use pocketmine\scheduler\PluginTask;
use pocketmine\Player;

class ZeusModeTask extends PluginTask{
    
    private $player, $plugin;
    
	public function __construct(Main $plugin, Player $player){
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->player = $player;
	}

    public function onRun($tick){
        if($this->plugin->getServer()->getPlayer($this->player->getName()) !== null){
            $this->plugin->strikeLightning($this->player->getPosition());
        } else{
            $this->plugin->getServer()->getScheduler()->cancelTask($this->plugin->tasks[$this->player->getName()]);
            unset($this->plugin->tasks[$this->player->getName()]);
        }
    }
}
