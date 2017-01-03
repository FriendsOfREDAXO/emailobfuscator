<?php

/** @var rex_addon $this */

echo rex_view::title($this->i18n('title'));

$content = '';

$func = rex_request('func', 'string');

if ($func == 'update') {

    $this->setConfig(rex_post('config', [
        ['autoload_css', 'bool'],
        ['autoload_js', 'bool'],
        ['articles', 'string'],
        ['templates', 'array[int]']
    ]));

    echo rex_view::success($this->i18n('config_saved'));
}

/* assets */

$content .= '
    <fieldset>
        <legend>' . $this->i18n('config_assets') . '</legend>';

$formElements = [];
$n = [];
$n['label'] = '<label for="autoload_css">' . $this->i18n('config_assets_css') . '</label>';
$n['field'] = '<input type="checkbox" id="autoload_css" name="config[autoload_css]" value="1" ' . ($this->getConfig('autoload_css') ? ' checked="checked"' : '') . '>';
$formElements[] = $n;

$n = [];
$n['label'] = '<label for="autoload_js">' . $this->i18n('config_assets_js') . '</label>';
$n['field'] = '<input type="checkbox" id="autoload_js" name="config[autoload_js]" value="1" ' . ($this->getConfig('autoload_js') ? ' checked="checked"' : '') . '>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$assets = $fragment->parse('core/form/checkbox.php');

$formElements = [];
$n = [];
$n['label'] = $this->i18n('config_load_assets');
$n['field'] = $assets;
$n['note'] = rex_i18n::rawMsg('emailobfuscator_config_assets_note', rex_url::backendPage('packages', ['subpage' => 'help', 'package' => $this->getPackageId()]));
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '
    </fieldset>';


/* whitelist */

$content .= '
    <fieldset>
        <legend>' . $this->i18n('config_whitelist') . '</legend>';

$formElements = [];
$n = [];
$n['label'] = '<label for="emailobfuscator-config-articles">' . $this->i18n('config_articles') . '</label>';
$n['field'] = rex_var_linklist::getWidget(1, 'config[articles]', $this->getConfig('articles'));
$formElements[] = $n;

$n = [];
$n['label'] = '<label for="emailobfuscator-config-templates">' . $this->i18n('config_templates') . '</label>';
$select = new rex_select();
$select->setId('emailobfuscator-config-templates');
$select->setMultiple();
$select->setSize(10);
$select->setAttribute('class', 'form-control');
$select->setName('config[templates][]');
$select->addSqlOptions('SELECT `name`, `id` FROM `' . rex::getTablePrefix() . 'template` ORDER BY `name` ASC');
$select->setSelected($this->getConfig('templates'));
$n['field'] = $select->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '
    </fieldset>';


/* buttons */

$formElements = [];
$n = [];
$n['field'] = '<a class="btn btn-abort" href="' . rex_url::currentBackendPage() . '">' . rex_i18n::msg('form_abort') . '</a>';
$formElements[] = $n;

$n = [];
$n['field'] = '<button class="btn btn-apply rex-form-aligned" type="submit" name="send" value="1"' . rex::getAccesskey(rex_i18n::msg('update'), 'apply') . '>' . rex_i18n::msg('update') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');


/* generate page */

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('config'));
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

$content = '
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        <input type="hidden" name="func" value="update">
        ' . $content . '
    </form>';

echo $content;