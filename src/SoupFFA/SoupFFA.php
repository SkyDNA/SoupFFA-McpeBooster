<?php

namespace SoupFFA;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\utils\{Textformat as C, Config};
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\tile\Sign;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class SoupFFA extends PluginBase implements Listener{
	
	public $prefix = C::GRAY."[".C::GREEN."SoupFFA".C::GRAY."]";

	public function onEnable(){
		$this->getLogger()->info($this->prefix . " by McpeBooster!");
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		
		$this->saveResource("/config.yml");
		
		if($this->getConfig()->get("arena") == "debug123"){
			$plugin = $this->getServer()->getPluginManager()->getPlugin("SoupFFA");
			$this->getLogger()->emergency("######################################################");
			$this->getLogger()->emergency(" Please change the SoupFFA world in the config.yml!!!");
			$this->getLogger()->emergency("######################################################");
			$this->getServer()->getPluginManager()->disablePlugin($plugin);
			return;
		}
		
		$this->getServer()->loadLevel($this->getConfig()->get("arena"));
		
	}
	
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$item = $event->getItem();
		$block = $event->getBlock();
		if($item->getId() === Item::MUSHROOM_STEW){
			$player->getInventory()->removeItem($item);
			$player->setHealth($player->getHealth() + 5);
			$player->setFood(20);
			$player->sendTip("§cYou have been healed!");
			return;
		}elseif($player->getLevel()->getTile($block) instanceof Sign) {
			$tile = $player->getLevel()->getTile($block);
			$text = $tile->getText();
			if ($text[0] == $this->prefix) {
				if($text[2] == "§2Join"){
					$this->ArenaJoin($player);
					return;
				}
			}
			$player->sendMessage( $this->prefix ." §cYou can not join SoupFFA!");
			return;
		}
	}
	
	public function onSignCreate(SignChangeEvent $event){
		$player = $event->getPlayer();
		if($event->getLine(0) == "SoupFFA"){
			if($player->isOp()){
				$event->setLine(0, $this->prefix);
				$event->setLine(2, "§2Join");
				$player->sendMessage($this->prefix. " §8JoinSign set!");
				return;
			}
			$player->sendMessage($this->prefix. " §cYou do not have the Permission to do that!");
			return;
		}
	}
	
	public function onDamage(EntityDamageEvent $event) {
		$entity = $event->getEntity();
		$cause = $event->getCause();
		
		if ($cause == EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
			if ($event instanceof EntityDamageByEntityEvent) {
				$killer = $event->getDamager();
				$welt = $killer->getLevel()->getFolderName();
				$arenaname = $this->getConfig()->get("arena");
				if($arenaname == $welt){
					if ($killer instanceof Player) {
					$message = $killer->getName();
					$x = $entity->getX();
					$y = $entity->getY();
					$z = $entity->getZ();
					
					$sx = $entity->getLevel()->getSafeSpawn()->getX();
					$sy = $entity->getLevel()->getSafeSpawn()->getY();
					$sz = $entity->getLevel()->getSafeSpawn()->getZ();
					
					$cp = $this->getConfig()->get("spawnprotection");
					
					if(abs($sx - $x) < $cp && abs($sy - $y) < $cp && abs($sz - $z) < $cp){
						
						$event->setCancelled(true);
						
						$killer->sendMessage($this->prefix . " PvP is only allowed further away from the spawn!");
						return;
					}elseif ($event->getDamage() >= $entity->getHealth()) {
						$event->setCancelled(true);
						
						$arenalevel = $this->getServer()->getLevelByName($arenaname);
						$arenaspawn = $arenalevel->getSafeSpawn();
						$entity->teleport($arenaspawn, 0, 0);
						
						$this->Title($entity, "§4Death", $killer->getName());
						$this->Title($killer, "§2Kill", $entity->getName());
						
						$this->SoupItems($entity);
						$this->SoupItems($killer);
						
						$entity->sendMessage($this->prefix . C::GRAY . " The Player " . C::RED . $killer->getName() . C::GRAY . " has killed you!");
						$killer->sendMessage($this->prefix . C::GRAY . " You have killed " . C::RED . $entity->getName() . C::GRAY . " !");
						return;
						}
					}
				}
			}
		}
	}
	
	/**
	* @param Player $player
	*/
	
	public function SoupItems(Player $player){
		  
		$inv = $player->getInventory();
		$inv->clearAll();
		$slots = array(1,2,3,4,5,6,7,8);
		foreach($slots as $s){
		    $inv->setItem($s, Item::get(282));
		}
		
		if($player->hasPermission("soupffa.vip")){
			$this->vipPlayer($player);
		}else{
			$this->normalPlayer($player);
		}
		$player->setFood(20);
		$player->setHealth(20);
	}
	
	/**
	* @param Player $player
	*/
	
	public function normalPlayer(Player $player){
		$inv = $player->getInventory();
		
		$sword = $this->getConfig()->get("sword");
		
		$inv->setItem(0, Item::get($sword));
		
		$helmet = $this->getConfig()->get("helmet");
		$chestplate = $this->getConfig()->get("chestplate");
		$leggings = $this->getConfig()->get("leggings");
		$boots = $this->getConfig()->get("boots");
		
		$inv->setHelmet(Item::get($helmet));
		$inv->setChestplate(Item::get($chestplate));
		$inv->setLeggings(Item::get($leggings));
		$inv->setBoots(Item::get($boots));
		$inv->sendArmorContents($player);
	}
	
	/**
	* @param Player $player
	*/
	
	public function vipPlayer(Player $player){
		$inv = $player->getInventory();
		
		$sword = $this->getConfig()->get("vipsword");
		
		$inv->setItem(0, Item::get($sword));
		
		$helmet = $this->getConfig()->get("viphelmet");
		$chestplate = $this->getConfig()->get("vipchestplate");
		$leggings = $this->getConfig()->get("vipleggings");
		$boots = $this->getConfig()->get("vipboots");
		
		$inv->setHelmet(Item::get($helmet));
		$inv->setChestplate(Item::get($chestplate));
		$inv->setLeggings(Item::get($leggings));
		$inv->setBoots(Item::get($boots));
		$inv->sendArmorContents($player);
	}
	
	/**
	* @param Player $player
	* @param $line1
	* @param $line2
	*/
	
	public function Title(Player $player, string $line1, string $line2){
		if($this->getServer()->getName() == "PocketMine-MP"){
			$player->addTitle($line1, $line2);
			return;
		}else{
			$player->sendTitle($line1, $line2);
			return;
		}
	}
	
	/**
	* @param Player $player
	*/
	
	public function ArenaJoin(Player $player){
		$arenaname = $this->getConfig()->get("arena");
		
		if(!$this->getServer()->isLevelLoaded($arenaname)){
			$this->getServer()->loadLevel($arenaname);
		}
		
		$arenalevel = $this->getServer()->getLevelByName($arenaname);
		$arenaspawn = $arenalevel->getSafeSpawn();
		$arenalevel->loadChunk($arenaspawn->getX(), $arenaspawn->getZ());
		$player->teleport($arenaspawn, 0, 0);
		$this->SoupItems($player);
		$player->sendMessage( $this->prefix ." You have joined SoupFFA!");
		$this->Title($player, "§6|§2SoupFFA§6|", "§8by McpeBooster");
	}
	
	/**
	* @param Player $player
	*/
	
	public function ArenaLeave(Player $player){
		
		$default = $this->getServer()->getDefaultLevel();
		$spawn = $default->getSafeSpawn();
		$player->teleport($spawn, 0, 0);
		$player->setFood(20);
		$player->setHealth(20);
		$inv = $player->getInventory();
		$inv->clearAll();
		$player->sendMessage($this->prefix." You left SoupFFA!");
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		if($cmd->getName() == "soupffa"){
			if($sender instanceof Player){
				$player = $sender;
				if(!empty($args[0])){
					if($args[0] == "join"){
					$world = $player->getLevel()->getFolderName();
					$arenaname = $this->getConfig()->get("arena");
						
						if($arenaname == $world){
							$player->sendMessage($this->prefix. " You are already in an SoupFFA Arena!");
							return;
						}else{
							$this->ArenaJoin($player);
							return;
						}
					
					}elseif($args[0] == "leave" or $args[0] == "quit"){
						$world = $player->getLevel()->getFolderName();
						$arenaname = $this->getConfig()->get("arena");
						
						if($arenaname == $world){
							$this->ArenaLeave($player);
							return;
						}else{
							$player->sendMessage($this->prefix. " You are not in an SoupFFA Arena!");
							return;
						}
					}
					
				}
				$player->sendMessage($this->prefix. " Syntax: /soupffa <join/quit>!");
				return;
			}
			$sender->sendMessage($this->prefix." §cThis Command can be only used Ingame!");
			return;
		}
	}
	
}