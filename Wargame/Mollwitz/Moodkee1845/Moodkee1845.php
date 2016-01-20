<?php
namespace Wargame\Mollwitz\Moodkee1845;
use \Wargame\Mollwitz\UnitFactory;
/**
 *
 * Copyright 2012-2015 David Rodal
 * User: David Markarian Rodal
 * Date: 3/8/15
 * Time: 5:48 PM
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
define("BRITISH_FORCE", 1);
define("SIKH_FORCE", 2);

global $force_name;
$force_name = array();
$force_name[SIKH_FORCE] = "Sikh";
$force_name[BRITISH_FORCE] = "British";

class Moodkee1845 extends \Wargame\Mollwitz\IndiaCore
{
    public $specialHexesMap = ['SpecialHexA'=>1, 'SpecialHexB'=>2, 'SpecialHexC'=>0];

    static function playMulti($name, $wargame, $arg = false)
    {
        $deployTwo = $playerOne = "British";
        $deployOne = $playerTwo = "Sikh";
        @include_once "playMulti.php";
    }

    static function getPlayerData($scenario){
        $forceName = ["Observer", "British", "Sikh"];
        return \Wargame\Battle::register($forceName,
            [$forceName[0], $forceName[2], $forceName[1]]);
    }

    function save()
    {
        $data = parent::save();
        $data->game = $this->game;
        $data->specialHexA = $this->specialHexA;
        $data->specialHexB = $this->specialHexB;

        return $data;
    }

    public function init()
    {

        $artRange = 3;
        $scenario = $this->scenario;

        UnitFactory::$injector = $this->force;


        /* Sikh */
        if ($scenario->commandControl) {
            for ($i = 0; $i < 3; $i++) {
                UnitFactory::create("infantry-1", SIKH_FORCE, "deployBox", "SikhInfBadge.png", 1, 1, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Sikh", false, 'hq');
            }
        }
        for ($i = 0; $i < 12; $i++) {
            UnitFactory::create("infantry-1", SIKH_FORCE, "deployBox", "SikhInfBadge.png", 4, 4, 3, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Sikh", false, 'infantry');
        }
        for ($i = 0; $i < 16; $i++) {
            UnitFactory::create("infantry-1", SIKH_FORCE, "deployBox", "SikhCavBadge.png", 4, 4, 5, true, STATUS_CAN_DEPLOY, "A", 1, 1, "Sikh", false, 'cavalry');
        }
        for ($i = 0; $i < 3; $i++) {
            UnitFactory::create("infantry-1", SIKH_FORCE, "deployBox", "SikhArtBadge.png", 4, 4, 3, true, STATUS_CAN_DEPLOY, "A", 1, 3, "Sikh", false, 'artillery');
        }


        /* British */
        if ($scenario->commandControl) {
            for ($i = 0; $i < 4; $i++) {
                UnitFactory::create("infantry-1", BRITISH_FORCE, "deployBox", "BritInfBadge.png", 1, 1, 5, true, STATUS_CAN_DEPLOY, "B", 1, 1, "British", false, 'hq');
            }
        }
        for ($i = 0; $i < 4; $i++) {
            UnitFactory::create("infantry-1", BRITISH_FORCE, "deployBox", "BritInfBadge.png", 7, 7, 4, true, STATUS_CAN_DEPLOY, "B", 1, 1, "British", false, 'infantry');
        }
        for ($i = 0; $i < 9; $i++) {
            UnitFactory::create("infantry-1", BRITISH_FORCE, "deployBox", "NativeInfBadge.png", 6, 6, 4, true, STATUS_CAN_DEPLOY, "B", 1, 1, "Native", false, 'infantry');
        }
        for ($i = 0; $i < 1; $i++) {
            UnitFactory::create("infantry-1", BRITISH_FORCE, "deployBox", "BritCavBadge.png", 7, 7, 6, true, STATUS_CAN_DEPLOY, "B", 1, 1, "British", false, 'cavalry');
        }
        for ($i = 0; $i < 5; $i++) {
            UnitFactory::create("infantry-1", BRITISH_FORCE, "deployBox", "NativeCavBadge.png", 6, 6, 6, true, STATUS_CAN_DEPLOY, "B", 1, 1, "Native", false, 'cavalry');
        }
        for ($i = 0; $i < 2; $i++) {
            UnitFactory::create("infantry-1", BRITISH_FORCE, "deployBox", "BritArtBadge.png", 4, 4, 3, true, STATUS_CAN_DEPLOY, "B", 1, 4, "British", false, 'artillery');
        }
        for ($i = 0; $i < 2; $i++) {
            UnitFactory::create("infantry-1", BRITISH_FORCE, "deployBox", "BritHorArtBadge.png", 4, 4, 5, true, STATUS_CAN_DEPLOY, "B", 1, 3, "British", false, 'horseartillery');
        }


    }

    function __construct($data = null, $arg = false, $scenario = false, $game = false)
    {
        parent::__construct($data, $arg, $scenario, $game);

        if ($data) {
            $this->specialHexA = $data->specialHexA;
            $this->specialHexB = $data->specialHexB;
        } else {

            $this->victory = new \Victory("\\Mollwitz\\Moodkee1845\\moodkee1845VictoryCore");
            $this->mapData->blocksZoc->blocked = true;
            $this->mapData->blocksZoc->blocksnonroad = true;

            $this->moveRules->enterZoc = "stop";
            $this->moveRules->exitZoc = "stop";
            $this->moveRules->noZocZoc = true;
            $this->moveRules->zocBlocksRetreat = true;

            // game data

            $this->gameRules->setMaxTurn(12);
            $this->gameRules->setInitialPhaseMode(RED_DEPLOY_PHASE, DEPLOY_MODE);
            $this->gameRules->attackingForceId = RED_FORCE; /* object oriented! */
            $this->gameRules->defendingForceId = BLUE_FORCE; /* object oriented! */
            $this->force->setAttackingForceId($this->gameRules->attackingForceId); /* so object oriented */


            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_DEPLOY_PHASE, DEPLOY_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(RED_DEPLOY_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, false);

            $this->gameRules->addPhaseChange(BLUE_MOVE_PHASE, BLUE_COMBAT_PHASE, COMBAT_SETUP_MODE, BLUE_FORCE, RED_FORCE, false);
            $this->gameRules->addPhaseChange(BLUE_COMBAT_PHASE, RED_MOVE_PHASE, MOVING_MODE, RED_FORCE, BLUE_FORCE, false);

            $this->gameRules->addPhaseChange(RED_MOVE_PHASE, RED_COMBAT_PHASE, COMBAT_SETUP_MODE, RED_FORCE, BLUE_FORCE, false);
            $this->gameRules->addPhaseChange(RED_COMBAT_PHASE, BLUE_MOVE_PHASE, MOVING_MODE, BLUE_FORCE, RED_FORCE, true);

        }
    }
}