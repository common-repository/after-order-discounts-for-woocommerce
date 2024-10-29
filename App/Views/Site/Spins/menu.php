<div class="spin-and-win-menu spin-menu-opener">
    <div class="spin_icon_text" style="opacity: 1; visibility: visible;">
        <span class="privy-floating-text"><?= $spin_to_win_text; ?></span>
    </div>
    <div class="spin_icon_img">
        <img src="<?= AODFW_URL ?>Assets/Images/win-wheel-icon.png">
    </div>
</div>
<roulette-wheel-container class="menu">
    <?= $roulette ?>
</roulette-wheel-container>
<script>
    tinyDrawer();
</script>