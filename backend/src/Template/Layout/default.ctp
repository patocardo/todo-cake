<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <!-- Include Vue.js built CSS here -->
    <?= $this->Html->css('/js/vue-app/dist/css/app.css') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <!-- Mounting point for your Vue app -->
    <div id="app">decime que es acá</div>

    <!-- Include Vue.js built JS here -->
    <?= $this->Html->script('/js/vue-app/dist/js/app.js') ?>
    <?= $this->Html->script('/js/vue-app/dist/js/chunk-vendors.js') ?>
</body>
</html>
