<section class="section" id="flash-wrapper">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissable">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php
    endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissable">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php
    endif; ?>
</section>
