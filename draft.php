
<?php

    require_once 'boot.php';

    if(!isset($_GET['id'])) {
        $draft = null;
    } else {
        $draft = get_draft_local($_GET['id']);
    }

    $faction_data = json_decode(file_get_contents('data/factions.json'), true);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TI4 - Milty Draft</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,300;0,600;1,300&family=Staatliches&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">

        <h1>Milty Draft</h1>

        <div id="tabs">
            <nav>
                <div class="content-wrap">
                    <div class="left">
                        <a class="active" href="#draft">Draft</a>
                        <a href="#map">Map</a>
                        <a href="#log">Log</a>
                        <a href="#config">Config</a>
                    </div>
                    <div class="right">
                        <a href="#faq">FAQ</a>
                    </div>
                </div>
            </nav>
            <div class="tab active" id="draft">
                <div class="content-wrap">
                <?php if($draft == null || $draft == false): ?>
                    <h2 class="error">Draft not found. (or something else went wrong)</h2>
                <?php else: ?>
                    <div class="status" id="turn">
                        <p>It's <span id="current-name">x's</span> turn to draft something. <span id="admin-msg">You are the admin so you can do this for them.</span></p>
                    </div>
                    <div class="status" id="done">
                        <p>This draft is over. <a class="map" href="#">View map</a></p>
                    </div>

                    <div class="players">
                        <?php $i = 0; ?>
                        <?php foreach($draft['draft']['players'] as $player): ?>
                            <?php $i++; ?>
                            <div id="player-<?= $player['id'] ?>" class="player <?= e($draft['draft']['current'] == $i, 'current') ?>">
                                <h3><span><?= $i ?></span> <?= $player['name'] ?></h3>

                                <span class="you" data-id="<?= $player['id'] ?>">you</span>
                                <p>
                                    Slice: <span class="chosen-slice">?</span><br />
                                    Faction: <span class="chosen-faction">?</span><br />
                                    Position: <span class="chosen-position">?</span>
                                </p>
                                <p class="center">
                                    <button class="claim" data-id="<?= $player['id'] ?>">Claim</button>
                                    <button class="unclaim" data-id="<?= $player['id'] ?>">Unclaim</button>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="factions draft-options">
                        <h3>Factions</h3>
                        <div class="options">
                            <?php foreach($draft['factions'] as $f): ?>
                                <?php $faction = $faction_data[$f]; ?>

                                <div class="faction option" data-homesystem="<?= $faction['homesystem'] ?>" data-faction="<?= $faction['name'] ?>">
                                    <div>
                                        <img src="<?= url('img/factions/ti_' . $faction['id'] . '.png') ?>" /><br />

                                        <span><?= $faction['name'] ?></span><br />
                                        <a href="#" data-id="<?= $faction['id'] ?>" class="open-reference">[reference]</a> <a target="_blank" href="<?= $faction['wiki'] ?>" class="more">[wiki]</a><br />
                                        <button class="draft" data-category="faction" data-value="<?= $faction['name'] ?>">Draft</button>
                                        <span class="drafted-by" data-category="faction" data-value="<?= $faction['name'] ?>"></span>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="slices draft-options">
                        <h3>Slices</h3>
                        <div class="options">
                            <?php foreach($draft['slices'] as $slice_id => $slice): ?>
                                <div class="slice option" data-slice="<?= $slice_id ?>">
                                    <div class="slice-graph">
                                        <div class="wrap">
                                            <?php foreach($slice['tiles'] as $i => $tile): ?>
                                                <img class="tile-<?= $i ?>" src="<?= url('img/tiles/ST_' . $tile . '.png') ?>" />
                                                <img class="zoom tile-<?= $i ?>" src="<?= url('img/tiles/ST_' . $tile . '.png') ?>" />
                                            <?php endforeach; ?>
                                            <img class="tile-h" src="<?= url('img/tiles/ST_0.png') ?>" />
                                        </div>
                                    </div>

                                    <div class="slice-info">
                                        <h4>Slice <?= $slice_id + 1 ?></h4>

                                        <div class="info">
                                            <?php foreach($slice['specialties'] as $s): ?>
                                                <img class="tech-specialty" src="<?= url('img/tech/' . $s . '.webp') ?>" alt="<?= $s ?>" />
                                            <?php endforeach; ?>

                                            <?php foreach($slice['wormholes'] as $w): ?>
                                                <abbr class="wormhole" title="<?= $w ?>"><?= e($w == "alpha", '&alpha;', '&beta;') ?></abbr>
                                            <?php endforeach; ?>
                                        </div>

                                        <p class="resource-count">
                                            Total:
                                            <abbr title="resources" class="resources"><?= $slice['total_resources'] ?></abbr>
                                            <abbr title="influence" class="influence"><?= $slice['total_influence'] ?></abbr>
                                        </p>

                                        <p class="resource-count">
                                            Optimal:
                                            <abbr title="resources" class="resources"><?= $slice['optimal_resources'] ?></abbr>
                                            <abbr title="influence" class="influence"><?= $slice['optimal_influence'] ?></abbr>
                                        </p>

                                        <p class="center">
                                            <button class="draft" data-category="slice" data-value="<?= $slice_id ?>">Draft</button>
                                            <span class="drafted-by" data-category="slice" data-value="<?= $slice_id ?>"></span>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>



                    <div class="positions draft-options">
                        <h3>Positions</h3>
                        <div class="options">
                            <?php for($i = 0; $i < count($draft['draft']['players']); $i++): ?>
                                <div class="position option" data-position="<?= $i ?>">
                        <span>
                        <?php if($i == 0): ?>
                            SPEAKER
                        <?php else: ?>
                            <?= ordinal($i + 1); ?>
                        <?php endif; ?>
                            </span>

                                    <button class="draft" data-category="position" data-value="<?= $i ?>">Draft</button>
                                    <span class="drafted-by" data-category="position" data-value="<?= $i ?>"></span>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <script>
                        window.draft = <?= json_encode($draft); ?>;
                    </script>

                <?php endif; ?>
                </div>
            </div>
            <div class="tab" id="config">
                <div class="content-wrap">
                    <h3>Configuration used</h3>

                    <p>
                        <label>Number of Players:</label> <strong><?= count($draft['draft']['players']) ?></strong>
                    </p>
                    <p>
                        <label>Number of Slices:</label> <strong><?= $draft['config']['num_slices'] ?></strong>
                    </p>
                    <p>
                        <label>Number of Factions:</label> <strong><?= $draft['config']['num_factions'] ?></strong>
                    </p>
                    <p>
                        <label>Include PoK:</label> <strong><?= e($draft['config']['include_pok'], 'yes', 'no') ?></strong>
                    </p>
                    <p>
                        <label>Include Keleres:</label> <strong><?= e($draft['config']['include_keleres'], 'yes', 'no') ?></strong>
                    </p>
                    <?php if(isset($draft['config']['must_include_wormholes_and_legendaries'])): ?>
                    <p>
                        <label>Map must include wormholes and Legendary Planets:</label> <strong><?= e($draft['config']['must_include_wormholes_and_legendaries'], 'yes', 'no') ?></strong>
                    </p>
                    <?php endif; ?>
                    <?php if(isset($draft['config']['min_legendaries'])): ?>
                        <p>
                            <label>Minimum wormholes:</label> <strong><?= $draft['config']['min_wormholes'] ?></strong>
                        </p>
                        <p>
                            <label>Minimum legendaries:</label> <strong><?= $draft['config']['min_legendaries'] ?></strong>
                        </p>
                    <?php endif; ?>
                    <p>
                        <label>Max. 1 wormhole per slice:</label> <strong><?= e(isset($draft['config']['max_1_wormhole']) && $draft['config']['max_1_wormhole'], 'yes', 'no') ?></strong>
                    </p>
                    <hr/>
                    <p>
                        <label>Minimum Optimal Influence:</label> <strong><?= $draft['config']['minimum_optimal_influence'] ?></strong>
                    </p>
                    <p>
                        <label>Minimum Optimal Resources:</label> <strong><?= $draft['config']['minimum_optimal_resources'] ?></strong>
                    </p>

                    <p>
                        <label>Minimum Optimal Total:</label> <strong><?= $draft['config']['minimum_optimal_total'] ?></strong>
                    </p>

                    <p>
                        <label>Maximum Optimal Total:</label> <strong><?= $draft['config']['maximum_optimal_total'] ?></strong>
                    </p>

                    <p>
                        <label>Custom Factions:</label> <strong>
                            <?php if($draft['config']['custom_factions'] != null): ?>
                                <?= implode(', ', $draft['config']['custom_factions']) ?>
                            <?php else: ?>
                                no
                            <?php endif; ?>
                    </p>
                    <p>
                        <label>Custom Slices:</label> <strong>
                            <?php if($draft['config']['custom_slices'] != null): ?>
                                <?php foreach($draft['config']['custom_slices'] as $slice): ?>
                                    <?= implode(',', $slice) ?><br />
                                <?php endforeach; ?>
                            <?php else: ?>
                                no
                            <?php endif; ?>
                        </strong>
                    </p>
                    <p>
                        <label>Slices Generated:</label>
                        <strong>
                        <?php foreach($draft['slices'] as $slice_id => $slice): ?>
                                <?= implode(',', $slice['tiles']); ?><br />
                        <?php endforeach; ?>
                        </strong>
                    </p>
                </div>
            </div>
            <div class="tab" id="map">
                <div class="content-wrap">
                    <h3>Map</h3>
                    <div class="map-container">
                        <div class="map-offset">
                            <div id="map-wrap">

                            </div>
                        </div>
                    </div>
                    <h3>Tiles</h3>
                    <p id="tile-gather"></p>
                    <h3>TTS Map String*</h3>
                    <p id="tts-string"></p>
                    <span class="help">*This feature is in development and hasn't been rigorously tested, so some maps might give odd results. If you find something wrong, please <a href="https://twitter.com/samtubbax" target="_blank">reach out</a> and let me know!</span>
                </div>
            </div>
            <div class="tab" id="log">
                <div class="content-wrap">
                    <h3>Log</h3>
                    <div id="log-content"></div>
                </div>
            </div>
            <?php require_once 'faq.php'; ?>
        </div>
    </div>

    <div class="popup" id="confirm-popup">
        <div class="content">
            <p>
                Are you sure you wish to draft the following <span id="confirm-category"></span>: <span id="confirm-value"></span>.<br />
                This can't be undone.
            </p>
            <p>
                <button id="confirm">Confirm</button>
                <button id="confirm-cancel">Cancel</button>
            </p>
        </div>
    </div>

    <div class="popup" id="reference-popup">
        <a class="btn close-reference invert">&times;</a>
        <img data-base="<?= url('img/factions/r_') ?>" src="" />
    </div>

    <div class="popup" id="error-popup">
        <div class="content">
            <p>
                Something went wrong. Maybe you left this tab open too long and the data is outdated. Try refreshing and trying again.
            </p>
            <p>Error: <span id="error-message"></span></p>
            <p>
                <button id="close-error">Ok</button>
            </p>

        </div>
    </div>


    <div id="loading">
        Loading. Please wait.<br />
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
            <circle cx="50" cy="50" r="0" fill="none" stroke="#fefcf8" stroke-width="2">
                <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="0s"></animate>
                <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="0s"></animate>
            </circle>
            <circle cx="50" cy="50" r="0" fill="none" stroke="#fefcf8" stroke-width="2">
                <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="-0.5s"></animate>
                <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="-0.5s"></animate>
            </circle>
        </svg>
    </div>

    <script>
        window.routes = {
            "claim": "<?= url('claim.php') ?>",
            "pick": "<?= url('pick.php') ?>",
            "tile_images": "<?= url('img/tiles') ?>"
        }
    </script>
    <script src="<?= url('js/vendor.js?v=' . $_ENV['VERSION']) ?>"></script>
    <script src="<?= url('js/draft.js?v=' . $_ENV['VERSION']) ?>"></script>
    <script src="<?= url('js/generate-map.js?v=' . $_ENV['VERSION']) ?>"></script>
</body>
