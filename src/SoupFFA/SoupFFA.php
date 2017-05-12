<?php

namespace SoupFFA;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\CommandMap;
use pocketmine\utils\{Textformat as C, Config};
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\tile\Sign;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\SignChangeEvent;

class SoupFFA extends PluginBase implements Listener{
	
	public $prefix = C::GRAY."[".C::GREEN."SoupFFA".C::GRAY."]";

	public function onEnable(){
		$this->getLogger()->info($this->prefix . " by McpeBooster!");
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		@mkdir($this->getDataFolder());
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);    
		if(empty($config->get("arena"))) {
			$config->set("arena", "debug123");
			$config->save();
		}
		if($config->get("arena") == "debug123"){
			$plugin = $this->getServer()->getPluginManager()->getPlugin("SoupFFA");
			$this->getLogger()->emergency("###############################################");
			$this->getLogger()->emergency(" Please change the SoupFFA world in the config.yml!!!");
			$this->getLogger()->emergency("###############################################");
			$this->getServer()->getPluginManager()->disablePlugin($plugin);
		}
		
		$this->getServer()->loadLevel($config->get("arena"));
		
	}
	
	#Events
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$item = $event->getItem();
		$block = $event->getBlock();
		if($item->getId() === Item::MUSHROOM_STEW){
			$player->getInventory()->removeItem($item);
			$player->setHealth($player->getHealth() + 5);
			$player->sendTip("§cYou are healed!");
			return;
		}elseif($player->getLevel()->getTile($block) instanceof Sign) {
			$tile = $player->getLevel()->getTile($block);
			$text = $tile->getText();
			if ($text[0] == $this->prefix) {
				if($text[2] == "§2Join"){
					$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);    
					$arenaname = $config->get("arena");
					$arenalevel = $this->getServer()->getLevelByName($arenaname);
					$arenaspawn = $arenalevel->getSafeSpawn();
					$player->teleport($arenaspawn, 0, 0);
					$this->SoupItems($player);
					$player->sendMessage( $this->prefix ." you have joined SoupFFA!");
					return;
				}
				else{
					$player->sendMessage( $this->prefix ." §c you can not join SoupFFA!");
					return;
				}
			}
		}
	}
	
	public function onSignCreate(SignChangeEvent $event){
		if($event->getPlayer()->isOp()){
			if($event->getLine(0) == "SoupFFA"){
				$event->setLine(0, $this->prefix);
				$event->setLine(2, "§2Join");
			}
		}
	}
	
	#Eigene
	public function SoupItems($player){
		$player->getInventory()->clearAll();
		$slots = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34);
		foreach($slots as $s){
		    $player->getInventory()->setItem($s, Item::get(282));
		}
		$player->getInventory()->setItem(0, Item::get(267));
	}
}