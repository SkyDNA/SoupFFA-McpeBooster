<?php
/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 16:02
 */

namespace SoupFFA\Listener;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use SoupFFA\SoupFFA;

class onBreak implements Listener {

    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();

        $plugin = SoupFFA::getInstance();

        $welt = $player->getLevel()->getFolderName();
        $arenaname = $plugin->getConfig()->get("arena");
        if ($arenaname == $welt) {
            $event->setCancelled();
        }
    }
}