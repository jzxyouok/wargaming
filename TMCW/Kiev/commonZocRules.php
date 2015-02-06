<?php
/**
 * Copyright davidrodal.com
 * User: david
 * Date: 2/1/15
 * Time: 4:02 PM
 */
?>
<span class="big">Zones of Control</span>

<p>
    The six hexes surrounding a unit constitute it's Zone of Control or <abbr
        title="Zone Of Control">ZOC</abbr>.
    <abbr title="Zone Of Control">ZOC</abbr>'s affect the movement of enemy units. The affect is
    dependant upon
    many factors.
</p>
<ol>
    <li><span class="big">Effects on Movement</span>

        <ol>
        <li>When a unit enters a hostile <abbr title="Zone Of Control">ZOC</abbr> it expends 3 additional
            <abbr title="Zone Of Control">MP</abbr>to enter the hex.</li>
            <li> When a unit leaves a <abbr title="Zone Of Control">ZOC</abbr> it must expend 2 additional <abbr title="Zone Of Control">MP</abbr>'s.</li>
            <li>If a unit does not have enough <abbr title="Zone Of Control">MP</abbr>'s to enter or leave a hex it may not do so.</li>
            <li>A unit may enter or leave as many <abbr title="Zone Of Control">ZOC</abbr> as they have <abbr title="Zone Of Control">MP</abbr>'s for.</li>
            <li>A unit may always move at least one hex, <em>unless</em> it is moving directly from one <abbr title="Zone Of Control">ZOC</abbr> to another.</li>
        </ol>
    </li>
    <li><span class="big">Effects on Combat</span>

        <p>If a unit is forced to retreat and cannot do so without entering an enemy <abbr title="Zone Of Control">ZOC</abbr>, they must instead
        lose one step.</p>
        <ol>
            <li>Friendly units negate enemy <abbr title="Zone Of Control">ZOC</abbr>'s for purposes of retreat, assuming the
                stacking rules are not violated. See <a href="#stackingRules">Stacking</a>.
            </li>
        </ol>

    </li>
</ol>