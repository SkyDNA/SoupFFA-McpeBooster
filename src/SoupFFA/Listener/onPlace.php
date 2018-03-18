<?php
/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 16:04
 */

namespace SoupFFA\Listener;

use SoupFFA\SoupFFA;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class onPlace implements Listener {

    public function onPlace(BlockPlaceEvent $event){
        $player = $event->getPlayer();

        $plugin = SoupFFA::getInstance();

        $welt = $player->getLevel()->getFolderName();
        $arenaname = $plugin->getConfig()->get("arena");
        if ($arenaname == $welt) {
            $event->setCancelled();
        }
    }
}
