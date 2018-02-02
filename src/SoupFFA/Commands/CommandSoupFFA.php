<?php
/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 15:13
 */

namespace SoupFFA\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use SoupFFA\Player\SoupPlayer;
use SoupFFA\SoupFFA;

class CommandSoupFFA extends Command {

    public function __construct() {
        $this->plugin = SoupFFA::getInstance();
        parent::__construct("soupffa", "", "SoupFFA Main Command", ["soupffa", "soup"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if ($sender instanceof SoupPlayer) {
            $player = $sender;
            if (!empty($args[0])) {
                $plugin = SoupFFA::getInstance();
                if ($args[0] == "join") {
                    $world = $player->getLevel()->getFolderName();
                    $arenaname = $plugin->getConfig()->get("arena");

                    if ($arenaname == $world) {
                        $player->sendMessage(SoupFFA::PREFIX . $plugin->getLanguage()->get("player.join.already"));
                        return false;
                    } else {
                        $player->ArenaJoin();
                        return true;
                    }
                } elseif ($args[0] == "leave" or $args[0] == "quit") {
                    $world = $player->getLevel()->getFolderName();
                    $arenaname = $plugin->getConfig()->get("arena");

                    if ($arenaname == $world) {
                        $player->ArenaLeave();
                        return true;
                    } else {
                        $player->sendMessage(SoupFFA::PREFIX . $plugin->getLanguage()->get("player.quit.noarena"));
                        return false;
                    }
                }
            }
            $player->sendMessage(SoupFFA::PREFIX . " Syntax: /soupffa <join/quit>!");
            return false;
        }
        $sender->sendMessage(SoupFFA::PREFIX . " §7by §6McpeBooster§7!");
        return false;
    }
}