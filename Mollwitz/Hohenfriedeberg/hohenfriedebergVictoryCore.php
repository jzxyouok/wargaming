<?php
/*
Copyright 2012-2015 David Rodal

This program is free software; you can redistribute it
and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version

This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
   */

/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 5/7/13
 * Time: 7:06 PM
 * To change this template use File | Settings | File Templates.
 */
include "victoryCore.php";
class hohenfriedebergVictoryCore extends victoryCore
{

    function __construct($data)
    {
        if($data) {
            $this->movementCache = $data->victory->movementCache;
            $this->victoryPoints = $data->victory->victoryPoints;
            $this->gameOver = $data->victory->gameOver;
        } else {
            $this->victoryPoints = array(0, 0, 0);
            $this->movementCache = new stdClass();
            $this->gameOver = false;
        }
    }

    public function reduceUnit($args)
    {
        $unit = $args[0];
        $mult = 1;
        if($unit->class == "cavalry" || $unit->class == "artillery"){
            $mult = 2;
        }
        $this->scoreKills($unit, $mult);
    }

    public function specialHexChange($args)
    {
        $battle = Battle::getBattle();

        list($mapHexName, $forceId) = $args;
        if(in_array($mapHexName,$battle->specialHexA)){
            if ($forceId == PRUSSIAN_FORCE) {
                $this->victoryPoints[PRUSSIAN_FORCE]  += 5;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='prussian'>+5 Prussian vp</span>";
            }
            if ($forceId == AUSTRIAN_FORCE) {
                $this->victoryPoints[PRUSSIAN_FORCE]  -= 5;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='austrian'>-5 Prussian vp</span>";
            }
        }
        if(in_array($mapHexName,$battle->specialHexB) || in_array($mapHexName,$battle->specialHexC)){
            $vp = 5;
            if(in_array($mapHexName,$battle->specialHexC)){
                $vp = 10;
            }
            if ($forceId == AUSTRIAN_FORCE) {
                $this->victoryPoints[AUSTRIAN_FORCE]  += $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='austrian'>+$vp Austrian vp</span>";
            }
            if ($forceId == PRUSSIAN_FORCE) {
                $this->victoryPoints[AUSTRIAN_FORCE]  -= $vp;
                $battle->mapData->specialHexesVictory->$mapHexName = "<span class='prussian'>-$vp Austrian vp</span>";
            }
        }
    }
    protected function checkVictory($attackingId,$battle){
        $prussianWinTurn = 12;
        if($battle->scenario->deployForward){
            $prussianWinTurn = 11;
        }
        $gameRules = $battle->gameRules;
        $turn = $gameRules->turn;
        if(!$this->gameOver){
            $prussianWin = $austrianWin = false;
            if(($this->victoryPoints[AUSTRIAN_FORCE] >= 60) && ($this->victoryPoints[AUSTRIAN_FORCE] - ($this->victoryPoints[PRUSSIAN_FORCE]) >= 10)){
                $austrianWin = true;
            }
            if(($this->victoryPoints[PRUSSIAN_FORCE] >= 60) && ($this->victoryPoints[PRUSSIAN_FORCE] - $this->victoryPoints[AUSTRIAN_FORCE] >= 10)){
                $prussianWin = true;
            }
            if($prussianWin && $turn > $prussianWinTurn && $turn <= 15){
                $this->winner = 0;
                $gameRules->flashMessages[] = "Tie Game";
            }
            if(!$prussianWin && $turn > 15){
                $this->winner = AUSTRIAN_FORCE;
                $gameRules->flashMessages[] = "Austrians Win";
            }
            if($prussianWin && $turn <= $prussianWinTurn){
                $this->winner = PRUSSIAN_FORCE;
                $msg = "Prussian Win 60 On or before turn $prussianWinTurn";
                $gameRules->flashMessages[] = $msg;
            }
            if($austrianWin || $prussianWin){
                $gameRules->flashMessages[] = "Game Over";
                $this->gameOver = true;
                return true;
            }
        }
        return false;
    }
}
