<?php
/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 14:39
 */

namespace SoupFFA\Player;


use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\network\Sourceinterface;
use SoupFFA\SoupFFA;

class SoupPlayer extends Player {

    private $plugin;

    /**
     * SoupPlayer constructor.
     * @param Sourceinterface $interface
     * @param $clientID
     * @param $ip
     * @param $port
     */
    public function __construct(Sourceinterface $interface, $clientID, $ip, $port) {
        parent::__construct($interface, $clientID, $ip, $port);
        $this->plugin = SoupFFA::getInstance();
    }

    /**
     * @return bool
     */
    public function ArenaJoin() {
        $arenaname = $this->plugin->getConfig()->get("arena");

        if (!$this->plugin->getServer()->isLevelLoaded($arenaname)) {
            $this->plugin->getServer()->loadLevel($arenaname);
        }

        $arenalevel = $this->getServer()->getLevelByName($arenaname);
        $arenaspawn = $arenalevel->getSafeSpawn();
        $arenalevel->loadChunk($arenaspawn->getX(), $arenaspawn->getZ());
        $this->teleport($arenaspawn, 0, 0);
        $this->giveKit();
        $this->sendMessage(SoupFFA::PREFIX . $this->plugin->getLanguage()->get("player.join"));
        $this->plugin->updateSign($arenaname);
        return true;
    }

    /**
     * @return bool
     */
    public function ArenaLeave() {
        $default = $this->plugin->getServer()->getDefaultLevel();
        $spawn = $default->getSafeSpawn();
        $this->teleport($spawn, 0, 0);
        $this->setFood(20);
        $this->setHealth(20);
        $this->getInventory()->clearAll();
        $this->sendMessage(SoupFFA::PREFIX . $this->plugin->getLanguage()->get("player.quit"));
        $this->plugin->updateSign($this->plugin->getConfig()->get("arena"));
        return true;
    }

    /**
     * @return bool
     */
    public function giveKit() {
        $inv = $this->getInventory();
        $inv->clearAll();
        $slots = array(1, 2, 3, 4, 5, 6, 7, 8);
        foreach ($slots as $s) {
            $inv->setItem($s, Item::get(282));
        }

        if ($this->plugin->getConfig()->get("enablevip") == true && $this->hasPermission($this->plugin->getConfig()->get("permissionvip"))) {
            $sword = $this->plugin->getConfig()->get("sword");
            $helmet = $this->plugin->getConfig()->get("helmet");
            $chestplate = $this->plugin->getConfig()->get("chestplate");
            $leggings = $this->plugin->getConfig()->get("leggings");
            $boots = $this->plugin->getConfig()->get("boots");
        } else {
            $sword = $this->plugin->getConfig()->get("vipsword");
            $helmet = $this->plugin->getConfig()->get("viphelmet");
            $chestplate = $this->plugin->getConfig()->get("vipchestplate");
            $leggings = $this->plugin->getConfig()->get("vipleggings");
            $boots = $this->plugin->getConfig()->get("vipboots");
        }

        $inv->setItem(0, Item::get($sword));

        $inv->setHelmet(Item::get($helmet));
        $inv->setChestplate(Item::get($chestplate));
        $inv->setLeggings(Item::get($leggings));
        $inv->setBoots(Item::get($boots));
        $inv->sendArmorContents($this);

        $this->setFood(20);
        $this->setHealth(20);
        return true;
    }
}