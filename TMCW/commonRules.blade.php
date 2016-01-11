<?php
/**
 *
 * Copyright 2012-2015 David Rodal
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
?><style type="text/css">
    #header {
        /*display:none;*/
    }

    .exclusive {
        color: green;
    }

    #gameRules {
        font-family: sans-serif;
    }

    #gameRules table, #gameRules th, #gameRules td {
        border: 1px solid black;
    }

    #gameRules h1 {
        color: #338833;
        font-size: 60px;

    }


</style>
<div class="dropDown" id="GRWrapper" style="font-weight:normal">
    <h4 class="WrapperLabel" title="Game Rules">Rules</h4>

    <div id="GR" style="display:none">
        <div class="close">X</div>
        <div id="gameRules">
            <?php $playerOne = $force_name[1];
            $playerTwo = $force_name[2]; ?>
            <h1>    <?= $name ?>    </h1>

            <h2>Rules of Play</h2>

            <h2>Design Context</h2>

            <p><?= $name ?> is a continuation of the gaming framework first pioneered by the game The Martian Civil War.
                We hope you enjoy playing our game.</p>


            <ol class="topNumbers">
                <li id="contentsRules">
                    @include('wargame::TMCW.commonContents');
                    <?php //include "commonContents.php";?>
                </li>
                <li id="unitsRules">
                    {{--@include('wargame::TMCW.commonUnitsRules');--}}

                <?php //include "commonUnitsRules.php" ?>
                </li>
                <li id="sopRules">
                    @include('wargame::TMCW.commonSequenceOfPlay')

                <?php //include "commonSequenceOfPlay.php" ?>
                </li>
                <li id="stackingRules">
                    @include('wargame::TMCW.commonStacking')
                <?php //include "commonStacking.php" ?>
                </li>
                <li id="moveRules">
                    @include('wargame::TMCW.commonMoveRules')

                <?php //include "commonMoveRules.php" ?>
                </li>
                <li id="zocRules">
                    @include('wargame::TMCW.commonZocRules')

                <?php //include "commonZocRules.php"; ?>

                </li>
                <li id="combatRules">
                    {{--@include('wargame::TMCW.commonCombatRules')--}}

                <?php //include "commonCombatRules.php"; ?>
                </li>

                <li class="exclusive" id="victoryConditions">
                    @include('wargame::TMCW.victoryConditions')

                <?php //include "victoryConditions.php"; ?>
                </li>

            </ol>
        </div>
    </div>
</div>

