<?php
global $force_name;?>
@include('wargame::TMCW.Amph.amph-header')
<style type="text/css">
    <?php
         include_once "TMCW/Amph/all.css";
?>
</style>
</head>
<?php
global $force_name;
$playerNum = !empty($playerNum) ? $playerNum : 0;
$player = $force_name[$playerNum];
$youAre = $force_name[$playerNum];
$deployTwo = $playerOne = $force_name[1];
$deployOne = $playerTwo = $force_name[2];
    //@include_once "view.php";
        ?>
@section('commonRules')
    @include('wargame::TMCW.commonRules')
@endsection
@section('victoryConditions')
    @include('wargame::TMCW.Amph.victoryConditions')
@endsection
@section('exclusiveRules')
    @include('wargame::TMCW.exclusiveRules')
@endsection
@include('wargame::stdIncludes.view')
