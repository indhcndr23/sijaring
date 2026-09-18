<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">LAYANAN INTERNAL</li>
                <li class="static-menu mm-active">
                    <a href="/layanan-internal" class="active" aria-expanded="true">
                        <i class="bx bx-home"></i> Home
                    </a>
                </li>

                <?php foreach ($menus as $head => $menu): ?>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="<?= esc($menu->icon); ?>"></i>
                            <span><?= esc(ucwords(str_replace('_', ' ', $head))); ?></span>
                        </a>

                        <ul class="sub-menu" aria-expanded="false">
                            <?php foreach ($menu->units as $unit): ?>
                                <li>
                                    <a href="javascript:void(0);" class="has-arrow">
                                        <?= esc($unit->nama); ?>
                                    </a>

                                    <ul class="sub-menu" aria-expanded="false">
                                        <?php foreach ($unit->items as $item): ?>
                                            <li>
                                                <a href="<?= esc($item->url); ?>" target="_blank">
                                                    <?= esc($item->label); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                    </li>
                <?php endforeach; ?>

            </ul>
        </div>
    </div>
</div>
