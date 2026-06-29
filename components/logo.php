<?php
$logo_class ??= 'logo';
$logo_href   = nav_url('/');
$wrap_h1   ??= false;
?>
<?php if ($wrap_h1): ?><h1 style="display:contents;"><?php endif; ?>
<a href="<?= $logo_href ?>" class="<?= $logo_class ?>" style="text-decoration:none;color:inherit;">
    NEWBALLET<span class="logo-era">ERA</span>
</a>
<?php if ($wrap_h1): ?></h1><?php endif; ?>
