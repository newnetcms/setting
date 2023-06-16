<?php

use Newnet\Setting\SettingAdminMenuKey;

AdminMenu::addItem(__('core::menu.system.index'), [
    'id' => SettingAdminMenuKey::SYSTEM,
    'icon' => 'fas fa-cogs',
    'order' => 10000,
]);

AdminMenu::addItem(__('core::menu.setting.index'), [
    'id' => SettingAdminMenuKey::SYSTEM_SETTING,
    'parent' => SettingAdminMenuKey::SYSTEM,
    'route' => 'setting.admin.setting.index',
    'icon' => 'fas fa-cog',
    'order' => 1,
]);
