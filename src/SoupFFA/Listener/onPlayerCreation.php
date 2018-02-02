<?php
/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 15:49
 */

namespace SoupFFA\Listener;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use SoupFFA\Player\SoupPlayer;

class onPlayerCreation implements Listener {

    public function onPlayerCreation(PlayerCreationEvent $event){
        $event->setPlayerClass(SoupPlayer::class);
    }
}